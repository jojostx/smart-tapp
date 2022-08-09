<?php

namespace App\Models\Tenant;

use App\Concerns\Models\CanSendAccessActivationNotification;
use App\Traits\AccessActivationNotifiable;
use App\Traits\AccessStatusManageable;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Access extends Model implements CanSendAccessActivationNotification
{
    use HasFactory, GeneratesUuid, BindsOnUuid, AccessActivationNotifiable, AccessStatusManageable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'validity_period',
        'expiry_period',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'validity_period' => 'integer',
        'expiry_period' => 'integer',
        'issued_at' => 'datetime',
    ];

    /**
     * Get the valid_until attribute of the access.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function valid_until(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->asDateTime($attributes['issued_at'])->addDays($attributes['validity_period']);
            },
        );
    }

    /**
     * Get the name for the access.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $plate_number = DB::table('vehicles')->find($attributes['vehicle_id'])?->plate_number;

                return $plate_number ? 'Access_' . $plate_number : "Access_{$attributes['id']}";
            },
        );
    }

    /**
     * Get the driver for the access.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the vehicle for the access.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the parking lot for the access.
     */
    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }

    /**
     * Get the creator for the access.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the issuer for the access.
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
