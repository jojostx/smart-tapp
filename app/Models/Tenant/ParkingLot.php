<?php

namespace App\Models\Tenant;

use App\Enums\Models\FeatureResources;
use App\Traits\ParkingLotStatusManageable;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Jojostx\Larasubs\Models\Subscription;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParkingLot extends Model
{
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;
    use ParkingLotStatusManageable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'status' => ParkingLotStatus::class,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        /** 
         * @note we use the created and deleted event in order to
         * prevent error: call to member function on null
         */
        static::created(function ($parkingLot) {
            /** @var Tenant */
            $tenant = \tenant();

            if ($tenant) {
                \tenancy()->central(function () use ($tenant) {
                    /** @var Subscription */
                    $subscription = $tenant->subscription;
                    $featureSlug = FeatureResources::PARKING_LOTS->value;

                    if (blank($subscription) || $subscription->missingFeature($featureSlug)) {
                        return false;
                    }


                    $feature = $subscription->plan->getFeatureBySlug($featureSlug);

                    $subscription->useUnitsOnFeature($feature, 1);
                });
            }
        });

        static::deleted(function ($parkingLot) {
            /** @var Tenant */
            $tenant = \tenant();

            if ($tenant) {
                \tenancy()->central(function () use ($tenant) {
                    /** @var Subscription */
                    $subscription = $tenant->subscription;
                    $featureSlug = FeatureResources::PARKING_LOTS->value;

                    if (blank($subscription) || $subscription->missingFeature($featureSlug)) {
                        return false;
                    }


                    $feature = $subscription->plan->getFeatureBySlug($featureSlug);

                    $subscription->useUnitsOnFeature($feature, -1);
                });
            }
        });
    }

    /**
     * Get the parking lot's qrcode svg.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function qrcode(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return replaceQrCodeAttributes(QrCode::generate(json_encode($attributes['uuid'])), 'w-full max-w-md', 'parkinglot_qrcode');
            },
        )->shouldCache();
    }

    /**
     * Get the accesses for the parking lot.
     */
    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class, 'parking_lot_id');
    }

    /**
     * The users that belong to the role.
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'accesses');
    }

    /**
     * The admin users assigned to supervise the parking lot.
     */
    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'administrations')
            ->as('administration')
            ->withPivot('expires_at')
            ->withTimestamps();
    }
}
