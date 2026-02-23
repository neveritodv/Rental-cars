<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VehicleInsurance;
use App\Models\VehicleTechnicalCheck;
use App\Models\VehicleOilChange;
use App\Models\RentalContract;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class SendReminderNotifications extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send reminder notifications for expiring items';

    public function handle()
    {
        $this->info('Sending reminder notifications...');
        
        $this->checkInsurances();
        $this->checkTechnicalChecks();
        $this->checkOilChanges();
        $this->checkContracts();
        
        $this->info('All reminders sent successfully!');
    }

    /**
     * Check insurance expirations (30 days before)
     */
    private function checkInsurances()
    {
        $targetDate = Carbon::now()->addDays(30)->toDateString();
        
        $insurances = VehicleInsurance::with('vehicle')
            ->whereDate('next_insurance_date', $targetDate)
            ->get();
            
        foreach ($insurances as $insurance) {
            $vehicle = $insurance->vehicle;
            
            // Get all users in this agency
            $users = User::where('agency_id', $vehicle->agency_id)->get();
            
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => '⚠️ Assurance bientôt expirée',
                    'message' => "L'assurance du véhicule {$vehicle->registration_number} expire dans 30 jours",
                    'link' => route('backoffice.vehicles.insurances.show', [$vehicle->id, $insurance->id]),
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            }
            
            $this->info("Insurance reminder sent for vehicle: {$vehicle->registration_number}");
        }
    }

    /**
     * Check technical check expirations (30 days before)
     */
    private function checkTechnicalChecks()
    {
        $targetDate = Carbon::now()->addDays(30)->toDateString();
        
        $checks = VehicleTechnicalCheck::with('vehicle')
            ->whereDate('next_check_date', $targetDate)
            ->get();
            
        foreach ($checks as $check) {
            $vehicle = $check->vehicle;
            
            $users = User::where('agency_id', $vehicle->agency_id)->get();
            
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => '🔧 Contrôle technique à prévoir',
                    'message' => "Le contrôle technique du véhicule {$vehicle->registration_number} est dû dans 30 jours",
                    'link' => route('backoffice.vehicles.technical-checks.show', [$vehicle->id, $check->id]),
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            }
            
            $this->info("Technical check reminder sent for vehicle: {$vehicle->registration_number}");
        }
    }

    /**
     * Check oil changes (within 1000km of next mileage)
     */
    private function checkOilChanges()
    {
        $oilChanges = VehicleOilChange::with('vehicle')
            ->whereNotNull('next_mileage')
            ->whereRaw('next_mileage - mileage <= 1000')
            ->whereRaw('mileage < next_mileage')
            ->get();
            
        foreach ($oilChanges as $oilChange) {
            $vehicle = $oilChange->vehicle;
            $kmLeft = $oilChange->next_mileage - $oilChange->mileage;
            
            $users = User::where('agency_id', $vehicle->agency_id)->get();
            
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => '🛢️ Vidange bientôt nécessaire',
                    'message' => "Le véhicule {$vehicle->registration_number} aura besoin d'une vidange dans {$kmLeft} km",
                    'link' => route('backoffice.vehicles.oil-changes.show', [$vehicle->id, $oilChange->id]),
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            }
            
            $this->info("Oil change reminder sent for vehicle: {$vehicle->registration_number}");
        }
    }

    /**
     * Check contract endings (7 days before)
     */
    private function checkContracts()
    {
        $targetDate = Carbon::now()->addDays(7)->toDateString();
        
        $contracts = RentalContract::with('vehicle')
            ->whereDate('end_date', $targetDate)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();
            
        foreach ($contracts as $contract) {
            $vehicle = $contract->vehicle;
            
            $users = User::where('agency_id', $contract->agency_id)->get();
            
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => '📄 Contrat bientôt expiré',
                    'message' => "Le contrat #{$contract->contract_number} pour le véhicule {$vehicle->registration_number} expire dans 7 jours",
                    'link' => route('backoffice.rental-contracts.show', $contract->id),
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            }
            
            $this->info("Contract reminder sent: {$contract->contract_number}");
        }
    }
}