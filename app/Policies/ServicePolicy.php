<?php

namespace App\Policies;

use App\Models\User;
use App\CatalogModule\Models\Service;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
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
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ViewAny:Service');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function view(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('View:Service');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('Create:Service');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function update(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('Update:Service');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function delete(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('Delete:Service');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('DeleteAny:Service');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function forceDelete(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('ForceDelete:Service');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('ForceDeleteAny:Service');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function restore(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('Restore:Service');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('RestoreAny:Service');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function replicate(User $user, Service $service): bool
    {
        // For provider panel, allow if user is provider and owns the service
        if ($user->hasRole(Provider::ROLE)) {
            $provider = $user->provider;
            return $provider && $service->provider_id === $provider->id;
        }
        
        return $user->can('Replicate:Service');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        // For provider panel, allow if user is provider
        if ($user->hasRole(Provider::ROLE)) {
            return true;
        }
        
        return $user->can('Reorder:Service');
    }

}
