<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Support\CountryCatalog;
use App\Support\PaymentGatewayCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $method = trim((string) $request->string('method'));
        $gateway = trim((string) $request->string('gateway'));

        return view('backend.payments.index', [
            'payments' => Payment::query()
                ->with('order.branch')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('transaction_ref', 'like', "%{$search}%")
                            ->orWhere('method', 'like', "%{$search}%")
                            ->orWhere('gateway_code', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('order', fn ($order) => $order->where('order_no', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($method !== '', fn ($query) => $query->where('method', $method))
                ->when($gateway !== '', fn ($query) => $query->where('gateway_code', $gateway))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'orders' => FoodOrder::query()->latest()->limit(100)->get(['id', 'order_no']),
            'paymentGatewayDefinitions' => PaymentGatewayCatalog::definitions(),
            'countries' => CountryCatalog::options(),
        ]);
    }

    public function show(Payment $payment): View
    {
        return view('backend.payments.show', [
            'payment' => $payment->load('order.restaurant', 'order.branch', 'order.customer', 'order.items'),
            'paymentGatewayDefinitions' => PaymentGatewayCatalog::definitions(),
            'countries' => CountryCatalog::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'food_order_id' => ['required', 'exists:food_orders,id'],
            'method' => ['required', 'in:cash,card,upi,online'],
            'gateway_code' => ['nullable', 'string', 'max:50'],
            'gateway_country' => ['nullable', 'string', 'size:2'],
            'status' => ['required', 'in:pending,paid,failed,refunded'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_ref' => ['nullable', 'string', 'max:120'],
        ]);

        if (! empty($data['gateway_code'])) {
            $data['method'] = PaymentGatewayCatalog::methodFor($data['gateway_code']);
        }

        Payment::query()->create([
            ...$data,
            'paid_at' => $data['status'] === 'paid' ? now() : null,
        ]);

        return back()->with('success', 'Payment added.');
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'method' => ['required', 'in:cash,card,upi,online'],
            'gateway_code' => ['nullable', 'string', 'max:50'],
            'gateway_country' => ['nullable', 'string', 'size:2'],
            'status' => ['required', 'in:pending,paid,failed,refunded'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_ref' => ['nullable', 'string', 'max:120'],
        ]);

        if (! empty($data['gateway_code'])) {
            $data['method'] = PaymentGatewayCatalog::methodFor($data['gateway_code']);
        }

        $payment->update([
            ...$data,
            'paid_at' => $data['status'] === 'paid' ? ($payment->paid_at ?? now()) : null,
        ]);

        return back()->with('success', 'Payment updated.');
    }

    public function slip(Payment $payment)
    {
        $payment->load('order.restaurant', 'order.branch', 'order.customer', 'order.items');

        if (request()->boolean('pdf')) {
            $pdf = Pdf::loadView('backend.payments.slip', ['payment' => $payment])->setPaper('a5', 'portrait');

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'payment-slip-'.$payment->id.'.pdf', ['Content-Type' => 'application/pdf']);
        }

        return view('backend.payments.slip', ['payment' => $payment]);
    }
}


