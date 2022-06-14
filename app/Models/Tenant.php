<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use App\Traits\MustVerifyTenantEmail;
use Illuminate\Notifications\Notifiable;

class Tenant extends BaseTenant implements TenantWithDatabase, MustVerifyEmail
{
    use HasDatabase, MustVerifyTenantEmail, HasDomains, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'domain',
        'organization',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'password',
            'domain',
            'organization',
        ];
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
