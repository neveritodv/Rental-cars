<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_credit_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            
            $table->integer('payment_number'); // Numéro de la mensualité
            $table->date('due_date'); // Date d'échéance
            $table->date('paid_date')->nullable(); // Date de paiement
            $table->decimal('amount', 15, 2); // Montant
            $table->decimal('principal', 15, 2); // Part du capital
            $table->decimal('interest', 15, 2); // Part des intérêts
            $table->decimal('penalty', 15, 2)->default(0); // Pénalité de retard
            
            $table->enum('status', ['paid', 'pending', 'late', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['vehicle_credit_id', 'payment_number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};