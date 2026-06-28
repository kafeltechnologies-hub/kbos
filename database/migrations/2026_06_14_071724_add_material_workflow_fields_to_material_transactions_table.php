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
        Schema::table('material_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('material_transactions', 'payment_voucher_id')) {
                $table->foreignId('payment_voucher_id')->nullable()->constrained('payment_vouchers')->nullOnDelete();
            }

            if (!Schema::hasColumn('material_transactions', 'from_project_id')) {
                $table->foreignId('from_project_id')->nullable()->constrained('projects')->nullOnDelete();
            }

            if (!Schema::hasColumn('material_transactions', 'to_project_id')) {
                $table->foreignId('to_project_id')->nullable()->constrained('projects')->nullOnDelete();
            }

            if (!Schema::hasColumn('material_transactions', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable();
            }

            if (!Schema::hasColumn('material_transactions', 'account_holder_phone')) {
                $table->string('account_holder_phone')->nullable();
            }

            if (!Schema::hasColumn('material_transactions', 'expected_return_date')) {
                $table->date('expected_return_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_transactions', function (Blueprint $table) {
            //
        });
    }
};
