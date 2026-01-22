<?php

namespace App\UsersModule\Resources\UserResource\Pages;

use App\UsersModule\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        
        if ($data['user_type'] === 'existing') {
            $user = User::find($data['existing_user_id']);

            if ($user) {
                // Assign selected role
                if (!empty($data['role'])) {
                    $role = Role::find($data['role']);
                    if ($role) {
                        $user->assignRole($role->name);
                    }
                }

                // Update password only if filled
                if (isset($data['password'])) {
                    $user->password = $data['password'];
                }

                // Update active status if provided
                if (isset($data['active'])) {
                    $user->active = $data['active'];
                }

                $user->save();

            }

            // Redirect and halt creation process
            $this->redirect(static::getResource()::getUrl('index'));
            $this->halt();
        }
    }
}
