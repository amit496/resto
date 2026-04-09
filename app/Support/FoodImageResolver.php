<?php

namespace App\Support;

use Illuminate\Support\Str;

class FoodImageResolver
{
    private const IMAGE_BANK = [
        'pizza' => [
            'https://images.unsplash.com/photo-1513104890138-7c749659a591',
            'https://images.unsplash.com/photo-1594007654729-407eedc4be65',
            'https://images.unsplash.com/photo-1548365328-9f547fb0953b',
        ],
        'burger' => [
            'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',
            'https://images.unsplash.com/photo-1550317138-10000687a72b',
            'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9',
        ],
        'biryani' => [
            'https://images.unsplash.com/photo-1701579231305-d84d8af9a3fd',
            'https://images.unsplash.com/photo-1633945274309-2c16e5b9b4d8',
            'https://images.unsplash.com/photo-1642821373181-696a54913e93',
        ],
        'chinese' => [
            'https://images.unsplash.com/photo-1569718212165-3a8278d5f624',
            'https://images.unsplash.com/photo-1585032226651-759b368d7246',
            'https://images.unsplash.com/photo-1617622141573-36be5d5461db',
        ],
        'indian-main-course' => [
            'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398',
            'https://images.unsplash.com/photo-1546833999-b9f581a1996d',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4',
        ],
        'snacks-starters' => [
            'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec',
            'https://images.unsplash.com/photo-1518013431117-eb1465fa5752',
            'https://images.unsplash.com/photo-1601050690597-df0568f70950',
        ],
        'rolls-wraps' => [
            'https://images.unsplash.com/photo-1539252554453-80ab65ce3586',
            'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d',
            'https://images.unsplash.com/photo-1626700051175-6818013e1d4f',
        ],
        'sandwich' => [
            'https://images.unsplash.com/photo-1528735602780-2552fd46c7af',
            'https://images.unsplash.com/photo-1553909489-cd47e0ef937f',
            'https://images.unsplash.com/photo-1509722747041-616f39b57569',
        ],
        'pasta-noodles' => [
            'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9',
            'https://images.unsplash.com/photo-1556761223-4c4282c73f77',
            'https://images.unsplash.com/photo-1473093295043-cdd812d0e601',
        ],
        'drinks-beverages' => [
            'https://images.unsplash.com/photo-1544145945-f90425340c7e',
            'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd',
            'https://images.unsplash.com/photo-1497534446932-c925b458314e',
        ],
        'desserts' => [
            'https://images.unsplash.com/photo-1563805042-7684c019e1cb',
            'https://images.unsplash.com/photo-1551024506-0bccd828d307',
            'https://images.unsplash.com/photo-1488477181946-6428a0291777',
        ],
        'food' => [
            'https://images.unsplash.com/photo-1504674900247-0877df9cc836',
            'https://images.unsplash.com/photo-1498837167922-ddd27525d352',
            'https://images.unsplash.com/photo-1512621776951-a57141f2eefd',
        ],
    ];

    private const SUBCATEGORY_BUCKETS = [
        'veg-pizza' => 'pizza',
        'non-veg-pizza' => 'pizza',
        'cheese-pizza' => 'pizza',
        'veg-burger' => 'burger',
        'chicken-burger' => 'burger',
        'double-patty-burger' => 'burger',
        'veg-biryani' => 'biryani',
        'chicken-biryani' => 'biryani',
        'mutton-biryani' => 'biryani',
        'fried-rice' => 'chinese',
        'noodles' => 'chinese',
        'manchurian' => 'chinese',
        'paneer-dishes' => 'indian-main-course',
        'chicken-curry' => 'indian-main-course',
        'dal-sabzi' => 'indian-main-course',
        'samosa' => 'snacks-starters',
        'french-fries' => 'snacks-starters',
        'momos' => 'snacks-starters',
        'veg-roll' => 'rolls-wraps',
        'chicken-roll' => 'rolls-wraps',
        'paneer-roll' => 'rolls-wraps',
        'veg-sandwich' => 'sandwich',
        'grilled-sandwich' => 'sandwich',
        'cheese-sandwich' => 'sandwich',
        'white-sauce-pasta' => 'pasta-noodles',
        'red-sauce-pasta' => 'pasta-noodles',
        'hakka-noodles' => 'pasta-noodles',
        'cold-drinks' => 'drinks-beverages',
        'juice' => 'drinks-beverages',
        'milkshakes' => 'drinks-beverages',
        'coffee-tea' => 'drinks-beverages',
        'ice-cream' => 'desserts',
        'cake' => 'desserts',
        'brownie' => 'desserts',
        'gulab-jamun' => 'desserts',
    ];

    public static function category(?string $name): string
    {
        return self::imageForBucket(self::detectBucket(Str::slug((string) $name)), 1, 1200, 900);
    }

    public static function subcategory(?string $name): string
    {
        $slug = Str::slug((string) $name);
        $bucket = self::SUBCATEGORY_BUCKETS[$slug] ?? self::detectBucket($slug);

        return self::imageForBucket($bucket, 2, 1200, 900);
    }

    public static function product(?string $name, int $variant = 1): string
    {
        return self::imageForBucket(self::detectBucket(Str::slug((string) $name)), $variant, 1200, 900);
    }

    private static function detectBucket(string $slug): string
    {
        if (Str::contains($slug, ['pizza', 'margherita', 'pepperoni', 'farmhouse', 'cheese'])) {
            return 'pizza';
        }
        if (Str::contains($slug, ['burger', 'patty'])) {
            return 'burger';
        }
        if (Str::contains($slug, 'biryani')) {
            return 'biryani';
        }
        if (Str::contains($slug, ['noodle', 'fried-rice', 'manchurian', 'chinese'])) {
            return 'chinese';
        }
        if (Str::contains($slug, ['paneer', 'dal', 'curry', 'sabzi', 'main-course'])) {
            return 'indian-main-course';
        }
        if (Str::contains($slug, ['samosa', 'fries', 'momos', 'starter', 'snack'])) {
            return 'snacks-starters';
        }
        if (Str::contains($slug, ['roll', 'wrap', 'frankie'])) {
            return 'rolls-wraps';
        }
        if (Str::contains($slug, 'sandwich')) {
            return 'sandwich';
        }
        if (Str::contains($slug, ['pasta', 'alfredo', 'arrabbiata'])) {
            return 'pasta-noodles';
        }
        if (Str::contains($slug, ['drink', 'juice', 'shake', 'coffee', 'tea', 'beverage', 'soda', 'cola'])) {
            return 'drinks-beverages';
        }
        if (Str::contains($slug, ['dessert', 'cake', 'brownie', 'ice-cream', 'gulab-jamun', 'kulfi'])) {
            return 'desserts';
        }

        return 'food';
    }

    private static function imageForBucket(string $bucket, int $variant, int $width, int $height): string
    {
        $images = self::IMAGE_BANK[$bucket] ?? self::IMAGE_BANK['food'];
        $base = $images[($variant - 1) % count($images)];

        return sprintf('%s?auto=format&fit=crop&w=%d&h=%d&q=80', $base, $width, $height);
    }
}
