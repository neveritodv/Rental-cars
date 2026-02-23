<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create one if none exists)
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Clear existing notifications
        Notification::where('user_id', $user->id)->delete();

        // Create sample notifications
        $notifications = [
            [
                'type' => 'success',
                'title' => 'Contrôle créé',
                'message' => 'Le contrôle CTRL-202602-2336 a été créé avec succès',
                'link' => '/backoffice/controls/1',
                'is_read' => false,
            ],
            [
                'type' => 'info',
                'title' => 'Nouveau message',
                'message' => 'Vous avez un nouveau message de l\'équipe',
                'is_read' => false,
            ],
            [
                'type' => 'warning',
                'title' => 'Vidange requise',
                'message' => 'Le véhicule 54545 nécessite une vidange',
                'link' => '/backoffice/vehicles/1',
                'is_read' => true,
            ],
            [
                'type' => 'error',
                'title' => 'Paiement en retard',
                'message' => 'Le paiement pour le contrat #CTR-202602-0001 est en retard',
                'link' => '/backoffice/rental-contracts/1',
                'is_read' => false,
            ],
            [
                'type' => 'success',
                'title' => 'Nouveau véhicule ajouté',
                'message' => 'Le véhicule Toyota Corolla 2025 a été ajouté à la flotte',
                'is_read' => false,
            ],
            [
                'type' => 'info',
                'title' => 'Rappel d\'assurance',
                'message' => 'L\'assurance du véhicule 54545 expire dans 30 jours',
                'link' => '/backoffice/vehicles/1/insurances',
                'is_read' => false,
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::create(array_merge($notif, [
                'user_id' => $user->id,
            ]));
        }

        $this->command->info('Sample notifications created successfully!');
    }
}