<?php

namespace App\Enums\Models;

enum ReparkRequestStatus: string
{
    case UNRESOLVED = 'unresolved';
    case PENDING = 'pending'; // this means the blocker has reparked and is requesting for confirmation
    case RESOLVED = 'resolved';

    public static function toArray(): array
    {
        return collect(self::cases())->flatMap(function ($status) {
            return [$status->value => ucfirst($status->value)];
        })->toArray();
    }

    public static function toDescriptionArray(): array
    {
        return [
            self::RESOLVED->value => 'The repark request has been resolved.',
            self::PENDING->value => 'The blocker has reparked and is requesting for confirmation.',
            self::UNRESOLVED->value => 'The repark request is unresolved.',
        ];
    }
}
