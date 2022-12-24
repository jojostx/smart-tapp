<?php

namespace App\Models\Tenant;

use App\Enums\Models\FeatureResources;
use App\Enums\Models\UserAccountStatus;
use App\Enums\Roles\UserRole;
use App\Notifications\Tenant\User\ResetPassword;
use App\Notifications\Tenant\User\SetPassword;
use App\Traits\MustVerifyNewEmail;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Jojostx\Larasubs\Models\Subscription;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use MustVerifyNewEmail;
    use HasApiTokens;
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;
    use Notifiable;
    use HasRoles;

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
            if (filled($user->phone_number)) {
                $user->phone_number_e164 = phone($user->phone_number, 'NG')->formatE164();
            }

            $user->tenant_id = tenant('id');
        });

        static::saving(function (User $user) {
            if (filled($user->phone_number)) {
                $user->phone_number_e164 = phone($user->phone_number, 'NG')->formatE164();
            }

            $user->tenant_id = tenant('id');
        });

        static::updating(function (User $user) {
            if (filled($user->phone_number)) {
                $user->phone_number_e164 = phone($user->phone_number, 'NG')->formatE164();
            }

            $user->tenant_id = tenant('id');
        });

        /** 
         * @note we use the created and deleted event in order to
         * prevent error: call to member function on null
         */
        static::created(function (User $user) {
            if (filled($user->phone_number)) {
                $user->phone_number_e164 = phone($user->phone_number, 'NG')->formatE164();
            }

            if ($tenant = \tenant()) {
                \tenancy()->central(function () use ($tenant) {
                    /** @var Subscription */
                    $subscription = $tenant->subscription;
                    $featureSlug = FeatureResources::TEAM_MEMBERS->value;

                    if (blank($subscription) || $subscription->missingFeature($featureSlug)) {
                        return false;
                    }

                    $feature = $subscription->plan->getFeatureBySlug($featureSlug);

                    $subscription->useUnitsOnFeature($feature, 1);
                });
            }
        });

        static::deleted(function (User $user) {
            if ($tenant = \tenant()) {
                \tenancy()->central(function () use ($tenant) {
                    /** @var Subscription */
                    $subscription = $tenant->subscription;
                    $featureSlug = FeatureResources::TEAM_MEMBERS->value;

                    if (blank($subscription) || $subscription->missingFeature($featureSlug)) {
                        return false;
                    }

                    $feature = $subscription->plan->getFeatureBySlug($featureSlug);

                    $subscription->useUnitsOnFeature($feature, -1);
                });
            }
        });
    }

    public static function getSuperAdmin(): static
    {
        return static::query()->role(UserRole::SUPER_ADMIN)->first();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the set new password notification.
     *
     * @return void
     */
    public function sendCreateNewPasswordNotification()
    {
        /**
         * @var \Illuminate\Auth\Passwords\PasswordBroker $broker
         */
        $broker = Password::broker('users');

        $token = $broker->createToken($this);

        $this->notify(new SetPassword($token));
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
     * Check if the user's account status is activated their account.
     */
    public function hasActivatedAccount()
    {
        return $this->created_at !== $this->updated_at &&
            $this->email_verified_at != null && 
            !$this->isInactive();
    }

    /**
     * activates the user's account.
     *
     * @param  bool  $saveAfterFill
     * @return bool
     */
    public function activateAccount(bool $saveAfterFill = true): bool
    {
        if ($this->isActive()) {
            return false;
        }

        $this->forceFill([
            'status' => UserAccountStatus::ACTIVE,
        ]);

        return $saveAfterFill ? $this->save() : true;
    }

    /**
     * deactivates the user's account
     *
     * @param  bool  $saveAfterFill
     * @return bool
     */
    public function deactivateAccount(bool $saveAfterFill = true): bool
    {
        if ($this->isDeactivated()) {
            return false;
        }

        $this->forceFill([
            'status' => UserAccountStatus::DEACTIVATED,
        ]);

        return $saveAfterFill ? $this->save() : true;
    }

    /**
     * checks if the parking lot is assigned to the user
     * (expirable or non-expirable admin privilege).
     *
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return bool
     */
    public function isAdminOfParkingLot(ParkingLot $parkingLot): bool
    {
        return $this->parkingLots()
            ->where('parking_lots.id', $parkingLot->getKey())
            ->exists();
    }

    /**
     * checks if the user is a main admin of the parking lot
     * (non-expirable admin privilege).
     *
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return bool
     */
    public function isMainAdminOfParkingLot(ParkingLot $parkingLot): bool
    {
        return $this->parkingLots()
            ->wherePivotNull('expires_at')
            ->where('parking_lots.id', $parkingLot->getKey())
            ->exists();
    }

    /**
     * checks if the user is a sub admin of the parking lot
     * (expirable admin privilege).
     *
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return bool
     */
    public function isSubAdminOfParkingLot(ParkingLot $parkingLot): bool
    {
        return $this->parkingLots()
            ->wherePivotNotNull('expires_at')
            ->where('parking_lots.id', $parkingLot->getKey())
            ->exists();
    }

    /**
     * checks if the user's privilege is not expired or eternal
     *
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return bool
     */
    public function canAdminParkingLot(ParkingLot $parkingLot): bool
    {
        return $this->parkingLots()
            ->where('parking_lots.id', $parkingLot->getKey())
            ->where(function (Builder $query) {
                return $query
                    ->whereNull('administrations.expires_at')
                    ->orWhere('administrations.expires_at', '>=', Carbon::now());
            })
            ->exists();
    }

    /**
     * The parking lots assigned to the admin user.
     */
    public function parkingLots(): BelongsToMany
    {
        return $this->belongsToMany(ParkingLot::class, 'administrations')
            ->as('administration')
            ->withPivot('expires_at')
            ->withTimestamps();
    }
}
