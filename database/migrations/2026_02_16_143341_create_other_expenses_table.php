<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('other_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // OTH-001
            $table->string('full_name');
            $table->string('position'); // Fonction / Service
            $table->string('id_number'); // N° pièce
            $table->string('id_type'); // CIB, Passeport, Autres
            $table->string('id_type_other')->nullable();
            $table->decimal('amount_fcfa', 15, 2);
            $table->string('payment_reason'); // Motif
            $table->string('payment_reason_other')->nullable();
            $table->string('description'); // SOMAGEP, EDM, etc.
            $table->string('description_other')->nullable();
            $table->text('additional_details')->nullable(); // Détails complémentaires
            $table->string('payment_mode');
            $table->date('date'); // Ajout d'un champ date pour le suivi
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('other_expenses');
    }
};