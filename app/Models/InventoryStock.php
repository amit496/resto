<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $fillable = [
        'restaurant_id',
        'product_id',
        'current_stock',
        'reorder_level',
        'unit',
        'last_restocked_at',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'last_restocked_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}

