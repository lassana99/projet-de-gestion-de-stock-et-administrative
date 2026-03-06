<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndBrandToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Vérifier si la colonne 'status' existe avant d'ajouter
            if (!Schema::hasColumn('products', 'status')) {
                $table->string('status')->nullable()->after('image');
            }
            // Vérifier si la colonne 'brand' existe avant d'ajouter
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Supprimer la colonne 'brand' si elle existe
            if (Schema::hasColumn('products', 'brand')) {
                $table->dropColumn('brand');
            }
            // Supprimer la colonne 'status' si elle existe
            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
