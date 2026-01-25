<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\State;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatePolicy
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
        return $user->can('ViewAny:State');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function view(User $user, State $state): bool
    {
        return $user->can('View:State');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:State');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function update(User $user, State $state): bool
    {
        return $user->can('Update:State');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function delete(User $user, State $state): bool
    {
        return $user->can('Delete:State');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:State');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function forceDelete(User $user, State $state): bool
    {
        return $user->can('ForceDelete:State');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:State');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function restore(User $user, State $state): bool
    {
        return $user->can('Restore:State');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:State');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param State $state
     * @return bool
     */
    public function replicate(User $user, State $state): bool
    {
        return $user->can('Replicate:State');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:State');
    }

}
