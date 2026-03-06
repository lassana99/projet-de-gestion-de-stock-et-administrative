<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFundingToMachinesProfitabilitiesTable extends Migration
{
    public function up()
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (!Schema::hasColumn('machines_profitabilities', 'funding')) {
                $table->decimal('funding', 14, 2)->nullable()->after('profit');
            }
        });
    }

    public function down()
    {
        Schema::table('machines_profitabilities', function (Blueprint $table) {
            if (Schema::hasColumn('machines_profitabilities', 'funding')) {
                $table->dropColumn('funding');
            }
        });
    }
}
