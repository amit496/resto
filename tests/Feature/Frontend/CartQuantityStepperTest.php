<?php

namespace Tests\Feature\Frontend;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Product;
use App\Models\Restaurant;
use App\Services\Frontend\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartQuantityStepperTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_quantity_can_be_increased_and_decreased_with_stepper_buttons(): void
    {
        $restaurant = Restaurant::query()->create([
            'name' => 'Test Restaurant',
            'slug' => Str::slug('Test Restaurant'),
            'status' => 'active',
        ]);

        $product = Product::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Test Product',
            'slug' => Str::slug('Test Product'),
            'type' => ProductTypeEnum::VEG,
            'base_price' => 120,
            'status' => ProductStatusEnum::ACTIVE,
            'has_variants' => false,
        ]);

        $this->post(route('frontend.cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $this->patch(route('frontend.cart.items.update', $product->id.':base'), [
            'delta' => 1,
        ])->assertRedirect();

        $summary = app(CartService::class)->summary();
        $this->assertSame(2, $summary['count']);

        $this->patch(route('frontend.cart.items.update', $product->id.':base'), [
            'delta' => -1,
        ])->assertRedirect();

        $summary = app(CartService::class)->summary();
        $this->assertSame(1, $summary['count']);

        $this->patch(route('frontend.cart.items.update', $product->id.':base'), [
            'delta' => -1,
        ])->assertRedirect();

        $summary = app(CartService::class)->summary();
        $this->assertSame(0, $summary['count']);
    }
}
