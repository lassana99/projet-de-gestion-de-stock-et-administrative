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
        // Ajoute la colonne 'code_client'
        Schema::table('devis', function (Blueprint $table) {
            $table->string('code_client')->nullable()->after('client_address'); 
            // Nous plaçons la colonne après 'client_address' pour la logique, 
            // mais l'emplacement n'a pas d'importance fonctionnelle.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprime la colonne 'code_client' en cas d'annulation (rollback)
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('code_client');
        });
    }
};