<?php

namespace App\Policies;

use App\Models\User;
use App\UsersModule\Models\Users\Customer;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
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
        // For provider panel, allow if user is provider (can view customers who have reservations with them)
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ViewAny:Customer');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function view(User $user, Customer $customer): bool
    {
        // For provider panel, allow if user is provider and customer has reservations with them
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $customer->reservations()->where('reservable_id', $provider->id)->exists();
        }
        
        return $user->can('View:Customer');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Providers shouldn't create customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Create:Customer');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function update(User $user, Customer $customer): bool
    {
        // Providers shouldn't update customer data
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Update:Customer');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Providers shouldn't delete customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Delete:Customer');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        // Providers shouldn't delete customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('DeleteAny:Customer');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        // Providers shouldn't delete customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDelete:Customer');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        // Providers shouldn't delete customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDeleteAny:Customer');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function restore(User $user, Customer $customer): bool
    {
        // Providers shouldn't restore customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Restore:Customer');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        // Providers shouldn't restore customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('RestoreAny:Customer');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function replicate(User $user, Customer $customer): bool
    {
        // Providers shouldn't replicate customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Replicate:Customer');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        // Providers shouldn't reorder customers
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Reorder:Customer');
    }

}
