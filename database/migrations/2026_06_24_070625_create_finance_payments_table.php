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
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_type'); // receipt, payment
            $table->string('payment_no')->unique();
            $table->date('payment_date')->nullable();

            $table->foreignId('finance_document_id')->nullable()->constrained('finance_documents')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained('finance_budgets')->nullOnDelete();

            $table->string('party_name')->nullable(); // customer, supplier, staff
            $table->string('purpose')->nullable(); // invoice, supplier, payroll, operations, budget
            $table->string('payment_method')->nullable(); // cash, bank, momo, cheque

            $table->foreignId('cash_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('debit_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('credit_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->boolean('apply_wht')->default(false);
            $table->decimal('wht_rate', 8, 2)->default(0);
            $table->decimal('wht_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);

            $table->string('external_reference')->nullable();
            $table->text('narration')->nullable();

            $table->string('status')->default('draft'); // draft, posted, approved, reversed, cancelled

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_payments');
    }
};
