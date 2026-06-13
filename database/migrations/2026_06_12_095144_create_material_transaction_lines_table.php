<?php

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
        Schema::create('material_transaction_lines', function (Blueprint $table) {

            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('material_transactions')
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained('materials');

            $table->decimal('quantity',18,2);

            $table->decimal('unit_cost',18,2);

            $table->decimal('line_total',18,2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transaction_lines');
    }
};
