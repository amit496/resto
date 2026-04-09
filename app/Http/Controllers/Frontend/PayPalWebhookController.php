<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Payment;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $setting = AppSetting::query()->first();
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'paypal');
        $credentials = $gateway['credentials'] ?? [];

        $clientId = trim((string) ($credentials['client_id'] ?? ''));
        $clientSecret = trim((string) ($credentials['client_secret'] ?? ''));
        $mode = strtolower(trim((string) ($credentials['mode'] ?? $gateway['mode'] ?? 'live')));
        $webhookId = trim((string) ($credentials['webhook_id'] ?? ''));

        if ($clientId === '' || $clientSecret === '' || $webhookId === '') {
            return response()->json(['message' => 'PayPal webhook is not configured.'], 400);
        }

        $payloadRaw = $request->getContent();
        $event = json_decode($payloadRaw, true);
        if (! is_array($event) || empty($event['event_type']) || empty($event['id'])) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $eventId = (string) $event['id'];
        $eventType = (string) $event['event_type'];

        // Idempotency guard
        $inserted = DB::table('payment_webhook_events')->insertOrIgnore([
            'gateway_code' => 'paypal',
            'event_id' => $eventId,
            'payment_id' => null,
            'payload' => json_encode($event),
            'received_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if (! $inserted) {
            return response()->json(['received' => true]);
        }

        $authAlgo = (string) $request->header('PAYPAL-AUTH-ALGO', '');
        $certUrl = (string) $request->header('PAYPAL-CERT-URL', '');
        $transmissionId = (string) $request->header('PAYPAL-TRANSMISSION-ID', '');
        $transmissionSig = (string) $request->header('PAYPAL-TRANSMISSION-SIG', '');
        $transmissionTime = (string) $request->header('PAYPAL-TRANSMISSION-TIME', '');

        if ($authAlgo === '' || $certUrl === '' || $transmissionId === '' || $transmissionSig === '' || $transmissionTime === '') {
            return response()->json(['message' => 'Missing PayPal headers.'], 400);
        }

        $base = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        try {
            $token = $this->accessToken($base, $clientId, $clientSecret);
            $verify = Http::withToken($token)
                ->acceptJson()
                ->post($base.'/v1/notifications/verify-webhook-signature', [
                    'auth_algo' => $authAlgo,
                    'cert_url' => $certUrl,
                    'transmission_id' => $transmissionId,
                    'transmission_sig' => $transmissionSig,
                    'transmission_time' => $transmissionTime,
                    'webhook_id' => $webhookId,
                    'webhook_event' => $event,
                ]);

            $verify->throw();
            $status = strtoupper((string) ($verify->json('verification_status') ?? ''));
            if ($status !== 'SUCCESS') {
                return response()->json(['message' => 'Invalid signature.'], 400);
            }
        } catch (Throwable $e) {
            Log::warning('PayPal webhook verification failed', [
                'message' => $e->getMessage(),
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);
            return response()->json(['message' => 'Webhook verification failed.'], 400);
        }

        $resource = is_array($event['resource'] ?? null) ? $event['resource'] : [];
        $paypalOrderId = (string) ($resource['id'] ?? '');
        $relatedOrderId = (string) data_get($resource, 'supplementary_data.related_ids.order_id', '');
        $lookupOrderId = $relatedOrderId ?: $paypalOrderId;

        $payment = Payment::query()
            ->where('gateway_code', 'paypal')
            ->where(function ($query) use ($lookupOrderId, $paypalOrderId, $relatedOrderId) {
                if ($lookupOrderId !== '') {
                    $query->where('transaction_ref', $lookupOrderId)
                        ->orWhere('gateway_payload->paypal_order_id', $lookupOrderId);
                }
                if ($paypalOrderId !== '' && $paypalOrderId !== $lookupOrderId) {
                    $query->orWhere('transaction_ref', $paypalOrderId)
                        ->orWhere('gateway_payload->paypal_order_id', $paypalOrderId);
                }
                if ($relatedOrderId !== '' && $relatedOrderId !== $lookupOrderId && $relatedOrderId !== $paypalOrderId) {
                    $query->orWhere('transaction_ref', $relatedOrderId)
                        ->orWhere('gateway_payload->paypal_order_id', $relatedOrderId);
                }
            })
            ->first();

        if (! $payment) {
            return response()->json(['received' => true]);
        }

        DB::table('payment_webhook_events')
            ->where('gateway_code', 'paypal')
            ->where('event_id', $eventId)
            ->update(['payment_id' => $payment->id, 'updated_at' => now()]);

        return DB::transaction(function () use ($payment, $eventType, $eventId, $resource) {
            if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $captureId = (string) ($resource['id'] ?? '');

                $payment->forceFill([
                    'status' => PaymentStatusEnum::PAID->value,
                    'paid_at' => $payment->paid_at ?? now(),
                    'transaction_ref' => $captureId ?: $payment->transaction_ref,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'webhook_event_id' => $eventId,
                        'paypal_capture_id' => $captureId ?: null,
                    ]),
                ])->save();

                $order = $payment->order;
                if ($order && (($order->status?->value ?? $order->status) === OrderStatusEnum::PENDING->value)) {
                    $order->status = OrderStatusEnum::PREPARING->value;
                    $order->save();
                }

                return response()->json(['received' => true]);
            }

            if (in_array($eventType, ['PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.REFUNDED', 'PAYMENT.CAPTURE.REVERSED'], true)) {
                $payment->forceFill([
                    'status' => PaymentStatusEnum::FAILED->value,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'webhook_event_id' => $eventId,
                    ]),
                ])->save();
            } else {
                $payment->forceFill([
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'webhook_event_id' => $eventId,
                    ]),
                ])->save();
            }

            return response()->json(['received' => true]);
        });
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
            throw new \RuntimeException('PayPal access token missing.');
        }

        return $token;
    }
}

