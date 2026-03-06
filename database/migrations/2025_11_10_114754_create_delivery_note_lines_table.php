<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_note_lines', function (Blueprint $table) {
            $table->id();

            // Lien vers le Bordereau de Livraison (clé étrangère)
            $table->foreignId('delivery_note_id')->constrained('delivery_notes')->onDelete('cascade');

            // Détails du produit
            $table->string('product_name');
            $table->string('unit_of_measure')->nullable(); // Si nécessaire

            // Quantités
            $table->integer('quantity_ordered'); // Qté du bon de commande/facture
            $table->integer('quantity_delivered'); // Qté réellement livrée
            
            // Observation spécifique à la ligne
            $table->string('observation')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_lines');
    }
};