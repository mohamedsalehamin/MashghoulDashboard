<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Slider;
use Illuminate\Auth\Access\HandlesAuthorization;

class SliderPolicy
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
        return $user->can('ViewAny:Slider');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function view(User $user, Slider $slider): bool
    {
        return $user->can('View:Slider');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Slider');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function update(User $user, Slider $slider): bool
    {
        return $user->can('Update:Slider');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function delete(User $user, Slider $slider): bool
    {
        return $user->can('Delete:Slider');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Slider');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function forceDelete(User $user, Slider $slider): bool
    {
        return $user->can('ForceDelete:Slider');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Slider');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function restore(User $user, Slider $slider): bool
    {
        return $user->can('Restore:Slider');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Slider');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Slider $slider
     * @return bool
     */
    public function replicate(User $user, Slider $slider): bool
    {
        return $user->can('Replicate:Slider');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Slider');
    }

}
