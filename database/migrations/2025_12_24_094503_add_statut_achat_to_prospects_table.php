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
            Schema::table('prospects', function (Blueprint $table) {
                $table->enum('statut_achat', ['OUI', 'NON'])
                    ->default('NON')
                    ->after('comment');
            });
        }

    public function down(): void
        {
            Schema::table('prospects', function (Blueprint $table) {
                $table->dropColumn('statut_achat');
            });
        }

};
