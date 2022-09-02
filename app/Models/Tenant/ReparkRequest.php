<?php

namespace App\Models\Tenant;

use App\Enums\Models\ReparkRequestStatus;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReparkRequest extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid, SoftDeletes, MassPrunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ReparkRequestStatus::class,
    ];

    /**
     * Create a new repark request from blocker and blockee accesses
     * 
     * @param Access $blocker_access
     * @param Access $blockee_access
     * 
     * @return self|null
     * @static
     */
    public static function createFromAccess(Access $blocker_access, Access $blockee_access): ?self
    {
        $blocker_vehicle_id = $blocker_access->vehicle->getKey();
        $blockee_vehicle_id = $blockee_access->vehicle->getKey();
        $blocker_driver_id = $blocker_access->driver->getKey();
        $blockee_driver_id = $blockee_access->driver->getKey();

        if ($blocker_access->is($blockee_access)) {
            return null;
        }

        if ($blocker_vehicle_id === $blockee_vehicle_id || $blocker_driver_id === $blockee_driver_id) {
            return null;
        }

        // try to find a repark request that has the same blocker_driver, blocker_vehicle, blockee_vehicle and blockee_driver
        // if found, set the status to unresolved
        // and if the repark request has been softdeleted remove softdelete
        // return the repark request.
        $reparkRequest = static::where([
            ['blockee_vehicle_id', '=', $blockee_vehicle_id],
            ['blocker_vehicle_id', '=', $blocker_vehicle_id],
            ['blockee_driver_id', '=', $blockee_driver_id],
            ['blocker_driver_id', '=', $blocker_driver_id],
        ])->latest()->first();

        if (filled($reparkRequest)) {
            $reparkRequest->{$reparkRequest->getDeletedAtColumn()} = null;
            $reparkRequest->status = ReparkRequestStatus::UNRESOLVED;

            return $reparkRequest->save() ? $reparkRequest : null;
        }

        return static::forceCreate([
            'blockee_access_id' => $blockee_access->getKey(),
            'blocker_access_id' => $blocker_access->getKey(),
            'blockee_vehicle_id' => $blockee_vehicle_id,
            'blocker_vehicle_id' => $blocker_vehicle_id,
            'blockee_driver_id' => $blockee_driver_id,
            'blocker_driver_id' => $blocker_driver_id
        ]);
    }

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonth());
    }

    /**
     * Checks if the repark request has been resolved.
     */
    public function isResolved(): bool
    {
        return $this->status == ReparkRequestStatus::RESOLVED;
    }

    /**
     * Checks if the repark request is unresolved.
     */
    public function isUnresolved(): bool
    {
        return $this->status == ReparkRequestStatus::UNRESOLVED;
    }

    /**
     * Scope a query to only query access of the type indicated by the $type parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param \App\Enums\Models\ReparkRequestStatus $type
     * @throws \ValueError
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, string|ReparkRequestStatus $type = '')
    {
        $status = is_string($type) ? ReparkRequestStatus::from($type) : $type;

        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include unresolved repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnresolved(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::UNRESOLVED->value);
    }

    /**
     * Scope a query to only include resolving repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeResolving(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::RESOLVING->value);
    }

    /**
     * Scope a query to only include resolved repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeResolved(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::RESOLVED->value);
    }

    /**
     * Get the driver for the access.
     */
    public function blockerDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'blocker_driver_id');
    }

    /**
     * Get the driver for the access.
     */
    public function blockeeDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'blockee_driver_id');
    }

    /**
     * Get the driver for the access.
     */
    public function blockerVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'blocker_vehicle_id');
    }

    /**
     * Get the driver for the access.
     */
    public function blockeeVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'blockee_vehicle_id');
    }

    /**
     * Get the driver for the access.
     */
    public function blockerAccess(): BelongsTo
    {
        return $this->belongsTo(Access::class, 'blocker_access_id');
    }

    /**
     * Get the driver for the access.
     */
    public function blockeeAccess(): BelongsTo
    {
        return $this->belongsTo(Access::class, 'blockee_access_id');
    }
}
