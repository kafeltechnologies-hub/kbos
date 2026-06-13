<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wbs_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('wbs_templates', 'project_type')) {
                $table->string('project_type')->nullable()->after('id');
            }

            if (! Schema::hasColumn('wbs_templates', 'wbs_code')) {
                $table->string('wbs_code')->nullable()->after('project_type');
            }

            if (! Schema::hasColumn('wbs_templates', 'title')) {
                $table->string('title')->nullable()->after('wbs_code');
            }

            if (! Schema::hasColumn('wbs_templates', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('wbs_templates', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('description');
            }

            if (! Schema::hasColumn('wbs_templates', 'active')) {
                $table->boolean('active')->default(true)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        //
    }
};