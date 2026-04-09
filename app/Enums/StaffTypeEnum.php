<?php

namespace App\Enums;

enum StaffTypeEnum: string
{
    case WAITER = 'waiter';
    case CHEF = 'chef';
    case CLEANER = 'cleaner';
    case CASHIER = 'cashier';
    case DELIVERY = 'delivery';
    case SUPERVISOR = 'supervisor';
    case RECEPTIONIST = 'receptionist';
    case SECURITY = 'security';
    case BARTENDER = 'bartender';
    case STOREKEEPER = 'storekeeper';
    case DISHWASHER = 'dishwasher';
    case TRAINER = 'trainer';
    case EVENT = 'event_staff';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::WAITER => 'Waiter',
            self::CHEF => 'Chef',
            self::CLEANER => 'Cleaning Staff',
            self::CASHIER => 'Cashier',
            self::DELIVERY => 'Delivery',
            self::SUPERVISOR => 'Supervisor',
            self::RECEPTIONIST => 'Receptionist',
            self::SECURITY => 'Security Guard',
            self::BARTENDER => 'Bartender',
            self::STOREKEEPER => 'Storekeeper',
            self::DISHWASHER => 'Dishwasher',
            self::TRAINER => 'Trainer',
            self::EVENT => 'Event Staff',
            self::OTHER => 'Other',
        };
    }
}
