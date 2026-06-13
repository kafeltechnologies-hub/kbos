<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addColumnIfMissing(Blueprint $table, string $column, string $type): void
    {
        //
    }

    public function up(): void
    {
        Schema::table('project_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('project_payments', 'voucher_number')) {
                $table->string('voucher_number')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'voucher_type')) {
                $table->string('voucher_type')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'amount_paid')) {
                $table->decimal('amount_paid', 18, 2)->default(0);
            }

            if (!Schema::hasColumn('project_payments', 'vat_applicable')) {
                $table->boolean('vat_applicable')->default(false);
            }

            if (!Schema::hasColumn('project_payments', 'vat_amount')) {
                $table->decimal('vat_amount', 18, 2)->default(0);
            }

            if (!Schema::hasColumn('project_payments', 'getfund_amount')) {
                $table->decimal('getfund_amount', 18, 2)->default(0);
            }

            if (!Schema::hasColumn('project_payments', 'nhil_amount')) {
                $table->decimal('nhil_amount', 18, 2)->default(0);
            }

            if (!Schema::hasColumn('project_payments', 'payment_narration')) {
                $table->string('payment_narration')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payee_name')) {
                $table->string('payee_name')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payee_type')) {
                $table->string('payee_type')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payee_account')) {
                $table->string('payee_account')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payee_phone')) {
                $table->string('payee_phone')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payee_tin')) {
                $table->string('payee_tin')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'bank_account')) {
                $table->string('bank_account')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'cheque_number')) {
                $table->string('cheque_number')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'momo_number')) {
                $table->string('momo_number')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'transaction_reference')) {
                $table->string('transaction_reference')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'prepared_by')) {
                $table->string('prepared_by')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'checked_by')) {
                $table->string('checked_by')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'approved_by')) {
                $table->string('approved_by')->nullable();
            }

            if (!Schema::hasColumn('project_payments', 'payment_purpose')) {
                $table->text('payment_purpose')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_payments', function (Blueprint $table) {
            // Leave empty for now to avoid accidentally dropping useful existing columns.
        });
    }
};