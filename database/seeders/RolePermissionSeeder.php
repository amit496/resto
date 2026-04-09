<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionEnum::values() as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        foreach (RoleEnum::values() as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', PermissionEnum::values())
            ->get()
            ->keyBy('name');

        Role::findByName(RoleEnum::SUPERADMIN->value, 'web')
            ->syncPermissions($permissions->values());

        Role::findByName(RoleEnum::ADMIN->value, 'web')
            ->syncPermissions($permissions->only([
                PermissionEnum::DASHBOARD_VIEW->value,
                PermissionEnum::USERS_VIEW->value,
                PermissionEnum::USERS_CREATE->value,
                PermissionEnum::USERS_EDIT->value,
                PermissionEnum::USERS_DELETE->value,
                PermissionEnum::AUDIT_LOGS_VIEW->value,
                PermissionEnum::PROFILE_EDIT->value,
            ])->values());

        Role::findByName(RoleEnum::MANAGER->value, 'web')
            ->syncPermissions($permissions->only([
                PermissionEnum::DASHBOARD_VIEW->value,
                PermissionEnum::PROFILE_EDIT->value,
            ])->values());

        Role::findByName(RoleEnum::STAFF->value, 'web')
            ->syncPermissions($permissions->only([
                PermissionEnum::DASHBOARD_VIEW->value,
                PermissionEnum::PROFILE_EDIT->value,
            ])->values());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
