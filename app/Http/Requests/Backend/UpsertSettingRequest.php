<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gatewayRules = [];

        foreach (array_keys(config('payment_gateways.definitions', [])) as $gatewayCode) {
            $gatewayRules["payment_gateways.$gatewayCode.enabled"] = ['nullable', 'boolean'];
            $gatewayRules["payment_gateways.$gatewayCode.label"] = ['nullable', 'string', 'max:120'];
            $gatewayRules["payment_gateways.$gatewayCode.mode"] = ['nullable', 'string', 'max:30'];
            $gatewayRules["payment_gateways.$gatewayCode.countries"] = ['nullable', 'string', 'max:500'];
            $gatewayRules["payment_gateways.$gatewayCode.credentials"] = ['nullable', 'array'];
        }

        return [
            'restaurant_id' => ['nullable', 'exists:restaurants,id'],
            'app_name' => ['required', 'string', 'max:160'],
            'app_logo' => ['nullable', 'string', 'max:255'],
            'app_favicon' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:4'],
            'timezone' => ['required', 'string', 'max:60'],
            'order_prefix' => ['required', 'string', 'max:10'],
            'auto_accept_orders' => ['nullable', 'boolean'],
            'allow_scheduled_orders' => ['nullable', 'boolean'],
            'allow_guest_checkout' => ['nullable', 'boolean'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'packing_fee' => ['nullable', 'numeric', 'min:0'],
            'est_prep_time_min' => ['nullable', 'integer', 'min:1'],
            'est_delivery_time_min' => ['nullable', 'integer', 'min:1'],
            'max_delivery_km' => ['nullable', 'integer', 'min:1'],
            'enable_coupons' => ['nullable', 'boolean'],
            'enable_tips' => ['nullable', 'boolean'],
            'enable_kot' => ['nullable', 'boolean'],
            'enable_stock_deduction' => ['nullable', 'boolean'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'support_email' => ['nullable', 'email', 'max:120'],
            'invoice_footer' => ['nullable', 'string'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'payment_default_country' => ['nullable', 'string', 'size:2'],
            ...$gatewayRules,
            'home_sliders' => ['nullable', 'array'],
            'home_sliders.*.title' => ['nullable', 'string', 'max:160'],
            'home_sliders.*.description' => ['nullable', 'string', 'max:500'],
            'home_sliders.*.button_label' => ['nullable', 'string', 'max:80'],
            'home_sliders.*.button_url' => ['nullable', 'string', 'max:255'],
            'home_sliders.*.desktop_image' => ['nullable', 'image', 'max:5120'],
            'home_sliders.*.mobile_image' => ['nullable', 'image', 'max:5120'],
            'home_sliders.*.desktop_image_existing' => ['nullable', 'string', 'max:255'],
            'home_sliders.*.mobile_image_existing' => ['nullable', 'string', 'max:255'],
            'home_sliders.*.remove_desktop_image' => ['nullable', 'boolean'],
            'home_sliders.*.remove_mobile_image' => ['nullable', 'boolean'],
            'home_sliders.*.is_active' => ['nullable', 'boolean'],
            'home_banners' => ['nullable', 'array'],
            'home_banners.*.title' => ['nullable', 'string', 'max:160'],
            'home_banners.*.description' => ['nullable', 'string', 'max:500'],
            'home_banners.*.button_label' => ['nullable', 'string', 'max:80'],
            'home_banners.*.button_url' => ['nullable', 'string', 'max:255'],
            'home_banners.*.desktop_image' => ['nullable', 'image', 'max:5120'],
            'home_banners.*.mobile_image' => ['nullable', 'image', 'max:5120'],
            'home_banners.*.desktop_image_existing' => ['nullable', 'string', 'max:255'],
            'home_banners.*.mobile_image_existing' => ['nullable', 'string', 'max:255'],
            'home_banners.*.remove_desktop_image' => ['nullable', 'boolean'],
            'home_banners.*.remove_mobile_image' => ['nullable', 'boolean'],
            'home_banners.*.is_active' => ['nullable', 'boolean'],
            'home_how_it_works' => ['nullable', 'array'],
            'home_how_it_works.*.title' => ['nullable', 'string', 'max:160'],
            'home_how_it_works.*.description' => ['nullable', 'string', 'max:500'],
            'home_how_it_works.*.icon' => ['nullable', 'string', 'max:40'],
            'home_how_it_works.*.is_active' => ['nullable', 'integer', 'in:0,1'],
            'home_promo_popup' => ['nullable', 'array'],
            'home_promo_popup.is_active' => ['nullable', 'integer', 'in:0,1'],
            'home_promo_popup.title' => ['nullable', 'string', 'max:160'],
            'home_promo_popup.body' => ['nullable', 'string', 'max:800'],
            'home_promo_popup.button_label' => ['nullable', 'string', 'max:80'],
            'home_promo_popup.button_url' => ['nullable', 'string', 'max:500'],
            'home_promo_popup.delay_seconds' => ['nullable', 'integer', 'min:0', 'max:600'],
            'footer_app_links' => ['nullable', 'array'],
            'footer_app_links.ios_url' => ['nullable', 'string', 'max:500'],
            'footer_app_links.android_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}

