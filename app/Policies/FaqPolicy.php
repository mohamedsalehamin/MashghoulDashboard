<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Faq;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaqPolicy
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
        return $user->can('ViewAny:Faq');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function view(User $user, Faq $faq): bool
    {
        return $user->can('View:Faq');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Faq');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function update(User $user, Faq $faq): bool
    {
        return $user->can('Update:Faq');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function delete(User $user, Faq $faq): bool
    {
        return $user->can('Delete:Faq');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Faq');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->can('ForceDelete:Faq');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Faq');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function restore(User $user, Faq $faq): bool
    {
        return $user->can('Restore:Faq');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Faq');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Faq $faq
     * @return bool
     */
    public function replicate(User $user, Faq $faq): bool
    {
        return $user->can('Replicate:Faq');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Faq');
    }

}
