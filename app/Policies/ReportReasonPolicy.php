<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\ReportReason;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportReasonPolicy
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
        return $user->can('ViewAny:ReportReason');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function view(User $user, ReportReason $reportReason): bool
    {
        return $user->can('View:ReportReason');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:ReportReason');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function update(User $user, ReportReason $reportReason): bool
    {
        return $user->can('Update:ReportReason');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function delete(User $user, ReportReason $reportReason): bool
    {
        return $user->can('Delete:ReportReason');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ReportReason');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function forceDelete(User $user, ReportReason $reportReason): bool
    {
        return $user->can('ForceDelete:ReportReason');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ReportReason');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function restore(User $user, ReportReason $reportReason): bool
    {
        return $user->can('Restore:ReportReason');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ReportReason');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param ReportReason $reportReason
     * @return bool
     */
    public function replicate(User $user, ReportReason $reportReason): bool
    {
        return $user->can('Replicate:ReportReason');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ReportReason');
    }

}
