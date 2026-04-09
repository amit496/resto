<?php

namespace App\Models;

use App\Enums\RestaurantStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RestaurantStatusEnum::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

