<?php

namespace App\Enums\Models;

Enum AccessStatus: int {
  case Inactive = 1;
  case Issued = 2;
  case Active = 3;
}