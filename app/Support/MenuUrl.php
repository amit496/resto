<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Subcategory;

class MenuUrl
{
    public static function category(Category $category, array $query = []): string
    {
        return route('frontend.menu.category', array_merge(['category' => $category->slug], $query));
    }

    public static function subcategory(Category $category, Subcategory $subcategory, array $query = []): string
    {
        return route('frontend.menu.subcategory', array_merge([
            'category' => $category->slug,
            'subcategory' => $subcategory->slug,
        ], $query));
    }
}
