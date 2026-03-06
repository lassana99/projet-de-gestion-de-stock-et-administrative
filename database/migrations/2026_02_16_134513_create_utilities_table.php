<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('utilities', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // Format UTIL-001
            $table->string('month');
            $table->string('description'); // SOMAGEP, EDM, etc.
            $table->string('description_other')->nullable();
            $table->string('reference')->nullable();
            $table->date('issue_date');
            $table->decimal('amount_fcfa', 15, 2);
            $table->string('payment_mode');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('utilities');
    }
};