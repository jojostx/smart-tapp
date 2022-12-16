<?php

namespace App\Enums;

enum Permission: string
{
    case UPDATE_ADMIN_USERS = 'admin-users.update';

    public static function toArray(): array
    {
        return collect(self::cases())->flatMap(function (Permission $role) {
            return [$role->value => ucfirst($role->value)];
        })->toArray();
    }

    public static function toDescriptionArray(): array
    {
        return [
            self::UPDATE_ADMIN_USERS->value => 'Access for the parking lot can be created',
        ];
    }
}
