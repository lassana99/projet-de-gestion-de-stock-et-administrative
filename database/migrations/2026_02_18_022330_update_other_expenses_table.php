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
        Schema::table('other_expenses', function (Blueprint $table) {
            // 1. Suppression des champs d'identité et de fonction
            $table->dropColumn(['position', 'id_number', 'id_type', 'id_type_other']);

            // 2. Suppression de l'ancienne description (dropdown) et de son champ "Autres"
            $table->dropColumn(['description', 'description_other']);

            // 3. Ajout du nouveau champ "Désignation" (en format text pour avoir la même taille que détails complémentaires)
            // On le place après le motif de paiement
            $table->text('designation')->after('payment_reason_other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_expenses', function (Blueprint $table) {
            // Réajout des colonnes en cas de rollback
            $table->string('position');
            $table->string('id_number');
            $table->string('id_type');
            $table->string('id_type_other')->nullable();
            $table->string('description');
            $table->string('description_other')->nullable();
            
            // Suppression de la désignation
            $table->dropColumn('designation');
        });
    }
};