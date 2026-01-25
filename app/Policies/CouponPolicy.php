<?php

namespace App\Policies;

use App\Models\User;
use App\ContentModule\Models\Coupon;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
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
        return $user->can('ViewAny:Coupon');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->can('View:Coupon');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Coupon');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->can('Update:Coupon');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->can('Delete:Coupon');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Coupon');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->can('ForceDelete:Coupon');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Coupon');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->can('Restore:Coupon');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Coupon');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function replicate(User $user, Coupon $coupon): bool
    {
        return $user->can('Replicate:Coupon');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Coupon');
    }

}
