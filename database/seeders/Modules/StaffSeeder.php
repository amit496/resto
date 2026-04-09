<?php

namespace Database\Seeders\Modules;

use App\Enums\RoleEnum;
use App\Enums\StaffTypeEnum;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::query()->orderBy('id')->get();

        if ($branches->isEmpty()) {
            return;
        }

        $types = StaffTypeEnum::cases();
        $index = 1;

        foreach ($branches as $branch) {
            $managerEmail = Str::slug($branch->name)."-manager@foodihub.test";
            $manager = User::query()->updateOrCreate(
                ['email' => $managerEmail],
                [
                    'name' => 'Manager '.$branch->name,
                    'phone' => '9'.str_pad((string) $index, 9, '0', STR_PAD_LEFT),
                    'branch_id' => $branch->id,
                    'staff_type' => StaffTypeEnum::SUPERVISOR->value,
                    'is_active' => true,
                    'password' => Hash::make('password'),
                    'profile_image' => 'admin/assets/img/profiles/avatar-0'.((($index - 1) % 5) + 1).'.jpg',
                ]
            );
            $manager->syncRoles([RoleEnum::MANAGER->value]);
            $index++;

            foreach ($types as $type) {
                $email = Str::slug($branch->name)."-{$type->value}-{$index}@foodihub.test";

                $user = User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $type->label().' '.$branch->name.' '.$index,
                        'phone' => '9'.str_pad((string) $index, 9, '0', STR_PAD_LEFT),
                        'branch_id' => $branch->id,
                        'staff_type' => $type->value,
                        'is_active' => true,
                        'password' => Hash::make('password'),
                        'profile_image' => 'admin/assets/img/profiles/avatar-0'.((($index - 1) % 5) + 1).'.jpg',
                    ]
                );

                $user->syncRoles([RoleEnum::STAFF->value]);
                $index++;
            }
        }
    }
}
