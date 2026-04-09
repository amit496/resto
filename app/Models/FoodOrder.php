<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'customer_id',
        'customer_address_id',
        'receipt_token',
        'billing_country',
        'billing_currency',
        'payment_gateway',
        'delivery_boy_id',
        'delivery_name',
        'delivery_phone',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_landmark',
        'delivery_city',
        'delivery_state',
        'delivery_postal_code',
        'delivery_country_code',
        'delivery_instructions',
        'coupon_id',
        'coupon_code',
        'order_no',
        'order_type',
        'order_source',
        'status',
        'subtotal',
        'tax_amount',
        'service_charge',
        'delivery_fee',
        'packing_fee',
        'discount_amount',
        'loyalty_points_used',
        'loyalty_discount_amount',
        'total_amount',
        'notes',
        'bill_no',
        'bill_status',
        'billed_at',
        'created_by',
        'billed_by',
    ];

    protected function casts(): array
    {
        return [
            'order_type' => OrderTypeEnum::class,
            'status' => OrderStatusEnum::class,
            'billed_at' => 'datetime',
            'billing_country' => 'string',
            'billing_currency' => 'string',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'packing_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'loyalty_discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function billedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billed_by');
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(DeliveryTrackingEvent::class, 'food_order_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'food_order_id');
    }
}

