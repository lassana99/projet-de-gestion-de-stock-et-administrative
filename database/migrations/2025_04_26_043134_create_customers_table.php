<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);               // Nom du client
                $table->string('address', 255);            // Adresse
                $table->string('domain', 255);             // Domaine
                $table->string('contact', 255);            // Contact
                $table->string('email', 255)->nullable();  // Email (nullable)
                $table->string('payment_deadline', 50)->nullable(); // Échéance de paiement (nullable)
                $table->string('payment_method', 100)->nullable();  // Mode de paiement (nullable)
                $table->string('nif', 100)->nullable();     // NIF (nullable)
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
