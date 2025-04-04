<?php

namespace App\Enums\Models;

enum AccessStatus: string
{
    case INACTIVE = 'inactive';
    case ISSUED = 'issued';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';

    /**
     * @param  array<string>  $exclude the cases that should be excluded
     */
    public static function toArray(array $exclude = []): array
    {
        return collect(self::cases())
          ->flatMap(function ($status) {
              return [$status->value => ucfirst($status->value)];
          })
          ->except(collect($exclude)
            ->transform(function ($item) {
                return strtolower($item);
            }))
          ->toArray();
    }

    public static function toDescriptionArray(): array
    {
        return [
            self::INACTIVE->value => 'The Access will be deactivated and cannot be used by the driver.',
            self::ISSUED->value => "The Access will be sent to the driver's phone and can be activated.",
            self::ACTIVE->value => "The Access will be activated and sent to the driver's phone.",
            self::EXPIRED->value => 'The Access will be deactivated and cannot be used by the driver.',
        ];
    }
}
