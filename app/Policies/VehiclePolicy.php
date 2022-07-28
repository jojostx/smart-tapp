<?php

namespace App\Policies;

use App\Enums\Models\AccessStatus;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
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
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Vehicle $vehicle)
    {
        // can only view vehicles with access to the parking lot they supervise
        // algo:
        // retrieve all the accesses for the vehicle with a status not inactive
        // 1. check if the access's status is active and it's parking lot is administered by the admin, if yes allow
        return $vehicle
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
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Vehicle $vehicle)
    {
        return $vehicle
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
     * @param  \App\Models\Tenant\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Vehicle $vehicle)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Vehicle $vehicle)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Vehicle $vehicle)
    {
        return false;
    }
}
