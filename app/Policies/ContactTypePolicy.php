<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\ContactType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactTypePolicy
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
        return $user->can('ViewAny:ContactType');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function view(User $user, ContactType $contactType): bool
    {
        return $user->can('View:ContactType');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:ContactType');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function update(User $user, ContactType $contactType): bool
    {
        return $user->can('Update:ContactType');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function delete(User $user, ContactType $contactType): bool
    {
        return $user->can('Delete:ContactType');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ContactType');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function forceDelete(User $user, ContactType $contactType): bool
    {
        return $user->can('ForceDelete:ContactType');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ContactType');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function restore(User $user, ContactType $contactType): bool
    {
        return $user->can('Restore:ContactType');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ContactType');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param ContactType $contactType
     * @return bool
     */
    public function replicate(User $user, ContactType $contactType): bool
    {
        return $user->can('Replicate:ContactType');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ContactType');
    }

}
