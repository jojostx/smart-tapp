<?php

namespace App\Enums\Roles;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';

    public static function toArray(): array
    {
        return collect(self::cases())->flatMap(function (UserRole $role) {
            return [$role->value => ucfirst($role->value)];
        })->toArray();
    }

    public static function toDescriptionArray(): array
    {
        return [
            self::SUPER_ADMIN->value => 'Access for the parking lot can be created',
            self::ADMIN->value => 'Access for the Parking Lot can not be created.',
        ];
    }
}
