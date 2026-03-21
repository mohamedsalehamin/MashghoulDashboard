<?php

namespace App\Policies;

use App\Models\User;
use App\ReportsModule\Models\SubscriptionPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:SubscriptionPayment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('View:SubscriptionPayment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:SubscriptionPayment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('Update:SubscriptionPayment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('Delete:SubscriptionPayment');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:SubscriptionPayment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('ForceDelete:SubscriptionPayment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:SubscriptionPayment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('Restore:SubscriptionPayment');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:SubscriptionPayment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SubscriptionPayment $subscriptionPayment): bool
    {
        return $user->can('Replicate:SubscriptionPayment');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:SubscriptionPayment');
    }
}
