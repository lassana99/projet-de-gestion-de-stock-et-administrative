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
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            // Ajout des nouveaux champs s'ils n'existent pas encore
            if (!Schema::hasColumn('machines_profitabilities', 'purchase_reference')) {
                $table->string('purchase_reference')->nullable();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'selling_price')) {
                $table->decimal('selling_price', 14, 2)->nullable();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'other_costs')) {
                $table->decimal('other_costs', 14, 2)->nullable();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'conversion_rate')) {
                $table->decimal('conversion_rate', 12, 4)->nullable();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'urban_transport')) {
                $table->decimal('urban_transport', 12, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'concierge')) {
                $table->decimal('concierge', 12, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'transport_source_bko')) {
                $table->decimal('transport_source_bko', 12, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'customs')) {
                $table->decimal('customs', 12, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'land_transport')) {
                $table->decimal('land_transport', 12, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'margin')) {
                $table->decimal('margin', 5, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('machines_profitabilities', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (Schema::hasColumn('machines_profitabilities', 'purchase_reference')) {
                $table->dropColumn('purchase_reference');
            }
            if (Schema::hasColumn('machines_profitabilities', 'selling_price')) {
                $table->dropColumn('selling_price');
            }
            if (Schema::hasColumn('machines_profitabilities', 'other_costs')) {
                $table->dropColumn('other_costs');
            }
            if (Schema::hasColumn('machines_profitabilities', 'conversion_rate')) {
                $table->dropColumn('conversion_rate');
            }
            // On ne drop pas les colonnes modifiées dans la migration 'change()', juste les change en rollback
            // Il est possible d'ajouter une migration inverse si nécessaire
        });
    }
};
