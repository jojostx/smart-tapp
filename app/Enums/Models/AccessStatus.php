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
        self::INACTIVE->value => 'The Access will be deactivated and cannot be used by the customer.',   
        self::ISSUED->value => "The Access will be sent to the customer's phone and can be activated.",   
        self::ACTIVE->value => "The Access will be activated and sent to the customer's phone.",
    ];
  }
}
