<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sécurité : Si la table n'existe pas encore, on ne fait rien
        if (!Schema::hasTable('machines_profitabilities')) {
            return;
        }

        // ÉTAPE 1 : Nettoyage (Suppression des colonnes obsolètes)
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            $columnsToDrop = [
                'product_name', 'serial_number', 'image', 'quantity', 'client',
                'address', 'currency', 'returns', 'margin_returns', 'tax_sale_price',
                'total_sale_price', 'total_sale_price_ttc', 'other_costs', 'tax_rate'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('machines_profitabilities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // ÉTAPE 2 : Reconstruction (Ajout des nouvelles colonnes)
        // On utilise un nouveau bloc pour que MySQL connaisse la nouvelle structure avant de placer les "after"
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (!Schema::hasColumn('machines_profitabilities', 'purchase_reference')) {
                $table->string('purchase_reference')->after('id');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'unit_purchase_price')) {
                $table->decimal('unit_purchase_price', 12, 2)->after('purchase_reference');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'weight')) {
                $table->integer('weight')->after('unit_purchase_price');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'brand')) {
                $table->string('brand')->after('weight');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'status')) {
                $table->string('status')->after('brand');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'conversion_rate')) {
                $table->decimal('conversion_rate', 12, 4)->nullable()->after('status');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'urban_transport')) {
                $table->decimal('urban_transport', 12, 2)->nullable()->after('conversion_rate');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'concierge')) {
                $table->decimal('concierge', 12, 2)->nullable()->after('urban_transport');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'transport_source_bko')) {
                $table->decimal('transport_source_bko', 12, 2)->nullable()->after('concierge');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'customs')) {
                $table->decimal('customs', 12, 2)->nullable()->after('transport_source_bko');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'land_transport')) {
                $table->decimal('land_transport', 12, 2)->nullable()->after('customs');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'margin')) {
                $table->decimal('margin', 5, 2)->after('land_transport');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'selling_price')) {
                $table->decimal('selling_price', 14, 2)->nullable()->after('margin');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'profit')) {
                $table->decimal('profit', 14, 2)->nullable()->after('selling_price');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('machines_profitabilities')) {
            Schema::table('machines_profitabilities', function (Blueprint $table) {
                // 1. Supprimer les colonnes ajoutées
                $cols = [
                    'purchase_reference', 'unit_purchase_price', 'weight', 'brand', 'status',
                    'conversion_rate', 'urban_transport', 'concierge', 'transport_source_bko',
                    'customs', 'land_transport', 'margin', 'selling_price', 'profit'
                ];
                foreach($cols as $c) {
                    if (Schema::hasColumn('machines_profitabilities', $c)) { $table->dropColumn($c); }
                }

                // 2. Recréer les anciennes colonnes
                $table->decimal('tax_rate', 5, 2)->nullable();
                $table->string('product_name')->nullable();
                $table->string('serial_number')->nullable();
                $table->string('image')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('client')->nullable();
                $table->text('address')->nullable();
                $table->decimal('currency', 12, 2)->nullable();
                $table->decimal('returns', 14, 2)->nullable();
                $table->decimal('margin_returns', 14, 2)->nullable();
                $table->decimal('tax_sale_price', 14, 2)->nullable();
                $table->decimal('total_sale_price', 14, 2)->nullable();
                $table->decimal('total_sale_price_ttc', 14, 2)->nullable();
                $table->decimal('other_costs', 14, 2)->nullable();
            });
        }
    }
};