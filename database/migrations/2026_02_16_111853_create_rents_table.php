<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable(); // Numéro
            $table->string('month'); // Mois (ex: Janvier 2024)
            $table->string('structure'); // Structure
            $table->string('reference')->nullable(); // Référence
            $table->date('issue_date'); // Date d'émission
            $table->decimal('amount_fcfa', 15, 2); // Montant en FCFA
            $table->string('payment_mode'); // Mode de payement
            $table->string('payment_mode_other')->nullable(); // Si "Autres" est choisi
            $table->enum('status', ['Payé', 'Payé partiellement', 'Non Payé']); // Statut
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rents');
    }
};