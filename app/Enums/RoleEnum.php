<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case STAFF = 'staff';

    public static function values(): array
    {
        return array_map(static fn (self $role) => $role->value, self::cases());
    }
}

