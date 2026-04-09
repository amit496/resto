<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class FoodOrderItem extends Model
{
    protected $fillable = [
        'food_order_id',
        'product_id',
        'product_variant_id',
        'product_name_snapshot',
        'unit_price',
        'quantity',
        'line_total',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class, 'food_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

