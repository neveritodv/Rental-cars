<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AgencyUserSeeder::class);
        $this->call(RolesAndSuperAdminSeeder::class);
        $this->call(AgencyRolesPermissionsSeeder::class);
        
        $this->command?->info('=================================');
        $this->command?->info('✅ ALL SEEDERS COMPLETED SUCCESSFULLY');
        $this->command?->info('=================================');
        $this->command?->info('');
        $this->command?->info('🔐 TEST ACCOUNTS:');
        $this->command?->info('  Super Admin:    super@admin.com / password123');
        $this->command?->info('  Agency Admin:   admin@agency1.com / password123');
        $this->command?->info('  Agency Manager: manager@agency1.com / password123');
        $this->command?->info('  Agency Staff:   staff@agency1.com / password123');
        $this->command?->info('');
        $this->command?->info('📊 DASHBOARD: /backoffice/dashboard');
    }
}