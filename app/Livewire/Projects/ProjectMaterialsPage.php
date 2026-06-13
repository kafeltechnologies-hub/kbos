<?php

namespace App\Livewire\Projects;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialTransaction;
use App\Models\MaterialTransactionLine;
use App\Models\MaterialWaybill;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ProjectMaterialsPage extends Component
{
    public string $search = '';
    public string $activeTab = 'dashboard';
    public string $materialSearch = '';
    public string $transactionSearch = '';
    public string $receiptSearch = '';
    public string $issueSearch = '';
    public string $waybillSearch = '';
    
    public bool $isEditingMaterial = false;
    public bool $isEditingTransaction = false;

    public ?int $editingMaterialId = null;
    public ?int $editingTransactionId = null;

    public ?string $category_code = null;
    public ?string $category_name = null;
    public ?string $category_description = null;

    public ?int $category_id = null;
    public ?string $material_code = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $unit = null;
    public float|int|string $standard_price = 0;
    public float|int|string $selling_price = 0;
    public float|int|string $minimum_stock = 0;
    public float|int|string $maximum_stock = 0;
    public float|int|string $reorder_level = 0;
    public ?string $barcode = null;
    public bool $active = true;
    public string $selectedReportType = 'stock_summary';
    public ?int $reportProjectId = null;
    public ?int $reportMaterialId = null;
    public ?string $reportDateFrom = null;
    public ?string $reportDateTo = null;

    public string $transaction_type = 'receive';
    public ?int $project_id = null;
    public ?string $transaction_date = null;
    public ?string $reference = null;
    public ?string $remarks = null;
    public string $transaction_status = 'draft';
    public array $transactionLines = [];

    public ?int $editingWaybillId = null;
    public ?int $waybill_transaction_id = null;
    public ?string $transporter_name = null;
    public ?string $driver_name = null;
    public ?string $driver_phone = null;
    public ?string $vehicle_number = null;
    public ?string $delivery_location = null;
    public ?string $loaded_by = null;
    public ?string $received_by = null;

    public array $transactionTypes = [
        'receive' => 'Receive Stock / GRN',
        'issue_project' => 'Issue to Project',
        'issue_sale' => 'Issue for Sale',
        'return' => 'Return to Stock',
        'adjustment' => 'Stock Adjustment',
    ];

    public array $statuses = [
        'draft',
        'approved',
        'reversed',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->transaction_date = now()->toDateString();
        $this->transactionLines = [$this->blankTransactionLine()];
    }

    public function saveCategory(): void
    {
        $this->validate([
            'category_code' => ['required', 'string', 'max:255', 'unique:material_categories,category_code'],
            'category_name' => ['required', 'string', 'max:255'],
            'category_description' => ['nullable', 'string'],
        ]);

        MaterialCategory::create([
            'category_code' => strtoupper(trim($this->category_code)),
            'category_name' => $this->category_name,
            'description' => $this->category_description,
            'active' => true,
        ]);

        $this->clearCategoryForm();

        session()->flash('success', 'Material category saved successfully.');
    }

    public function clearCategoryForm(): void
    {
        $this->reset([
            'category_code',
            'category_name',
            'category_description',
        ]);
    }

    public function generateMaterialCode(): string
    {
        $last = Material::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'MAT' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function saveMaterial(): void
    {
        $this->validate([
            'material_code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'standard_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'maximum_stock' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:material_categories,id'],
        ]);

        Material::updateOrCreate(
            ['id' => $this->editingMaterialId],
            [
                'category_id' => $this->category_id,
                'material_code' => $this->material_code ?: $this->generateMaterialCode(),
                'name' => $this->name,
                'description' => $this->description,
                'unit' => $this->unit,
                'standard_price' => $this->standard_price ?: 0,
                'selling_price' => $this->selling_price ?: 0,
                'minimum_stock' => $this->minimum_stock ?: 0,
                'maximum_stock' => $this->maximum_stock ?: 0,
                'reorder_level' => $this->reorder_level ?: 0,
                'barcode' => $this->barcode,
                'active' => $this->active,
            ]
        );

        $this->clearMaterialForm();

        session()->flash('success', 'Material saved successfully.');
    }

    public function editMaterial(int $id): void
    {
        $material = Material::findOrFail($id);

        $this->activeTab = 'materials';
        $this->isEditingMaterial = true;
        $this->editingMaterialId = $material->id;
        $this->category_id = $material->category_id;
        $this->material_code = $material->material_code;
        $this->name = $material->name;
        $this->description = $material->description;
        $this->unit = $material->unit;
        $this->standard_price = $material->standard_price ?? 0;
        $this->selling_price = $material->selling_price ?? 0;
        $this->minimum_stock = $material->minimum_stock ?? 0;
        $this->maximum_stock = $material->maximum_stock ?? 0;
        $this->reorder_level = $material->reorder_level ?? 0;
        $this->barcode = $material->barcode;
        $this->active = (bool) $material->active;

        session()->flash('info', 'Material loaded for editing.');
    }

    public function clearMaterialForm(): void
    {
        $this->reset([
            'isEditingMaterial',
            'editingMaterialId',
            'category_id',
            'material_code',
            'name',
            'description',
            'unit',
            'standard_price',
            'selling_price',
            'minimum_stock',
            'maximum_stock',
            'reorder_level',
            'barcode',
            'active',
        ]);

        $this->standard_price = 0;
        $this->selling_price = 0;
        $this->minimum_stock = 0;
        $this->maximum_stock = 0;
        $this->reorder_level = 0;
        $this->active = true;
    }

    public function blankTransactionLine(): array
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

    public function generateTransactionNo(): string
    {
        $prefix = match ($this->transaction_type) {
            'receive' => 'GRN',
            'issue_project', 'issue_sale' => 'MIV',
            'return' => 'RTN',
            'adjustment' => 'ADJ',
            default => 'MTX',
        };

        $last = MaterialTransaction::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function addTransactionLine(): void
    {
        $this->transactionLines[] = $this->blankTransactionLine();
        $this->calculateLines();
    }

    public function removeTransactionLine(int $index): void
    {
        unset($this->transactionLines[$index]);
        $this->transactionLines = array_values($this->transactionLines);

        if (count($this->transactionLines) === 0) {
            $this->transactionLines[] = $this->blankTransactionLine();
        }

        $this->calculateLines();
    }

    public function materialSelected(int $index): void
    {
        $materialId = $this->transactionLines[$index]['material_id'] ?? null;

        if (! $materialId) {
            return;
        }

        $material = Material::find($materialId);

        if (! $material) {
            return;
        }

        $this->transactionLines[$index]['material_code'] = $material->material_code ?? '';
        $this->transactionLines[$index]['description'] = $material->description ?: $material->name;
        $this->transactionLines[$index]['unit'] = $material->unit ?? '';
        $this->transactionLines[$index]['unit_cost'] = $material->standard_price ?? $material->selling_price ?? 0;

        $this->calculateLines();
    }

    public function updatedTransactionLines(): void
    {
        $this->calculateLines();
    }

    public function calculateLines(): void
    {
        foreach ($this->transactionLines as $index => $line) {
            $qty = (float) ($line['quantity'] ?? 0);
            $cost = (float) ($line['unit_cost'] ?? 0);

            $this->transactionLines[$index]['line_total'] = round($qty * $cost, 2);
        }
    }

    public function saveTransaction(): void
    {
        $this->calculateLines();

        $this->validate([
            'transaction_type' => ['required', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'transaction_date' => ['required', 'date'],
            'transaction_status' => ['required', 'string'],
            'transactionLines' => ['required', 'array', 'min:1'],
            'transactionLines.*.material_id' => ['required', 'exists:materials,id'],
            'transactionLines.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'transactionLines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($this->transaction_type === 'issue_project' && ! $this->project_id) {
            $this->addError('project_id', 'Select a project when issuing materials to a project.');
            return;
        }

        DB::transaction(function () {
            if ($this->isEditingTransaction && $this->editingTransactionId) {
                $transaction = MaterialTransaction::findOrFail($this->editingTransactionId);

                if ($transaction->status === 'approved') {
                    session()->flash('info', 'Approved transactions cannot be edited. Reverse first.');
                    return;
                }

                $transaction->update([
                    'transaction_type' => $this->transaction_type,
                    'project_id' => $this->project_id,
                    'transaction_date' => $this->transaction_date,
                    'reference' => $this->reference,
                    'remarks' => $this->remarks,
                    'status' => $this->transaction_status,
                ]);

                $transaction->lines()->delete();
            } else {
                $transaction = MaterialTransaction::create([
                    'transaction_no' => $this->generateTransactionNo(),
                    'transaction_type' => $this->transaction_type,
                    'project_id' => $this->project_id,
                    'transaction_date' => $this->transaction_date,
                    'reference' => $this->reference,
                    'remarks' => $this->remarks,
                    'status' => $this->transaction_status,
                ]);
            }

            foreach ($this->transactionLines as $line) {
                $qty = abs((float) $line['quantity']);
                $cost = (float) ($line['unit_cost'] ?? 0);

                $transaction->lines()->create([
                    'material_id' => $line['material_id'],
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'line_total' => round($qty * $cost, 2),
                ]);
            }
        });

        $this->clearTransactionForm();
        session()->flash('success', 'Stock transaction saved successfully.');
    }

    public function editTransaction(int $id): void
    {
        $transaction = MaterialTransaction::with(['lines.material', 'waybill'])->findOrFail($id);

        $this->activeTab = 'transactions';
        $this->editingTransactionId = $transaction->id;
        $this->isEditingTransaction = true;
        $this->transaction_type = $transaction->transaction_type;
        $this->project_id = $transaction->project_id;
        $this->transaction_date = $transaction->transaction_date?->format('Y-m-d');
        $this->reference = $transaction->reference;
        $this->remarks = $transaction->remarks;
        $this->transaction_status = $transaction->status;

        $this->transactionLines = $transaction->lines->map(fn ($line) => [
            'material_id' => $line->material_id,
            'material_code' => $line->material?->material_code,
            'description' => $line->material?->description ?: $line->material?->name,
            'unit' => $line->material?->unit,
            'quantity' => $line->quantity,
            'unit_cost' => $line->unit_cost,
            'line_total' => $line->line_total,
        ])->toArray();

        if (count($this->transactionLines) === 0) {
            $this->transactionLines = [$this->blankTransactionLine()];
        }
    }

    public function approveTransaction(int $id): void
    {
        $transaction = MaterialTransaction::findOrFail($id);

        if ($transaction->status !== 'draft') {
            session()->flash('info', 'Only draft transactions can be approved.');
            return;
        }

        $transaction->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Stock transaction approved successfully.');
    }

    public function reverseTransaction(int $id): void
    {
        $transaction = MaterialTransaction::findOrFail($id);

        if ($transaction->status !== 'approved') {
            session()->flash('info', 'Only approved transactions can be reversed.');
            return;
        }

        $transaction->update(['status' => 'reversed']);
        session()->flash('success', 'Stock transaction reversed successfully.');
    }

    public function deleteTransaction(int $id): void
    {
        $transaction = MaterialTransaction::findOrFail($id);

        if ($transaction->status === 'approved') {
            session()->flash('info', 'Approved transactions cannot be deleted. Reverse first.');
            return;
        }

        $transaction->delete();
        session()->flash('success', 'Stock transaction deleted successfully.');
    }

    public function clearTransactionForm(): void
    {
        $this->reset([
            'isEditingTransaction',
            'editingTransactionId',
            'transaction_type',
            'project_id',
            'transaction_date',
            'reference',
            'remarks',
            'transaction_status',
        ]);

        $this->transaction_type = 'receive';
        $this->transaction_status = 'draft';
        $this->transaction_date = now()->toDateString();
        $this->transactionLines = [$this->blankTransactionLine()];
    }

    public function generateWaybillNo(): string
    {
        $last = MaterialWaybill::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'WB' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function createWaybill(int $transactionId): void
        {
            $transaction = MaterialTransaction::with('waybill', 'project')->findOrFail($transactionId);

            if ($transaction->transaction_type !== 'issue_project') {
                session()->flash('info', 'Waybill can only be created for project issue transactions.');
                return;
            }

            if ($transaction->waybill) {
                $this->editWaybill($transaction->waybill->id);
                return;
            }

            $this->activeTab = 'waybills';
            $this->editingWaybillId = null;
            $this->waybill_transaction_id = $transaction->id;
            $this->delivery_location = $transaction->project?->location;

            session()->flash('info', 'Fill the waybill details and click Save Waybill.');
        }

    public function editWaybill(int $waybillId): void
        {
            $waybill = MaterialWaybill::findOrFail($waybillId);

            $this->activeTab = 'waybills';
            $this->editingWaybillId = $waybill->id;
            $this->waybill_transaction_id = $waybill->transaction_id;
            $this->transporter_name = $waybill->transporter_name;
            $this->driver_name = $waybill->driver_name;
            $this->driver_phone = $waybill->driver_phone;
            $this->vehicle_number = $waybill->vehicle_number;
            $this->delivery_location = $waybill->delivery_location;
            $this->loaded_by = $waybill->loaded_by;
            $this->received_by = $waybill->received_by;
        }

    public function saveWaybill(): void
        {
            $this->validate([
                'waybill_transaction_id' => ['required', 'exists:material_transactions,id'],
                'transporter_name' => ['nullable', 'string', 'max:255'],
                'driver_name' => ['nullable', 'string', 'max:255'],
                'driver_phone' => ['nullable', 'string', 'max:255'],
                'vehicle_number' => ['nullable', 'string', 'max:255'],
                'delivery_location' => ['nullable', 'string', 'max:255'],
                'loaded_by' => ['nullable', 'string', 'max:255'],
                'received_by' => ['nullable', 'string', 'max:255'],
            ]);

            $transaction = MaterialTransaction::findOrFail($this->waybill_transaction_id);

            if ($transaction->transaction_type !== 'issue_project') {
                session()->flash('info', 'Waybill can only be created for project issue transactions.');
                return;
            }

            if ($this->editingWaybillId) {
                $waybill = MaterialWaybill::findOrFail($this->editingWaybillId);

                $waybill->update([
                    'transaction_id' => $transaction->id,
                    'transporter_name' => $this->transporter_name,
                    'driver_name' => $this->driver_name,
                    'driver_phone' => $this->driver_phone,
                    'vehicle_number' => $this->vehicle_number,
                    'delivery_location' => $this->delivery_location,
                    'loaded_by' => $this->loaded_by,
                    'received_by' => $this->received_by,
                    'status' => 'issued',
                ]);
            } else {
                MaterialWaybill::create([
                    'waybill_no' => $this->generateWaybillNo(),
                    'transaction_id' => $transaction->id,
                    'transporter_name' => $this->transporter_name,
                    'driver_name' => $this->driver_name,
                    'driver_phone' => $this->driver_phone,
                    'vehicle_number' => $this->vehicle_number,
                    'delivery_location' => $this->delivery_location,
                    'loaded_by' => $this->loaded_by,
                    'received_by' => $this->received_by,
                    'status' => 'issued',
                ]);
            }

            $this->clearWaybillForm();

            $this->activeTab = 'waybills';

            session()->flash('success', 'Waybill saved to register successfully.');
        }

    public function clearWaybillForm(): void
        {
            $this->reset([
                'editingWaybillId',
                'waybill_transaction_id',
                'transporter_name',
                'driver_name',
                'driver_phone',
                'vehicle_number',
                'delivery_location',
                'loaded_by',
                'received_by',
            ]);
        }

    public function stockQuantity(int $materialId): float
            {
                $received = MaterialTransactionLine::query()
                    ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
                    ->where('material_transactions.status', 'approved')
                    ->whereIn('material_transactions.transaction_type', ['receive', 'return', 'adjustment'])
                    ->where('material_transaction_lines.material_id', $materialId)
                    ->sum('material_transaction_lines.quantity');

                $issued = MaterialTransactionLine::query()
                    ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
                    ->where('material_transactions.status', 'approved')
                    ->whereIn('material_transactions.transaction_type', ['issue_project', 'issue_sale'])
                    ->where('material_transaction_lines.material_id', $materialId)
                    ->sum('material_transaction_lines.quantity');

                return (float) $received - (float) $issued;
            }

    
    
    public function render()
        {
            $categories = Schema::hasTable('material_categories')
                ? MaterialCategory::where('active', true)->orderBy('category_name')->get()
                : collect();

            $materials = Material::with('category')
                ->when($this->materialSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', "%{$this->materialSearch}%")
                            ->orWhere('material_code', 'like', "%{$this->materialSearch}%")
                            ->orWhere('description', 'like', "%{$this->materialSearch}%");
                    });
                })
                ->orderBy('name')
                ->get();

            $allMaterials = Material::where('active', true)
                ->orderBy('name')
                ->get();

            $projects = Project::orderBy('project_name')->get();

            $transactions = MaterialTransaction::with([
                'project',
                'lines.material',
                'waybill',
                'approvedBy',
            ])
                ->when($this->transactionSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('transaction_no', 'like', "%{$this->transactionSearch}%")
                            ->orWhere('transaction_type', 'like', "%{$this->transactionSearch}%")
                            ->orWhere('reference', 'like', "%{$this->transactionSearch}%")
                            ->orWhere('status', 'like', "%{$this->transactionSearch}%");
                    });
                })
                ->latest()
                ->take(150)
                ->get();

            $receiptTransactions = MaterialTransaction::with([
                'project',
                'lines.material',
            ])
                ->where('transaction_type', 'receive')
                ->when($this->receiptSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('transaction_no', 'like', "%{$this->receiptSearch}%")
                            ->orWhere('reference', 'like', "%{$this->receiptSearch}%")
                            ->orWhere('status', 'like', "%{$this->receiptSearch}%");
                    });
                })
                ->latest()
                ->get();

            $issueTransactions = MaterialTransaction::with([
                'project',
                'lines.material',
                'waybill',
            ])
                ->whereIn('transaction_type', ['issue_project', 'issue_sale'])
                ->when($this->issueSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('transaction_no', 'like', "%{$this->issueSearch}%")
                            ->orWhere('reference', 'like', "%{$this->issueSearch}%")
                            ->orWhere('status', 'like', "%{$this->issueSearch}%");
                    });
                })
                ->latest()
                ->get();

            $waybillTransactions = MaterialTransaction::with([
                'project',
                'lines.material',
                'waybill',
            ])
                ->where('transaction_type', 'issue_project')
                ->when($this->waybillSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('transaction_no', 'like', "%{$this->waybillSearch}%")
                            ->orWhereHas('project', function ($projectQuery) {
                                $projectQuery->where('project_name', 'like', "%{$this->waybillSearch}%")
                                    ->orWhere('project_code', 'like', "%{$this->waybillSearch}%");
                            })
                            ->orWhereHas('waybill', function ($waybillQuery) {
                                $waybillQuery->where('waybill_no', 'like', "%{$this->waybillSearch}%")
                                    ->orWhere('transporter_name', 'like', "%{$this->waybillSearch}%")
                                    ->orWhere('driver_name', 'like', "%{$this->waybillSearch}%")
                                    ->orWhere('vehicle_number', 'like', "%{$this->waybillSearch}%");
                            });
                    });
                })
                ->latest()
                ->get();

            return view('livewire.projects.project-materials-page', compact(
                'categories',
                'materials',
                'allMaterials',
                'projects',
                'transactions',
                'receiptTransactions',
                'issueTransactions',
                'waybillTransactions'
            ))->layout('layouts.erp');
        }
}