<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('settlements', function (Blueprint $table) {
        $table->id();
        $table->enum('type', ['debt', 'credit']); // debt = Client nous doit, credit = On doit au fournisseur
        $table->string('entity_name'); // Nom du client ou fournisseur
        $table->string('address')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->decimal('amount', 15, 2);
        $table->date('issue_date'); // Date d'émission
        $table->date('due_date');   // Date d'échéance
        $table->enum('status', ['pending', 'paid'])->default('pending'); // En attente ou Payé
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
