<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (! Schema::hasColumn('materials', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('material_categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('materials', 'standard_price')) {
                $table->decimal('standard_price', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('materials', 'selling_price')) {
                $table->decimal('selling_price', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('materials', 'minimum_stock')) {
                $table->decimal('minimum_stock', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('materials', 'maximum_stock')) {
                $table->decimal('maximum_stock', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('materials', 'reorder_level')) {
                $table->decimal('reorder_level', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('materials', 'barcode')) {
                $table->string('barcode')->nullable();
            }

            if (! Schema::hasColumn('materials', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            if (! Schema::hasColumn('materials', 'active')) {
                $table->boolean('active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            //
        });
    }
};