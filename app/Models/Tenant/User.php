<?php

namespace App\Models\Tenant;

use App\Enums\Roles\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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
        'phone_verified_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user)
        {
            if (tenant()) {
                $user->tenant_id = tenant('id');
            }
        });
    }

    /**
     * Get an array of roles name attribute that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function rolesArray(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->roles()
                    ->get(['name'])
                    ->pluck('name')
                    ->map(fn ($role) => str($role)->replaceMatches('/[^A-Za-z0-9]++/', ' '))
                    ->toArray();
            },
        )->shouldCache();
    }

    /**
     * Check whether the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Check whether the user is a admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }

    public function canAccessFilament(): bool
    {
        return true;
    }
}
