<?php

namespace App\Http\Requests\Backend;

use App\Enums\RestaurantStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $restaurantId = $this->route('restaurant')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('restaurants', 'slug')->ignore($restaurantId)],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image_file' => ['nullable', 'image', 'max:6144'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(RestaurantStatusEnum::class)],
        ];
    }
}

