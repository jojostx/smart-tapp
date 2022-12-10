<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use App\Traits\MustVerifyTenantEmail;
use Illuminate\Notifications\Notifiable;
use Jojostx\Larasubs\Models\Concerns\HasSubscriptions;

/**
 * @mixin IdeHelperTenant
 */
class Tenant extends BaseTenant implements TenantWithDatabase, MustVerifyEmail
{
    use HasDatabase, MustVerifyTenantEmail, HasDomains, Notifiable, HasSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'domain',
        'organization',
    ];
    
    /**
     * The custom columns that are assignable when creating or updating the model.
     *
     * @return array
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'password',
            'domain',
            'organization',
            'email_verified_at'
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
