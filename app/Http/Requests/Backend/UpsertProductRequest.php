<?php

namespace App\Http\Requests\Backend;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:80'],
            'type' => ['required', Rule::enum(ProductTypeEnum::class)],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(ProductStatusEnum::class)],
            'description' => ['nullable', 'string'],
            'product_images' => ['nullable', 'array'],
            'product_images.*' => ['image', 'max:4096'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string', 'max:255'],
            'has_variants' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:120'],
            'variants.*.value' => ['nullable', 'string', 'max:120'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:80'],
            'variants.*.is_default' => ['nullable', 'boolean'],
            'variants.*.status' => ['nullable', Rule::enum(ProductStatusEnum::class)],
            'redirect_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

