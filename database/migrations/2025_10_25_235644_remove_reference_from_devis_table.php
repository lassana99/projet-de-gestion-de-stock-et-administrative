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
            // Suppression de la colonne 'reference'
            $table->dropColumn('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            // Rajout de la colonne 'reference' si l'on annule la migration
            // (Assurez-vous qu'elle supporte l'unicité si elle l'avait)
            $table->string('reference')->nullable()->after('id');
        });
    }
};