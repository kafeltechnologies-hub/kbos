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
        Schema::create('invoice_voucher_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_voucher_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('item_code')->nullable();
            $table->text('description');
            $table->string('unit')->nullable();

            $table->decimal('quantity', 18, 2)->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_voucher_items');
    }
};
