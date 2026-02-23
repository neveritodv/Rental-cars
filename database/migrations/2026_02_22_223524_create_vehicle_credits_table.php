<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Informations du crédit
            $table->string('credit_number')->unique();
            $table->string('creditor_name'); // Nom du créancier (banque, institution)
            $table->decimal('total_amount', 15, 2); // Montant total du crédit
            $table->decimal('down_payment', 15, 2)->default(0); // Apport initial
            $table->decimal('monthly_payment', 15, 2); // Mensualité
            $table->integer('duration_months'); // Durée en mois
            $table->decimal('interest_rate', 5, 2)->default(0); // Taux d'intérêt
            $table->date('start_date'); // Date de début
            $table->date('end_date'); // Date de fin
            $table->decimal('remaining_amount', 15, 2); // Montant restant
            $table->integer('remaining_months')->nullable(); // Mois restants
            
            // Statut
            $table->enum('status', ['active', 'completed', 'defaulted', 'pending'])->default('active');
            
            // Documents
            $table->string('contract_file')->nullable(); // Fichier du contrat
            $table->json('documents')->nullable(); // Autres documents
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('credit_number');
            $table->index('status');
            $table->index(['vehicle_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_credits');
    }
};