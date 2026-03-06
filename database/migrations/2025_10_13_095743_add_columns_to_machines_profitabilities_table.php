<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (!Schema::hasColumn('machines_profitabilities', 'suppliername')) {
                $table->string('suppliername', 255)->nullable()->after('funding'); // Ajout après funding
            }
            if (!Schema::hasColumn('machines_profitabilities', 'request')) {
                $table->string('request', 255)->nullable()->after('suppliername');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'global_urbain_transport')) {
                $table->decimal('global_urbain_transport', 14, 2)->nullable()->after('request');
            }
            if (!Schema::hasColumn('machines_profitabilities', 'quantity')) {
                $table->integer('quantity')->nullable()->after('global_urbain_transport');
            }
        });
    }

    public function down(): void
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (Schema::hasColumn('machines_profitabilities', 'suppliername')) {
                $table->dropColumn('suppliername');
            }
            if (Schema::hasColumn('machines_profitabilities', 'request')) {
                $table->dropColumn('request');
            }
            if (Schema::hasColumn('machines_profitabilities', 'global_urbain_transport')) {
                $table->dropColumn('global_urbain_transport');
            }
            if (Schema::hasColumn('machines_profitabilities', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
