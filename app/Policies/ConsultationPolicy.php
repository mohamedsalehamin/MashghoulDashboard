<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Reservation\Consultation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
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
        return $user->can('ViewAny:Consultation');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function view(User $user, Consultation $consultation): bool
    {
        return $user->can('View:Consultation');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Consultation');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function update(User $user, Consultation $consultation): bool
    {
        return $user->can('Update:Consultation');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->can('Delete:Consultation');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Consultation');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function forceDelete(User $user, Consultation $consultation): bool
    {
        return $user->can('ForceDelete:Consultation');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Consultation');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function restore(User $user, Consultation $consultation): bool
    {
        return $user->can('Restore:Consultation');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Consultation');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Consultation $consultation
     * @return bool
     */
    public function replicate(User $user, Consultation $consultation): bool
    {
        return $user->can('Replicate:Consultation');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Consultation');
    }

}
