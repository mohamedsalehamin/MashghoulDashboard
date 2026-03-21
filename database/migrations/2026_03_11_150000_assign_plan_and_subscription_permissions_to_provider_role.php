<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(
            ['name' => 'provider', 'guard_name' => 'web']
        );

        $permissions = [
            'ViewAny:Plan',
            'View:Plan',
            'view_any_subscription',
            'view_subscription',
        ];

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
            $role->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'provider')->where('guard_name', 'web')->first();
        if ($role) {
            $role->revokePermissionTo([
                'ViewAny:Plan',
                'View:Plan',
                'view_any_subscription',
                'view_subscription',
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
