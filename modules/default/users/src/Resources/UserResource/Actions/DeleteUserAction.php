<?php

namespace App\UsersModule\Resources\UserResource\Actions;

use Filament\Actions\Action;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DeleteUserAction
{
    static public function make()
    {
        return Action::make('manageRoles')
            ->label(__('panel.actions.delete'))
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalHeading(__('panel.actions.delete'))
            // ->modalDescription(__('panel.actions.delete_user_description'))
            ->action(function (User $record): void {
                // Debug current roles
                $currentRoles = $record->roles;
                $roleDetails = [];
                
                foreach ($currentRoles as $role) {
                    $roleDetails[] = [
                        'id' => $role->id,
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                    ];
                }
                
                Log::info('User roles before processing', [
                    'user_id' => $record->id,
                    'user_name' => $record->name,
                    'roles' => $roleDetails
                ]);
                
                $hasCustomerRole = $record->hasRole('customer');
                
                if ($hasCustomerRole) {
                    // Keep only 'customer' and 'panel_user' roles
                    $allowedRoles = ['customer', 'panel_user'];
                    $currentRoleNames = $record->roles->pluck('name')->toArray();

                    // Calculate roles to be removed
                    $rolesToRemove = array_diff($currentRoleNames, $allowedRoles);

                    // Keep only the allowed roles
                    $rolesToKeep = array_intersect($currentRoleNames, $allowedRoles);

                    Log::info('Role processing details', [
                        'user_id' => $record->id,
                        'current_roles' => $currentRoleNames,
                        'roles_to_keep' => $rolesToKeep,
                        'roles_to_remove' => $rolesToRemove
                    ]);

                    // Sync roles - this will remove all other roles
                    $record->syncRoles($rolesToKeep);

                    $roleChanges = count($rolesToRemove) > 0 
                        ? "Removed roles: " . implode(', ', $rolesToRemove)
                        : "No roles needed to be removed";

                    // Notification::make()
                    //     ->title('User roles updated')
                    //     ->body("User '{$record->name}' now has only " . implode(' and ', $rolesToKeep) . " roles. $roleChanges")
                    //     ->success()
                    //     ->send();
                } else {
                    // Delete user if they don't have customer role
                    $userName = $record->name;
                    $record->delete();

                    // Notification::make()
                    //     ->title('User deleted')
                    //     ->body("User '{$userName}' has been deleted as they did not have the customer role")
                    //     ->warning()
                    //     ->send();
                }
            });
    }
}