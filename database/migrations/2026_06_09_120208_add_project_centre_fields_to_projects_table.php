<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'project_type')) {
                $table->string('project_type')->nullable()->after('project_name');
            }

            if (! Schema::hasColumn('projects', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'start_date')) {
                $table->date('start_date')->nullable();
            }

            if (! Schema::hasColumn('projects', 'end_date')) {
                $table->date('end_date')->nullable();
            }

            if (! Schema::hasColumn('projects', 'duration_days')) {
                $table->integer('duration_days')->default(0);
            }

            if (! Schema::hasColumn('projects', 'contract_amount')) {
                $table->decimal('contract_amount', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('projects', 'estimated_cost')) {
                $table->decimal('estimated_cost', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('projects', 'budget_amount')) {
                $table->decimal('budget_amount', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('projects', 'expected_profit')) {
                $table->decimal('expected_profit', 18, 2)->default(0);
            }

            if (! Schema::hasColumn('projects', 'profit_margin')) {
                $table->decimal('profit_margin', 8, 2)->default(0);
            }

            if (! Schema::hasColumn('projects', 'priority')) {
                $table->string('priority')->default('normal');
            }

            if (! Schema::hasColumn('projects', 'scope_summary')) {
                $table->text('scope_summary')->nullable();
            }

            if (! Schema::hasColumn('projects', 'objectives')) {
                $table->text('objectives')->nullable();
            }

            if (! Schema::hasColumn('projects', 'location')) {
                $table->text('location')->nullable();
            }

            if (! Schema::hasColumn('projects', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (! Schema::hasColumn('projects', 'project_manager')) {
                $table->string('project_manager')->nullable();
            }

            if (! Schema::hasColumn('projects', 'site_engineer')) {
                $table->string('site_engineer')->nullable();
            }

            if (! Schema::hasColumn('projects', 'client_representative')) {
                $table->string('client_representative')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};