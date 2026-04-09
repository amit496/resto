<?php

namespace App\Http\Requests\Backend;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertFoodOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $restaurantId = (int) $this->input('restaurant_id');

        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(function ($query) use ($restaurantId) {
                    if ($restaurantId > 0) {
                        $query->where('restaurant_id', $restaurantId);
                    }
                }),
            ],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'delivery_boy_id' => ['nullable', 'exists:delivery_boys,id'],
            'order_type' => ['required', Rule::enum(OrderTypeEnum::class)],
            'status' => ['required', Rule::enum(OrderStatusEnum::class)],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment.method' => ['nullable', Rule::enum(PaymentMethodEnum::class)],
            'payment.gateway_code' => ['nullable', 'string', 'max:50'],
            'payment.gateway_country' => ['nullable', 'string', 'size:2'],
            'payment.status' => ['nullable', Rule::enum(PaymentStatusEnum::class)],
            'payment.amount' => ['nullable', 'numeric', 'min:0'],
            'payment.transaction_ref' => ['nullable', 'string', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('restaurant_id')) {
            return;
        }

        if (! config('app.single_restaurant_mode', true)) {
            return;
        }

        $restaurantId = Restaurant::query()->value('id');
        if ($restaurantId) {
            $this->merge(['restaurant_id' => $restaurantId]);
        }
    }
}

