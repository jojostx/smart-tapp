<?php

namespace App\Enums\Models;

enum AccessStatus: string
{
  case INACTIVE = 'inactive';
  case ISSUED = 'issued';
  case ACTIVE = 'active';

  public static function toArray(): array
  {
    return collect(self::cases())->flatMap(function (AccessStatus $status) {
      return [$status->value => ucfirst($status->value)];
    })->toArray();
  }

  public static function toDescriptionArray(): array
  {
    return [
        self::INACTIVE->value => 'The access will be deactivated and cannot be used by the customer',   
        self::ISSUED->value => 'The access will be capable of being activated by the customer',   
        self::ACTIVE->value => 'The access will be activated and can be used by the user',
    ];
  }
}
