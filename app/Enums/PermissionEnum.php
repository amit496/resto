<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case DASHBOARD_VIEW = 'dashboard.view';
    case USERS_VIEW = 'users.view';
    case USERS_CREATE = 'users.create';
    case USERS_EDIT = 'users.edit';
    case USERS_DELETE = 'users.delete';
    case AUDIT_LOGS_VIEW = 'audit-logs.view';
    case PROFILE_EDIT = 'profile.edit';

    public static function values(): array
    {
        return array_map(static fn (self $permission) => $permission->value, self::cases());
    }
}

