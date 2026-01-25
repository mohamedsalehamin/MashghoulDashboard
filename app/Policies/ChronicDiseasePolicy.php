<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\ChronicDisease;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChronicDiseasePolicy
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
        return $user->can('ViewAny:ChronicDisease');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function view(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('View:ChronicDisease');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:ChronicDisease');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function update(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('Update:ChronicDisease');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function delete(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('Delete:ChronicDisease');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ChronicDisease');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function forceDelete(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('ForceDelete:ChronicDisease');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ChronicDisease');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function restore(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('Restore:ChronicDisease');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ChronicDisease');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param ChronicDisease $chronicDisease
     * @return bool
     */
    public function replicate(User $user, ChronicDisease $chronicDisease): bool
    {
        return $user->can('Replicate:ChronicDisease');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ChronicDisease');
    }

}
