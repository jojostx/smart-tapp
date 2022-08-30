<?php

namespace App\Enums\Models;

enum ReparkRequestStatus: string
{
  case UNRESOLVED = 'unresolved';
  case RESOLVING = 'resolving';
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
      self::RESOLVING->value => 'The repark request is being resolved.',
      self::UNRESOLVED->value => 'The repark request is unresolved.',
    ];
  }
}
