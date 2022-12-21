<?php

namespace App\Models\Tenant;

use App\Traits\MustVerifyPhoneNumber;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;
    use Notifiable;
    use MustVerifyPhoneNumber;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
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
        static::creating(function (Driver $driver) {
            if (filled($driver->phone_number)) {
                $driver->phone_number_e164 = phone($driver->phone_number, 'NG')->formatE164();
            }
        });

        static::saving(function (Driver $driver) {
            if (filled($driver->phone_number)) {
                $driver->phone_number_e164 = phone($driver->phone_number, 'NG')->formatE164();
            }
        });

        static::updating(function (Driver $driver) {
            if (filled($driver->phone_number)) {
                $driver->phone_number_e164 = phone($driver->phone_number, 'NG')->formatE164();
            }
        });
    }

    public function routeNotificationForTermii($notification)
    {
        return $this->getPhoneNumberForVerification() ?? $this->phone_number_e164;
    }

    public function routeNotificationForAfricasTalking($notification)
    {
        return $this->getPhoneNumberForVerification() ?? $this->phone_number_e164;
    }

    /**
     * Get the name for the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identifierforfilament(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return str("Driver-{$attributes['phone_number']}")->kebab()->ucfirst();
            },
        )->shouldCache();
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'accesses');
    }

    public function accesses(): HasMany
    {
        return $this->HasMany(Access::class);
    }
}
