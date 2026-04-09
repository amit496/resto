<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatusEnum;
use App\Models\AppSetting;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RazorpayGatewayService
{
    public function createPaymentLink(FoodOrder $order, Payment $payment, ?AppSetting $setting): array
    {
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'razorpay');
        $credentials = $gateway['credentials'] ?? [];
        $keyId = trim((string) ($credentials['key_id'] ?? ''));
        $keySecret = trim((string) ($credentials['key_secret'] ?? ''));

        if ($keyId === '' || $keySecret === '') {
            return [
                'success' => false,
                'error' => 'Razorpay credentials are missing in settings.',
            ];
        }

        $currency = strtoupper((string) ($setting?->currency ?: 'INR'));
        $amountPaise = (int) round(((float) $payment->amount) * 100);
        $callbackUrl = route('frontend.razorpay.callback', [
            'order' => $order,
            'token' => $order->receipt_token,
        ]);

        try {
            $response = Http::withBasicAuth($keyId, $keySecret)
                ->asJson()
                ->post('https://api.razorpay.com/v1/payment_links', [
                    'amount' => $amountPaise,
                    'currency' => $currency,
                    'reference_id' => $order->order_no,
                    'description' => 'Order '.$order->order_no,
                    'customer' => [
                        'name' => (string) ($order->customer?->name ?: 'Customer'),
                        'email' => (string) ($order->customer?->email ?: ''),
                        'contact' => (string) ($order->customer?->phone ?: ''),
                    ],
                    'notify' => [
                        'sms' => false,
                        'email' => false,
                    ],
                    'callback_url' => $callbackUrl,
                    'callback_method' => 'get',
                ]);

            $response->throw();
            $payload = $response->json();
            $shortUrl = (string) ($payload['short_url'] ?? '');
            $paymentLinkId = (string) ($payload['id'] ?? '');

            if ($shortUrl === '' || $paymentLinkId === '') {
                return [
                    'success' => false,
                    'error' => 'Razorpay payment link could not be created.',
                ];
            }

            $payment->forceFill([
                'status' => PaymentStatusEnum::PENDING->value,
                'paid_at' => null,
                'transaction_ref' => $payment->transaction_ref ?: $paymentLinkId,
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'provider' => 'razorpay',
                    'payment_link_id' => $paymentLinkId,
                    'payment_url' => $shortUrl,
                    'currency' => $currency,
                ]),
            ])->save();

            return [
                'success' => true,
                'payment_url' => $shortUrl,
                'payment_link_id' => $paymentLinkId,
            ];
        } catch (Throwable $e) {
            Log::warning('Razorpay payment link creation failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Razorpay payment is temporarily unavailable.',
            ];
        }
    }
}

