<?php

namespace App\Policies;

use App\CatalogModule\Models\Reservation\Rate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Admin can view all ratings in dashboard.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rate $rate): bool
    {
        // Admin can view all
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Provider can view ratings on their profile
        if ($rate->provider_id === $user->id) {
            return true;
        }

        // User can view their own ratings
        return $rate->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * Only admins can create manual ratings from dashboard.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rate $rate): bool
    {
        // Admin can update any
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // User can update their own rating (not replies)
        if (!$rate->isReply() && $rate->user_id === $user->id) {
            return true;
        }

        // Provider can update their own reply
        if ($rate->isReply() && $rate->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rate $rate): bool
    {
        // Admin can delete any
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Provider can delete their own replies
        if ($rate->isReply() && $rate->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Rate $rate): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Rate $rate): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Rate $rate): bool
    {
        return false; // Ratings should not be replicated
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return false; // Ratings should not be reordered
    }

    // ========================================
    // CUSTOM POLICIES
    // ========================================

    /**
     * Determine whether the user can reply to a rating.
     * Only the provider being rated can reply.
     */
    public function reply(User $user, Rate $rate): bool
    {
        // Can't reply to a reply
        if ($rate->isReply()) {
            return false;
        }

        // Only the provider being rated can reply
        return $rate->provider_id === $user->id;
    }

    /**
     * Determine whether the user can approve a rating.
     * Only admins can approve manual ratings.
     */
    public function approve(User $user, Rate $rate): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can reject a rating.
     * Only admins can reject ratings.
     */
    public function reject(User $user, Rate $rate): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }
}

