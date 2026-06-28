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
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('budget_code')->nullable();
            $table->string('budget_name');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('project_name')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_code')->nullable();
            $table->string('account_name')->nullable();
            $table->string('financial_year');
            $table->string('period')->default('annual');
            $table->decimal('budget_amount', 18, 2)->default(0);
            $table->decimal('alert_threshold', 5, 2)->default(80);
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_budgets');
    }
};
