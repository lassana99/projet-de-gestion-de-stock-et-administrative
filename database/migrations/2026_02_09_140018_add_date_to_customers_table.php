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
        Schema::table('customers', function (Blueprint $table) {
            // Ajoute une colonne de type date après le champ code_client
            // On la met en nullable pour ne pas bloquer les enregistrements existants
            $table->date('date')->nullable()->after('code_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Supprime la colonne en cas de rollback (annulation de la migration)
            $table->dropColumn('date');
        });
    }
};