<?php

namespace App\Models;

use App\Enums\CouponStatusEnum;
use App\Enums\CouponTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'restaurant_id',
        'code',
        'type',
        'value',
        'min_order_amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponTypeEnum::class,
            'status' => CouponStatusEnum::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}

