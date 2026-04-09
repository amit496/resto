<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatusEnum;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class StripeGatewayService
{
    public function createCheckoutSession(FoodOrder $order, Payment $payment, AppSetting $setting): array
    {
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'stripe');
        $credentials = $gateway['credentials'] ?? [];
        $secretKey = trim((string) ($credentials['secret_key'] ?? ''));

        if ($secretKey === '') {
            return [
                'success' => false,
                'error' => 'Stripe secret key is missing in settings.',
            ];
        }

        try {
            $customer = $order->customer ?: Customer::query()->findOrFail($order->customer_id);
            $stripeCustomerId = $this->ensureCustomer($customer, $secretKey);
            $currency = strtolower((string) ($setting->currency ?? 'usd'));
            $successUrl = route('frontend.orders.receipt', [
                'order' => $order,
                'token' => $order->receipt_token,
            ]).'?checkout=session&session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('frontend.orders.receipt', [
                'order' => $order,
                'token' => $order->receipt_token,
            ]).'?checkout=cancelled';

            $response = Http::withBasicAuth($secretKey, '')
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'mode' => 'payment',
                    'customer' => $stripeCustomerId,
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'client_reference_id' => $order->order_no,
                    'payment_method_types[0]' => 'card',
                    'line_items[0][price_data][currency]' => $currency,
                    'line_items[0][price_data][product_data][name]' => 'Order '.$order->order_no,
                    'line_items[0][price_data][unit_amount]' => (int) round(((float) $payment->amount) * 100),
                    'line_items[0][quantity]' => 1,
                    'metadata[order_id]' => (string) $order->id,
                    'metadata[payment_id]' => (string) $payment->id,
                    'metadata[order_no]' => $order->order_no,
                ]);

            $response->throw();
            $payload = $response->json();
            $checkoutUrl = (string) ($payload['url'] ?? '');
            $checkoutSessionId = (string) ($payload['id'] ?? '');
            $paymentIntentId = (string) ($payload['payment_intent'] ?? '');

            if ($checkoutUrl === '' || $checkoutSessionId === '') {
                return [
                    'success' => false,
                    'error' => 'Stripe session could not be created.',
                ];
            }

            $payment->forceFill([
                'status' => PaymentStatusEnum::PENDING->value,
                'paid_at' => null,
                'transaction_ref' => $paymentIntentId ?: $payment->transaction_ref,
                'stripe_checkout_session_id' => $checkoutSessionId,
                'stripe_payment_intent_id' => $paymentIntentId ?: null,
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'provider' => 'stripe',
                    'checkout_session_id' => $checkoutSessionId,
                    'checkout_url' => $checkoutUrl,
                    'payment_intent_id' => $paymentIntentId ?: null,
                    'stripe_customer_id' => $stripeCustomerId,
                ]),
            ])->save();

            return [
                'success' => true,
                'checkout_url' => $checkoutUrl,
                'checkout_session_id' => $checkoutSessionId,
                'payment_intent_id' => $paymentIntentId ?: null,
                'stripe_customer_id' => $stripeCustomerId,
            ];
        } catch (Throwable $e) {
            Log::warning('Stripe checkout session creation failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Stripe checkout is temporarily unavailable.',
            ];
        }
    }

    public function verifyWebhookSignature(string $payload, ?string $signatureHeader, string $secret, int $tolerance = 300): bool
    {
        if ($signatureHeader === null || trim($secret) === '') {
            return false;
        }

        $timestamp = null;
        $expectedSignatures = [];
        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key === 't') {
                $timestamp = (int) $value;
            } elseif ($key === 'v1' && $value) {
                $expectedSignatures[] = $value;
            }
        }

        if (! $timestamp || $expectedSignatures === []) {
            return false;
        }

        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        $signedPayload = $timestamp.'.'.$payload;
        $computed = hash_hmac('sha256', $signedPayload, $secret);

        return in_array($computed, $expectedSignatures, true);
    }

    private function ensureCustomer(Customer $customer, string $secretKey): string
    {
        if ($customer->stripe_customer_id) {
            return $customer->stripe_customer_id;
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->asForm()
            ->post('https://api.stripe.com/v1/customers', [
                'email' => $customer->email,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'metadata[customer_id]' => (string) $customer->id,
            ]);

        $response->throw();
        $stripeCustomerId = (string) ($response->json('id') ?? '');

        if ($stripeCustomerId === '') {
            throw new RequestException($response);
        }

        $customer->forceFill(['stripe_customer_id' => $stripeCustomerId])->save();

        return $stripeCustomerId;
    }
}
