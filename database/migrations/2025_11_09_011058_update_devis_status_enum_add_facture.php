<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer la définition actuelle de la colonne ENUM (si nécessaire, sinon décommenter le change)
        // ATTENTION : Le support de DB::statement peut varier selon votre SGBD (MySQL est OK).
        
        // 1. Définir le nouveau set de valeurs ENUM (incluant 'facturé')
        // Ex: ('draft', 'sent', 'accepted', 'rejected', 'facturé')
        DB::statement("ALTER TABLE `devis` CHANGE `status` `status` ENUM('draft', 'sent', 'accepted', 'rejected', 'facturé') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancien set de valeurs (retirer 'facturé')
        DB::statement("ALTER TABLE `devis` CHANGE `status` `status` ENUM('draft', 'sent', 'accepted', 'rejected') NOT NULL DEFAULT 'draft'");
    }
};