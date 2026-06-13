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
        Schema::create('projects', function (Blueprint $table) {
                $table->id();

                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();

                $table->string('project_code')->unique();
                $table->string('project_name');

                $table->string('project_type')->nullable();
                $table->string('location')->nullable();

                $table->decimal('contract_amount', 18, 2)->default(0);
                $table->decimal('budget_amount', 18, 2)->default(0);

                $table->date('start_date')->nullable();
                $table->date('expected_end_date')->nullable();
                $table->date('actual_end_date')->nullable();

                $table->string('status')->default('draft');
                $table->text('description')->nullable();

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
