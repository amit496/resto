<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'app_name',
        'app_logo',
        'app_favicon',
        'currency',
        'currency_symbol',
        'decimal_places',
        'timezone',
        'order_prefix',
        'auto_accept_orders',
        'allow_scheduled_orders',
        'allow_guest_checkout',
        'min_order_amount',
        'tax_percent',
        'service_charge',
        'delivery_fee',
        'packing_fee',
        'est_prep_time_min',
        'est_delivery_time_min',
        'max_delivery_km',
        'enable_coupons',
        'enable_tips',
        'enable_kot',
        'enable_stock_deduction',
        'support_phone',
        'support_email',
        'invoice_footer',
        'theme_color',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'auto_accept_orders' => 'boolean',
            'allow_scheduled_orders' => 'boolean',
            'allow_guest_checkout' => 'boolean',
            'enable_coupons' => 'boolean',
            'enable_tips' => 'boolean',
            'enable_kot' => 'boolean',
            'enable_stock_deduction' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}

