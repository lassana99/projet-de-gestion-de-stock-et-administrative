<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();

            // Lien vers la facture d'origine (clé étrangère)
            // Assurez-vous que le nom de la table 'invoices' est correct
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            
            // Numéro spécifique pour le Bordereau de Livraison (ex: BL-0001)
            $table->string('delivery_note_number')->unique(); 

            // Informations du client (copiées de la facture pour la stabilité)
            $table->string('client_name');
            $table->text('client_address')->nullable();
            $table->string('code_client')->nullable();

            // Référence au Bon de Commande du client
            $table->string('purchase_order_number')->nullable();
            
            // Détails de la livraison
            $table->date('date_delivery');
            $table->string('delivery_location')->nullable();
            $table->text('driver_notes')->nullable();
            
            // Statut du BL (ex: generated, delivered, partial)
            $table->string('status')->default('generated');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};