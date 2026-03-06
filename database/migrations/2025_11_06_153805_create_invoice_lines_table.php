<?php

// database/migrations/..._create_invoice_lines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers la Facture
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price_ht', 10, 0);
            $table->decimal('total_ht', 10, 0); // (quantity * unit_price_ht)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};