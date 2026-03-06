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
        Schema::table('devis', function (Blueprint $table) {
            // Ajout du champ pour le numéro de devis auto-généré
            // La colonne est nullable initialement et unique pour éviter les doublons après génération
            $table->string('devis_number')->nullable()->unique()->after('reference'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('devis_number');
        });
    }
};