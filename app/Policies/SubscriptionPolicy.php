<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
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
        return $user->can('view_any_subscription');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->can('{{ View }}');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_subscription');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->can('update_subscription');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->can('delete_subscription');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_subscription');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function forceDelete(User $user, Subscription $subscription): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function restore(User $user, Subscription $subscription): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function replicate(User $user, Subscription $subscription): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }

}
