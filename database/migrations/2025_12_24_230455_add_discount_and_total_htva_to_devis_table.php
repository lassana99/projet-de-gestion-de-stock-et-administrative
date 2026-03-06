<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            
            // 1. On vérifie si "discount" existe déjà avant de l'ajouter
            if (!Schema::hasColumn('devis', 'discount')) {
                if (Schema::hasColumn('devis', 'total_ht')) {
                    $table->decimal('discount', 18, 2)->default(0)->after('total_ht');
                } else {
                    $table->decimal('discount', 18, 2)->default(0);
                }
            }

            // 2. On vérifie si "total_htva" existe déjà avant de l'ajouter
            if (!Schema::hasColumn('devis', 'total_htva')) {
                // On essaie de le placer après discount s'il existe
                if (Schema::hasColumn('devis', 'discount')) {
                    $table->decimal('total_htva', 18, 2)->default(0)->after('discount');
                } else {
                    $table->decimal('total_htva', 18, 2)->default(0);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('devis', 'discount')) $columnsToDrop[] = 'discount';
            if (Schema::hasColumn('devis', 'total_htva')) $columnsToDrop[] = 'total_htva';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};