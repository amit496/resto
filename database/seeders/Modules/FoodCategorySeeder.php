<?php

namespace Database\Seeders\Modules;

use App\Enums\RestaurantStatusEnum;
use App\Models\Restaurant;
use App\Support\FoodImageResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FoodCategorySeeder extends Seeder
{
    private const CATEGORY_TREE = [
        'Pizza' => ['Veg Pizza', 'Non-Veg Pizza', 'Cheese Pizza'],
        'Burger' => ['Veg Burger', 'Chicken Burger', 'Double Patty Burger'],
        'Biryani' => ['Veg Biryani', 'Chicken Biryani', 'Mutton Biryani'],
        'Chinese' => ['Fried Rice', 'Noodles', 'Manchurian'],
        'Indian Main Course' => ['Paneer Dishes', 'Chicken Curry', 'Dal & Sabzi'],
        'Snacks / Starters' => ['Samosa', 'French Fries', 'Momos'],
        'Rolls & Wraps' => ['Veg Roll', 'Chicken Roll', 'Paneer Roll'],
        'Sandwich' => ['Veg Sandwich', 'Grilled Sandwich', 'Cheese Sandwich'],
        'Pasta / Noodles' => ['White Sauce Pasta', 'Red Sauce Pasta', 'Hakka Noodles'],
        'Drinks / Beverages' => ['Cold Drinks', 'Juice', 'Milkshakes', 'Coffee / Tea'],
        'Desserts' => ['Ice Cream', 'Cake', 'Brownie', 'Gulab Jamun'],
    ];

    public function run(): void
    {
        foreach (Restaurant::query()->get() as $restaurant) {
            foreach (self::CATEGORY_TREE as $categoryName => $subcategories) {
                $category = $restaurant->categories()->updateOrCreate(
                    ['slug' => Str::slug($categoryName.'-'.$restaurant->id)],
                    [
                        'name' => $categoryName,
                        'image' => FoodImageResolver::category($categoryName),
                        'status' => RestaurantStatusEnum::ACTIVE,
                    ]
                );

                foreach ($subcategories as $subName) {
                    $category->subcategories()->updateOrCreate(
                        ['slug' => Str::slug($subName.'-'.$restaurant->id)],
                        [
                            'name' => $subName,
                            'image' => FoodImageResolver::subcategory($subName),
                            'status' => RestaurantStatusEnum::ACTIVE,
                        ]
                    );
                }
            }
        }
    }
}
