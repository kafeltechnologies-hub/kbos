<?php

namespace App\Livewire\Projects;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Company;
use App\Models\CostCenter;
use App\Models\Project;
use Livewire\Component;

class ProjectsPage extends Component
{
    
    public string $search = '';
 
    
    

    public ?int $company_id = null;
    public ?int $branch_id = null;
    public ?int $client_id = null;
    public ?int $cost_center_id = null;

    public ?string $project_name = null;
    public ?string $project_type = null;
    public ?string $location = null;
    public ?string $description = null;

    public float|int|string $contract_amount = 0;
    public float|int|string $budget_amount = 0;

    public ?string $start_date = null;
    public ?string $expected_end_date = null;
    public ?string $actual_end_date = null;

    public string $status = 'draft';

    public array $projectTypes = [
        'Electrical Contracting',
        'Rural Electrification',
        'Substation Works',
        'Solar Installation',
        'ICT Project',
        'Maintenance Contract',
        'Consulting',
        'Other',
    ];

    public array $statuses = [
        'draft',
        'awarded',
        'mobilized',
        'in_progress',
        'completed',
        'certified',
        'paid',
        'closed',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->companies = Company::where('active', true)->orderBy('name')->get()->toArray();
        $this->branches = Branch::where('active', true)->orderBy('name')->get()->toArray();
        $this->clients = Client::where('active', true)->orderBy('name')->get()->toArray();
        $this->costCenters = CostCenter::where('active', true)->orderBy('name')->get()->toArray();

        $this->loadProjects();
    }

    public function updatedSearch(): void
    {
        $this->loadProjects();
    }
    
    public function testClick(): void
        {
            session()->flash('info', 'Livewire click is working.');
        }

    public function loadProjects(): void
    {
        $this->projects = Project::with(['company', 'branch', 'client', 'costCenter'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('project_name', 'like', "%{$this->search}%")
                        ->orWhere('project_code', 'like', "%{$this->search}%")
                        ->orWhere('project_type', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'project_code' => $project->project_code,
                    'project_name' => $project->project_name,
                    'project_type' => $project->project_type,
                    'location' => $project->location,
                    'contract_amount' => $project->contract_amount,
                    'budget_amount' => $project->budget_amount,
                    'status' => $project->status,
                    'company' => $project->company?->name,
                    'branch' => $project->branch?->name,
                    'client' => $project->client?->name,
                    'cost_center' => $project->costCenter?->name,
                ];
            })
            ->toArray();
    }

    public function createNew(): void
    {
        $this->clearForm();
        session()->flash('info', 'New project entry buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();
        session()->flash('info', 'Project entry buffer cleared successfully.');
    }

    public function sync(): void
    {
        $this->loadProjects();
        session()->flash('info', 'Project ledger synchronized successfully.');
    }

    public function generateProjectCode(): string
    {
        $lastProject = Project::latest('id')->first();
        $nextNumber = $lastProject ? $lastProject->id + 1 : 1;

        return 'PRJ' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function clearForm(): void
    {
        $this->reset([
            'company_id',
            'branch_id',
            'client_id',
            'cost_center_id',
            'project_name',
            'project_type',
            'location',
            'description',
            'contract_amount',
            'budget_amount',
            'start_date',
            'expected_end_date',
            'actual_end_date',
        ]);

        $this->contract_amount = 0;
        $this->budget_amount = 0;
        $this->status = 'draft';
    }

    public function save(): void
    {
        $this->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'project_name' => ['required', 'string', 'max:255'],
            'contract_amount' => ['nullable', 'numeric', 'min:0'],
            'budget_amount' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        Project::create([
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'client_id' => $this->client_id,
            'cost_center_id' => $this->cost_center_id,
            'project_code' => $this->generateProjectCode(),
            'project_name' => $this->project_name,
            'project_type' => $this->project_type,
            'location' => $this->location,
            'contract_amount' => $this->contract_amount ?: 0,
            'budget_amount' => $this->budget_amount ?: 0,
            'start_date' => $this->start_date,
            'expected_end_date' => $this->expected_end_date,
            'actual_end_date' => $this->actual_end_date,
            'status' => $this->status,
            'description' => $this->description,
        ]);

        $this->clearForm();
        $this->loadProjects();

        session()->flash('success', 'Project profile posted to ledger successfully.');
    }

    public function render()
        {
            $companies = Company::where('active', true)->orderBy('name')->get();
            $branches = Branch::where('active', true)->orderBy('name')->get();
            $clients = Client::where('active', true)->orderBy('name')->get();
            $costCenters = CostCenter::where('active', true)->orderBy('name')->get();

            $projects = Project::with(['company', 'branch', 'client', 'costCenter'])
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('project_name', 'like', "%{$this->search}%")
                            ->orWhere('project_code', 'like', "%{$this->search}%")
                            ->orWhere('project_type', 'like', "%{$this->search}%")
                            ->orWhere('location', 'like', "%{$this->search}%");
                    });
                })
                ->latest()
                ->get();

            return view('livewire.projects.projects-page', compact(
                'companies',
                'branches',
                'clients',
                'costCenters',
                'projects'
            ))->layout('layouts.erp');
        }
}