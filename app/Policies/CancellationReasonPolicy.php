<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\CancellationReason;
use Illuminate\Auth\Access\HandlesAuthorization;

class CancellationReasonPolicy
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
        return $user->can('ViewAny:CancellationReason');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function view(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('View:CancellationReason');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:CancellationReason');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function update(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('Update:CancellationReason');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function delete(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('Delete:CancellationReason');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:CancellationReason');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function forceDelete(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('ForceDelete:CancellationReason');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:CancellationReason');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function restore(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('Restore:CancellationReason');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:CancellationReason');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param CancellationReason $cancellationReason
     * @return bool
     */
    public function replicate(User $user, CancellationReason $cancellationReason): bool
    {
        return $user->can('Replicate:CancellationReason');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:CancellationReason');
    }

}
