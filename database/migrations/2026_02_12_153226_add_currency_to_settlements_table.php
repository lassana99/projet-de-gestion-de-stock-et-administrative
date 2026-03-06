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
        Schema::table('settlements', function (Blueprint $table) {
            // Ajout de la devise après le montant initial
            // Valeur par défaut : FCFA
            $table->string('currency')->default('FCFA')->after('amount');

            // Ajout du montant converti en FCFA
            // On utilise le même format decimal(15,2) que la colonne amount d'origine
            $table->decimal('amount_fcfa', 15, 2)->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlements', function (Blueprint $table) {
            // Suppression des colonnes en cas de rollback
            $table->dropColumn(['currency', 'amount_fcfa']);
        });
    }
};