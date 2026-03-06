<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMontantAPayerParItemToFundingsTable extends Migration
{
    public function up()
    {
        Schema::table('fundings', function (Blueprint $table) {
            $table->decimal('montant_a_payer_par_item', 15, 2)->after('nombre_d_items');
        });
    }

    public function down()
    {
        Schema::table('fundings', function (Blueprint $table) {
            $table->dropColumn('montant_a_payer_par_item');
        });
    }
}
