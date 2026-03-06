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
        Schema::table('invoices', function (Blueprint $table) {
            // 1. Augmenter la précision des colonnes de prix (Passer de 10,0 à 18,2)
            // Cela permet de gérer les centimes et des montants très élevés en FCFA
            $table->decimal('total_ht', 18, 2)->default(0.00)->change();
            $table->decimal('total_tva', 18, 2)->default(0.00)->change();
            $table->decimal('total_ttc', 18, 2)->default(0.00)->change();

            // 2. Ajouter la colonne total_htva (le montant HT après application de la remise)
            $table->decimal('total_htva', 18, 2)->default(0.00)->after('discount');
            
            // Note: La colonne 'discount' existe déjà, elle sera maintenant utilisée 
            // pour stocker le pourcentage (ex: 5.00 pour 5%).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // On retire la colonne ajoutée
            $table->dropColumn('total_htva');

            // On remet les colonnes à leur état précédent (Optionnel)
            $table->decimal('total_ht', 10, 0)->default(0)->change();
            $table->decimal('total_tva', 10, 0)->default(0)->change();
            $table->decimal('total_ttc', 10, 0)->default(0)->change();
        });
    }
};