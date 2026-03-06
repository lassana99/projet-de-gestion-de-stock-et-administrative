<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            // Ajout de la colonne date_profitability
            $table->date('date_profitability')->nullable()->after('margin');
        });
    }

    public function down(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            $table->dropColumn('date_profitability');
        });
    }
};