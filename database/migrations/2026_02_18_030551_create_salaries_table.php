<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // SAL-001
            $table->string('full_name');
            $table->string('position'); // Fonction / Service
            $table->string('id_number');
            $table->string('id_type'); // CIB, Passeport, Autres
            $table->string('id_type_other')->nullable();
            $table->decimal('amount_fcfa', 15, 2);
            $table->date('payment_date'); // Date de paiement
            $table->string('payment_mode');
            $table->text('additional_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('salaries');
    }
};