<?php

namespace App\Models;

use App\Enums\RestaurantStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'code',
        'phone',
        'manager_name',
        'manager_phone',
        'manager_email',
        'manager_photo',
        'address',
        'opening_time',
        'closing_time',
        'weekly_off',
        'latitude',
        'longitude',
        'delivery_radius_km',
        'gst_no',
        'fssai_no',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RestaurantStatusEnum::class,
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BranchReview::class);
    }

    public function foodReviews(): HasMany
    {
        return $this->hasMany(FoodReview::class);
    }
}

