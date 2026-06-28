<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_ledgers')) {
            Schema::create('general_ledgers', function (Blueprint $table) {
                $table->id();
                $table->string('entry_no')->nullable();
                $table->date('entry_date')->nullable();
                $table->date('transaction_date')->nullable();
                $table->date('date')->nullable();
                $table->string('account_code')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account')->nullable();
                $table->text('description')->nullable();
                $table->text('narration')->nullable();
                $table->decimal('debit', 18, 2)->default(0);
                $table->decimal('debit_amount', 18, 2)->default(0);
                $table->decimal('credit', 18, 2)->default(0);
                $table->decimal('credit_amount', 18, 2)->default(0);
                $table->decimal('amount', 18, 2)->default(0);
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->string('source_module')->nullable();
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('reference')->nullable();
                $table->string('status')->default('posted');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['source_module', 'source_type', 'source_id']);
                $table->index('account_name');
                $table->index('entry_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
