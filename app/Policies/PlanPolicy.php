<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
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
        return $user->can('ViewAny:Plan');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function view(User $user, Plan $plan): bool
    {
        return $user->can('View:Plan');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Plan');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function update(User $user, Plan $plan): bool
    {
        return $user->can('Update:Plan');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function delete(User $user, Plan $plan): bool
    {
        return $user->can('Delete:Plan');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Plan');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function forceDelete(User $user, Plan $plan): bool
    {
        return $user->can('ForceDelete:Plan');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Plan');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function restore(User $user, Plan $plan): bool
    {
        return $user->can('Restore:Plan');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Plan');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Plan $plan
     * @return bool
     */
    public function replicate(User $user, Plan $plan): bool
    {
        return $user->can('Replicate:Plan');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Plan');
    }

}
