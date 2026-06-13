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
            Schema::table('project_payments', function (Blueprint $table) {
                $table->string('voucher_number')->nullable()->after('payment_code');
                $table->string('payee_type')->nullable()->after('payee_name');
                $table->string('prepared_by')->nullable()->after('remarks');
                $table->string('approved_by')->nullable()->after('prepared_by');
                $table->text('payment_purpose')->nullable()->after('approved_by');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_payments', function (Blueprint $table) {
            //
        });
    }
};
