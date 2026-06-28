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
            Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->nullable();
            $table->string('asset_name');
            $table->string('asset_category')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 18, 2)->default(0);
            $table->decimal('current_value', 18, 2)->default(0);
            $table->decimal('salvage_value', 18, 2)->default(0);
            $table->integer('useful_life_years')->default(5);
            $table->string('depreciation_method')->default('straight_line');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('department')->nullable();
            $table->string('custodian')->nullable();
            $table->string('condition')->default('good');
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('fixed_asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets')->cascadeOnDelete();
            $table->string('movement_type');
            $table->date('movement_date')->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('from_custodian')->nullable();
            $table->string('to_custodian')->nullable();
            $table->foreignId('from_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('to_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets_tables');
    }
};
