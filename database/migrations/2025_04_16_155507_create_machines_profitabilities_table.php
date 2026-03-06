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
        // Vérifie si la table n'existe pas avant de la créer
        if (!Schema::hasTable('machines_profitabilities')) {
            Schema::create('machines_profitabilities', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('product_name', 255);
                $table->string('serial_number', 255)->nullable();
                $table->integer('weight');
                $table->string('brand', 255);
                $table->string('status', 255);
                $table->string('image', 255)->nullable();
                $table->integer('quantity');
                $table->string('client', 255);
                $table->text('address');
                $table->decimal('currency', 12, 2);
                $table->decimal('urban_transport', 12, 2)->nullable();
                $table->decimal('concierge', 12, 2)->nullable();
                $table->decimal('transport_source_bko', 12, 2);
                $table->decimal('customs', 12, 2)->nullable();
                $table->decimal('land_transport', 12, 2)->nullable();
                $table->decimal('returns', 14, 2);
                $table->decimal('margin_returns', 14, 2); // Marge appliquée au prix de vente
                $table->decimal('unit_purchase_price', 12, 2);
                $table->decimal('margin', 5, 2); // en %
                $table->decimal('tax_rate', 5, 2)->nullable(); // TVA en %
                $table->decimal('tax_sale_price', 14, 2)->nullable(); // TVA appliquée au prix de vente
                $table->decimal('total_sale_price', 14, 2)->nullable();
                $table->decimal('total_sale_price_ttc', 14, 2)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines_profitabilities');
    }
};
