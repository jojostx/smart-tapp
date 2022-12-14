<?php

namespace App\Models;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_6',
        'last_4',
        'issuer',
        'country',
        'type',
        'token',
        'expiry',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
    ];

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'));
    }
}
