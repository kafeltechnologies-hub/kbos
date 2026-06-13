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
        Schema::table('project_quotations', function (Blueprint $table) {
            $table->decimal('labor_charge', 18, 2)->default(0);
            $table->decimal('transport_charge', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->string('other_charges_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_quotations', function (Blueprint $table) {
            //
        });
    }
};
