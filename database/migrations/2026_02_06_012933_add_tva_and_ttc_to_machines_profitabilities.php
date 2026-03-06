<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            // Ajout de la TVA et du Prix TTC
            $table->decimal('tva', 14, 2)->nullable()->after('selling_price');
            $table->decimal('selling_price_ttc', 14, 2)->nullable()->after('tva');
        });
    }

    public function down(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            $table->dropColumn(['tva', 'selling_price_ttc']);
        });
    }
};