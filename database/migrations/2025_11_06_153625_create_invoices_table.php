<?php

// database/migrations/..._create_invoices_table.php

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers le Devis d'origine
            $table->foreignId('devis_id')->constrained()->onDelete('cascade');

            // Informations de la Facture
            $table->string('invoice_number')->unique(); // Numéro de facture (FCT-2025-...)
            $table->date('date_invoice');               // Date de la facture (la seule info à saisir à la création)
            $table->string('code_client')->nullable();
            $table->string('client');
            $table->text('client_address')->nullable();
            
            // Totaux
            $table->decimal('total_ht', 10, 0)->default(0);
            $table->decimal('total_tva', 10, 0)->default(0);
            $table->decimal('total_ttc', 10, 0)->default(0);
            $table->decimal('tax_rate', 4, 2)->default(0.18); // Taux de TVA (ex: 0.18)

            // Conditions
            $table->string('delivery_terms')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('delivery_location')->nullable();
            
            // Signature / État
            $table->string('signatory_name')->nullable();
            $table->string('status')->default('pending'); // En attente, Payée, Annulée, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};