<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Level;
use Illuminate\Auth\Access\HandlesAuthorization;

class LevelPolicy
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
        return $user->can('ViewAny:Level');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function view(User $user, Level $level): bool
    {
        return $user->can('View:Level');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Level');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function update(User $user, Level $level): bool
    {
        return $user->can('Update:Level');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function delete(User $user, Level $level): bool
    {
        return $user->can('Delete:Level');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Level');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function forceDelete(User $user, Level $level): bool
    {
        return $user->can('ForceDelete:Level');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Level');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function restore(User $user, Level $level): bool
    {
        return $user->can('Restore:Level');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Level');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Level $level
     * @return bool
     */
    public function replicate(User $user, Level $level): bool
    {
        return $user->can('Replicate:Level');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Level');
    }

}
