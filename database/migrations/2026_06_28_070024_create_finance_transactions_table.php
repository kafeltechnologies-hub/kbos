<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_type');
            // receipt, payment, transfer, loan, capital, refund, adjustment

            $table->string('transaction_category')->nullable();
            // customer_receipt, supplier_payment, payroll, tax, project, operations, loan_capital, internal_transfer

            $table->string('transaction_subtype')->nullable();
            // invoice_payment, bank_loan, supplier_advance, vat, ssnit, bank_to_cash, etc.

            $table->string('reference_no')->unique();
            $table->date('reference_date')->nullable();

            $table->foreignId('finance_document_id')->nullable()->constrained('finance_documents')->nullOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('finance_parties')->nullOnDelete();

            $table->string('party_name')->nullable();

            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->unsignedBigInteger('budget_id')->nullable();

            $table->foreignId('from_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('cash_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('wht_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);

            $table->string('currency')->default('GHS');
            $table->decimal('exchange_rate', 18, 6)->default(1);

            $table->string('payment_method')->nullable();
            // cash, bank, momo, cheque, card

            $table->string('external_reference')->nullable();

            $table->string('lender_name')->nullable();
            $table->decimal('interest_rate', 8, 2)->default(0);
            $table->string('interest_period')->nullable();
            $table->date('loan_start_date')->nullable();
            $table->date('loan_due_date')->nullable();

            $table->text('amount_in_words')->nullable();
            $table->text('narration')->nullable();

            $table->string('status')->default('draft');
            // draft, submitted, reviewed, approved, posted, reversed, cancelled

            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('prepared_at')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();

            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();

            $table->text('reverse_reason')->nullable();

            $table->integer('print_count')->default(0);
            $table->timestamp('last_printed_at')->nullable();
            $table->foreignId('last_printed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['transaction_type', 'status']);
            $table->index(['transaction_category', 'transaction_subtype']);
            $table->index('reference_date');
            $table->index('reference_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};