<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter softDeletes à la table credit_payments
        if (!Schema::hasColumn('credit_payments', 'deleted_at')) {
            Schema::table('credit_payments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Ajouter softDeletes à la table vehicle_credits si pas déjà fait
        if (!Schema::hasColumn('vehicle_credits', 'deleted_at')) {
            Schema::table('vehicle_credits', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('vehicle_credits', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};