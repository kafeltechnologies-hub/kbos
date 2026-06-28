<?php

namespace App\Livewire\Finance;

use App\Models\Project;
use App\Services\Finance\FinanceCoordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\WithFileUploads;

class FixedAssetsPage extends FinanceBasePage
{
    use WithFileUploads;

    public string $activeTab = 'register';

    public string $search = '';
    public ?string $filter_category = null;
    public ?string $filter_status = null;
    public ?string $filter_condition = null;
    public ?int $filter_project_id = null;

    public ?int $editingId = null;

    public $asset_photo = null;
    public $asset_document = null;

    public ?string $existing_asset_photo_path = null;
    public ?string $existing_asset_document_path = null;

    public string $asset_code = '';
    public string $asset_name = '';
    public string $asset_category = '';
    public string $serial_number = '';
    public string $model = '';
    public string $manufacturer = '';
    public ?string $purchase_date = null;
    public float|int|string $purchase_cost = 0;
    public float|int|string $current_value = 0;
    public float|int|string $salvage_value = 0;
    public int|string $useful_life_years = 5;
    public string $depreciation_method = 'straight_line';
    public ?int $project_id = null;
    public string $location = '';
    public string $department = '';
    public string $custodian = '';
    public string $condition = 'good';
    public string $status = 'active';
    public string $description = '';

    public ?int $movement_asset_id = null;
    public string $movement_type = 'transfer';
    public ?string $movement_date = null;
    public string $from_location = '';
    public string $to_location = '';
    public string $from_custodian = '';
    public string $to_custodian = '';
    public ?int $from_project_id = null;
    public ?int $to_project_id = null;
    public float|int|string $movement_amount = 0;
    public string $movement_remarks = '';

    public array $categories = [
        'Land',
        'Buildings',
        'Motor Vehicles',
        'Computers & Servers',
        'Office Equipment',
        'Tools & Equipment',
        'Plant & Machinery',
        'Furniture & Fittings',
        'Security Equipment',
        'Power Equipment',
    ];

    public array $conditions = [
        'new' => 'New',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
        'damaged' => 'Damaged',
        'obsolete' => 'Obsolete',
    ];

    public array $statuses = [
        'active' => 'Active',
        'assigned' => 'Assigned',
        'under_maintenance' => 'Under Maintenance',
        'transferred' => 'Transferred',
        'disposed' => 'Disposed',
        'lost' => 'Lost',
        'retired' => 'Retired',
    ];

    public array $movementTypes = [
        'transfer' => 'Transfer',
        'assignment' => 'Assignment',
        'maintenance' => 'Maintenance',
        'revaluation' => 'Revaluation',
        'depreciation' => 'Depreciation',
        'disposal' => 'Disposal',
        'loss' => 'Loss / Write-off',
        'return' => 'Return',
    ];

    public function mount(): void
    {
        $this->purchase_date = now()->toDateString();
        $this->movement_date = now()->toDateString();
    }

    private function hasTables(): bool
    {
        return Schema::hasTable('fixed_assets')
            && Schema::hasTable('fixed_asset_movements')
            && Schema::hasTable('general_ledgers');
    }

    public function rules(): array
    {
        return [
            'asset_name' => ['required', 'string', 'max:255'],
            'asset_category' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'useful_life_years' => ['required', 'integer', 'min:1'],
            'depreciation_method' => ['required', 'string'],
            'status' => ['required', 'string'],
            'condition' => ['required', 'string'],
            'asset_photo' => ['nullable', 'image', 'max:4096'],
            'asset_document' => ['nullable', 'file', 'max:51200'],
        ];
    }

    public function generateAssetCode(): string
    {
        $lastId = Schema::hasTable('fixed_assets')
            ? DB::table('fixed_assets')->max('id')
            : 0;

        return 'FA' . now()->format('Y') . str_pad((string) (($lastId ?? 0) + 1), 5, '0', STR_PAD_LEFT);
    }

    public function saveAsset(): void
    {
        if (! $this->hasTables()) {
            $this->addError('asset_name', 'Missing fixed asset tables or general_ledgers table.');
            return;
        }

        $this->validate();

        $photoPath = $this->existing_asset_photo_path;
        $documentPath = $this->existing_asset_document_path;

        if ($this->asset_photo) {
            $photoPath = $this->asset_photo->store('fixed-assets/photos', 'public');
        }

        if ($this->asset_document) {
            $documentPath = $this->asset_document->store('fixed-assets/documents', 'public');
        }

        $purchaseCost = (float) $this->purchase_cost;
        $currentValue = (float) ($this->current_value ?: $this->purchase_cost);

        $payload = [
            'asset_code' => $this->asset_code ?: $this->generateAssetCode(),
            'asset_name' => $this->asset_name,
            'asset_category' => $this->asset_category,
            'serial_number' => $this->serial_number,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $purchaseCost,
            'current_value' => $currentValue,
            'salvage_value' => (float) $this->salvage_value,
            'useful_life_years' => (int) $this->useful_life_years,
            'depreciation_method' => $this->depreciation_method,
            'project_id' => $this->project_id,
            'location' => $this->location,
            'department' => $this->department,
            'custodian' => $this->custodian,
            'condition' => $this->condition,
            'status' => $this->status,
            'description' => $this->description,
            'asset_photo_path' => $photoPath,
            'asset_document_path' => $documentPath,
            'updated_at' => now(),
        ];

        $assetId = null;

        DB::transaction(function () use ($payload, &$assetId) {
            if ($this->editingId) {
                DB::table('fixed_assets')
                    ->where('id', $this->editingId)
                    ->update($payload);

                $assetId = $this->editingId;
            } else {
                $payload['created_at'] = now();

                $assetId = DB::table('fixed_assets')->insertGetId($payload);
            }
        });

        $asset = DB::table('fixed_assets')->where('id', $assetId)->first();

        if ($asset && (float) $asset->purchase_cost > 0) {
            app(FinanceCoordinator::class)->postFixedAssetPurchase($asset);
        }

        $this->clearAssetForm();

        session()->flash('success', 'Fixed asset saved and synchronized to General Ledger.');
    }

    public function editAsset(int $id): void
    {
        if (! Schema::hasTable('fixed_assets')) {
            return;
        }

        $asset = DB::table('fixed_assets')->where('id', $id)->first();

        if (! $asset) {
            return;
        }

        $this->activeTab = 'register';
        $this->editingId = $asset->id;
        $this->asset_code = $asset->asset_code ?? '';
        $this->asset_name = $asset->asset_name ?? '';
        $this->asset_category = $asset->asset_category ?? '';
        $this->serial_number = $asset->serial_number ?? '';
        $this->model = $asset->model ?? '';
        $this->manufacturer = $asset->manufacturer ?? '';
        $this->purchase_date = $asset->purchase_date ?? now()->toDateString();
        $this->purchase_cost = $asset->purchase_cost ?? 0;
        $this->current_value = $asset->current_value ?? 0;
        $this->salvage_value = $asset->salvage_value ?? 0;
        $this->useful_life_years = $asset->useful_life_years ?? 5;
        $this->depreciation_method = $asset->depreciation_method ?? 'straight_line';
        $this->project_id = $asset->project_id ?? null;
        $this->location = $asset->location ?? '';
        $this->department = $asset->department ?? '';
        $this->custodian = $asset->custodian ?? '';
        $this->condition = $asset->condition ?? 'good';
        $this->status = $asset->status ?? 'active';
        $this->description = $asset->description ?? '';
        $this->existing_asset_photo_path = $asset->asset_photo_path ?? null;
        $this->existing_asset_document_path = $asset->asset_document_path ?? null;
        $this->asset_photo = null;
        $this->asset_document = null;
    }

    public function deleteAsset(int $id): void
    {
        if (! $this->hasTables()) {
            return;
        }

        DB::transaction(function () use ($id) {
            app(FinanceCoordinator::class)->deleteFixedAssetPostings($id);

            DB::table('fixed_asset_movements')
                ->where('fixed_asset_id', $id)
                ->delete();

            DB::table('fixed_assets')
                ->where('id', $id)
                ->delete();
        });

        session()->flash('success', 'Fixed asset and related GL postings deleted.');
    }

    public function clearAssetForm(): void
    {
        $this->editingId = null;
        $this->asset_code = '';
        $this->asset_name = '';
        $this->asset_category = '';
        $this->serial_number = '';
        $this->model = '';
        $this->manufacturer = '';
        $this->purchase_date = now()->toDateString();
        $this->purchase_cost = 0;
        $this->current_value = 0;
        $this->salvage_value = 0;
        $this->useful_life_years = 5;
        $this->depreciation_method = 'straight_line';
        $this->project_id = null;
        $this->location = '';
        $this->department = '';
        $this->custodian = '';
        $this->condition = 'good';
        $this->status = 'active';
        $this->description = '';
        $this->asset_photo = null;
        $this->asset_document = null;
        $this->existing_asset_photo_path = null;
        $this->existing_asset_document_path = null;
    }

    public function selectAssetForMovement(int $id): void
    {
        if (! Schema::hasTable('fixed_assets')) {
            return;
        }

        $asset = DB::table('fixed_assets')->where('id', $id)->first();

        if (! $asset) {
            return;
        }

        $this->activeTab = 'operations';
        $this->movement_asset_id = $asset->id;
        $this->from_location = $asset->location ?? '';
        $this->from_custodian = $asset->custodian ?? '';
        $this->from_project_id = $asset->project_id ?? null;
        $this->movement_date = now()->toDateString();
        $this->movement_amount = 0;
    }

    public function saveMovement(): void
    {
        if (! $this->hasTables()) {
            $this->addError('movement_asset_id', 'Missing fixed asset tables or general_ledgers table.');
            return;
        }

        $this->validate([
            'movement_asset_id' => ['required', 'integer'],
            'movement_type' => ['required', 'string'],
            'movement_date' => ['required', 'date'],
        ]);

        $asset = DB::table('fixed_assets')->where('id', $this->movement_asset_id)->first();

        if (! $asset) {
            $this->addError('movement_asset_id', 'Selected asset not found.');
            return;
        }

        DB::transaction(function () use ($asset) {
            DB::table('fixed_asset_movements')->insert([
                'fixed_asset_id' => $this->movement_asset_id,
                'movement_type' => $this->movement_type,
                'movement_date' => $this->movement_date,
                'from_location' => $this->from_location,
                'to_location' => $this->to_location,
                'from_custodian' => $this->from_custodian,
                'to_custodian' => $this->to_custodian,
                'from_project_id' => $this->from_project_id,
                'to_project_id' => $this->to_project_id,
                'amount' => (float) $this->movement_amount,
                'remarks' => $this->movement_remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $updates = ['updated_at' => now()];

            if (in_array($this->movement_type, ['transfer', 'assignment', 'return'], true)) {
                $updates['location'] = $this->to_location ?: $asset->location;
                $updates['custodian'] = $this->to_custodian ?: $asset->custodian;
                $updates['project_id'] = $this->to_project_id ?: $asset->project_id;
                $updates['status'] = $this->movement_type === 'return' ? 'active' : 'assigned';
            }

            if ($this->movement_type === 'maintenance') {
                $updates['status'] = 'under_maintenance';
            }

            if ($this->movement_type === 'revaluation') {
                $updates['current_value'] = (float) $this->movement_amount;
            }

            if ($this->movement_type === 'depreciation') {
                $updates['current_value'] = max(0, (float) $asset->current_value - (float) $this->movement_amount);
            }

            if ($this->movement_type === 'disposal') {
                $updates['status'] = 'disposed';
                $updates['current_value'] = 0;
            }

            if ($this->movement_type === 'loss') {
                $updates['status'] = 'lost';
                $updates['current_value'] = 0;
            }

            DB::table('fixed_assets')
                ->where('id', $this->movement_asset_id)
                ->update($updates);
        });

        $updatedAsset = DB::table('fixed_assets')
            ->where('id', $this->movement_asset_id)
            ->first();

        if ($updatedAsset && $this->movement_type === 'depreciation' && (float) $this->movement_amount > 0) {
            app(FinanceCoordinator::class)->postAssetDepreciation(
                $updatedAsset,
                (float) $this->movement_amount,
                $this->movement_date
            );
        }

        $this->clearMovementForm();

        session()->flash('success', 'Fixed asset operation saved and synchronized where applicable.');
    }

    public function clearMovementForm(): void
    {
        $this->movement_asset_id = null;
        $this->movement_type = 'transfer';
        $this->movement_date = now()->toDateString();
        $this->from_location = '';
        $this->to_location = '';
        $this->from_custodian = '';
        $this->to_custodian = '';
        $this->from_project_id = null;
        $this->to_project_id = null;
        $this->movement_amount = 0;
        $this->movement_remarks = '';
    }

    public function calculateDepreciation(int $assetId): float
    {
        if (! Schema::hasTable('fixed_assets')) {
            return 0;
        }

        $asset = DB::table('fixed_assets')->where('id', $assetId)->first();

        if (! $asset || (int) $asset->useful_life_years <= 0) {
            return 0;
        }

        return max(
            0,
            ((float) $asset->purchase_cost - (float) $asset->salvage_value) / (int) $asset->useful_life_years
        );
    }

    private function projectOptions()
    {
        return Schema::hasTable('projects')
            ? Project::orderBy('project_name')->get()
            : collect();
    }

    private function assetRows()
    {
        if (! Schema::hasTable('fixed_assets')) {
            return collect();
        }

        return DB::table('fixed_assets')
            ->leftJoin('projects', 'projects.id', '=', 'fixed_assets.project_id')
            ->select('fixed_assets.*', 'projects.project_name', 'projects.project_code')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('asset_code', 'ilike', "%{$this->search}%")
                        ->orWhere('asset_name', 'ilike', "%{$this->search}%")
                        ->orWhere('asset_category', 'ilike', "%{$this->search}%")
                        ->orWhere('serial_number', 'ilike', "%{$this->search}%")
                        ->orWhere('custodian', 'ilike', "%{$this->search}%")
                        ->orWhere('location', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->filter_category, fn ($q) => $q->where('asset_category', $this->filter_category))
            ->when($this->filter_status, fn ($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_condition, fn ($q) => $q->where('condition', $this->filter_condition))
            ->when($this->filter_project_id, fn ($q) => $q->where('project_id', $this->filter_project_id))
            ->orderByDesc('fixed_assets.id')
            ->get()
            ->map(function ($asset) {
                $asset->annual_depreciation = $this->calculateDepreciation($asset->id);
                $asset->net_book_value = (float) $asset->current_value;

                return $asset;
            });
    }

    private function movementRows()
    {
        if (! Schema::hasTable('fixed_asset_movements')) {
            return collect();
        }

        return DB::table('fixed_asset_movements')
            ->join('fixed_assets', 'fixed_assets.id', '=', 'fixed_asset_movements.fixed_asset_id')
            ->leftJoin('projects as from_projects', 'from_projects.id', '=', 'fixed_asset_movements.from_project_id')
            ->leftJoin('projects as to_projects', 'to_projects.id', '=', 'fixed_asset_movements.to_project_id')
            ->select(
                'fixed_asset_movements.*',
                'fixed_assets.asset_code',
                'fixed_assets.asset_name',
                'from_projects.project_name as from_project_name',
                'to_projects.project_name as to_project_name'
            )
            ->orderByDesc('fixed_asset_movements.id')
            ->take(100)
            ->get();
    }

    public function render()
    {
        $assets = $this->assetRows();
        $movements = $this->movementRows();
        $projects = $this->projectOptions();

        return view('livewire.finance.fixed-assets-page', [
            'assets' => $assets,
            'movements' => $movements,
            'projects' => $projects,
            'totalCost' => (float) $assets->sum('purchase_cost'),
            'totalCurrentValue' => (float) $assets->sum('current_value'),
            'totalDepreciation' => max(
                0,
                (float) $assets->sum('purchase_cost') - (float) $assets->sum('current_value')
            ),
            'activeAssets' => $assets->where('status', 'active')->count(),
            'assignedAssets' => $assets->where('status', 'assigned')->count(),
            'disposedAssets' => $assets->where('status', 'disposed')->count(),
            'financeNavLinks' => $this->financeNavLinks(),
            'categories' => $this->categories,
            'conditions' => $this->conditions,
            'statuses' => $this->statuses,
            'movementTypes' => $this->movementTypes,
            'hasFixedAssetTables' => $this->hasTables(),
        ])->layout($this->layoutName());
    }
}