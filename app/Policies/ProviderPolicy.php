<?php

namespace App\Policies;

use App\Models\User;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_provider');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function view(User $user, Provider $provider): bool
    {
        return $user->can('view_provider');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_provider');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function update(User $user, Provider $provider): bool
    {
        return $user->can('update_provider');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function delete(User $user, Provider $provider): bool
    {
        return $user->can('delete_provider');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_provider');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function forceDelete(User $user, Provider $provider): bool
    {
        return $user->can('force_delete_provider');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_provider');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function restore(User $user, Provider $provider): bool
    {
        return $user->can('restore_provider');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_provider');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\UsersModule\Models\Users\Provider  $provider
     * @return bool
     */
    public function replicate(User $user, Provider $provider): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }

}
