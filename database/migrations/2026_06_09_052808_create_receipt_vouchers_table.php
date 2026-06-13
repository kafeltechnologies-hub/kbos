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
        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_number')->unique();
            $table->date('receipt_date');

            $table->string('receipt_type');

            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('income_category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('payer_name');
            $table->string('receipt_method');
            $table->string('reference_no')->nullable();

            $table->decimal('project_value', 18, 2)->default(0);
            $table->decimal('previous_receipts', 18, 2)->default(0);
            $table->decimal('outstanding_before_receipt', 18, 2)->default(0);

            $table->decimal('amount_received', 18, 2)->default(0);
            $table->decimal('balance_after_receipt', 18, 2)->default(0);

            $table->text('amount_in_words')->nullable();
            $table->text('narration');

            $table->string('status')->default('draft');

            $table->string('prepared_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('received_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_vouchers');
    }
};
