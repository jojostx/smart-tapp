<?php

namespace App\Enums\Models;

enum UserAccountStatus: string
{
  case INACTIVE = 'inactive';
  case ACTIVE = 'active';
  case DEACTIVATED = 'deactivated';

  public static function toArray(): array
  {
    return collect(self::cases())->flatMap(function ($status) {
      return [$status->value => ucfirst($status->value)];
    })->toArray();
  }

  public static function toDescriptionArray(): array
  {
    return [
      self::INACTIVE->value => "The Admin User's account have not been activated",
      self::ACTIVE->value => 'The Admin User have set their password and activated their account.',
      self::DEACTIVATED->value => "The Admin User's account have been deactivated.",
    ];
  }
}
