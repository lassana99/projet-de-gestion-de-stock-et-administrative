<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsAddReferenceAndSuppliernameDropPurchasePrice extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('reference')->after('name');
            $table->string('suppliername')->after('category_id');
            $table->dropColumn('purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('purchase_price')->after('price');
            $table->dropColumn('reference');
            $table->dropColumn('suppliername');
        });
    }
}
