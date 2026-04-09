<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatusEnum;
use App\Models\AppSetting;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayPalGatewayService
{
    public function createCheckout(FoodOrder $order, Payment $payment, ?AppSetting $setting): array
    {
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'paypal');
        $credentials = $gateway['credentials'] ?? [];
        $clientId = trim((string) ($credentials['client_id'] ?? ''));
        $clientSecret = trim((string) ($credentials['client_secret'] ?? ''));
        $mode = strtolower(trim((string) ($credentials['mode'] ?? $gateway['mode'] ?? 'live')));

        if ($clientId === '' || $clientSecret === '') {
            return [
                'success' => false,
                'error' => 'PayPal credentials are missing in settings.',
            ];
        }

        $base = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $currency = strtoupper((string) ($setting?->currency ?: 'USD'));
        $returnUrl = route('frontend.paypal.return', ['order' => $order, 'token' => $order->receipt_token]);
        $cancelUrl = route('frontend.paypal.cancel', ['order' => $order, 'token' => $order->receipt_token]);

        try {
            $token = $this->accessToken($base, $clientId, $clientSecret);

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($base.'/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $order->order_no,
                            'description' => 'Order '.$order->order_no,
                            'amount' => [
                                'currency_code' => $currency,
                                'value' => number_format((float) $payment->amount, 2, '.', ''),
                            ],
                        ],
                    ],
                    'application_context' => [
                        'brand_name' => (string) ($setting?->app_name ?: config('app.name')),
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                    ],
                ]);

            $response->throw();
            $payload = $response->json();
            $paypalOrderId = (string) ($payload['id'] ?? '');
            $approve = collect($payload['links'] ?? [])
                ->firstWhere('rel', 'approve');
            $checkoutUrl = is_array($approve) ? (string) ($approve['href'] ?? '') : '';

            if ($paypalOrderId === '' || $checkoutUrl === '') {
                return [
                    'success' => false,
                    'error' => 'PayPal checkout could not be created.',
                ];
            }

            $payment->forceFill([
                'status' => PaymentStatusEnum::PENDING->value,
                'paid_at' => null,
                'transaction_ref' => $payment->transaction_ref ?: $paypalOrderId,
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'provider' => 'paypal',
                    'mode' => $mode,
                    'paypal_order_id' => $paypalOrderId,
                    'checkout_url' => $checkoutUrl,
                    'currency' => $currency,
                ]),
            ])->save();

            return [
                'success' => true,
                'checkout_url' => $checkoutUrl,
                'paypal_order_id' => $paypalOrderId,
            ];
        } catch (Throwable $e) {
            Log::warning('PayPal checkout creation failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'PayPal checkout is temporarily unavailable.',
            ];
        }
    }

    public function captureFromReturn(FoodOrder $order, Payment $payment, ?AppSetting $setting, string $tokenFromReturn): array
    {
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'paypal');
        $credentials = $gateway['credentials'] ?? [];
        $clientId = trim((string) ($credentials['client_id'] ?? ''));
        $clientSecret = trim((string) ($credentials['client_secret'] ?? ''));
        $mode = strtolower(trim((string) ($credentials['mode'] ?? $gateway['mode'] ?? 'live')));

        $base = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        if ($clientId === '' || $clientSecret === '') {
            return ['success' => false, 'error' => 'PayPal credentials are missing in settings.'];
        }

        $paypalOrderId = $tokenFromReturn ?: (string) ($payment->gateway_payload['paypal_order_id'] ?? '');
        if ($paypalOrderId === '') {
            return ['success' => false, 'error' => 'PayPal order token is missing.'];
        }

        try {
            $accessToken = $this->accessToken($base, $clientId, $clientSecret);

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post($base.'/v2/checkout/orders/'.$paypalOrderId.'/capture', []);
            $response->throw();
            $payload = $response->json();

            $status = strtoupper((string) ($payload['status'] ?? ''));
            if ($status !== 'COMPLETED') {
                return [
                    'success' => false,
                    'error' => 'PayPal capture is not completed.',
                    'status' => $status,
                ];
            }

            $captureId = (string) data_get($payload, 'purchase_units.0.payments.captures.0.id', '');

            $payment->forceFill([
                'status' => PaymentStatusEnum::PAID->value,
                'paid_at' => $payment->paid_at ?? now(),
                'transaction_ref' => $captureId ?: $paypalOrderId,
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'webhook_event' => 'return.capture',
                    'paypal_order_id' => $paypalOrderId,
                    'paypal_capture_id' => $captureId ?: null,
                    'paypal_status' => $status,
                ]),
            ])->save();

            return [
                'success' => true,
                'paypal_order_id' => $paypalOrderId,
                'paypal_capture_id' => $captureId,
            ];
        } catch (Throwable $e) {
            Log::warning('PayPal capture failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'paypal_order_id' => $paypalOrderId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'PayPal capture failed.',
            ];
        }
    }

    private function accessToken(string $base, string $clientId, string $clientSecret): string
    {
        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($base.'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        $response->throw();
        $token = (string) ($response->json('access_token') ?? '');

        if ($token === '') {
            throw new RequestException($response);
        }

        return $token;
    }
}

