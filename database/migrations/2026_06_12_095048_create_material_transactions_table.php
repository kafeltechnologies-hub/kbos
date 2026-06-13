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
        Schema::create('material_transactions', function (Blueprint $table) {

            $table->id();

            $table->string('transaction_no')->unique();

            $table->enum('transaction_type',[
                'receive',
                'issue_project',
                'issue_sale',
                'return',
                'adjustment'
            ]);

            $table->foreignId('project_id')
                ->nullable()
                ->constrained();

            $table->date('transaction_date');

            $table->string('reference')->nullable();

            $table->text('remarks')->nullable();

            $table->enum('status',[
                'draft',
                'approved',
                'reversed',
                'cancelled'
            ])->default('draft');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users');

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transactions');
    }
};
