<?php

namespace Database\Seeders\Modules;

use App\Models\AppSetting;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::query()->updateOrCreate(
            ['restaurant_id' => null],
            [
                'app_name' => 'Foodihub',
                'app_logo' => 'admin/assets/img/logo.svg',
                'app_favicon' => 'admin/assets/img/favicon.svg',
                'currency' => 'INR',
                'currency_symbol' => 'Rs',
                'decimal_places' => 2,
                'timezone' => 'Asia/Calcutta',
                'order_prefix' => 'ORD',
                'auto_accept_orders' => false,
                'allow_scheduled_orders' => true,
                'allow_guest_checkout' => true,
                'min_order_amount' => 99,
                'tax_percent' => 5,
                'service_charge' => 0,
                'delivery_fee' => 30,
                'packing_fee' => 10,
                'est_prep_time_min' => 20,
                'est_delivery_time_min' => 40,
                'max_delivery_km' => 12,
                'enable_coupons' => true,
                'enable_tips' => true,
                'enable_kot' => true,
                'enable_stock_deduction' => true,
                'support_phone' => '9999999999',
                'support_email' => 'support@foodihub.test',
                'invoice_footer' => 'Thank you for ordering with us.',
                'theme_color' => '#F59E0B',
                'meta' => [
                    'loyalty_point_value' => 1,
                    'loyalty_earn_per_amount' => 100,
                    'home_sliders' => [
                        [
                            'title' => 'Order pizza, biryani, burgers, drinks and desserts in one fast flow.',
                            'description' => 'Responsive homepage slider with desktop and mobile image support, seeded for demo and manageable from admin panel.',
                            'button_label' => 'Search Menu',
                            'button_url' => '/menu',
                            'desktop_image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=1600&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Late night cravings, quick offers, and smoother repeat ordering.',
                            'description' => 'Use homepage media in admin to control campaign slides, app-style promotions, and discoverability blocks.',
                            'button_label' => 'View Offers',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=1600&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                    ],
                    'home_banners' => [
                        [
                            'title' => 'Weekend Combo Deal',
                            'description' => 'Get flat savings on your favorite fast-moving dishes with a strong homepage promo banner.',
                            'button_label' => 'Grab deal',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Family Feast Offer',
                            'description' => 'Highlight high-value combo meals and time-limited discount campaigns from admin.',
                            'button_label' => 'Explore combos',
                            'button_url' => '/menu',
                            'desktop_image' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Free Delivery Push',
                            'description' => 'Use banners for free-delivery, app-exclusive coupon, or branch-specific promotions.',
                            'button_label' => 'Order now',
                            'button_url' => '/menu',
                            'desktop_image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                    ],
                ],
            ]
        );

        foreach (Restaurant::query()->get() as $restaurant) {
            AppSetting::query()->updateOrCreate(
                ['restaurant_id' => $restaurant->id],
                [
                    'app_name' => $restaurant->name.' Ordering',
                    'app_logo' => 'admin/assets/img/logo.svg',
                    'app_favicon' => 'admin/assets/img/favicon.svg',
                    'currency' => fake()->randomElement(['INR', 'USD']),
                    'currency_symbol' => fake()->randomElement(['Rs', '$']),
                    'decimal_places' => 2,
                    'timezone' => 'Asia/Calcutta',
                    'order_prefix' => 'R'.$restaurant->id,
                    'auto_accept_orders' => fake()->boolean(30),
                    'allow_scheduled_orders' => true,
                    'allow_guest_checkout' => fake()->boolean(80),
                    'min_order_amount' => fake()->numberBetween(49, 249),
                    'tax_percent' => fake()->randomElement([5, 8, 12]),
                    'service_charge' => fake()->numberBetween(0, 20),
                    'delivery_fee' => fake()->numberBetween(20, 80),
                    'packing_fee' => fake()->numberBetween(0, 25),
                    'est_prep_time_min' => fake()->numberBetween(15, 35),
                    'est_delivery_time_min' => fake()->numberBetween(25, 60),
                    'max_delivery_km' => fake()->numberBetween(5, 20),
                    'enable_coupons' => true,
                    'enable_tips' => fake()->boolean(70),
                    'enable_kot' => true,
                    'enable_stock_deduction' => true,
                    'support_phone' => $restaurant->phone,
                    'support_email' => $restaurant->email,
                    'invoice_footer' => 'Thanks for choosing '.$restaurant->name.'.',
                    'theme_color' => fake()->randomElement(['#F59E0B', '#0EA5E9', '#22C55E']),
                    'meta' => [
                        'loyalty_point_value' => 1,
                        'loyalty_earn_per_amount' => 100,
                        'home_sliders' => [
                            [
                                'title' => $restaurant->name.' specials delivered faster.',
                                'description' => 'Restaurant-level slider demo content seeded for admin-managed homepage campaigns.',
                                'button_label' => 'Browse menu',
                                'button_url' => '/menu',
                                'desktop_image' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=1600&q=80',
                                'mobile_image' => 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=900&q=80',
                                'is_active' => true,
                            ],
                        ],
                        'home_banners' => [
                            [
                                'title' => 'Featured branch offer',
                                'description' => 'Restaurant-specific homepage banner configured from seeded settings meta.',
                                'button_label' => 'See offer',
                                'button_url' => '/offers',
                                'desktop_image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                                'mobile_image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=80',
                                'is_active' => true,
                            ],
                        ],
                    ],
                ]
            );
        }
    }
}
