<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_contacts', function (Blueprint $table) {
            $table->id();
            // Clé étrangère vers prospects
            $table->foreignId('prospect_id')->constrained('prospects')->onDelete('cascade');
            
            $table->string('name')->nullable();          // Nom du contact
            $table->string('position')->nullable();      // Poste / Fonction
            $table->string('phone')->nullable();         // Téléphone
            $table->string('email')->nullable();         // Email
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_contacts');
    }
};