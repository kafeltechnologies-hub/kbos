<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('finance_documents', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('finance_documents', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }

            if (!Schema::hasColumn('finance_documents', 'posted_by')) {
                $table->foreignId('posted_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('finance_documents', 'posted_at')) {
                $table->timestamp('posted_at')->nullable()->after('posted_by');
            }

            if (!Schema::hasColumn('finance_documents', 'amount_in_words')) {
                $table->text('amount_in_words')->nullable()->after('grand_total');
            }

            if (!Schema::hasColumn('finance_documents', 'currency')) {
                $table->string('currency')->default('GHS')->after('amount_in_words');
            }

            if (!Schema::hasColumn('finance_documents', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('currency');
            }

            if (!Schema::hasColumn('finance_documents', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_name');
            }

            if (!Schema::hasColumn('finance_documents', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_address');
            }

            if (!Schema::hasColumn('finance_documents', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }

            if (!Schema::hasColumn('finance_documents', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('customer_email');
            }
        });

        Schema::table('finance_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('finance_payments', 'transaction_category')) {
                $table->string('transaction_category')->nullable()->after('payment_type');
            }

            if (!Schema::hasColumn('finance_payments', 'transaction_subtype')) {
                $table->string('transaction_subtype')->nullable()->after('transaction_category');
            }

            if (!Schema::hasColumn('finance_payments', 'lender_name')) {
                $table->string('lender_name')->nullable()->after('party_name');
            }

            if (!Schema::hasColumn('finance_payments', 'interest_rate')) {
                $table->decimal('interest_rate', 8, 2)->default(0)->after('lender_name');
            }

            if (!Schema::hasColumn('finance_payments', 'interest_period')) {
                $table->string('interest_period')->nullable()->after('interest_rate');
            }

            if (!Schema::hasColumn('finance_payments', 'loan_start_date')) {
                $table->date('loan_start_date')->nullable()->after('interest_period');
            }

            if (!Schema::hasColumn('finance_payments', 'loan_due_date')) {
                $table->date('loan_due_date')->nullable()->after('loan_start_date');
            }

            if (!Schema::hasColumn('finance_payments', 'amount_in_words')) {
                $table->text('amount_in_words')->nullable()->after('net_amount');
            }

            if (!Schema::hasColumn('finance_payments', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('finance_payments', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }

            if (!Schema::hasColumn('finance_payments', 'posted_by')) {
                $table->foreignId('posted_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('finance_payments', 'posted_at')) {
                $table->timestamp('posted_at')->nullable()->after('posted_by');
            }
        });
    }

    public function down(): void
    {
        // Leave empty for safety in production ERP data.
    }
};