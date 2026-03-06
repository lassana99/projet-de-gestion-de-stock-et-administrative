<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_prices', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('designation')->nullable(); // Désignation/Machine
            $table->decimal('unit_price', 15, 2)->nullable(); // Prix unitaire
            $table->string('currency')->nullable(); // Devise
            $table->enum('type', ['Originale', 'Aftermarket'])->nullable(); // Type
            $table->unsignedBigInteger('supplier_id')->nullable(); // Fournisseur
            $table->string('supplier_name')->nullable(); // Si "Autres"
            $table->date('date')->nullable(); // Date
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_prices');
    }
};
