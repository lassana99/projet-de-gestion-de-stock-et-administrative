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
        Schema::table('article_prices', function (Blueprint $table) {
            // Ajout des colonnes après 'currency'
            $table->string('incoterm')->nullable()->after('currency');
            $table->string('country')->nullable()->after('incoterm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_prices', function (Blueprint $table) {
            // Suppression des colonnes en cas de rollback
            $table->dropColumn(['incoterm', 'country']);
        });
    }
};