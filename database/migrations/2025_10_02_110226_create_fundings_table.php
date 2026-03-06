<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fundings', function (Blueprint $table) {
            $table->id();
            $table->string('motif');
            $table->string('nom_de_banque');
            $table->decimal('montant_emprunte', 15, 2);
            $table->integer('nombre_de_jours');
            $table->decimal('taux', 8, 4);
            $table->decimal('montant_a_payer', 15, 2);
            $table->integer('nombre_d_items');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fundings');
    }
};
