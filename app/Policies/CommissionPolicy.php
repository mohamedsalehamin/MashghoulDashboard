<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Commission;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommissionPolicy
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
        return $user->can('ViewAny:Commission');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function view(User $user, Commission $commission): bool
    {
        return $user->can('View:Commission');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Commission');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function update(User $user, Commission $commission): bool
    {
        return $user->can('Update:Commission');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function delete(User $user, Commission $commission): bool
    {
        return $user->can('Delete:Commission');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Commission');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function forceDelete(User $user, Commission $commission): bool
    {
        return $user->can('ForceDelete:Commission');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Commission');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function restore(User $user, Commission $commission): bool
    {
        return $user->can('Restore:Commission');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Commission');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Commission $commission
     * @return bool
     */
    public function replicate(User $user, Commission $commission): bool
    {
        return $user->can('Replicate:Commission');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Commission');
    }

}
