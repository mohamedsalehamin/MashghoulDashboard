<?php

namespace App\Console\Commands;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UpdateProviderPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shield:update-provider-permissions 
                            {--dry-run : Show what would be updated without making changes}
                            {--regenerate : Regenerate all permissions for provider role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update provider role permissions to match Filament Shield v4 format';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Updating provider permissions for Filament Shield v4...');
        
        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        $role = Role::where('name', 'provider')->where('guard_name', 'web')->first();
        
        if (!$role) {
            $this->error('Provider role not found!');
            return Command::FAILURE;
        }
        
        $this->info("Found provider role: {$role->name}");
        
        // Debug: Show available permissions count
        $allPermissions = $this->getAllShieldPermissions();
        $this->info("Available Shield permissions count: " . count($allPermissions));
        
        if ($this->option('regenerate')) {
            return $this->regeneratePermissions($role);
        }
        
        return $this->updatePermissions($role);
    }
    
    /**
     * Update existing permissions to v4 format
     */
    protected function updatePermissions(Role $role): int
    {
        $dryRun = $this->option('dry-run');
        
        $currentPermissions = $role->permissions->pluck('name')->toArray();
        $this->info("Current permissions count: " . count($currentPermissions));
        
        // Get all available permissions from Shield v4
        $allPermissions = $this->getAllShieldPermissions();
        
        // Map old permissions to new format
        $updatedPermissions = [];
        $removedPermissions = [];
        $addedPermissions = [];
        
        foreach ($currentPermissions as $permission) {
            // Check if permission exists in new format
            if (in_array($permission, $allPermissions)) {
                $updatedPermissions[] = $permission;
            } else {
                // Try to find equivalent in new format
                $newPermission = $this->mapToV4Format($permission);
                if ($newPermission && in_array($newPermission, $allPermissions)) {
                    $updatedPermissions[] = $newPermission;
                    $removedPermissions[] = $permission;
                    $addedPermissions[] = $newPermission;
                } else {
                    // Keep old permission if no mapping found
                    $updatedPermissions[] = $permission;
                }
            }
        }
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->table(
                ['Type', 'Permission'],
                array_merge(
                    array_map(fn($p) => ['Removed', $p], $removedPermissions),
                    array_map(fn($p) => ['Added', $p], $addedPermissions)
                )
            );
            return Command::SUCCESS;
        }
        
        // Sync permissions
        $permissionModels = collect($updatedPermissions)
            ->map(function ($permissionName) {
                return Utils::getPermissionModel()::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            })
            ->all();
        
        $role->syncPermissions($permissionModels);
        
        $this->info("Updated permissions count: " . count($updatedPermissions));
        $this->info("Removed: " . count($removedPermissions));
        $this->info("Added: " . count($addedPermissions));
        
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->info('Provider permissions updated successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Regenerate all permissions for provider role
     */
    protected function regeneratePermissions(Role $role): int
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Regenerating all permissions for provider role...');
        
        // Get all Shield permissions
        $allPermissions = $this->getAllShieldPermissions();
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->info("Would assign " . count($allPermissions) . " permissions");
            $this->table(
                ['Permission'],
                array_map(fn($p) => [$p], array_slice($allPermissions, 0, 20))
            );
            if (count($allPermissions) > 20) {
                $this->info("... and " . (count($allPermissions) - 20) . " more");
            }
            return Command::SUCCESS;
        }
        
        // Create or get all permissions
        $permissionModels = collect($allPermissions)
            ->map(function ($permissionName) {
                return Utils::getPermissionModel()::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            })
            ->all();
        
        $role->syncPermissions($permissionModels);
        
        $this->info("Assigned " . count($allPermissions) . " permissions to provider role");
        
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->info('Provider permissions regenerated successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Get all available permissions from Filament Shield v4
     */
    protected function getAllShieldPermissions(): array
    {
        $permissions = [];
        $separator = config('filament-shield.permissions.separator', ':');
        $case = config('filament-shield.permissions.case', 'pascal');
        
        // Get resource permissions
        $resources = FilamentShield::getResources();
        if ($resources) {
            foreach ($resources as $resourceKey => $resource) {
                $resourcePermissions = FilamentShield::getResourcePolicyActions($resourceKey);
                if ($resourcePermissions && is_array($resourcePermissions)) {
                    foreach ($resourcePermissions as $permission) {
                        if (is_string($permission)) {
                            $permissions[] = $permission;
                        }
                    }
                }
            }
        }
        
        // Get page permissions using transformPages
        $transformedPages = FilamentShield::transformPages();
        if ($transformedPages) {
            $prefix = config('filament-shield.pages.prefix', 'view');
            
            foreach ($transformedPages as $pageKey => $pageData) {
                // pageKey is the FQCN, pageData might be array or object
                $pageClass = is_string($pageKey) ? $pageKey : (is_object($pageKey) ? get_class($pageKey) : $pageKey);
                $pageName = class_basename($pageClass);
                
                // Convert to proper case
                if ($case === 'pascal') {
                    $pageName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $pageName)));
                }
                
                $permission = $prefix . $separator . $pageName;
                $permissions[] = $permission;
            }
        }
        
        // Get widget permissions using transformWidgets
        $transformedWidgets = FilamentShield::transformWidgets();
        if ($transformedWidgets) {
            $prefix = config('filament-shield.widgets.prefix', 'view');
            
            foreach ($transformedWidgets as $widgetKey => $widgetData) {
                // widgetKey is the FQCN, widgetData might be array or object
                $widgetClass = is_string($widgetKey) ? $widgetKey : (is_object($widgetKey) ? get_class($widgetKey) : $widgetKey);
                $widgetName = class_basename($widgetClass);
                
                // Convert to proper case
                if ($case === 'pascal') {
                    $widgetName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $widgetName)));
                }
                
                $permission = $prefix . $separator . $widgetName;
                $permissions[] = $permission;
            }
        }
        
        return array_unique(array_filter($permissions));
    }
    
    /**
     * Map old permission format to v4 format
     */
    protected function mapToV4Format(string $oldPermission): ?string
    {
        $separator = config('filament-shield.permissions.separator', ':');
        $case = config('filament-shield.permissions.case', 'pascal');
        
        // If already in v4 format (contains separator), return as is
        if (str_contains($oldPermission, $separator)) {
            return $oldPermission;
        }
        
        // Try to find equivalent in all available permissions
        $allPermissions = $this->getAllShieldPermissions();
        
        // Direct match
        if (in_array($oldPermission, $allPermissions)) {
            return $oldPermission;
        }
        
        // Try to find by converting format
        // v3 format: view_any_resource_name or viewAny_resource_name
        // v4 format: viewAny:ResourceName (with separator and PascalCase)
        
        // Handle resource permissions
        if (preg_match('/^(view_any|view|create|update|delete|delete_any|restore|force_delete|force_delete_any|restore_any|replicate|reorder)_(.+)$/', $oldPermission, $matches)) {
            $action = $matches[1];
            $resource = $matches[2];
            
            // Convert action to v4 format
            $actionMap = [
                'view_any' => 'viewAny',
                'delete_any' => 'deleteAny',
                'force_delete' => 'forceDelete',
                'force_delete_any' => 'forceDeleteAny',
                'restore_any' => 'restoreAny',
            ];
            $v4Action = $actionMap[$action] ?? $action;
            
            // Convert resource to PascalCase
            $resourceParts = explode('::', $resource);
            $v4Resource = implode('', array_map(function($part) {
                return implode('', array_map('ucfirst', explode('_', $part)));
            }, $resourceParts));
            
            $v4Permission = $v4Action . $separator . $v4Resource;
            
            if (in_array($v4Permission, $allPermissions)) {
                return $v4Permission;
            }
        }
        
        // Handle page permissions: page_PageName -> view:PageName
        if (preg_match('/^page_(.+)$/', $oldPermission, $matches)) {
            $pageName = $matches[1];
            $v4PageName = implode('', array_map('ucfirst', explode('_', $pageName)));
            $v4Permission = 'view' . $separator . $v4PageName;
            
            if (in_array($v4Permission, $allPermissions)) {
                return $v4Permission;
            }
        }
        
        // Handle widget permissions: widget_WidgetName -> view:WidgetName
        if (preg_match('/^widget_(.+)$/', $oldPermission, $matches)) {
            $widgetName = $matches[1];
            $v4WidgetName = implode('', array_map('ucfirst', explode('_', $widgetName)));
            $v4Permission = 'view' . $separator . $v4WidgetName;
            
            if (in_array($v4Permission, $allPermissions)) {
                return $v4Permission;
            }
        }
        
        return null;
    }
}

