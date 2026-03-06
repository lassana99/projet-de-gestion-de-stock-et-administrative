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
        Schema::table('purchases', function (Blueprint $table) {
            // Ajout de la colonne date_purchase de type date
            // On la place après purchaseimage pour garder un ordre logique
            $table->date('date_purchase')->nullable()->after('purchaseimage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Suppression de la colonne en cas de rollback
            $table->dropColumn('date_purchase');
        });
    }
};