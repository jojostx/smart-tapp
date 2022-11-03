<?php

namespace App\Policies;

use App\Enums\Models\AccessStatus;
use App\Models\Tenant\Driver;
use App\Models\Tenant\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() && $user->isActive();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Driver $driver)
    {
        // can only view drivers with access to the parking lot they supervise
        // algo:
        // retrieve all the accesses for the driver with a status of
        // 1. check if the access's status is active and it's parking lot is administered by the admin, if yes allow
        return $driver
            ->accesses()
            ->getQuery()
            ->whereNot('status', AccessStatus::INACTIVE)
            ->whereIn(
                'parking_lot_id',
                $user->parkingLots()->getQuery()->select('parking_lots.id')
            )
            ->exists();

        //    $parkingLot_ids = $user
        //     ->parkingLots()
        //     ->getQuery()
        //     ->pluck("parking_lots.id")
        //     ->toArray();
        //
        //    $driver
        //     ->accesses()
        //     ->getQuery()
        //     ->whereNot('status', AccessStatus::INACTIVE)
        //     ->whereIntegerInRaw('parking_lot_id', $parkingLot_ids)
        //     ->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // to create a new access for a vehicle and driver, the conditions below must be met.
        // 1. the driver must not have an access with the same vehicle as the one the driver is trying to create an access for. 
        // 2. not have an access with a status !== inactive;
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Driver $driver)
    {
        // can only update drivers with access to the parking lot they supervise
        return $driver
            ->accesses()
            ->getQuery()
            ->whereNot('status', AccessStatus::INACTIVE)
            ->whereIn(
                'parking_lot_id',
                $user->parkingLots()->getQuery()->select('parking_lots.id')
            )
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Driver $driver)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Driver $driver)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Driver $driver)
    {
        return false;
    }
}
