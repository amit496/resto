<?php

namespace App\Services\Backend;

use App\Models\Subcategory;
use Illuminate\Support\Str;

class SubcategoryService
{
    public function upsert(array $data, ?Subcategory $subcategory = null): Subcategory
    {
        $subcategory ??= new Subcategory();
        $subcategory->fill($data);
        $subcategory->slug = $data['slug'] ?: Str::slug($data['name']);
        $subcategory->save();

        return $subcategory;
    }
}

