<?php

namespace App\Support;

use App\Enums\PaymentMethodEnum;
use App\Models\AppSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaymentGatewayCatalog
{
    public static function definitions(): array
    {
        return config('payment_gateways.definitions', []);
    }

    public static function definition(string $code): ?array
    {
        return self::definitions()[$code] ?? null;
    }

    public static function settings(?AppSetting $setting): array
    {
        $meta = $setting?->meta ?? [];

        return $meta['payment_gateways'] ?? [];
    }

    public static function defaultCountry(?AppSetting $setting = null): ?string
    {
        $meta = $setting?->meta ?? [];
        $country = $meta['payment_default_country'] ?? config('payment_gateways.default_country', 'IN');

        return CountryCatalog::normalize($country) ?? config('payment_gateways.default_country', 'IN');
    }

    public static function gatewaySettings(?AppSetting $setting, string $code): array
    {
        $definition = self::definition($code) ?? [];
        $stored = self::settings($setting)[$code] ?? [];

        $merged = array_replace_recursive($definition, $stored);
        $merged['code'] = $code;
        $merged['label'] = trim((string) ($merged['label'] ?? Str::headline($code)));
        $merged['enabled'] = (bool) ($merged['enabled'] ?? false);
        $merged['countries'] = self::normalizeCountries($merged['countries'] ?? []);
        $merged['mode'] = strtolower(trim((string) ($merged['mode'] ?? 'live')));
        $merged['credentials'] = is_array($merged['credentials'] ?? null) ? $merged['credentials'] : [];

        return $merged;
    }

    public static function enabledForCountry(?AppSetting $setting, ?string $countryCode = null): array
    {
        $countryCode = CountryCatalog::normalize($countryCode) ?? self::defaultCountry($setting);
        $gateways = [];

        foreach (self::definitions() as $code => $definition) {
            $gateway = self::gatewaySettings($setting, $code);
            if (! ($gateway['enabled'] ?? false)) {
                continue;
            }
            if (! self::supportsCountry($gateway, $countryCode)) {
                continue;
            }
            $gateways[] = $gateway;
        }

        usort($gateways, fn (array $left, array $right) => ($left['sort'] ?? 999) <=> ($right['sort'] ?? 999));

        return $gateways;
    }

    public static function configuredGateways(?AppSetting $setting): array
    {
        $gateways = [];

        foreach (self::definitions() as $code => $definition) {
            $gateways[] = self::gatewaySettings($setting, $code);
        }

        usort($gateways, fn (array $left, array $right) => ($left['sort'] ?? 999) <=> ($right['sort'] ?? 999));

        return $gateways;
    }

    public static function supportsCountry(array $gateway, ?string $countryCode): bool
    {
        $countryCode = CountryCatalog::normalize($countryCode);
        $countries = self::normalizeCountries($gateway['countries'] ?? []);

        if ($countryCode === null || $countries === ['*'] || in_array('*', $countries, true)) {
            return true;
        }

        return in_array($countryCode, $countries, true);
    }

    public static function normalizeCountries(array|string|null $countries): array
    {
        if (is_string($countries)) {
            $countries = array_filter(array_map('trim', explode(',', $countries)));
        }

        $countries = Arr::wrap($countries);
        $countries = array_map(static fn ($country) => strtoupper(trim((string) $country)), $countries);
        $countries = array_values(array_filter($countries, static fn ($country) => $country !== ''));

        if ($countries === []) {
            return ['*'];
        }

        return array_values(array_unique($countries));
    }

    public static function displayLabel(string $code): string
    {
        return self::definition($code)['label'] ?? Str::headline($code);
    }

    public static function channelFor(string $code): string
    {
        return (string) (self::definition($code)['channel'] ?? 'online');
    }

    public static function methodFor(string $code): string
    {
        return match (strtolower($code)) {
            'cash' => PaymentMethodEnum::CASH->value,
            'stripe' => PaymentMethodEnum::CARD->value,
            'card' => PaymentMethodEnum::CARD->value,
            'paypal', 'razorpay', 'online' => PaymentMethodEnum::ONLINE->value,
            'paytm', 'phonepe', 'gpay', 'upi' => PaymentMethodEnum::UPI->value,
            default => PaymentMethodEnum::ONLINE->value,
        };
    }

    public static function isAvailableForCountry(?AppSetting $setting, string $code, ?string $countryCode): bool
    {
        $gateway = self::gatewaySettings($setting, $code);

        return (bool) ($gateway['enabled'] ?? false) && self::supportsCountry($gateway, $countryCode);
    }

    public static function defaultForCountry(?AppSetting $setting, ?string $countryCode = null): ?string
    {
        $gateways = self::enabledForCountry($setting, $countryCode);

        foreach ($gateways as $gateway) {
            if (($gateway['code'] ?? null) !== 'cash') {
                return $gateway['code'] ?? null;
            }
        }

        return $gateways[0]['code'] ?? null;
    }

    public static function receiptPayload(?AppSetting $setting, string $gatewayCode, float $amount, string $orderNo, ?string $countryCode = null, ?string $currency = null): array
    {
        $gateway = self::gatewaySettings($setting, $gatewayCode);
        $currency ??= (string) ($setting?->currency ?? 'INR');
        $countryCode = CountryCatalog::normalize($countryCode) ?? self::defaultCountry($setting);
        $payload = [
            'gateway_code' => $gatewayCode,
            'gateway_label' => $gateway['label'] ?? self::displayLabel($gatewayCode),
            'channel' => $gateway['channel'] ?? self::channelFor($gatewayCode),
            'country' => $countryCode,
            'currency' => $currency,
            'amount' => round($amount, 2),
            'order_no' => $orderNo,
        ];

        $vpa = trim((string) Arr::get($gateway, 'credentials.upi_vpa', Arr::get($gateway, 'credentials.vpa', '')));
        if (($payload['channel'] ?? null) === 'upi' && $vpa !== '') {
            $merchantName = trim((string) Arr::get($gateway, 'credentials.merchant_name', $setting?->app_name ?: config('app.name')));
            $notePrefix = trim((string) Arr::get($gateway, 'credentials.note_prefix', 'Order'));
            $note = trim($notePrefix.' '.$orderNo);

            $payload['payment_url'] = self::buildUpiIntentUrl($vpa, $merchantName, $amount, $currency, $note);
            $payload['upi_vpa'] = $vpa;
            $payload['merchant_name'] = $merchantName;
            $payload['note'] = $note;
        }

        return $payload;
    }

    public static function buildUpiIntentUrl(string $vpa, string $merchantName, float $amount, string $currency, string $note): string
    {
        $query = http_build_query([
            'pa' => $vpa,
            'pn' => $merchantName,
            'am' => number_format($amount, 2, '.', ''),
            'cu' => strtoupper($currency),
            'tn' => $note,
        ]);

        return 'upi://pay?'.$query;
    }
}
