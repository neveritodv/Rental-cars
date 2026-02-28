<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Agency2Seeder extends Seeder
{
    public function run(): void
    {
        $agency2 = Agency::where('email', 'contact@agency2.com')->first();
        
        if (!$agency2) {
            $this->command->error('❌ Agency 2 not found!');
            return;
        }

        // Agency Admin for Agency 2
        User::firstOrCreate(
            ['email' => 'admin@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Agency 2 Admin',
                'password' => Hash::make('password123'),
                'phone' => '+212600000020',
            ]
        );

        // Agency Manager for Agency 2
        User::firstOrCreate(
            ['email' => 'manager@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Agency 2 Manager',
                'password' => Hash::make('password123'),
                'phone' => '+212600000021',
            ]
        );

        // Agency Staff for Agency 2
        User::firstOrCreate(
            ['email' => 'staff@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Agency 2 Staff',
                'password' => Hash::make('password123'),
                'phone' => '+212600000022',
            ]
        );

        $this->command->info('✅ Agency 2 users created');
    }
}