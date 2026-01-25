<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Banner;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
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
        return $user->can('ViewAny:Banner');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('View:Banner');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Banner');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function update(User $user, Banner $banner): bool
    {
        return $user->can('Update:Banner');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('Delete:Banner');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Banner');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function forceDelete(User $user, Banner $banner): bool
    {
        return $user->can('ForceDelete:Banner');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Banner');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function restore(User $user, Banner $banner): bool
    {
        return $user->can('Restore:Banner');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Banner');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Banner $banner
     * @return bool
     */
    public function replicate(User $user, Banner $banner): bool
    {
        return $user->can('Replicate:Banner');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Banner');
    }

}
