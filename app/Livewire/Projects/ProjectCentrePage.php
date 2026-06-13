<?php

namespace App\Livewire\Projects;

use App\Models\Client;
use App\Models\Company;
use App\Models\Material;
use App\Models\Project;
use App\Models\WbsTemplate;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProjectCentrePage extends Component
{
    public string $search = '';

    public ?int $editingProjectId = null;
    public bool $isEditing = false;

    public ?string $project_code = null;
    public ?string $project_name = null;
    public ?string $project_type = null;

    public ?int $company_id = null;
    public ?int $client_id = null;

    public ?string $start_date = null;
    public ?string $end_date = null;
    public int $duration_days = 0;

    public $contract_amount = 0;
    public $estimated_cost = 0;
    public $budget_amount = 0;
    public $expected_profit = 0;
    public $profit_margin = 0;

    public string $status = 'draft';
    public string $priority = 'normal';

    public ?string $scope_summary = null;
    public ?string $objectives = null;
    public ?string $location = null;
    public ?string $notes = null;

    public ?string $project_manager = null;
    public ?string $site_engineer = null;
    public ?string $client_representative = null;

    public array $projectPhases = [];
    public array $wbsItems = [];
    public array $deliverables = [];
    public array $projectMaterials = [];
    public array $budgetLines = [];

    public array $projectTypes = [
        'electrical_distribution' => 'Electrical Distribution',
        'substation' => 'Substation Project',
        'renewable_energy' => 'Renewable Energy / Solar',
        'ict_infrastructure' => 'ICT Infrastructure',
        'security_systems' => 'Security Systems',
        'maintenance' => 'Maintenance Contract',
        'consultancy' => 'Consultancy',
        'general_contract' => 'General Contract',
    ];

    public array $statuses = [
        'draft',
        'planned',
        'approved',
        'in_progress',
        'on_hold',
        'completed',
        'closed',
        'cancelled',
    ];

    public array $priorities = [
        'low',
        'normal',
        'high',
        'urgent',
    ];

    public array $materialStatuses = [
        'planned',
        'requested',
        'procured',
        'delivered',
        'used',
        'returned',
    ];

    public array $wbsStatuses = [
        'pending',
        'in_progress',
        'completed',
        'delayed',
        'cancelled',
    ];

    public function mount(?int $projectId = null): void
        {
            $projectId = $projectId ?: request()->query('projectId');

            $this->start_date = now()->toDateString();
            $this->end_date = now()->addDays(30)->toDateString();

            $this->projectPhases = [$this->blankProjectPhase()];
            $this->wbsItems = [$this->blankWbsItem()];
            $this->deliverables = [$this->blankDeliverable()];
            $this->projectMaterials = [$this->blankProjectMaterial()];
            $this->budgetLines = [$this->blankBudgetLine()];

            if ($projectId) {
                $this->editProject($projectId);
            }

            $this->calculateTimeline();
            $this->calculateFinancials();
        }

    public function blankProjectPhase(): array
    {
        return [
            'phase_code' => '',
            'phase_name' => '',
            'description' => '',
            'start_date' => null,
            'end_date' => null,
            'responsible_person' => '',
            'budget_amount' => 0,
            'actual_cost' => 0,
            'progress_percent' => 0,
            'status' => 'pending',
        ];
    }

    public function blankWbsItem(): array
    {
        return [
            'wbs_code' => '',
            'title' => '',
            'description' => '',
            'start_date' => null,
            'end_date' => null,
            'responsible_person' => '',
            'budget_amount' => 0,
            'progress_percent' => 0,
            'status' => 'pending',
        ];
    }

    public function blankDeliverable(): array
    {
        return [
            'deliverable_name' => '',
            'description' => '',
            'due_date' => null,
            'owner' => '',
            'acceptance_criteria' => '',
            'status' => 'pending',
        ];
    }

    public function blankProjectMaterial(): array
    {
        return [
            'material_id' => null,
            'item_code' => '',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_cost' => 0,
            'line_total' => 0,
            'source' => '',
            'status' => 'planned',
        ];
    }

    public function blankBudgetLine(): array
    {
        return [
            'budget_category' => '',
            'description' => '',
            'estimated_amount' => 0,
            'actual_amount' => 0,
            'variance_amount' => 0,
        ];
    }

    public function updatedStartDate(): void
    {
        $this->calculateTimeline();
    }

    public function updatedEndDate(): void
    {
        $this->calculateTimeline();
    }

    public function updatedContractAmount(): void
    {
        $this->calculateFinancials();
    }

    public function updatedEstimatedCost(): void
    {
        $this->calculateFinancials();
    }

    public function updatedProjectType(): void
    {
        $this->loadWbsTemplate();
    }

    public function updatedProjectMaterials($value, $key): void
    {
        if (str_ends_with($key, '.material_id')) {
            $index = (int) explode('.', $key)[0];
            $this->loadMaterialIntoProjectMaterial($index);
        }

        $this->calculateProjectMaterials();
        $this->calculateFinancials();
    }

    public function updatedBudgetLines(): void
    {
        $this->calculateBudgetLines();
        $this->calculateFinancials();
    }

    public function updatedProjectPhases(): void
    {
        $this->calculateFinancials();
    }

    public function updatedWbsItems(): void
    {
        $this->calculateFinancials();
    }

    public function calculateTimeline(): void
    {
        if (! $this->start_date || ! $this->end_date) {
            $this->duration_days = 0;
            return;
        }

        $start = strtotime($this->start_date);
        $end = strtotime($this->end_date);

        if ($end < $start) {
            $this->duration_days = 0;
            return;
        }

        $this->duration_days = (int) floor(($end - $start) / 86400) + 1;
    }

    public function calculateProjectMaterials(): void
    {
        foreach ($this->projectMaterials as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);

            $this->projectMaterials[$index]['line_total'] = round($quantity * $unitCost, 2);
        }
    }

    public function calculateBudgetLines(): void
    {
        foreach ($this->budgetLines as $index => $line) {
            $estimated = (float) ($line['estimated_amount'] ?? 0);
            $actual = (float) ($line['actual_amount'] ?? 0);

            $this->budgetLines[$index]['variance_amount'] = round($estimated - $actual, 2);
        }
    }

    public function calculateFinancials(): void
    {
        $this->calculateProjectMaterials();
        $this->calculateBudgetLines();

        $materialTotal = collect($this->projectMaterials)
            ->sum(fn ($item) => (float) ($item['line_total'] ?? 0));

        $budgetTotal = collect($this->budgetLines)
            ->sum(fn ($line) => (float) ($line['estimated_amount'] ?? 0));

        $phaseBudgetTotal = collect($this->projectPhases)
            ->sum(fn ($line) => (float) ($line['budget_amount'] ?? 0));

        $wbsBudgetTotal = collect($this->wbsItems)
            ->sum(fn ($line) => (float) ($line['budget_amount'] ?? 0));

        $this->budget_amount = round(
            $budgetTotal + $phaseBudgetTotal + $wbsBudgetTotal,
            2
        );

        if ((float) $this->estimated_cost <= 0) {
            $this->estimated_cost = round(
                $materialTotal + $budgetTotal + $phaseBudgetTotal + $wbsBudgetTotal,
                2
            );
        }

        $this->expected_profit = round(
            (float) $this->contract_amount - (float) $this->estimated_cost,
            2
        );

        if ((float) $this->contract_amount > 0) {
            $this->profit_margin = round(
                ((float) $this->expected_profit / (float) $this->contract_amount) * 100,
                2
            );
        } else {
            $this->profit_margin = 0;
        }
    }

    public function loadWbsTemplate(): void
    {
        if (! $this->project_type || $this->isEditing) {
            return;
        }

        if (collect($this->wbsItems)->filter(fn ($item) => filled($item['title'] ?? null))->count() > 0) {
            return;
        }

        $templates = WbsTemplate::where('project_type', $this->project_type)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        if ($templates->isEmpty()) {
            return;
        }

        $this->wbsItems = $templates->map(fn ($template) => [
            'wbs_code' => $template->wbs_code,
            'title' => $template->title,
            'description' => $template->description,
            'start_date' => null,
            'end_date' => null,
            'responsible_person' => '',
            'budget_amount' => 0,
            'progress_percent' => 0,
            'status' => 'pending',
        ])->toArray();

        session()->flash('info', 'WBS template loaded for selected project type.');
    }

    public function loadMaterialIntoProjectMaterial(int $index): void
    {
        $materialId = $this->projectMaterials[$index]['material_id'] ?? null;

        if (! $materialId) {
            return;
        }

        $material = Material::find($materialId);

        if (! $material) {
            return;
        }

        $this->projectMaterials[$index]['item_code'] = $material->material_code ?? null;
        $this->projectMaterials[$index]['description'] = $material->description ?: $material->name;
        $this->projectMaterials[$index]['unit'] = $material->unit;
        $this->projectMaterials[$index]['unit_cost'] = $material->standard_price ?: $material->selling_price ?: 0;

        $this->calculateProjectMaterials();
        $this->calculateFinancials();
    }

    public function addProjectPhase(): void
    {
        $this->projectPhases[] = $this->blankProjectPhase();
    }

    public function removeProjectPhase(int $index): void
    {
        unset($this->projectPhases[$index]);

        $this->projectPhases = array_values($this->projectPhases);

        if (count($this->projectPhases) === 0) {
            $this->projectPhases[] = $this->blankProjectPhase();
        }

        $this->calculateFinancials();
    }

    public function addWbsItem(): void
    {
        $this->wbsItems[] = $this->blankWbsItem();
    }

    public function removeWbsItem(int $index): void
    {
        unset($this->wbsItems[$index]);

        $this->wbsItems = array_values($this->wbsItems);

        if (count($this->wbsItems) === 0) {
            $this->wbsItems[] = $this->blankWbsItem();
        }

        $this->calculateFinancials();
    }

    public function addDeliverable(): void
    {
        $this->deliverables[] = $this->blankDeliverable();
    }

    public function removeDeliverable(int $index): void
    {
        unset($this->deliverables[$index]);

        $this->deliverables = array_values($this->deliverables);

        if (count($this->deliverables) === 0) {
            $this->deliverables[] = $this->blankDeliverable();
        }
    }

    public function addProjectMaterial(): void
    {
        $this->projectMaterials[] = $this->blankProjectMaterial();
    }

    public function removeProjectMaterial(int $index): void
    {
        unset($this->projectMaterials[$index]);

        $this->projectMaterials = array_values($this->projectMaterials);

        if (count($this->projectMaterials) === 0) {
            $this->projectMaterials[] = $this->blankProjectMaterial();
        }

        $this->calculateProjectMaterials();
        $this->calculateFinancials();
    }

    public function addBudgetLine(): void
    {
        $this->budgetLines[] = $this->blankBudgetLine();
    }

    public function removeBudgetLine(int $index): void
    {
        unset($this->budgetLines[$index]);

        $this->budgetLines = array_values($this->budgetLines);

        if (count($this->budgetLines) === 0) {
            $this->budgetLines[] = $this->blankBudgetLine();
        }

        $this->calculateBudgetLines();
        $this->calculateFinancials();
    }

    public function generateProjectCode(): string
    {
        $last = Project::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'PRJ' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New project buffer initialized.');
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Project buffer cleared.');
    }

    public function sync(): void
    {
        $this->calculateTimeline();
        $this->calculateFinancials();

        session()->flash('info', 'Project centre synchronized.');
    }

    public function approveProject(int $projectId): void
    {
        Project::findOrFail($projectId)->update([
            'status' => 'approved',
        ]);

        session()->flash('success', 'Project approved successfully.');
    }

    public function startProject(int $projectId): void
    {
        Project::findOrFail($projectId)->update([
            'status' => 'in_progress',
        ]);

        session()->flash('success', 'Project moved to in-progress.');
    }

    public function completeProject(int $projectId): void
    {
        Project::findOrFail($projectId)->update([
            'status' => 'completed',
        ]);

        session()->flash('success', 'Project marked as completed.');
    }

    public function cancelProject(int $projectId): void
    {
        Project::findOrFail($projectId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Project cancelled.');
    }

    public function editProject(int $projectId): void
    {
        $project = Project::with([
            'company',
            'client',
            'phases',
            'wbsItems',
            'deliverables',
            'projectMaterials',
            'budgetLines',
        ])->findOrFail($projectId);

        $this->editingProjectId = $project->id;
        $this->isEditing = true;

        foreach ($project->only([
            'project_code',
            'project_name',
            'project_type',
            'company_id',
            'client_id',
            'start_date',
            'end_date',
            'duration_days',
            'contract_amount',
            'estimated_cost',
            'budget_amount',
            'expected_profit',
            'profit_margin',
            'status',
            'priority',
            'scope_summary',
            'objectives',
            'location',
            'notes',
            'project_manager',
            'site_engineer',
            'client_representative',
        ]) as $key => $value) {
            $this->{$key} = $value;
        }

        $this->projectPhases = $project->phases->map(fn ($item) => [
            'phase_code' => $item->phase_code,
            'phase_name' => $item->phase_name,
            'description' => $item->description,
            'start_date' => $item->start_date,
            'end_date' => $item->end_date,
            'responsible_person' => $item->responsible_person,
            'budget_amount' => $item->budget_amount,
            'actual_cost' => $item->actual_cost,
            'progress_percent' => $item->progress_percent,
            'status' => $item->status,
        ])->toArray();

        $this->wbsItems = $project->wbsItems->map(fn ($item) => [
            'wbs_code' => $item->wbs_code,
            'title' => $item->title,
            'description' => $item->description,
            'start_date' => $item->start_date,
            'end_date' => $item->end_date,
            'responsible_person' => $item->responsible_person,
            'budget_amount' => $item->budget_amount,
            'progress_percent' => $item->progress_percent,
            'status' => $item->status,
        ])->toArray();

        $this->deliverables = $project->deliverables->map(fn ($item) => [
            'deliverable_name' => $item->deliverable_name,
            'description' => $item->description,
            'due_date' => $item->due_date,
            'owner' => $item->owner,
            'acceptance_criteria' => $item->acceptance_criteria,
            'status' => $item->status,
        ])->toArray();

        $this->projectMaterials = $project->projectMaterials->map(fn ($item) => [
            'material_id' => $item->material_id,
            'item_code' => $item->item_code,
            'description' => $item->description,
            'unit' => $item->unit,
            'quantity' => $item->quantity,
            'unit_cost' => $item->unit_cost,
            'line_total' => $item->line_total,
            'source' => $item->source,
            'status' => $item->status,
        ])->toArray();

        $this->budgetLines = $project->budgetLines->map(fn ($item) => [
            'budget_category' => $item->budget_category,
            'description' => $item->description,
            'estimated_amount' => $item->estimated_amount,
            'actual_amount' => $item->actual_amount,
            'variance_amount' => $item->variance_amount,
        ])->toArray();

        if (count($this->projectPhases) === 0) {
            $this->projectPhases[] = $this->blankProjectPhase();
        }

        if (count($this->wbsItems) === 0) {
            $this->wbsItems[] = $this->blankWbsItem();
        }

        if (count($this->deliverables) === 0) {
            $this->deliverables[] = $this->blankDeliverable();
        }

        if (count($this->projectMaterials) === 0) {
            $this->projectMaterials[] = $this->blankProjectMaterial();
        }

        if (count($this->budgetLines) === 0) {
            $this->budgetLines[] = $this->blankBudgetLine();
        }

        $this->calculateTimeline();
        $this->calculateFinancials();

        session()->flash('info', 'Project loaded for editing.');
    }

    public function save(): void
    {
        $this->calculateTimeline();
        $this->calculateFinancials();

        $this->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'project_type' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'contract_amount' => ['nullable', 'numeric', 'min:0'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string'],
            'priority' => ['required', 'string'],

            'projectPhases' => ['nullable', 'array'],
            'projectPhases.*.phase_name' => ['nullable', 'string'],
            'projectPhases.*.budget_amount' => ['nullable', 'numeric', 'min:0'],
            'projectPhases.*.actual_cost' => ['nullable', 'numeric', 'min:0'],
            'projectPhases.*.progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'wbsItems' => ['nullable', 'array'],
            'wbsItems.*.title' => ['nullable', 'string'],
            'wbsItems.*.budget_amount' => ['nullable', 'numeric', 'min:0'],
            'wbsItems.*.progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'deliverables' => ['nullable', 'array'],
            'deliverables.*.deliverable_name' => ['nullable', 'string'],

            'projectMaterials' => ['nullable', 'array'],
            'projectMaterials.*.description' => ['nullable', 'string'],
            'projectMaterials.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'projectMaterials.*.unit_cost' => ['nullable', 'numeric', 'min:0'],

            'budgetLines' => ['nullable', 'array'],
            'budgetLines.*.budget_category' => ['nullable', 'string'],
            'budgetLines.*.estimated_amount' => ['nullable', 'numeric', 'min:0'],
            'budgetLines.*.actual_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $payload = [
            'project_code' => $this->project_code ?: $this->generateProjectCode(),
            'project_name' => $this->project_name,
            'project_type' => $this->project_type,
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_days' => $this->duration_days,
            'contract_amount' => $this->contract_amount,
            'estimated_cost' => $this->estimated_cost,
            'budget_amount' => $this->budget_amount,
            'expected_profit' => $this->expected_profit,
            'profit_margin' => $this->profit_margin,
            'status' => $this->status,
            'priority' => $this->priority,
            'scope_summary' => $this->scope_summary,
            'objectives' => $this->objectives,
            'location' => $this->location,
            'notes' => $this->notes,
            'project_manager' => $this->project_manager,
            'site_engineer' => $this->site_engineer,
            'client_representative' => $this->client_representative,
        ];

        $wasEditing = $this->isEditing;

        DB::transaction(function () use ($payload) {
            if ($this->isEditing && $this->editingProjectId) {
                $project = Project::findOrFail($this->editingProjectId);

                $project->update($payload);

                $project->phases()->delete();
                $project->wbsItems()->delete();
                $project->deliverables()->delete();
                $project->projectMaterials()->delete();
                $project->budgetLines()->delete();
            } else {
                $project = Project::create($payload);
            }

            foreach ($this->projectPhases as $item) {
                if (blank($item['phase_name'] ?? null)) {
                    continue;
                }

                $project->phases()->create($item);
            }

            foreach ($this->wbsItems as $item) {
                if (blank($item['title'] ?? null)) {
                    continue;
                }

                $project->wbsItems()->create($item);
            }

            foreach ($this->deliverables as $item) {
                if (blank($item['deliverable_name'] ?? null)) {
                    continue;
                }

                $project->deliverables()->create($item);
            }

            foreach ($this->projectMaterials as $item) {
                if (blank($item['description'] ?? null)) {
                    continue;
                }

                $project->projectMaterials()->create($item);
            }

            foreach ($this->budgetLines as $item) {
                if (blank($item['budget_category'] ?? null)) {
                    continue;
                }

                $project->budgetLines()->create($item);
            }
        });

        $this->clearForm();

        session()->flash(
            'success',
            $wasEditing ? 'Project updated successfully.' : 'Project created successfully.'
        );
    }

    public function clearForm(): void
    {
        $this->reset();

        $this->search = '';
        $this->start_date = now()->toDateString();
        $this->end_date = now()->addDays(30)->toDateString();

        $this->status = 'draft';
        $this->priority = 'normal';

        $this->contract_amount = 0;
        $this->estimated_cost = 0;
        $this->budget_amount = 0;
        $this->expected_profit = 0;
        $this->profit_margin = 0;

        $this->projectPhases = [$this->blankProjectPhase()];
        $this->wbsItems = [$this->blankWbsItem()];
        $this->deliverables = [$this->blankDeliverable()];
        $this->projectMaterials = [$this->blankProjectMaterial()];
        $this->budgetLines = [$this->blankBudgetLine()];

        $this->calculateTimeline();
        $this->calculateFinancials();
    }

    public function render()
    {
        $companies = Company::where('active', true)->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $materials = Material::where('active', true)->orderBy('name')->get();

        $projects = Project::with(['company', 'client'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('project_code', 'like', "%{$this->search}%")
                        ->orWhere('project_name', 'like', "%{$this->search}%")
                        ->orWhere('project_type', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%")
                        ->orWhere('priority', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.projects.project-centre-page', compact(
            'companies',
            'clients',
            'materials',
            'projects'
        ))->layout('layouts.erp');
    }
}