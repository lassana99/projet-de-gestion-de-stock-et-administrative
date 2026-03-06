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
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_devis')->nullable();
            $table->string('client')->nullable();
            $table->string('client_address')->nullable();
            $table->string('delivery_terms')->nullable(); // Délai de livraison
            $table->string('payment_terms')->nullable();  // Condition de règlement
            $table->date('validity')->nullable();         // Validité de l'offre
            $table->string('delivery_location')->nullable(); // Lieu de livraison
            $table->decimal('tax_rate', 5, 4)->default(0.18); // TVA
            $table->decimal('total_htva', 18, 2)->default(0); // Montant total HTVA
            $table->decimal('total_ttc', 18, 2)->default(0);  // Montant total TTC
            $table->enum('status', ['draft','sent','accepted','rejected'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
