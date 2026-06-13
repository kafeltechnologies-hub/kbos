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
        Schema::create('project_wbs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('budget_amount', 18, 2)->default(0);
            $table->decimal('actual_cost', 18, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->string('status')->default('pending');

            $table->timestamps();

            $table->unique(['project_id', 'code']);
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_wbs');
    }
};
