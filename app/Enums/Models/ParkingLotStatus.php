<?php

namespace App\Enums\Models;

enum ParkingLotStatus: string
{
  case OPEN = 'open';
  case FILLED = 'filled';
  case CLOSED = 'closed';

  public static function toArray(): array
  {
    return collect(self::cases())->flatMap(function (ParkingLotStatus $status) {
      return [$status->value => ucfirst($status->value)];
    })->toArray();
  }

  public static function toDescriptionArray(): array
  {
    return [
        self::OPEN->value => 'Access for the parking lot can be created',   
        self::FILLED->value => 'Access for the Parking Lot can not be created.',   
        self::CLOSED->value => 'New and existing Accesses for the Parking Lot will be deactivated.',   
    ];
  }
}
