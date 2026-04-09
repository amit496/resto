<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'restaurant_id',
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'sku',
        'type',
        'base_price',
        'status',
        'description',
        'images',
        'has_variants',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductTypeEnum::class,
            'status' => ProductStatusEnum::class,
            'images' => 'array',
            'has_variants' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FoodReview::class);
    }
}

