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
        Schema::create('project_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('item_code')->nullable();
            $table->string('description');
            $table->string('unit')->nullable();

            $table->decimal('quantity', 18, 2)->default(1);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2)->default(0);

            $table->string('source')->nullable();
            $table->string('status')->default('planned');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_materials');
    }
};
