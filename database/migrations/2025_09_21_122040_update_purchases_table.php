<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Supprime la colonne 'purchase_code' si elle existe
            if (Schema::hasColumn('purchases', 'purchase_code')) {
                $table->dropColumn('purchase_code');
            }

            // Renommer productname en purchasename
            if (Schema::hasColumn('purchases', 'productname') && !Schema::hasColumn('purchases', 'purchasename')) {
                $table->renameColumn('productname', 'purchasename');
            }

            // Renommer productimage en purchaseimage
            if (Schema::hasColumn('purchases', 'productimage') && !Schema::hasColumn('purchases', 'purchaseimage')) {
                $table->renameColumn('productimage', 'purchaseimage');
            }

            // Ajouter la colonne 'reference' unique et nullable si pas existante
            if (!Schema::hasColumn('purchases', 'reference')) {
                $table->string('reference')->unique()->nullable();
            }

            // Ajouter la colonne 'description' après 'purchasename'
            if (!Schema::hasColumn('purchases', 'description')) {
                $table->text('description')->nullable();
            }

            // Ajouter/modifier les autres colonnes en vérifiant leur existence
            if (!Schema::hasColumn('purchases', 'Category')) {
                $table->string('Category')->nullable();
            }

            if (!Schema::hasColumn('purchases', 'brand')) {
                $table->string('brand')->nullable();
            }

            if (!Schema::hasColumn('purchases', 'status')) {
                $table->string('status')->nullable();
            }

            if (!Schema::hasColumn('purchases', 'country')) {
                $table->string('country')->nullable();
            }

            // Renommer price en purchaseprice si nécessaire
            if (Schema::hasColumn('purchases', 'price') && !Schema::hasColumn('purchases', 'purchaseprice')) {
                $table->renameColumn('price', 'purchaseprice');
            }

            // Modifier la colonne quantity pour la rendre nullable (nécessite doctrine/dbal)
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Remettre à l'ancien nom productname
            if (Schema::hasColumn('purchases', 'purchasename') && !Schema::hasColumn('purchases', 'productname')) {
                $table->renameColumn('purchasename', 'productname');
            }

            // Remettre à l'ancien nom productimage
            if (Schema::hasColumn('purchases', 'purchaseimage') && !Schema::hasColumn('purchases', 'productimage')) {
                $table->renameColumn('purchaseimage', 'productimage');
            }

            if (Schema::hasColumn('purchases', 'reference')) {
                $table->dropUnique(['reference']);
                $table->dropColumn('reference');
            }

            if (Schema::hasColumn('purchases', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('purchases', 'Category')) {
                $table->dropColumn('Category');
            }

            if (Schema::hasColumn('purchases', 'brand')) {
                $table->dropColumn('brand');
            }

            if (Schema::hasColumn('purchases', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('purchases', 'country')) {
                $table->dropColumn('country');
            }

            // Renommer purchaseprice en price si nécessaire
            if (Schema::hasColumn('purchases', 'purchaseprice') && !Schema::hasColumn('purchases', 'price')) {
                $table->renameColumn('purchaseprice', 'price');
            }

            // Modifier quantity pour non nullable
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable(false)->change();
            }
        });
    }
};
