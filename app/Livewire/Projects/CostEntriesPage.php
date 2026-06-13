<?php

namespace App\Livewire\Projects;

use App\Models\Company;
use App\Models\CostCenter;
use App\Models\CostEntry;
use App\Models\Material;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CostEntriesPage extends Component
{
    public string $search = '';

    public bool $isEditing = false;

    public ?int $costEntryId = null;

    public ?int $company_id = null;
    public ?int $project_id = null;
    public ?int $cost_center_id = null;

    public ?string $cost_type = null;
    public ?string $description = null;
    public ?string $cost_date = null;

    public float|int|string $amount = 0;

    public string $status = 'posted';

    public array $materialLines = [];

    public array $otherCostLines = [];

    public array $costTypes = [
        'labour',
        'material',
        'fuel',
        'transport',
        'subcontractor',
        'equipment',
        'accommodation',
        'overhead',
        'miscellaneous',
    ];

    public array $statuses = [
        'draft',
        'posted',
        'approved',
        'reversed',
    ];

    public function mount(): void
    {
        $this->cost_date = now()->toDateString();

        $this->materialLines = [
            $this->blankMaterialLine(),
        ];

        $this->otherCostLines = [
            $this->blankOtherCostLine(),
        ];
    }

    public function blankMaterialLine(): array
    {
        return [
            'material_id' => null,
            'material_code' => '',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_cost' => 0,
            'line_total' => 0,
        ];
    }

    public function blankOtherCostLine(): array
    {
        return [
            'cost_type' => '',
            'description' => '',
            'amount' => 0,
        ];
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New cost entry buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Cost entry buffer cleared successfully.');
    }

    public function sync(): void
    {
        $this->calculateMaterialLines();
        $this->calculateAmountFromLines();

        session()->flash('info', 'Cost ledger synchronized successfully.');
    }

    public function generateCostCode(): string
    {
        $lastCost = CostEntry::latest('id')->first();

        $nextNumber = $lastCost ? $lastCost->id + 1 : 1;

        return 'CST' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function addMaterialLine(): void
    {
        $this->materialLines[] = $this->blankMaterialLine();

        $this->calculateAmountFromLines();
    }

    public function removeMaterialLine(int $index): void
    {
        unset($this->materialLines[$index]);

        $this->materialLines = array_values($this->materialLines);

        if (count($this->materialLines) === 0) {
            $this->materialLines[] = $this->blankMaterialLine();
        }

        $this->calculateMaterialLines();
        $this->calculateAmountFromLines();
    }

    public function addOtherCostLine(): void
    {
        $this->otherCostLines[] = $this->blankOtherCostLine();

        $this->calculateAmountFromLines();
    }

    public function removeOtherCostLine(int $index): void
    {
        unset($this->otherCostLines[$index]);

        $this->otherCostLines = array_values($this->otherCostLines);

        if (count($this->otherCostLines) === 0) {
            $this->otherCostLines[] = $this->blankOtherCostLine();
        }

        $this->calculateAmountFromLines();
    }

    public function materialSelected(int $index): void
    {
        $materialId = $this->materialLines[$index]['material_id'] ?? null;

        if (! $materialId) {
            return;
        }

        $material = Material::find($materialId);

        if (! $material) {
            return;
        }

        $this->materialLines[$index]['material_code'] =
            $material->material_code ?? '';

        $this->materialLines[$index]['description'] =
            $material->description ?: $material->name;

        $this->materialLines[$index]['unit'] =
            $material->unit ?? '';

        $this->materialLines[$index]['unit_cost'] =
            $material->standard_price
            ?? $material->selling_price
            ?? $material->unit_cost
            ?? 0;

        $this->calculateMaterialLines();
        $this->calculateAmountFromLines();
    }

    public function updatedMaterialLines(): void
    {
        $this->calculateMaterialLines();
        $this->calculateAmountFromLines();
    }

    public function updatedOtherCostLines(): void
    {
        $this->calculateAmountFromLines();
    }

    public function calculateMaterialLines(): void
    {
        foreach ($this->materialLines as $index => $line) {
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitCost = (float) ($line['unit_cost'] ?? 0);

            $this->materialLines[$index]['line_total'] =
                round($quantity * $unitCost, 2);
        }
    }

    public function calculateAmountFromLines(): void
    {
        $this->calculateMaterialLines();

        $materialTotal = collect($this->materialLines)
            ->sum(fn ($line) => (float) ($line['line_total'] ?? 0));

        $otherCostTotal = collect($this->otherCostLines)
            ->sum(fn ($line) => (float) ($line['amount'] ?? 0));

        $this->amount = round($materialTotal + $otherCostTotal, 2);

        if ($materialTotal > 0 && $otherCostTotal <= 0) {
            $this->cost_type = 'material';
        } elseif ($materialTotal <= 0 && $otherCostTotal > 0 && filled($this->otherCostLines[0]['cost_type'] ?? null)) {
            $this->cost_type = strtolower((string) $this->otherCostLines[0]['cost_type']);
        } elseif ($materialTotal > 0 && $otherCostTotal > 0) {
            $this->cost_type = 'miscellaneous';
        }
    }

    public function editCostEntry(int $id): void
    {
        $entry = CostEntry::findOrFail($id);

        $this->costEntryId = $entry->id;
        $this->isEditing = true;

        $this->company_id = $entry->company_id;
        $this->project_id = $entry->project_id;
        $this->cost_center_id = $entry->cost_center_id;

        $this->cost_type = $entry->cost_type;
        $this->description = $entry->description;
        $this->amount = $entry->amount;
        $this->cost_date = $entry->cost_date;
        $this->status = $entry->status;

        $this->materialLines = [
            $this->blankMaterialLine(),
        ];

        $this->otherCostLines = [
            [
                'cost_type' => $entry->cost_type,
                'description' => $entry->description,
                'amount' => $entry->amount,
            ],
        ];

        session()->flash('info', 'Cost entry loaded for editing.');
    }

    public function cancelCostEntry(int $id): void
    {
        $entry = CostEntry::findOrFail($id);

        $entry->update([
            'status' => 'reversed',
        ]);

        session()->flash('success', 'Cost entry reversed successfully.');
    }

    public function clearForm(): void
    {
        $this->reset([
            'company_id',
            'project_id',
            'cost_center_id',
            'cost_type',
            'description',
            'amount',
            'status',
            'costEntryId',
            'isEditing',
        ]);

        $this->amount = 0;
        $this->status = 'posted';
        $this->cost_date = now()->toDateString();

        $this->materialLines = [
            $this->blankMaterialLine(),
        ];

        $this->otherCostLines = [
            $this->blankOtherCostLine(),
        ];
    }

    public function save(): void
    {
        $this->calculateAmountFromLines();

        $this->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'cost_type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'cost_date' => ['required', 'date'],
            'status' => ['required', 'string'],

            'materialLines' => ['nullable', 'array'],
            'materialLines.*.material_id' => ['nullable', 'exists:materials,id'],
            'materialLines.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'materialLines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],

            'otherCostLines' => ['nullable', 'array'],
            'otherCostLines.*.cost_type' => ['nullable', 'string'],
            'otherCostLines.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () {
            if ($this->isEditing && $this->costEntryId) {
                $entry = CostEntry::findOrFail($this->costEntryId);

                $entry->update([
                    'company_id' => $this->company_id,
                    'project_id' => $this->project_id,
                    'cost_center_id' => $this->cost_center_id,
                    'cost_type' => $this->cost_type,
                    'description' => $this->description ?: $this->buildDescriptionFromLines(),
                    'amount' => $this->amount,
                    'cost_date' => $this->cost_date,
                    'status' => $this->status,
                ]);
            } else {
                CostEntry::create([
                    'company_id' => $this->company_id,
                    'project_id' => $this->project_id,
                    'cost_center_id' => $this->cost_center_id,
                    'cost_code' => $this->generateCostCode(),
                    'cost_type' => $this->cost_type,
                    'description' => $this->description ?: $this->buildDescriptionFromLines(),
                    'amount' => $this->amount,
                    'cost_date' => $this->cost_date,
                    'status' => $this->status,
                ]);
            }
        });

        $wasEditing = $this->isEditing;

        $this->clearForm();

        session()->flash(
            'success',
            $wasEditing
                ? 'Cost entry updated successfully.'
                : 'Cost entry posted to project ledger successfully.'
        );
    }

    public function buildDescriptionFromLines(): string
    {
        $descriptions = [];

        foreach ($this->materialLines as $line) {
            if (blank($line['description'] ?? null)) {
                continue;
            }

            $descriptions[] =
                'Material: ' .
                ($line['description'] ?? '') .
                ' Qty: ' .
                ($line['quantity'] ?? 0) .
                ' Unit: ' .
                ($line['unit'] ?? '') .
                ' Total: ' .
                number_format((float) ($line['line_total'] ?? 0), 2);
        }

        foreach ($this->otherCostLines as $line) {
            if (blank($line['cost_type'] ?? null) && blank($line['description'] ?? null)) {
                continue;
            }

            $descriptions[] =
                'Cost: ' .
                ($line['cost_type'] ?? '') .
                ' - ' .
                ($line['description'] ?? '') .
                ' Amount: ' .
                number_format((float) ($line['amount'] ?? 0), 2);
        }

        return implode(' | ', $descriptions);
    }

    public function render()
    {
        $companies = Company::where('active', true)
            ->orderBy('name')
            ->get();

        $projects = Project::orderBy('project_name')
            ->get();

        $costCenters = CostCenter::where('active', true)
            ->orderBy('name')
            ->get();

        $materials = Material::orderBy('name')
            ->get();

        $costEntries = CostEntry::with(['company', 'project', 'costCenter'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('cost_code', 'like', "%{$this->search}%")
                        ->orWhere('cost_type', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.projects.cost-entries-page', compact(
            'companies',
            'projects',
            'costCenters',
            'materials',
            'costEntries'
        ))->layout('layouts.erp');
    }
}