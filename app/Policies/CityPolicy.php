<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\City;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy
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
        return $user->can('ViewAny:City');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function view(User $user, City $city): bool
    {
        return $user->can('View:City');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:City');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function update(User $user, City $city): bool
    {
        return $user->can('Update:City');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function delete(User $user, City $city): bool
    {
        return $user->can('Delete:City');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:City');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function forceDelete(User $user, City $city): bool
    {
        return $user->can('ForceDelete:City');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:City');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function restore(User $user, City $city): bool
    {
        return $user->can('Restore:City');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:City');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function replicate(User $user, City $city): bool
    {
        return $user->can('Replicate:City');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:City');
    }

}
