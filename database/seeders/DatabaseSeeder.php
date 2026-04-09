<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\Modules\CouponSeeder;
use Database\Seeders\Modules\CustomerSeeder;
use Database\Seeders\Modules\CustomerAddressSeeder;
use Database\Seeders\Modules\DeliveryBoySeeder;
use Database\Seeders\Modules\EngagementSeeder;
use Database\Seeders\Modules\FoodCategorySeeder;
use Database\Seeders\Modules\FoodItemSeeder;
use Database\Seeders\Modules\NotificationSeeder;
use Database\Seeders\Modules\OrderSeeder;
use Database\Seeders\Modules\PaymentSeeder;
use Database\Seeders\Modules\RestaurantSeeder;
use Database\Seeders\Modules\StaffSeeder;
use Database\Seeders\Modules\SettingSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RestaurantSeeder::class,
            FoodCategorySeeder::class,
            FoodItemSeeder::class,
            CustomerSeeder::class,
            DeliveryBoySeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            StaffSeeder::class,
            SettingSeeder::class,
            EngagementSeeder::class,
            NotificationSeeder::class,
            DummyDataSeeder::class,
            CustomerAddressSeeder::class,
        ]);

        $superAdminPassword = env('SEEDER_SUPERADMIN_PASSWORD', 'password');
        $adminPassword = env('SEEDER_ADMIN_PASSWORD', 'password');

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'profile_image' => 'admin/assets/img/profiles/avatar-01.jpg',
                'password' => Hash::make($superAdminPassword),
            ]
        );

        $superAdmin->syncRoles([RoleEnum::SUPERADMIN->value]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'profile_image' => 'admin/assets/img/profiles/avatar-02.jpg',
                'password' => Hash::make($adminPassword),
            ]
        );

        $admin->syncRoles([RoleEnum::ADMIN->value]);
    }
}
