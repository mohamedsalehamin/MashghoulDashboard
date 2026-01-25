<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Seat;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ViewAny:Seat');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function view(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('View:Seat');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('Create:Seat');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function update(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('Update:Seat');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function delete(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('Delete:Seat');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('DeleteAny:Seat');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function forceDelete(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('ForceDelete:Seat');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ForceDeleteAny:Seat');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function restore(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('Restore:Seat');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('RestoreAny:Seat');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Seat $seat
     * @return bool
     */
    public function replicate(User $user, Seat $seat): bool
    {
        // For provider panel, allow if user is provider and owns the seat
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $seat->provider_id === $provider->id;
        }
        
        return $user->can('Replicate:Seat');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('Reorder:Seat');
    }

}
