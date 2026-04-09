<?php

namespace App\Models;

use App\Enums\RestaurantStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo_path',
        'cover_image',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RestaurantStatusEnum::class,
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function deliveryBoys(): HasMany
    {
        return $this->hasMany(DeliveryBoy::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }
}

