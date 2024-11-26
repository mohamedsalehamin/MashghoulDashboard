<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_any_cancellation::reason","create_cancellation::reason","update_cancellation::reason","delete_cancellation::reason","delete_any_cancellation::reason","view_any_catalog::branch","view_catalog::branch","create_catalog::branch","update_catalog::branch","delete_catalog::branch","delete_any_catalog::branch","view_any_catalog::category","view_catalog::category","create_catalog::category","update_catalog::category","delete_catalog::category","delete_any_catalog::category","view_any_catalog::option","create_catalog::option","update_catalog::option","delete_catalog::option","delete_any_catalog::option","view_any_catalog::product","create_catalog::product","update_catalog::product","delete_catalog::product","delete_any_catalog::product","view_any_content::banner","create_content::banner","update_content::banner","delete_content::banner","delete_any_content::banner","view_any_content::contact","view_content::contact","update_content::contact","delete_content::contact","delete_any_content::contact","view_any_content::contact::type","create_content::contact::type","update_content::contact::type","delete_content::contact::type","delete_any_content::contact::type","view_any_content::customer::review","create_content::customer::review","update_content::customer::review","delete_content::customer::review","delete_any_content::customer::review","view_any_content::faq","create_content::faq","update_content::faq","delete_content::faq","delete_any_content::faq","view_any_content::page","create_content::page","update_content::page","delete_content::page","delete_any_content::page","view_any_content::wholesale::request","view_content::wholesale::request","delete_content::wholesale::request","delete_any_content::wholesale::request","view_any_crm::address::book","view_crm::address::book","create_crm::address::book","update_crm::address::book","delete_crm::address::book","delete_any_crm::address::book","view_any_crm::coupon","view_crm::coupon","create_crm::coupon","update_crm::coupon","delete_crm::coupon","delete_any_crm::coupon","view_any_crm::customer","create_crm::customer","update_crm::customer","delete_crm::customer","delete_any_crm::customer","view_any_employees::user","create_employees::user","update_employees::user","delete_employees::user","delete_any_employees::user","view_any_locations::city","create_locations::city","update_locations::city","delete_locations::city","delete_any_locations::city","view_any_locations::district","create_locations::district","update_locations::district","delete_locations::district","delete_any_locations::district","view_any_notification","view_notification","delete_notification","delete_any_notification","view_any_order","view_order","update_order","delete_order","delete_any_order","view_any_reports::customer::payment","create_reports::customer::payment","update_reports::customer::payment","delete_reports::customer::payment","delete_any_reports::customer::payment","view_any_shield::role","create_shield::role","update_shield::role","delete_shield::role","delete_any_shield::role","page_EditProfile","page_SendNotifications","page_ManageDeveloper","page_ManageGeneral","page_ManageThirdParty","page_Themes","widget_GlobalOrderStats","widget_CustomersChart","widget_OrdersChart","widget_OrdersGroupByBranch","widget_OrderReceiptMethods","widget_LatestOrders","widget_BestSellingProducts","widget_TopBranches","widget_ChosenForYouTable","widget_Contacts","widget_DealOfTheDay"]},{"name":"panel_user","guard_name":"web","permissions":[]},{"name":"customer","guard_name":"web","permissions":[]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
