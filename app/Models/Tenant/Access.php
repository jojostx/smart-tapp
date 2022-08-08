<?php

namespace App\Models\Tenant;

use App\Enums\Models\AccessStatus;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Access extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
        'valid_until',
        'expires_after',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => AccessStatus::class,
        'issued_at' => 'datetime',
        'valid_until' => 'datetime',
        'expires_after' => 'integer',
    ];

    /**
     * checks if the access is expired.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !blank($this->valid_until) && now()->lte($this->valid_until);
    }

    /**
     * sets the access status to active.
     *
     * @return void
     */
    public function activate(): void
    {
        // deactivate every other accesses with the same vehicle.
        Access::where('vehicle_id', $this->vehicle->id)->update(['status' => AccessStatus::INACTIVE]);

        $this->save(['status' => AccessStatus::ACTIVE]);
    }

    /**
     * deactivate the access.
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->save(['status' => AccessStatus::INACTIVE]);
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
     * Get the name for the access.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function state(): Attribute
    {
        // [expired, issued, active, inactive]
        // to specify when the access is expired, the 'expires_after' attribute should be set and elapsed and the 'valid_until' is not in the past.
        // to specify when the access is issued the 'expires_after' attribute should be set and not elapsed and the 'valid_until' attribute should not be in the past.
        // to specify when the access is active, the 'expires_after' attribute should be null and the 'valid_until' attribute should not be in the past.
        // to specify when the access is inactive the expires_after' attribute should be null and the 'valid_until' attribute should be in the past.
        return Attribute::make(
            get: function ($value, $attributes) {
                $issued_at = $this->asDateTime($attributes['issued_at']);
                $valid_until =  $this->asDateTime($attributes['valid_until']);
                $expires_after = $attributes['expires_after'];

                $elapsed = now()->greaterThan($issued_at->addMinutes($expires_after));
                $invalid = now()->greaterThan($valid_until->subMinutes($expires_after));

                // expired
                if (filled($expires_after) && $elapsed && !$invalid) {
                    return AccessStatus::EXPIRED;
                }

                // issued
                if (filled($expires_after) && !$elapsed && !$invalid) {
                    return AccessStatus::ISSUED;
                }

                // active
                if (blank($expires_after) && !$invalid) {
                    return AccessStatus::ACTIVE;
                }

                // inactive | deactivated
                if (blank($expires_after) && $invalid) {
                    return AccessStatus::INACTIVE;
                }

                return AccessStatus::INACTIVE;
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
