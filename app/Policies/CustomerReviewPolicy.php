<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\CustomerReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerReviewPolicy
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
        return $user->can('ViewAny:CustomerReview');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function view(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('View:CustomerReview');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:CustomerReview');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function update(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('Update:CustomerReview');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function delete(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('Delete:CustomerReview');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:CustomerReview');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function forceDelete(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('ForceDelete:CustomerReview');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:CustomerReview');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function restore(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('Restore:CustomerReview');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:CustomerReview');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param CustomerReview $customerReview
     * @return bool
     */
    public function replicate(User $user, CustomerReview $customerReview): bool
    {
        return $user->can('Replicate:CustomerReview');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:CustomerReview');
    }

}
