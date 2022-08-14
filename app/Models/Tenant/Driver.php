<?php

namespace App\Models\Tenant;

use App\Traits\MustVerifyPhoneNumber;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use HasFactory, GeneratesUuid, BindsOnUuid, Notifiable, MustVerifyPhoneNumber;

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
