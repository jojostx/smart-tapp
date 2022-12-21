<?php

namespace App\Policies;

use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParkingLotPolicy
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
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ParkingLot $parkingLot)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->isActive() && $user->canAdminParkingLot($parkingLot);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Tenant\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ParkingLot $parkingLot)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // can only update parking lots that the have non-expired admin privilege over
        return $user->isAdmin() && $user->canAdminParkingLot($parkingLot);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ParkingLot $parkingLot)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ParkingLot $parkingLot)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\ParkingLot  $parkingLot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ParkingLot $parkingLot)
    {
        return $user->isSuperAdmin();
    }
}
