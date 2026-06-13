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
        Schema::create('payment_vouchers', function (Blueprint $table) {

            $table->id();

            $table->string('voucher_number')->unique();

            $table->date('voucher_date');

            $table->string('payment_type');

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('expense_category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('payee_name');

            $table->string('payment_method');

            $table->string('reference_no')->nullable();

            $table->text('narration');

            $table->decimal('gross_amount',18,2)->default(0);

            $table->boolean('vat_applicable')->default(false);

            $table->decimal('vat_amount',18,2)->default(0);

            $table->decimal('getfund_amount',18,2)->default(0);

            $table->decimal('nhil_amount',18,2)->default(0);

            $table->decimal('withholding_tax',18,2)->default(0);

            $table->decimal('net_payment',18,2)->default(0);

            $table->string('status')->default('draft');

            $table->string('prepared_by')->nullable();

            $table->string('approved_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
