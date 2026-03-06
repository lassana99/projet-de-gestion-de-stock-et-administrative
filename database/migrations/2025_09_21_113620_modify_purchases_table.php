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

            // Ajouter la colonne 'reference' unique et nullable
            if (!Schema::hasColumn('purchases', 'reference')) {
                $table->string('reference')->unique()->nullable();
            }

            // Ajouter la colonne 'description' après 'productname'
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

            // Pour renommer 'price' en 'purchaseprice' uniquement si 'price' existe et 'purchaseprice' pas encore
            if (Schema::hasColumn('purchases', 'price') && !Schema::hasColumn('purchases', 'purchaseprice')) {
                $table->renameColumn('price', 'purchaseprice');
            }

            // Modifier la colonne quantity pour la rendre nullable si elle ne l'est pas déjà
            // Note : modifier les colonnes avec change() nécessite l'extension doctrine/dbal installée
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
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

            // Renommer 'purchaseprice' en 'price' uniquement si la colonne existe
            if (Schema::hasColumn('purchases', 'purchaseprice') && !Schema::hasColumn('purchases', 'price')) {
                $table->renameColumn('purchaseprice', 'price');
            }

            // Rendre quantity non nullable si nécessaire
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable(false)->change();
            }
        });
    }
};
