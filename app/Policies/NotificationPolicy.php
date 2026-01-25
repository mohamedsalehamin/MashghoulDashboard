<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
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
        // For provider panel, allow if user is provider (can view their own notifications)
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ViewAny:Notification');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function view(User $user, Notification $notification): bool
    {
        // For provider panel, allow if user is provider and notification belongs to them
        if ($user->hasRole(Provider::ROLE)) {
            return $notification->notifiable_id === $user->id;
        }
        
        return $user->can('View:Notification');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Notifications are created by system, not by users
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Create:Notification');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function update(User $user, Notification $notification): bool
    {
        // Notifications can't be updated
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Update:Notification');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function delete(User $user, Notification $notification): bool
    {
        // For provider panel, allow if user is provider and notification belongs to them
        if ($user->hasRole(Provider::ROLE)) {
            return $notification->notifiable_id === $user->id;
        }
        
        return $user->can('Delete:Notification');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        // For provider panel, allow if user is provider (can delete their own notifications)
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('DeleteAny:Notification');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function forceDelete(User $user, Notification $notification): bool
    {
        // Providers shouldn't force delete notifications
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDelete:Notification');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        // Providers shouldn't force delete notifications
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('ForceDeleteAny:Notification');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function restore(User $user, Notification $notification): bool
    {
        // Notifications can't be restored
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Restore:Notification');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        // Notifications can't be restored
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('RestoreAny:Notification');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function replicate(User $user, Notification $notification): bool
    {
        // Notifications can't be replicated
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Replicate:Notification');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        // Notifications can't be reordered
        if ($user->hasRole(Provider::ROLE)) {
            return false;
        }
        
        return $user->can('Reorder:Notification');
    }

}
