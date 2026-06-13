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
        Schema::create('project_receipts', function (Blueprint $table) {
                $table->id();

                $table->foreignId('project_id')->constrained()->cascadeOnDelete();

                $table->string('receipt_code')->unique();
                $table->string('receipt_number')->unique();

                $table->decimal('contract_value', 18, 2)->default(0);
                $table->decimal('total_received_before', 18, 2)->default(0);
                $table->decimal('amount_received', 18, 2)->default(0);
                $table->decimal('outstanding_balance', 18, 2)->default(0);

                $table->date('date_received');

                $table->string('receipt_method')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account')->nullable();
                $table->string('cheque_number')->nullable();
                $table->string('momo_number')->nullable();
                $table->string('transaction_reference')->nullable();

                $table->string('received_from')->nullable();
                $table->string('payer_phone')->nullable();
                $table->string('payer_tin')->nullable();

                $table->text('receipt_narration')->nullable();
                $table->text('remarks')->nullable();

                $table->string('prepared_by')->nullable();
                $table->string('approved_by')->nullable();

                $table->string('status')->default('posted');

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_receipts');
    }
};
