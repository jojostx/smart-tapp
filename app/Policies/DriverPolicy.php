<?php

namespace App\Policies;

use App\Enums\Models\AccessStatus;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

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
        if ($user->isSuperAdmin()) {
            return true;
        }

        // can only view drivers with access to the parking lot they supervise
        return $driver->accesses
            ->contains(fn (Access $access) => $user->canAdminParkingLot($access->parkingLot));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

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
        if ($user->isSuperAdmin()) {
            return true;
        }

        // can only update drivers with access to the parking lot they supervise
        return $driver->accesses
            ->contains(fn (Access $access) => $user->canAdminParkingLot($access->parkingLot));
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
        if ($user->isSuperAdmin()) {
            return true;
        }

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
        if ($user->isSuperAdmin()) {
            return true;
        }

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
        if ($user->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}
