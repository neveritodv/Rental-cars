<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Define Module Modules & Resources with Actions
        |--------------------------------------------------------------------------
        | Format: "module.resource.action"
        | Modules: dashboard, agencies, users, vehicles, bookings, reports
        | Resources: general (for now, can be extended)
        | Actions: view, create, edit, delete
        */
        $modulesConfig = [
            'dashboard' => [
                'general' => ['view'], // Dashboard view only
            ],
            'agencies' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'users' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicles' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'bookings' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'reports' => [
                'general' => ['view'], // Reports view only
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Generate All Permissions (CRUD Strategy)
        |--------------------------------------------------------------------------
        */
        $allPermissions = [];
        foreach ($modulesConfig as $module => $resources) {
            foreach ($resources as $resource => $actions) {
                foreach ($actions as $action) {
                    $permissionName = "{$module}.{$resource}.{$action}";

                    Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'backoffice',
                    ]);

                    $allPermissions[] = $permissionName;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */
        $roles = [
            'super-admin',
            'agency-admin',
            'agency-manager',
            'agency-staff',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'backoffice',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions to Roles
        |--------------------------------------------------------------------------
        */

        // Super-Admin: All permissions
        Role::findByName('super-admin', 'backoffice')
            ->syncPermissions($allPermissions);

        // Agency-Admin: All permissions except agencies management
        $agencyAdminPerms = array_filter($allPermissions, function ($perm) {
            return !str_starts_with($perm, 'agencies.');
        });
        Role::findByName('agency-admin', 'backoffice')
            ->syncPermissions(array_values($agencyAdminPerms));

        // Agency-Manager: View + Create/Edit/Delete bookings & vehicles, view dashboard
        $agencyManagerPerms = [
            'dashboard.general.view',
            'vehicles.general.view',
            'vehicles.general.create',
            'vehicles.general.edit',
            'vehicles.general.delete',
            'bookings.general.view',
            'bookings.general.create',
            'bookings.general.edit',
            'bookings.general.delete',
            'reports.general.view',
        ];
        Role::findByName('agency-manager', 'backoffice')
            ->syncPermissions($agencyManagerPerms);

        // Agency-Staff: Dashboard & Reports view only
        $agencyStaffPerms = [
            'dashboard.general.view',
            'reports.general.view',
        ];
        Role::findByName('agency-staff', 'backoffice')
            ->syncPermissions($agencyStaffPerms);

        /*
        |--------------------------------------------------------------------------
        | Assign Roles to Users
        |--------------------------------------------------------------------------
        */
        $superAdmin = User::where('email', 'admin@agency1.com')->first();
        if ($superAdmin) {
            $superAdmin->syncRoles(['super-admin']);
        }

        // Clear cache after seeding
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('✅ Roles & permissions seeded successfully');
        $this->command?->info("✅ Created " . count($allPermissions) . " permissions with CRUD strategy");
        $this->command?->info('✅ Super Admin assigned to admin@agency1.com');
    }
}
