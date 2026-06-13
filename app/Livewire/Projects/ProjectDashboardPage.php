<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectBudgetLine;
use App\Models\ProjectDeliverable;
use App\Models\ProjectMaterial;
use App\Models\ProjectPhase;
use App\Models\ProjectWbsItem;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ProjectDashboardPage extends Component
{
    public function render()
    {
        $totalProjects = Project::count();

        $draftProjects = Project::where('status', 'draft')->count();

        $plannedProjects = Project::where('status', 'planned')->count();

        $approvedProjects = Project::where('status', 'approved')->count();

        $activeProjects = Project::whereIn('status', [
            'approved',
            'in_progress',
        ])->count();

        $completedProjects = Project::whereIn('status', [
            'completed',
            'closed',
        ])->count();

        $cancelledProjects = Project::where('status', 'cancelled')->count();

        $contractValue = Project::sum('contract_amount');

        $estimatedCost = Project::sum('estimated_cost');

        $budgetAmount = Project::sum('budget_amount');

        $expectedProfit = Project::sum('expected_profit');

        $profitMargin = $contractValue > 0
            ? round(($expectedProfit / $contractValue) * 100, 2)
            : 0;

        $phaseCount = Schema::hasTable('project_phases')
            ? ProjectPhase::count()
            : 0;

        $wbsCount = Schema::hasTable('project_wbs_items')
            ? ProjectWbsItem::count()
            : 0;

        $deliverableCount = Schema::hasTable('project_deliverables')
            ? ProjectDeliverable::count()
            : 0;

        $materialCount = Schema::hasTable('project_materials')
            ? ProjectMaterial::count()
            : 0;

        $budgetLineCount = Schema::hasTable('project_budget_lines')
            ? ProjectBudgetLine::count()
            : 0;

        $materialCost = Schema::hasTable('project_materials')
            ? ProjectMaterial::sum('line_total')
            : 0;

        $budgetEstimated = Schema::hasTable('project_budget_lines')
            ? ProjectBudgetLine::sum('estimated_amount')
            : 0;

        $budgetActual = Schema::hasTable('project_budget_lines')
            ? ProjectBudgetLine::sum('actual_amount')
            : 0;

        $budgetVariance = $budgetEstimated - $budgetActual;

        $averageProgress = 0;

        if (Schema::hasTable('project_phases') && ProjectPhase::count() > 0) {
            $averageProgress = round(ProjectPhase::avg('progress_percent'), 2);
        }

        $recentProjects = Project::with(['company', 'client'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.projects.project-dashboard-page', compact(
            'totalProjects',
            'draftProjects',
            'plannedProjects',
            'approvedProjects',
            'activeProjects',
            'completedProjects',
            'cancelledProjects',
            'contractValue',
            'estimatedCost',
            'budgetAmount',
            'expectedProfit',
            'profitMargin',
            'phaseCount',
            'wbsCount',
            'deliverableCount',
            'materialCount',
            'budgetLineCount',
            'materialCost',
            'budgetEstimated',
            'budgetActual',
            'budgetVariance',
            'averageProgress',
            'recentProjects'
        ))->layout('layouts.erp');
    }
}