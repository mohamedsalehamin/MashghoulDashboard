<?php

namespace App\Policies;

use App\Models\User;
use App\ReportsModule\Models\CustomersPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomersPaymentPolicy
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
        return $user->can('ViewAny:CustomersPayment');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function view(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('View:CustomersPayment');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:CustomersPayment');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function update(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('Update:CustomersPayment');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function delete(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('Delete:CustomersPayment');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:CustomersPayment');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function forceDelete(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('ForceDelete:CustomersPayment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:CustomersPayment');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function restore(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('Restore:CustomersPayment');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:CustomersPayment');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param CustomersPayment $customersPayment
     * @return bool
     */
    public function replicate(User $user, CustomersPayment $customersPayment): bool
    {
        return $user->can('Replicate:CustomersPayment');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:CustomersPayment');
    }

}
