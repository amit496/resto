<?php

namespace Database\Seeders\Modules;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Product;
use App\Models\Restaurant;
use App\Support\FoodImageResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FoodItemSeeder extends Seeder
{
    private const PRODUCT_BLUEPRINTS = [
        'Pizza' => [
            'Veg Pizza' => ['Margherita Pizza', 'Farmhouse Pizza', 'Veggie Delight Pizza', 'Tandoori Veg Pizza', 'Corn & Capsicum Pizza', 'Paneer Tikka Pizza', 'Mushroom Loaded Pizza'],
            'Non-Veg Pizza' => ['Chicken BBQ Pizza', 'Pepperoni Pizza', 'Chicken Sausage Pizza', 'Spicy Chicken Pizza', 'Smoked Chicken Pizza', 'Chicken Keema Pizza', 'Peri Peri Chicken Pizza'],
            'Cheese Pizza' => ['Cheese Burst Pizza', 'Four Cheese Pizza', 'Mozzarella Special Pizza', 'Cheesy Jalapeno Pizza', 'Double Cheese Pizza', 'Creamy Cheese Pizza'],
        ],
        'Burger' => [
            'Veg Burger' => ['Classic Veg Burger', 'Aloo Tikki Burger', 'Crispy Veg Burger', 'Veggie Supreme Burger', 'Spicy Veg Burger', 'Mayo Veg Burger', 'Loaded Veg Burger'],
            'Chicken Burger' => ['Classic Chicken Burger', 'Crispy Chicken Burger', 'Grilled Chicken Burger', 'Spicy Chicken Burger', 'Peri Peri Chicken Burger', 'Chicken Cheese Burger', 'Smoky Chicken Burger'],
            'Double Patty Burger' => ['Double Patty Burger', 'Double Chicken Burger', 'Monster Veg Burger', 'Loaded Double Burger', 'Double Cheese Burger', 'Double Crunch Burger'],
        ],
        'Biryani' => [
            'Veg Biryani' => ['Veg Dum Biryani', 'Paneer Biryani', 'Mushroom Biryani', 'Hyderabadi Veg Biryani', 'Kaju Veg Biryani', 'Mix Veg Biryani', 'Soya Chaap Biryani'],
            'Chicken Biryani' => ['Chicken Dum Biryani', 'Hyderabadi Chicken Biryani', 'Boneless Chicken Biryani', 'Spicy Chicken Biryani', 'Lucknowi Chicken Biryani', 'Tandoori Chicken Biryani', 'Butter Chicken Biryani'],
            'Mutton Biryani' => ['Mutton Dum Biryani', 'Spicy Mutton Biryani', 'Kolkata Mutton Biryani', 'Special Mutton Biryani', 'Royal Mutton Biryani', 'Awadhi Mutton Biryani'],
        ],
        'Chinese' => [
            'Fried Rice' => ['Veg Fried Rice', 'Egg Fried Rice', 'Chicken Fried Rice', 'Schezwan Fried Rice', 'Paneer Fried Rice', 'Burnt Garlic Rice'],
            'Noodles' => ['Veg Noodles', 'Chicken Noodles', 'Egg Noodles', 'Schezwan Noodles', 'Garlic Noodles', 'Chilli Noodles', 'Hakka Noodles'],
            'Manchurian' => ['Veg Manchurian', 'Paneer Manchurian', 'Chicken Manchurian', 'Dry Manchurian', 'Gravy Manchurian', 'Mushroom Manchurian', 'Corn Manchurian'],
        ],
        'Indian Main Course' => [
            'Paneer Dishes' => ['Paneer Butter Masala', 'Kadai Paneer', 'Shahi Paneer', 'Paneer Lababdar', 'Paneer Do Pyaza', 'Paneer Tikka Masala', 'Paneer Bhurji'],
            'Chicken Curry' => ['Butter Chicken', 'Kadai Chicken', 'Chicken Masala', 'Chicken Handi', 'Chicken Rogan Josh', 'Chicken Kolhapuri', 'Chicken Curry'],
            'Dal & Sabzi' => ['Dal Tadka', 'Dal Makhani', 'Mix Veg Curry', 'Aloo Jeera', 'Bhindi Masala', 'Malai Kofta', 'Chana Masala'],
        ],
        'Snacks / Starters' => [
            'Samosa' => ['Classic Samosa', 'Punjabi Samosa', 'Cheese Samosa', 'Paneer Samosa', 'Mini Samosa', 'Aloo Samosa'],
            'French Fries' => ['Salted Fries', 'Peri Peri Fries', 'Cheese Fries', 'Loaded Fries', 'Masala Fries', 'Crispy Fries', 'Garlic Fries'],
            'Momos' => ['Veg Momos', 'Paneer Momos', 'Chicken Momos', 'Fried Momos', 'Tandoori Momos', 'Cheese Momos', 'Kurkure Momos'],
        ],
        'Rolls & Wraps' => [
            'Veg Roll' => ['Veg Kathi Roll', 'Spicy Veg Wrap', 'Cheese Veg Roll', 'Tandoori Veg Roll', 'Crunchy Veg Roll', 'Mayo Veg Wrap', 'Masala Veg Frankie'],
            'Chicken Roll' => ['Chicken Kathi Roll', 'Chicken Tikka Roll', 'Spicy Chicken Wrap', 'Egg Chicken Roll', 'Mayo Chicken Roll', 'Peri Peri Chicken Roll', 'Smoked Chicken Frankie'],
            'Paneer Roll' => ['Paneer Tikka Roll', 'Malai Paneer Roll', 'Spicy Paneer Wrap', 'Cheese Paneer Roll', 'Kadhai Paneer Roll', 'Makhani Paneer Roll', 'Hariyali Paneer Roll'],
        ],
        'Sandwich' => [
            'Veg Sandwich' => ['Classic Veg Sandwich', 'Bombay Veg Sandwich', 'Corn Veg Sandwich', 'Mayo Veg Sandwich', 'Masala Veg Sandwich', 'Veg Club Sandwich', 'Garden Fresh Sandwich'],
            'Grilled Sandwich' => ['Grilled Veg Sandwich', 'Grilled Corn Sandwich', 'Grilled Paneer Sandwich', 'Grilled Chicken Sandwich', 'Toasted Veg Sandwich', 'Peri Peri Grilled Sandwich', 'Cafe Grill Sandwich'],
            'Cheese Sandwich' => ['Cheese Corn Sandwich', 'Cheese Chilli Sandwich', 'Loaded Cheese Sandwich', 'Cheese Burst Sandwich', 'Cheese Paneer Sandwich', 'Triple Cheese Sandwich', 'Four Cheese Toastie'],
        ],
        'Pasta / Noodles' => [
            'White Sauce Pasta' => ['White Sauce Pasta', 'Cheesy White Pasta', 'Mushroom White Pasta', 'Veg Alfredo Pasta', 'Chicken Alfredo Pasta', 'Creamy Corn Pasta', 'Herb Garlic Pasta'],
            'Red Sauce Pasta' => ['Red Sauce Pasta', 'Arrabbiata Pasta', 'Spicy Tomato Pasta', 'Cheese Red Pasta', 'Veg Red Sauce Pasta', 'Chicken Red Pasta', 'Roasted Garlic Red Pasta'],
            'Hakka Noodles' => ['Veg Hakka Noodles', 'Chicken Hakka Noodles', 'Egg Hakka Noodles', 'Paneer Hakka Noodles', 'Schezwan Hakka Noodles', 'Burnt Garlic Hakka Noodles', 'Street Style Hakka Noodles'],
        ],
        'Drinks / Beverages' => [
            'Cold Drinks' => ['Coke', 'Pepsi', 'Sprite', 'Thumbs Up', 'Fanta', 'Lemon Soda'],
            'Juice' => ['Orange Juice', 'Mango Juice', 'Watermelon Juice', 'Pineapple Juice', 'Mixed Fruit Juice', 'Mosambi Juice'],
            'Milkshakes' => ['Chocolate Milkshake', 'Oreo Milkshake', 'Vanilla Milkshake', 'Strawberry Milkshake', 'Butterscotch Milkshake', 'Cold Coffee Shake'],
            'Coffee / Tea' => ['Hot Coffee', 'Cappuccino', 'Cold Coffee', 'Masala Tea', 'Green Tea', 'Black Coffee'],
        ],
        'Desserts' => [
            'Ice Cream' => ['Vanilla Ice Cream', 'Chocolate Ice Cream', 'Butterscotch Ice Cream', 'Strawberry Ice Cream', 'Kulfi Scoop', 'Mango Ice Cream'],
            'Cake' => ['Chocolate Cake', 'Red Velvet Cake', 'Black Forest Cake', 'Pineapple Cake', 'Butterscotch Cake', 'Truffle Cake'],
            'Brownie' => ['Chocolate Brownie', 'Walnut Brownie', 'Sizzling Brownie', 'Choco Chip Brownie', 'Fudge Brownie', 'Brownie Sundae'],
            'Gulab Jamun' => ['Gulab Jamun', 'Hot Gulab Jamun', 'Mini Gulab Jamun', 'Gulab Jamun Rabri', 'Dry Fruit Gulab Jamun', 'Jamun Sundae'],
        ],
    ];

    public function run(): void
    {
        foreach (Restaurant::query()->with(['categories.subcategories'])->get() as $restaurant) {
            foreach ($restaurant->categories as $category) {
                $subcategories = $category->subcategories->keyBy('name');
                $items = $this->buildProductsForCategory($category->name);

                foreach ($items as $index => $item) {
                    $subcategory = $subcategories->get($item['subcategory']);
                    if (! $subcategory) {
                        continue;
                    }

                    $name = sprintf('%s %s %d', $item['name'], $category->name, $index + 1);

                    Product::query()->updateOrCreate(
                        [
                            'restaurant_id' => $restaurant->id,
                            'slug' => Str::slug($name),
                        ],
                        [
                            'category_id' => $category->id,
                            'subcategory_id' => $subcategory->id,
                            'name' => $name,
                            'sku' => strtoupper(Str::substr(Str::slug($category->name), 0, 3)).'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                            'type' => $item['type'],
                            'base_price' => $item['price'],
                            'status' => ProductStatusEnum::ACTIVE,
                            'description' => $item['description'],
                            'images' => [
                                FoodImageResolver::product($item['name'], 1),
                                FoodImageResolver::product($item['name'], 2),
                                FoodImageResolver::product($item['name'], 3),
                            ],
                            'has_variants' => false,
                        ]
                    );
                }
            }
        }
    }

    private function buildProductsForCategory(string $categoryName): array
    {
        $blueprint = self::PRODUCT_BLUEPRINTS[$categoryName] ?? [];
        $items = [];

        foreach ($blueprint as $subcategory => $products) {
            foreach ($products as $name) {
                $items[] = [
                    'subcategory' => $subcategory,
                    'name' => $name,
                    'type' => $this->resolveType($categoryName, $subcategory, $name),
                    'price' => $this->priceFor($categoryName),
                    'description' => $this->descriptionFor($name, $subcategory),
                ];
            }
        }

        return $items;
    }

    private function resolveType(string $categoryName, string $subcategory, string $name): ProductTypeEnum
    {
        $value = Str::lower($categoryName.' '.$subcategory.' '.$name);

        if (Str::contains($value, ['chicken', 'mutton', 'pepperoni'])) {
            return ProductTypeEnum::NON_VEG;
        }

        if (Str::contains($value, 'egg')) {
            return ProductTypeEnum::EGG;
        }

        if (Str::contains($value, ['drink', 'juice', 'shake', 'coffee', 'tea', 'soda'])) {
            return ProductTypeEnum::BEVERAGE;
        }

        return ProductTypeEnum::VEG;
    }

    private function descriptionFor(string $name, string $subcategory): string
    {
        $lines = [
            "{$name} prepared fresh for fast delivery and dine-in orders.",
            "A crowd-favorite pick from our {$subcategory} section with restaurant-style plating.",
            "Balanced taste, strong presentation, and perfect for app-first ordering.",
        ];

        return Arr::random($lines);
    }

    private function priceFor(string $categoryName): int
    {
        return match ($categoryName) {
            'Pizza' => fake()->numberBetween(229, 499),
            'Burger' => fake()->numberBetween(139, 319),
            'Biryani' => fake()->numberBetween(189, 429),
            'Chinese' => fake()->numberBetween(149, 349),
            'Indian Main Course' => fake()->numberBetween(179, 389),
            'Snacks / Starters' => fake()->numberBetween(79, 249),
            'Rolls & Wraps' => fake()->numberBetween(109, 279),
            'Sandwich' => fake()->numberBetween(99, 259),
            'Pasta / Noodles' => fake()->numberBetween(159, 339),
            'Drinks / Beverages' => fake()->numberBetween(59, 239),
            'Desserts' => fake()->numberBetween(89, 279),
            default => fake()->numberBetween(109, 389),
        };
    }
}
