<?php

namespace App\Enums\Models;

enum AccessStatus: string
{
  case Inactive = 'inactive';
  case Issued = 'issued';
  case Active = 'active';
}
