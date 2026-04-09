<?php

namespace App\Services\Backend;

use App\Models\AppSetting;
use App\Support\CountryCatalog;
use App\Support\PaymentGatewayCatalog;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class SettingService
{
    use HandlesImageUploads;

    public function upsert(array $data): AppSetting
    {
        $paymentDefaultCountry = Arr::pull($data, 'payment_default_country', null);
        $paymentGateways = Arr::pull($data, 'payment_gateways', []);
        $sliderItems = Arr::pull($data, 'home_sliders', []);
        $bannerItems = Arr::pull($data, 'home_banners', []);
        $howItWorksSteps = Arr::pull($data, 'home_how_it_works', '__missing__');
        $promoPopup = Arr::pull($data, 'home_promo_popup', '__missing__');
        $footerAppLinks = Arr::pull($data, 'footer_app_links', '__missing__');
        $setting = AppSetting::query()->firstOrNew(['restaurant_id' => $data['restaurant_id'] ?? null]);
        $existingMeta = $setting->meta ?? [];
        $setting->fill($data);
        $setting->meta = array_merge($existingMeta, [
            'payment_default_country' => CountryCatalog::normalize($paymentDefaultCountry) ?: ($existingMeta['payment_default_country'] ?? config('payment_gateways.default_country', 'IN')),
            'payment_gateways' => $this->normalizePaymentGateways($paymentGateways, $existingMeta['payment_gateways'] ?? []),
            'home_sliders' => $this->normalizeMediaItems($sliderItems, $existingMeta['home_sliders'] ?? [], 'home-slider'),
            'home_banners' => $this->normalizeMediaItems($bannerItems, $existingMeta['home_banners'] ?? [], 'home-banner'),
            'home_how_it_works' => $howItWorksSteps === '__missing__'
                ? ($existingMeta['home_how_it_works'] ?? [])
                : $this->normalizeHowItWorks(is_array($howItWorksSteps) ? $howItWorksSteps : []),
            'home_promo_popup' => $promoPopup === '__missing__'
                ? ($existingMeta['home_promo_popup'] ?? [])
                : $this->normalizePromoPopup(is_array($promoPopup) ? $promoPopup : [], $existingMeta['home_promo_popup'] ?? []),
            'footer_app_links' => $footerAppLinks === '__missing__'
                ? ($existingMeta['footer_app_links'] ?? [])
                : $this->normalizeFooterAppLinks(is_array($footerAppLinks) ? $footerAppLinks : [], $existingMeta['footer_app_links'] ?? []),
        ]);
        $setting->save();

        return $setting;
    }

    private function normalizeMediaItems(array $items, array $existingItems, string $folder): array
    {
        return collect($items)
            ->map(function ($item, $index) use ($existingItems, $folder) {
                $existingItem = $existingItems[$index] ?? [];
                $desktopExisting = $item['desktop_image_existing'] ?? $existingItem['desktop_image'] ?? null;
                $mobileExisting = $item['mobile_image_existing'] ?? $existingItem['mobile_image'] ?? null;
                $removeDesktop = (bool) ($item['remove_desktop_image'] ?? false);
                $removeMobile = (bool) ($item['remove_mobile_image'] ?? false);

                if ($removeDesktop && $desktopExisting) {
                    $this->deleteImageAndThumb($desktopExisting);
                    $desktopExisting = null;
                }

                if ($removeMobile && $mobileExisting) {
                    $this->deleteImageAndThumb($mobileExisting);
                    $mobileExisting = null;
                }

                $desktopPath = $this->storeMediaImage($item['desktop_image'] ?? null, $desktopExisting, $folder.'/desktop');
                $mobilePath = $this->storeMediaImage($item['mobile_image'] ?? null, $mobileExisting, $folder.'/mobile');

                if (! $desktopPath && ! $mobilePath) {
                    return null;
                }

                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'button_label' => trim((string) ($item['button_label'] ?? '')),
                    'button_url' => trim((string) ($item['button_url'] ?? '')),
                    'desktop_image' => $desktopPath,
                    'mobile_image' => $mobilePath ?: $desktopPath,
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ];
            })
            ->filter(function ($item) {
                return $item !== null && ($item['desktop_image'] || $item['mobile_image']);
            })
            ->values()
            ->all();
    }

    private function storeMediaImage(mixed $image, ?string $existingPath, string $folder): ?string
    {
        if (! ($image instanceof UploadedFile)) {
            return $existingPath;
        }

        if ($existingPath) {
            $this->deleteImageAndThumb($existingPath);
        }

        return $this->uploadCompressedImage($image, $folder)['path'];
    }

    private function normalizePaymentGateways(array $gateways, array $existingGateways): array
    {
        $normalized = [];

        foreach (array_keys(config('payment_gateways.definitions', [])) as $code) {
            $stored = $gateways[$code] ?? [];
            $existing = $existingGateways[$code] ?? [];
            $definition = PaymentGatewayCatalog::definition($code) ?? [];

            $enabled = (bool) ($stored['enabled'] ?? $existing['enabled'] ?? ($definition['enabled'] ?? false));
            $label = trim((string) ($stored['label'] ?? $existing['label'] ?? ($definition['label'] ?? $code)));
            $mode = strtolower(trim((string) ($stored['mode'] ?? $existing['mode'] ?? 'live')));
            $countries = PaymentGatewayCatalog::normalizeCountries($stored['countries'] ?? $existing['countries'] ?? ($definition['countries'] ?? ['*']));
            $credentials = $this->normalizeCredentialBag($stored['credentials'] ?? $existing['credentials'] ?? []);

            $normalized[$code] = [
                'enabled' => $enabled,
                'label' => $label,
                'mode' => $mode,
                'countries' => $countries,
                'credentials' => $credentials,
            ];
        }

        return $normalized;
    }

    private function normalizeCredentialBag(array $credentials): array
    {
        return collect($credentials)
            ->map(function ($value) {
                if (is_string($value)) {
                    return trim($value);
                }

                if (is_array($value)) {
                    return collect($value)->map(fn ($item) => is_string($item) ? trim($item) : $item)->all();
                }

                return $value;
            })
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function normalizeHowItWorks(array $items): array
    {
        return collect($items)
            ->map(function (array $item) {
                $active = $item['is_active'] ?? 1;

                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'icon' => trim((string) ($item['icon'] ?? '')),
                    'is_active' => (int) $active === 1,
                ];
            })
            ->filter(fn (array $row) => $row['title'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    private function normalizePromoPopup(array $raw, array $existing): array
    {
        $active = $raw['is_active'] ?? $existing['is_active'] ?? false;

        return [
            'is_active' => (int) $active === 1,
            'title' => trim((string) ($raw['title'] ?? $existing['title'] ?? '')),
            'body' => trim((string) ($raw['body'] ?? $existing['body'] ?? '')),
            'button_label' => trim((string) ($raw['button_label'] ?? $existing['button_label'] ?? '')),
            'button_url' => trim((string) ($raw['button_url'] ?? $existing['button_url'] ?? '')),
            'delay_seconds' => max(0, min(600, (int) ($raw['delay_seconds'] ?? $existing['delay_seconds'] ?? 2))),
        ];
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  array<string, mixed>  $existing
     * @return array<string, string>
     */
    private function normalizeFooterAppLinks(array $raw, array $existing): array
    {
        return [
            'ios_url' => trim((string) ($raw['ios_url'] ?? $existing['ios_url'] ?? '')),
            'android_url' => trim((string) ($raw['android_url'] ?? $existing['android_url'] ?? '')),
        ];
    }
}

