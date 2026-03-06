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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('price');
            $table->bigInteger('purchase_price');
            $table->text('description')->nullable();
            $table->integer('category_id');
            $table->string('image');
            
            // L'ordre est respecté simplement en plaçant les colonnes ici
            $table->string('status')->nullable(); 
            $table->string('brand', 255)->nullable(); 
            
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};