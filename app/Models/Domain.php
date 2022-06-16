<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Domain as ModelsDomain;

/**
 * @mixin IdeHelperDomain
 */
class Domain extends ModelsDomain
{
    use HasFactory;
}
