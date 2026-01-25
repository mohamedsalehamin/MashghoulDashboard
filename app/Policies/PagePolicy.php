<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
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
        return $user->can('ViewAny:Page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function view(User $user, Page $page): bool
    {
        return $user->can('View:Page');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Page');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function update(User $user, Page $page): bool
    {
        return $user->can('Update:Page');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->can('Delete:Page');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Page');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $user->can('ForceDelete:Page');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Page');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->can('Restore:Page');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Page');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Page $page
     * @return bool
     */
    public function replicate(User $user, Page $page): bool
    {
        return $user->can('Replicate:Page');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Page');
    }

}
