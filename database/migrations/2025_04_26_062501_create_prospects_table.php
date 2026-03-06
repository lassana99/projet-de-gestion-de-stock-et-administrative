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
        if (!Schema::hasTable('prospects')) {
            Schema::create('prospects', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);         // Nom du prospect
                $table->string('address', 255);      // Adresse
                $table->string('domain', 255);       // Domaine
                $table->string('contact', 255);      // Contact
                $table->string('email', 255)->nullable();     // Email (nullable)
                $table->string('website', 255)->nullable();   // Site web (nullable)
                $table->string('need', 255)->nullable();      // Besoin (nullable)
                $table->text('comment')->nullable();           // Commentaire (nullable)
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
        Schema::dropIfExists('prospects');
    }
};
