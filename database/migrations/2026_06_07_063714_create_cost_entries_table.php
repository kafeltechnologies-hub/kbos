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
        Schema::create('cost_entries', function (Blueprint $table) {
                $table->id();

                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();

                $table->string('cost_code')->unique();

                $table->string('cost_type'); 
                // labour, material, fuel, transport, subcontractor, accommodation, equipment, overhead

                $table->nullableMorphs('source');

                $table->text('description')->nullable();

                $table->decimal('amount', 18, 2)->default(0);

                $table->date('cost_date');

                $table->string('status')->default('posted');

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_entries');
    }
};
