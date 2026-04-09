<?php

namespace App\Http\Requests\Backend;

use App\Enums\DeliveryBoyStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertDeliveryBoyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'vehicle_no' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::enum(DeliveryBoyStatusEnum::class)],
            'redirect_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

