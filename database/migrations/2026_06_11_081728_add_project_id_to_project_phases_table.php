<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_phases', function (Blueprint $table) {
            if (! Schema::hasColumn('project_phases', 'project_id')) {
                $table->foreignId('project_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('projects')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        //
    }
};