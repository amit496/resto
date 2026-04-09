<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\FoodOrder;
use App\Services\Payments\PayPalGatewayService;
use Illuminate\Http\RedirectResponse;

class PayPalReturnController extends Controller
{
    public function __construct(private readonly PayPalGatewayService $payPalGatewayService)
    {
    }

    public function __invoke(FoodOrder $order, string $token): RedirectResponse
    {
        abort_if($order->receipt_token === null || ! hash_equals((string) $order->receipt_token, (string) $token), 403);

        $payment = $order->payments()->where('gateway_code', 'paypal')->latest()->first();
        if (! $payment) {
            return redirect()->route('frontend.orders.receipt', ['order' => $order, 'token' => $token])
                ->with('error', 'PayPal payment not found for this order.');
        }

        $setting = AppSetting::query()->first();
        $paypalToken = (string) request()->query('token', '');
        $result = $this->payPalGatewayService->captureFromReturn($order, $payment, $setting, $paypalToken);

        if (! ($result['success'] ?? false)) {
            return redirect()->route('frontend.orders.receipt', ['order' => $order, 'token' => $token])
                ->with('error', $result['error'] ?? 'PayPal payment could not be captured.');
        }

        if (($order->status?->value ?? $order->status) === OrderStatusEnum::PENDING->value) {
            $order->status = OrderStatusEnum::PREPARING->value;
            $order->save();
        }

        return redirect()->route('frontend.orders.receipt', ['order' => $order, 'token' => $token])
            ->with('success', 'Payment completed successfully.');
    }
}

