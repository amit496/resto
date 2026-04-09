<?php

namespace App\Http\Requests\Backend;

use App\Enums\RestaurantStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subcategoryId = $this->route('subcategory')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('subcategories', 'slug')->ignore($subcategoryId)],
            'status' => ['required', Rule::enum(RestaurantStatusEnum::class)],
        ];
    }
}

