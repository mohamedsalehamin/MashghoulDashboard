<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Reservation;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
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
        
        return $user->can('ViewAny:Reservation');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function view(User $user, Reservation $reservation): bool
    {
        // For provider panel, allow if user is provider and reservation belongs to them
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $reservation->reservable_id === $provider->id;
        }
        
        return $user->can('View:Reservation');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Providers shouldn't create reservations (customers create them)
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Create:Reservation');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function update(User $user, Reservation $reservation): bool
    {
        // For provider panel, allow if user is provider and reservation belongs to them
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $reservation->reservable_id === $provider->id;
        }
        
        return $user->can('Update:Reservation');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Providers shouldn't delete reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Delete:Reservation');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        // Providers shouldn't delete reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('DeleteAny:Reservation');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        // Providers shouldn't delete reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDelete:Reservation');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        // Providers shouldn't delete reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDeleteAny:Reservation');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        // Providers shouldn't restore reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Restore:Reservation');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        // Providers shouldn't restore reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('RestoreAny:Reservation');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function replicate(User $user, Reservation $reservation): bool
    {
        // Providers shouldn't replicate reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Replicate:Reservation');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        // Providers shouldn't reorder reservations
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Reorder:Reservation');
    }

}
