<?php

namespace App\Models;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInfo extends Model
{
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization',
        'name',
        'email',
        'tax_number',
        'address',
        'zip_code',
    ];

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'));
    }
}
