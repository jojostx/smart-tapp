<?php

namespace App\Traits;

use App\Enums\Models\FeatureResources;
use App\Enums\Models\ParkingLotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

trait ParkingLotStatusManageable
{
    abstract public function accesses(): HasMany;

    abstract public function getKey();

    /** 
     * gets the maximum number of vehicles per parking lot
     * based on the tenant's subscription
     */
    public function getMaxCapacity(): int
    {
        /** @var \App\Models\Tenant */
        $tenant = \tenant();

        return $tenant->subscription->getMaxFeatureUnits(FeatureResources::PARKING_LOTS->value);
    }

    /**
     * Scope a query to only query parking lots of the type indicated by the $type parameter
     *
     * @method static whereStatus() get parking lots of the type indicated by the $type parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Enums\Models\ParkingLotStatus  $status
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \ValueError
     */
    public function scopeWhereStatus(Builder $query, string | ParkingLotStatus $status = 'open')
    {
        $status = is_string($status) ? ParkingLotStatus::from($status) : $status;

        $query = match ($status) {
            ParkingLotStatus::OPEN => $query->whereOpen(),
            ParkingLotStatus::CLOSED => $query->whereClosed(),
            ParkingLotStatus::FILLED => $query->whereFilled(),
            default => $query
        };

        return $query;
    }

    /**
     * Scope a query to only include OPEN parking lots.
     * open: where the total 'not inactive' (issued|active|expired) accesses
     * is less than the parking lot's max_capacity and the status is set to OPEN
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereOpen(Builder $query)
    {
        return $query->where('status', ParkingLotStatus::OPEN);
    }

    /**
     * Scope a query to only include expired parking lots.
     * closed: where the total parking lot's status is set to CLOSED
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereClosed(Builder $query)
    {
        return $query->where('status', ParkingLotStatus::CLOSED);
    }

    /**
     * Scope a query to only include expired parking lots.
     * filled: where the total 'not inactive' (issued|active|expired)
     * accesses is equal to the parking lot's max_capacity
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFilled(Builder $query)
    {
        return $query->whereHas('accesses', function (Builder $query) {
            $query->whereNotInactive();
        }, '>=', $this->getMaxCapacity());
    }

    /**
     * Get the current status of the parking lot
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->isFilled()) {
                    return ParkingLotStatus::FILLED;
                }

                return ParkingLotStatus::from($value);
            },
        );
    }

    /**
     * checks if the parking lot is open.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === ParkingLotStatus::OPEN;
    }

    /**
     * checks if the parking lot is closed || locked.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === ParkingLotStatus::CLOSED;
    }

    /**
     * checks if the parking lot is closed || locked.
     *
     * @return bool
     */
    public function isFilled(): bool
    {
        $this->loadCount(['accesses' => function ($query) {
            $query->whereNotInactive();
        }]);

        return $this->accesses_count >= $this->getMaxCapacity();
    }

    /**
     * open the parking lot by setting the status to OPEN.
     */
    public function open(): bool
    {
        return $this->forceFill([
            'status' => ParkingLotStatus::OPEN->value,
        ])->save();
    }

    /**
     * close the parking lot by setting the status to CLOSED.
     * this prevents creation of accesses for the parking lot
     */
    public function close(): bool
    {
        return $this->forceFill([
            'status' => ParkingLotStatus::CLOSED->value,
        ])->save();
    }

    /**
     * close the parking lot by setting the status to CLOSED.
     * this prevents creation of accesses for the parking lot
     * and deactivates all accesses for this parking lot.
     */
    public function lockdown(): bool
    {
        try {
            DB::beginTransaction();

            // deactivate all accesses
            $this->deactivateAccesses();
            // close the parking lot
            $this->close();

            DB::commit();

            // check if all related accesses are inactive
            return $this->accesses()->whereNotInactive()->doesntExist();
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    /**
     * deactivate all related accesses
     */
    public function deactivateAccesses()
    {
        // deactivate all accesses
        return DB::table('accesses')
            ->where('parking_lot_id', $this->getKey())
            ->update([
                'expiry_period' => 0,
                'issued_at' => DB::raw('DATE_SUB(`issued_at`, INTERVAL IFNULL(`validity_period`, 1) DAY)')
            ]);
    }
}
