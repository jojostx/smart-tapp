<?php

namespace App\Policies;

use App\Models\Tenant\Access;
use App\Models\Tenant\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessPolicy
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
     * @param  \App\Models\Tenant\Access  $access
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Access $access)
    {
        return $user->isAdmin();
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
     * @param  \App\Models\Tenant\Access  $access
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Access $access)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Access  $access
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Access $access)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Access  $access
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Access $access)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Tenant\User  $user
     * @param  \App\Models\Tenant\Access  $access
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Access $access)
    {
        return $user->isAdmin();
    }
}
