<?php

namespace App\Models\Tenant;

use App\Enums\Models\UserAccountStatus;
use App\Enums\Roles\UserRole;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory,  GeneratesUuid, BindsOnUuid, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'status',
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
        'status' => UserAccountStatus::class,
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
        static::creating(function (User $user) {
            if (tenant()) {
                $user->tenant_id = tenant('id');
            }

            if (filled($user->phone_number)) {
                $user->phone_number_e164 = phone($user->phone_number, 'NG')->formatE164();
            }
        });
    }

    /**
     * Check whether the user can access the filament dashboard.
     */
    public function canAccessFilament(): bool
    {
        return $this->isActive();
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
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }

    /**
     * Check if the user's account status is 'inactive'.
     */
    public function isInactive(): bool
    {
        return $this->status == UserAccountStatus::INACTIVE;
    }

    /**
     * Check if the user's account status is 'active'.
     */
    public function isActive(): bool
    {
        return $this->status == UserAccountStatus::ACTIVE;
    }

    /**
     * Check if the user's account status is 'deactivated'.
     */
    public function isDeactivated(): bool
    {
        return $this->status == UserAccountStatus::DEACTIVATED;
    }

    /**
     * checks if the parking lot is assigned to the admin user.
     * 
     * @param \App\Models\Tenant\ParkingLot $parkingLot
     * 
     * @return bool
     */
    public function administersParkingLot(ParkingLot $parkingLot): bool
    {
        return $this->parkingLots()->where('parking_lots.id', $parkingLot->getKey())->exists();
    }

    /**
     * The parking lots assigned to the admin user.
     */
    public function parkingLots(): BelongsToMany
    {
        return $this->belongsToMany(ParkingLot::class)->withTimestamps();
    }
}
