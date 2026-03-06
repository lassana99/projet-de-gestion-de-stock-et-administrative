<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('phone');
            $table->string('marital_status')->nullable()->after('specialty'); // Ex: Célibataire, Marié, etc.
            $table->integer('children_count')->default(0)->after('marital_status');
            $table->string('emergency_contact')->nullable()->after('children_count');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 
                'email', 
                'marital_status', 
                'children_count', 
                'emergency_contact'
            ]);
        });
    }
};