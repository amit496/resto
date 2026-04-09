<?php

namespace App\Models;

use App\Enums\DeliveryBoyStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class DeliveryBoy extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'vehicle_no',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryBoyStatusEnum::class,
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }
}

