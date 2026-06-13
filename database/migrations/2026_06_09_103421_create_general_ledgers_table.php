<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_ledgers', function (Blueprint $table) {

            $table->id();

            $table->date('posting_date');

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            $table->string('reference_no')->nullable();
            $table->string('reference_type')->nullable();

            $table->text('description')->nullable();

            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            $table->timestamps();

            $table->index('posting_date');
            $table->index('reference_no');
            $table->index('reference_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};