<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('company_name', 255);
                $table->string('country_origin', 100)->nullable();
                $table->string('contact_person', 255)->nullable();
                $table->string('specialty', 255)->nullable();
                $table->string('brand', 255)->nullable();
                $table->string('contact', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->string('website', 255)->nullable();
                $table->string('payment_deadline', 50)->nullable();
                $table->string('nif', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
