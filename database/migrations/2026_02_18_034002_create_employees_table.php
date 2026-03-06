<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('matricule')->unique(); // MAT-001
        $table->string('full_name');
        $table->string('position');
        $table->string('id_number');
        $table->string('id_type');
        $table->string('id_type_other')->nullable();
        $table->string('photo')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
