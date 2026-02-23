<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'rental_contracts',
            'vehicles',
            'clients',
            'bookings',
            'agencies',
            'agents',
            'users',
            'invoices',
            'payments',
            'vehicle_brands',
            'vehicle_models',
            'agency_subscriptions',
            'contract_clients',
            'financial_accounts',
            'transaction_categories',
            'financial_transactions',
            'invoice_items',
            'vehicle_vignettes',
            'vehicle_insurances',
            'vehicle_oil_changes',
            'vehicle_technical_checks',
            'vehicle_controls',
            'vehicle_control_items',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'rental_contracts',
            'vehicles',
            'clients',
            'bookings',
            'agencies',
            'agents',
            'users',
            'invoices',
            'payments',
            'vehicle_brands',
            'vehicle_models',
            'agency_subscriptions',
            'contract_clients',
            'financial_accounts',
            'transaction_categories',
            'financial_transactions',
            'invoice_items',
            'vehicle_vignettes',
            'vehicle_insurances',
            'vehicle_oil_changes',
            'vehicle_technical_checks',
            'vehicle_controls',
            'vehicle_control_items',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};