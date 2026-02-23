<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AgencyRolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Get or create the agency
        $agency = Agency::where('email', 'contact@agency1.com')->first();

        if (!$agency) {
            $this->command?->error('❌ Agency not found. Please run AgencyUserSeeder first.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create or Update Users with Different Roles
        |--------------------------------------------------------------------------
        */

        // Admin User - agency-admin role
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000003',
                'status' => 'active',
            ]
        );

        // Manager User - agency-manager role
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Manager User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000010',
                'status' => 'active',
            ]
        );

        // Staff User - agency-staff role
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Staff User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000011',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Roles to Users
        |--------------------------------------------------------------------------
        */

        // Assign agency-admin role to admin user
        $adminUser->syncRoles(['agency-admin']);

        // Assign agency-manager role to manager user
        $managerUser->syncRoles(['agency-manager']);

        // Assign agency-staff role to staff user
        $staffUser->syncRoles(['agency-staff']);

        // Clear cache after seeding
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Output Information
        |--------------------------------------------------------------------------
        */
        $this->command?->info('✅ Agency roles & permissions seeded successfully');
        $this->command?->info('');
        $this->command?->info('📋 Created Users with Roles:');
        $this->command?->info('  • Admin User (agency-admin)');
        $this->command?->info('    Email: admin@agency1.com | Password: password123');
        $this->command?->info('');
        $this->command?->info('  • Manager User (agency-manager)');
        $this->command?->info('    Email: manager@agency1.com | Password: password123');
        $this->command?->info('');
        $this->command?->info('  • Staff User (agency-staff)');
        $this->command?->info('    Email: staff@agency1.com | Password: password123');
        $this->command?->info('');
        $this->command?->info('🔒 Test Permissions:');
        $this->command?->info('  • Admin: Full access (all permissions)');
        $this->command?->info('  • Manager: Vehicles & Bookings CRUD + Dashboard & Reports view');
        $this->command?->info('  • Staff: Dashboard & Reports view only');
    }
}
