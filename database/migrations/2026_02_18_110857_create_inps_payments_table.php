<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    // Table principale
    Schema::create('inps_payments', function (Blueprint $table) {
        $table->id();
        $table->string('number')->unique(); // INP-001
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('amount_fcfa', 15, 2);
        $table->date('payment_date');
        $table->string('payment_mode');
        $table->text('additional_details')->nullable();
        $table->timestamps();
    });

    // Table Pivot pour la multi-sélection des employés
    Schema::create('employee_inps_payment', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->foreignId('inps_payment_id')->constrained()->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inps_payments');
    }
};
