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
            // On doit d’abord supprimer la colonne existante avant de la recréer
            $table->dropColumn('validity');
        });

        Schema::table('devis', function (Blueprint $table) {
            $table->string('validity')->nullable(); // On recrée la colonne en type string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('validity');
        });

        Schema::table('devis', function (Blueprint $table) {
            $table->date('validity')->nullable(); // Retour au type date
        });
    }
};
