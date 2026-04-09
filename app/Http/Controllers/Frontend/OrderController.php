<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FoodOrder;
use App\Models\Refund;
use App\Services\Frontend\CartService;
use App\Services\Frontend\FrontendOrderService;
use App\Services\Frontend\FrontendPageService;
use App\Services\Payments\PayPalGatewayService;
use App\Services\Payments\RazorpayGatewayService;
use App\Services\Payments\StripeGatewayService;
use App\Support\CountryCatalog;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly FrontendPageService $pageService,
        private readonly CartService $cartService,
        private readonly FrontendOrderService $orderService,
        private readonly StripeGatewayService $stripeGatewayService,
        private readonly RazorpayGatewayService $razorpayGatewayService,
        private readonly PayPalGatewayService $payPalGatewayService
    ) {
    }

    public function index(): View
    {
        $customer = Auth::guard('customer')->user();
        abort_if(! $customer, 403);

        $site = $this->pageService->siteData();

        return view('frontend.orders.index', [
            ...$site,
            'orders' => FoodOrder::query()
                ->with(['branch', 'payments'])
                ->where('customer_id', $customer->id)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $site = $this->pageService->siteData();
        $customer = Auth::guard('customer')->user();
        $setting = $site['setting'] ?? null;
        $cart = $this->cartService->summary($setting, $customer, (string) request()->string('order_type', 'delivery'));
        if ($cart['items']->isEmpty()) {
            return redirect()->route('frontend.cart.index')->with('success', 'Add items to cart before checkout.');
        }

        if ($setting && $setting->allow_guest_checkout === false && ! $customer) {
            return redirect()->route('customer.login')->with('error', 'Please login to continue checkout.');
        }

        return view('frontend.orders.checkout', [
            ...$site,
            'cart' => $cart,
            'branches' => Branch::query()->where('status', 'active')->orderBy('name')->get(),
            'customer' => $customer,
            'customerAddresses' => $customer ? $customer->addresses()->orderByDesc('is_default')->latest()->get() : collect(),
            'countries' => CountryCatalog::options(),
            'paymentGateways' => PaymentGatewayCatalog::configuredGateways($setting),
            'selectedCountry' => old('billing_country', PaymentGatewayCatalog::defaultCountry($setting)),
            'selectedGateway' => old('payment_gateway', PaymentGatewayCatalog::defaultForCountry($setting, old('billing_country', PaymentGatewayCatalog::defaultCountry($setting)))),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $site = $this->pageService->siteData();
        $setting = $site['setting'] ?? null;
        $customer = Auth::guard('customer')->user();
        if ($setting && $setting->allow_guest_checkout === false && ! $customer) {
            return redirect()->route('customer.login')->with('error', 'Please login to continue checkout.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'customer_address_id' => ['nullable', 'integer'],
            'delivery_name' => ['nullable', 'string', 'max:120'],
            'delivery_phone' => ['nullable', 'string', 'max:30'],
            'delivery_address_line_1' => ['nullable', 'string', 'max:500'],
            'delivery_address_line_2' => ['nullable', 'string', 'max:500'],
            'delivery_landmark' => ['nullable', 'string', 'max:120'],
            'delivery_city' => ['nullable', 'string', 'max:80'],
            'delivery_state' => ['nullable', 'string', 'max:80'],
            'delivery_postal_code' => ['nullable', 'string', 'max:20'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'billing_country' => ['required', 'string', 'size:2'],
            'branch_id' => ['required', 'exists:branches,id'],
            'order_type' => ['required', 'in:delivery,takeaway,dine_in'],
            'payment_gateway' => ['required', 'string', 'max:50'],
            'transaction_ref' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:60'],
            'loyalty_points' => ['nullable', 'integer', 'min:0'],
        ]);

        if (! PaymentGatewayCatalog::isAvailableForCountry($setting, $data['payment_gateway'], $data['billing_country'])) {
            return back()
                ->withErrors(['payment_gateway' => 'Selected gateway is not available for the chosen country.'])
                ->withInput();
        }

        $channel = PaymentGatewayCatalog::channelFor($data['payment_gateway']);
        if ($channel === 'upi' && trim((string) ($data['transaction_ref'] ?? '')) === '') {
            return back()
                ->withErrors(['transaction_ref' => 'Transaction reference is required for UPI payments.'])
                ->withInput();
        }

        $data['payment_method'] = PaymentGatewayCatalog::methodFor($data['payment_gateway']);

        $order = $this->orderService->place($data, $setting, $customer);
        $order->load('payments', 'customer');

        $payment = $order->payments->first();
        if ($payment) {
            if ($payment->gateway_code === 'stripe') {
                $stripe = $this->stripeGatewayService->createCheckoutSession($order, $payment, $setting);
                if (($stripe['success'] ?? false) && ! empty($stripe['checkout_url'])) {
                    return redirect()->away($stripe['checkout_url']);
                }

                return redirect()->route('frontend.orders.receipt', [
                    'order' => $order,
                    'token' => $order->receipt_token,
                ])->with('error', $stripe['error'] ?? 'Stripe checkout could not be created.');
            }

            if ($payment->gateway_code === 'razorpay') {
                $razorpay = $this->razorpayGatewayService->createPaymentLink($order, $payment, $setting);
                if (($razorpay['success'] ?? false) && ! empty($razorpay['payment_url'])) {
                    return redirect()->away($razorpay['payment_url']);
                }

                return redirect()->route('frontend.orders.receipt', [
                    'order' => $order,
                    'token' => $order->receipt_token,
                ])->with('error', $razorpay['error'] ?? 'Razorpay payment link could not be created.');
            }

            if ($payment->gateway_code === 'paypal') {
                $paypal = $this->payPalGatewayService->createCheckout($order, $payment, $setting);
                if (($paypal['success'] ?? false) && ! empty($paypal['checkout_url'])) {
                    return redirect()->away($paypal['checkout_url']);
                }

                return redirect()->route('frontend.orders.receipt', [
                    'order' => $order,
                    'token' => $order->receipt_token,
                ])->with('error', $paypal['error'] ?? 'PayPal checkout could not be created.');
            }
        }

        return redirect()->route('frontend.orders.show', $order)->with('success', 'Order placed successfully.');
    }

    public function receipt(FoodOrder $order, string $token): View
    {
        abort_if($order->receipt_token === null || ! hash_equals((string) $order->receipt_token, (string) $token), 403);

        return view('frontend.orders.show', [
            ...$this->pageService->siteData(),
            'order' => $order->load(['items.product', 'branch', 'customer', 'payments', 'trackingEvents', 'refunds']),
            'trackingEvents' => $order->trackingEvents()->latest('event_at')->get(),
            'refunds' => $order->refunds()->latest()->get(),
            'canRequestRefund' => false,
        ]);
    }

    public function show(FoodOrder $order): View
    {
        $customer = Auth::guard('customer')->user();
        if ($order->customer_id && (! $customer || $customer->id !== $order->customer_id)) {
            abort(403);
        }

        return view('frontend.orders.show', [
            ...$this->pageService->siteData(),
            'order' => $order->load(['items.product', 'branch', 'customer', 'payments', 'trackingEvents', 'refunds']),
            'trackingEvents' => $order->trackingEvents()->latest('event_at')->get(),
            'refunds' => $order->refunds()->latest()->get(),
            'canRequestRefund' => $customer && $order->payments->contains(fn ($payment) => $payment->status->value === 'paid'),
        ]);
    }

    public function requestRefund(Request $request, FoodOrder $order): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer || $order->customer_id !== $customer->id) {
            abort(403);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = $order->payments()->where('status', 'paid')->latest('paid_at')->first();
        if (! $payment) {
            return back()->with('error', 'No paid payment found for this order.');
        }

        $existingAmount = Refund::query()
            ->where('food_order_id', $order->id)
            ->where('status', '!=', 'rejected')
            ->sum('amount');

        $available = max(0, (float) $payment->amount - (float) $existingAmount);
        if ($data['amount'] > $available) {
            return back()->with('error', 'Requested refund exceeds available amount.');
        }

        Refund::query()->create([
            'payment_id' => $payment->id,
            'food_order_id' => $order->id,
            'amount' => $data['amount'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Refund request submitted.');
    }
}

