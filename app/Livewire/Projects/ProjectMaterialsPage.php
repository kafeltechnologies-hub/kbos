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

    public string $transaction_type = 'receive';
    public ?int $project_id = null;
    public ?int $payment_voucher_id = null;
    public ?int $receipt_voucher_id = null;
    public ?int $from_project_id = null;
    public ?int $to_project_id = null;

    public ?string $transaction_date = null;
    public ?string $reference = null;
    public ?string $remarks = null;
    public string $transaction_status = 'draft';

    public ?string $account_holder_name = null;
    public ?string $account_holder_phone = null;
    public ?string $expected_return_date = null;

    public array $transactionLines = [];

    public array $sourceProjectStock = [];
    public array $borrowedReturnMaterials = [];

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
        'purchase_resale' => 'Purchase Stock for Resale',
        'receive' => 'Receive Stock / GRN',
        'issue_project' => 'Issue to Project',
        'return_project' => 'Return from Project to Store',
        'transfer_project' => 'Transfer Between Projects',
        'issue_sale' => 'Issue for Sale',
        'issue_account' => 'Issue on Account / Borrowed Out',
        'return_account' => 'Return Borrowed Stock',
        'adjustment' => 'Stock Adjustment',
    ];

    public array $statuses = [
        'draft',
        'posted',
        'pending',
        'approved',
        'reversed',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->transaction_date = now()->toDateString();
        $this->transactionLines = [$this->blankTransactionLine()];
    }

    public function updatedTransactionType(): void
    {
        $this->from_project_id = null;
        $this->to_project_id = null;
        $this->project_id = null;
        $this->payment_voucher_id = null;
        $this->receipt_voucher_id = null;
        $this->account_holder_name = null;
        $this->account_holder_phone = null;
        $this->expected_return_date = null;
        $this->sourceProjectStock = [];
        $this->borrowedReturnMaterials = [];
        $this->transactionLines = [$this->blankTransactionLine()];

        if ($this->transaction_type === 'return_account') {
            $this->borrowedReturnMaterials = $this->borrowedStockSummary();
        }
    }

    public function updatedFromProjectId(): void
    {
        if (in_array($this->transaction_type, ['return_project', 'transfer_project'], true)) {
            $this->sourceProjectStock = $this->from_project_id
                ? $this->projectStockSummary((int) $this->from_project_id)
                : [];

            $this->transactionLines = [$this->blankTransactionLine()];
        }
    }

    public function saveCategory(): void
    {
        $this->validate([
            'category_code' => ['required', 'string', 'max:255'],
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
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:material_categories,id'],
            'standard_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
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
            'barcode',
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
            'available_stock' => 0,
        ];
    }

    public function addTransactionLine(): void
    {
        $this->transactionLines[] = $this->blankTransactionLine();
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

        $available = $this->availableForTransaction((int) $materialId);

        $this->transactionLines[$index]['material_code'] = $material->material_code;
        $this->transactionLines[$index]['description'] = $material->description ?: $material->name;
        $this->transactionLines[$index]['unit'] = $material->unit;
        $this->transactionLines[$index]['unit_cost'] = $material->standard_price ?? 0;
        $this->transactionLines[$index]['available_stock'] = $available;

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

    public function generateTransactionNo(): string
    {
        $prefix = match ($this->transaction_type) {
            'purchase_resale' => 'PRS',
            'receive' => 'GRN',
            'issue_project', 'issue_sale', 'issue_account' => 'MIV',
            'return_project', 'return_account' => 'RTN',
            'transfer_project' => 'TRF',
            'adjustment' => 'ADJ',
            default => 'MTX',
        };

        $last = MaterialTransaction::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function saveTransaction(): void
    {
        $this->calculateLines();

        $this->validate([
            'transaction_type' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
            'transaction_status' => ['required', 'string'],
            'transactionLines' => ['required', 'array', 'min:1'],
            'transactionLines.*.material_id' => ['required', 'exists:materials,id'],
            'transactionLines.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($this->transaction_type === 'issue_project' && ! $this->project_id) {
            $this->addError('project_id', 'Select the project receiving the materials.');
            return;
        }

        if ($this->transaction_type === 'return_project' && ! $this->from_project_id) {
            $this->addError('from_project_id', 'Select the project returning the materials.');
            return;
        }

        if ($this->transaction_type === 'transfer_project') {
            if (! $this->from_project_id || ! $this->to_project_id) {
                $this->addError('from_project_id', 'Select both source and destination projects.');
                return;
            }

            if ((int) $this->from_project_id === (int) $this->to_project_id) {
                $this->addError('to_project_id', 'Source and destination project cannot be the same.');
                return;
            }
        }

        if ($this->transaction_type === 'issue_account' && ! $this->account_holder_name) {
            $this->addError('account_holder_name', 'Enter the borrower or account holder name.');
            return;
        }

        if ($this->transaction_type === 'return_account' && ! $this->account_holder_name) {
            $this->addError('account_holder_name', 'Select or enter the borrower returning the stock.');
            return;
        }

        if ($this->transaction_type === 'issue_sale' && ! $this->receipt_voucher_id) {
            $this->addError('receipt_voucher_id', 'Select the finance receipt voucher for this sale.');
            return;
        }

        $this->checkDuplicateMaterialsInTransaction();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->checkStockAvailability();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        DB::transaction(function () {
            if ($this->isEditingTransaction && $this->editingTransactionId) {
                $transaction = MaterialTransaction::findOrFail($this->editingTransactionId);

                if ($transaction->status === 'approved') {
                    session()->flash('info', 'Approved transactions cannot be edited.');
                    return;
                }

                $transaction->update($this->transactionPayload());
                $transaction->lines()->delete();
            } else {
                $transaction = MaterialTransaction::create(array_merge(
                    ['transaction_no' => $this->generateTransactionNo()],
                    $this->transactionPayload()
                ));
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

            if ($this->transaction_type === 'issue_project' && $this->transporter_name) {
                $transaction->waybill()->updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    [
                        'waybill_no' => $transaction->waybill?->waybill_no ?? $this->generateWaybillNo(),
                        'transporter_name' => $this->transporter_name,
                        'driver_name' => $this->driver_name,
                        'driver_phone' => $this->driver_phone,
                        'vehicle_number' => $this->vehicle_number,
                        'delivery_location' => $this->delivery_location,
                        'loaded_by' => $this->loaded_by,
                        'received_by' => $this->received_by,
                        'status' => 'issued',
                    ]
                );
            }
        });

        $this->clearTransactionForm();

        session()->flash('success', 'Stock transaction saved successfully. Approve it to post finance entries.');
    }

    private function transactionPayload(): array
    {
        return [
            'transaction_type' => $this->transaction_type,
            'project_id' => $this->transaction_type === 'issue_project' ? $this->project_id : null,
            'payment_voucher_id' => $this->payment_voucher_id,
            'receipt_voucher_id' => $this->receipt_voucher_id,
            'from_project_id' => $this->from_project_id,
            'to_project_id' => $this->to_project_id,
            'account_holder_name' => $this->account_holder_name,
            'account_holder_phone' => $this->account_holder_phone,
            'expected_return_date' => $this->expected_return_date,
            'transaction_date' => $this->transaction_date,
            'reference' => $this->reference,
            'remarks' => $this->remarks,
            'status' => $this->transaction_status,
        ];
    }

    private function checkDuplicateMaterialsInTransaction(): void
    {
        $materialIds = collect($this->transactionLines)
            ->pluck('material_id')
            ->filter()
            ->map(fn ($id) => (int) $id);

        if ($materialIds->count() !== $materialIds->unique()->count()) {
            $this->addError(
                'transactionLines',
                'The same material cannot appear more than once in one transaction. Combine the quantities into one line.'
            );
        }
    }

    private function checkStockAvailability(): void
    {
        foreach ($this->transactionLines as $line) {
            $materialId = (int) ($line['material_id'] ?? 0);
            $qty = (float) ($line['quantity'] ?? 0);

            if (! $materialId || $qty <= 0) {
                continue;
            }

            if (in_array($this->transaction_type, [
                'issue_project',
                'issue_sale',
                'issue_account',
                'return_project',
                'transfer_project',
                'return_account',
            ], true)) {
                $available = $this->availableForTransaction($materialId);

                if ($this->isEditingTransaction && $this->editingTransactionId) {
                    $existingQty = MaterialTransactionLine::where('transaction_id', $this->editingTransactionId)
                        ->where('material_id', $materialId)
                        ->sum('quantity');

                    $available += (float) $existingQty;
                }

                if ($available <= 0 || $qty > $available) {
                    $material = Material::find($materialId);

                    $this->addError(
                        'transactionLines',
                        'Insufficient stock for ' . ($material?->name ?? 'selected material') .
                        '. Available: ' . number_format($available, 2)
                    );

                    return;
                }
            }
        }
    }

    public function editTransaction(int $id): void
    {
        $transaction = MaterialTransaction::with(['lines.material', 'waybill'])->findOrFail($id);

        if ($transaction->status === 'approved') {
            session()->flash('info', 'Approved transactions cannot be edited.');
            return;
        }

        $this->activeTab = 'transactions';
        $this->isEditingTransaction = true;
        $this->editingTransactionId = $transaction->id;

        $this->transaction_type = $transaction->transaction_type;
        $this->project_id = $transaction->project_id;
        $this->payment_voucher_id = $transaction->payment_voucher_id ?? null;
        $this->receipt_voucher_id = $transaction->receipt_voucher_id ?? null;
        $this->from_project_id = $transaction->from_project_id ?? null;
        $this->to_project_id = $transaction->to_project_id ?? null;
        $this->account_holder_name = $transaction->account_holder_name ?? null;
        $this->account_holder_phone = $transaction->account_holder_phone ?? null;
        $this->expected_return_date = $transaction->expected_return_date
            ? (string) $transaction->expected_return_date
            : null;

        $this->transaction_date = $transaction->transaction_date
            ? $transaction->transaction_date->format('Y-m-d')
            : now()->toDateString();

        $this->reference = $transaction->reference;
        $this->remarks = $transaction->remarks;
        $this->transaction_status = $transaction->status;

        if (in_array($this->transaction_type, ['return_project', 'transfer_project'], true)) {
            $this->sourceProjectStock = $this->from_project_id
                ? $this->projectStockSummary((int) $this->from_project_id)
                : [];
        }

        $this->transactionLines = $transaction->lines->map(function ($line) {
            return [
                'material_id' => $line->material_id,
                'material_code' => $line->material?->material_code ?? '',
                'description' => $line->material?->description ?: $line->material?->name,
                'unit' => $line->material?->unit ?? '',
                'quantity' => $line->quantity,
                'unit_cost' => $line->unit_cost,
                'line_total' => $line->line_total,
                'available_stock' => $this->availableForTransaction((int) $line->material_id),
            ];
        })->toArray();

        if (count($this->transactionLines) === 0) {
            $this->transactionLines = [$this->blankTransactionLine()];
        }

        if ($transaction->waybill) {
            $this->transporter_name = $transaction->waybill->transporter_name;
            $this->driver_name = $transaction->waybill->driver_name;
            $this->driver_phone = $transaction->waybill->driver_phone;
            $this->vehicle_number = $transaction->waybill->vehicle_number;
            $this->delivery_location = $transaction->waybill->delivery_location;
            $this->loaded_by = $transaction->waybill->loaded_by;
            $this->received_by = $transaction->waybill->received_by;
        }
    }

    public function approveTransaction(int $id): void
    {
        $transaction = MaterialTransaction::with(['lines.material'])->findOrFail($id);

        if (! in_array(strtolower($transaction->status), ['draft', 'posted', 'pending'], true)) {
            session()->flash('info', 'Only draft, posted or pending transactions can be approved.');
            return;
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->postFinanceForMaterialTransaction($transaction->fresh(['lines.material']));
        });

        session()->flash('success', 'Transaction approved and finance entries posted successfully.');
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
            session()->flash('info', 'Approved transactions cannot be deleted.');
            return;
        }

        $transaction->lines()->delete();
        $transaction->waybill()?->delete();
        $transaction->delete();

        session()->flash('success', 'Stock transaction deleted successfully.');
    }

    private function postFinanceForMaterialTransaction(MaterialTransaction $transaction): void
    {
        if (! class_exists(\App\Models\GeneralLedger::class)) {
            return;
        }

        $value = (float) $transaction->lines->sum('line_total');

        if ($value <= 0) {
            return;
        }

        $date = $transaction->transaction_date?->format('Y-m-d') ?? now()->toDateString();

        $narration = 'Material transaction ' . $transaction->transaction_no .
            ' - ' . strtoupper(str_replace('_', ' ', $transaction->transaction_type));

        match ($transaction->transaction_type) {
            'receive', 'purchase_resale' => $this->postLedgerPair(
                $transaction,
                $date,
                $narration,
                'Inventory Asset',
                $transaction->payment_voucher_id ? 'Payment Voucher Clearing' : 'Supplier Payable',
                $value
            ),

            'issue_project' => $this->postLedgerPair(
                $transaction,
                $date,
                $narration,
                'Project Material Cost',
                'Inventory Asset',
                $value,
                $transaction->project_id
            ),

            'return_project' => $this->postLedgerPair(
                $transaction,
                $date,
                $narration,
                'Inventory Asset',
                'Project Material Cost',
                $value,
                $transaction->from_project_id
            ),

            'issue_account' => $this->postLedgerPair(
                $transaction,
                $date,
                $narration,
                'Material Receivables',
                'Inventory Asset',
                $value
            ),

            'return_account' => $this->postLedgerPair(
                $transaction,
                $date,
                $narration,
                'Inventory Asset',
                'Material Receivables',
                $value
            ),

            'issue_sale' => $this->postSaleLedger($transaction, $date, $narration, $value),

            'transfer_project' => $this->postProjectTransferLedger($transaction, $date, $narration, $value),

            default => null,
        };
    }

    private function postSaleLedger(MaterialTransaction $transaction, string $date, string $narration, float $value): void
    {
        $this->postLedgerPair(
            $transaction,
            $date,
            $narration . ' - Cost of Sale',
            'Cost of Goods Sold',
            'Inventory Asset',
            $value
        );

        $saleValue = (float) $transaction->lines->sum(function ($line) {
            return (float) ($line->material?->selling_price ?? $line->unit_cost) * (float) $line->quantity;
        });

        if ($saleValue <= 0) {
            $saleValue = $value;
        }

        $this->postLedgerPair(
            $transaction,
            $date,
            $narration . ' - Sales Revenue',
            'Receipt Voucher Clearing',
            'Material Sales Revenue',
            $saleValue
        );
    }

    private function postProjectTransferLedger(MaterialTransaction $transaction, string $date, string $narration, float $value): void
    {
        $this->postLedgerLine(
            $transaction,
            $date,
            $narration . ' - Charge destination project',
            'Project Material Cost',
            $value,
            0,
            $transaction->to_project_id
        );

        $this->postLedgerLine(
            $transaction,
            $date,
            $narration . ' - Reduce source project',
            'Project Material Cost',
            0,
            $value,
            $transaction->from_project_id
        );
    }

    private function postLedgerPair(
        MaterialTransaction $transaction,
        string $date,
        string $narration,
        string $debitAccount,
        string $creditAccount,
        float $amount,
        ?int $projectId = null
    ): void {
        $this->postLedgerLine($transaction, $date, $narration, $debitAccount, $amount, 0, $projectId);
        $this->postLedgerLine($transaction, $date, $narration, $creditAccount, 0, $amount, $projectId);
    }

    private function postLedgerLine(
        MaterialTransaction $transaction,
        string $date,
        string $narration,
        string $accountName,
        float $debit,
        float $credit,
        ?int $projectId = null
    ): void {
        if (! class_exists(\App\Models\GeneralLedger::class)) {
            return;
        }

        if (! Schema::hasTable('general_ledgers')) {
            return;
        }

        $columns = Schema::getColumnListing('general_ledgers');

        $possible = [
            'entry_date' => $date,
            'transaction_date' => $date,
            'date' => $date,
            'account_name' => $accountName,
            'account' => $accountName,
            'description' => $narration,
            'narration' => $narration,
            'debit' => $debit,
            'debit_amount' => $debit,
            'credit' => $credit,
            'credit_amount' => $credit,
            'amount' => $debit > 0 ? $debit : $credit,
            'project_id' => $projectId,
            'source_module' => 'materials',
            'source_type' => 'material_transaction',
            'source_id' => $transaction->id,
            'reference' => $transaction->transaction_no,
            'status' => 'posted',
            'created_by' => auth()->id(),
        ];

        $data = [];

        foreach ($possible as $key => $value) {
            if (in_array($key, $columns, true)) {
                $data[$key] = $value;
            }
        }

        \App\Models\GeneralLedger::create($data);
    }

    public function stockQuantity(int $materialId): float
    {
        $received = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->whereIn('material_transactions.transaction_type', [
                'receive',
                'purchase_resale',
                'return_project',
                'return_account',
                'adjustment',
            ])
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        $issued = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->whereIn('material_transactions.transaction_type', [
                'issue_project',
                'issue_sale',
                'issue_account',
            ])
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        return (float) $received - (float) $issued;
    }

    public function projectMaterialBalance(int $projectId, int $materialId): float
    {
        $issuedToProject = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'issue_project')
            ->where('material_transactions.project_id', $projectId)
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        $transferredIn = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'transfer_project')
            ->where('material_transactions.to_project_id', $projectId)
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        $returnedToStore = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'return_project')
            ->where('material_transactions.from_project_id', $projectId)
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        $transferredOut = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'transfer_project')
            ->where('material_transactions.from_project_id', $projectId)
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        return (float) $issuedToProject
            + (float) $transferredIn
            - (float) $returnedToStore
            - (float) $transferredOut;
    }

    public function projectStockSummary(?int $projectId): array
    {
        if (! $projectId) {
            return [];
        }

        $materialIds = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->where(function ($query) use ($projectId) {
                $query->where(function ($q) use ($projectId) {
                    $q->where('material_transactions.transaction_type', 'issue_project')
                        ->where('material_transactions.project_id', $projectId);
                })
                    ->orWhere(function ($q) use ($projectId) {
                        $q->where('material_transactions.transaction_type', 'transfer_project')
                            ->where('material_transactions.to_project_id', $projectId);
                    })
                    ->orWhere(function ($q) use ($projectId) {
                        $q->where('material_transactions.transaction_type', 'return_project')
                            ->where('material_transactions.from_project_id', $projectId);
                    })
                    ->orWhere(function ($q) use ($projectId) {
                        $q->where('material_transactions.transaction_type', 'transfer_project')
                            ->where('material_transactions.from_project_id', $projectId);
                    });
            })
            ->pluck('material_transaction_lines.material_id')
            ->unique()
            ->values();

        return Material::whereIn('id', $materialIds)
            ->orderBy('name')
            ->get()
            ->map(function ($material) use ($projectId) {
                return [
                    'id' => $material->id,
                    'code' => $material->material_code,
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'balance' => $this->projectMaterialBalance($projectId, $material->id),
                ];
            })
            ->filter(fn ($row) => (float) $row['balance'] > 0)
            ->values()
            ->toArray();
    }

    public function projectsWithStock()
    {
        $projectIds = MaterialTransaction::query()
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('transaction_type', 'issue_project')
                        ->whereNotNull('project_id');
                })
                    ->orWhere(function ($q) {
                        $q->where('transaction_type', 'transfer_project')
                            ->whereNotNull('to_project_id');
                    });
            })
            ->get()
            ->map(fn ($transaction) => $transaction->project_id ?: $transaction->to_project_id)
            ->filter()
            ->unique()
            ->values();

        return Project::whereIn('id', $projectIds)
            ->orderBy('project_name')
            ->get()
            ->filter(fn ($project) => count($this->projectStockSummary($project->id)) > 0)
            ->values();
    }

    public function borrowedStockSummary(): array
    {
        $materialIds = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->whereIn('material_transactions.transaction_type', ['issue_account', 'return_account'])
            ->pluck('material_transaction_lines.material_id')
            ->unique()
            ->values();

        $rows = [];

        foreach ($materialIds as $materialId) {
            $borrowers = MaterialTransaction::query()
                ->where('status', 'approved')
                ->whereIn('transaction_type', ['issue_account', 'return_account'])
                ->whereHas('lines', function ($query) use ($materialId) {
                    $query->where('material_id', $materialId);
                })
                ->select('account_holder_name', 'account_holder_phone')
                ->distinct()
                ->get();

            foreach ($borrowers as $borrower) {
                $name = $borrower->account_holder_name;
                $phone = $borrower->account_holder_phone;

                if (! $name) {
                    continue;
                }

                $issued = MaterialTransactionLine::query()
                    ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
                    ->where('material_transactions.status', 'approved')
                    ->where('material_transactions.transaction_type', 'issue_account')
                    ->where('material_transactions.account_holder_name', $name)
                    ->where('material_transactions.account_holder_phone', $phone)
                    ->where('material_transaction_lines.material_id', $materialId)
                    ->sum('material_transaction_lines.quantity');

                $returned = MaterialTransactionLine::query()
                    ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
                    ->where('material_transactions.status', 'approved')
                    ->where('material_transactions.transaction_type', 'return_account')
                    ->where('material_transactions.account_holder_name', $name)
                    ->where('material_transactions.account_holder_phone', $phone)
                    ->where('material_transaction_lines.material_id', $materialId)
                    ->sum('material_transaction_lines.quantity');

                $balance = (float) $issued - (float) $returned;

                if ($balance <= 0) {
                    continue;
                }

                $material = Material::find($materialId);

                $key = $name . '-' . ($phone ?? '') . '-' . $materialId;

                $rows[$key] = [
                    'borrower_name' => $name,
                    'borrower_phone' => $phone,
                    'material_id' => $material?->id,
                    'material_code' => $material?->material_code,
                    'material_name' => $material?->name,
                    'unit' => $material?->unit,
                    'borrowed_qty' => (float) $issued,
                    'returned_qty' => (float) $returned,
                    'balance' => $balance,
                ];
            }
        }

        return collect($rows)
            ->sortBy([
                ['borrower_name', 'asc'],
                ['material_name', 'asc'],
            ])
            ->values()
            ->toArray();
    }

    public function availableForTransaction(int $materialId): float
    {
        if (in_array($this->transaction_type, ['return_project', 'transfer_project'], true)) {
            return $this->from_project_id
                ? $this->projectMaterialBalance((int) $this->from_project_id, $materialId)
                : 0;
        }

        if ($this->transaction_type === 'return_account') {
            return collect($this->borrowedStockSummary())
                ->where('material_id', $materialId)
                ->sum('balance');
        }

        return $this->stockQuantity($materialId);
    }

    public function materialsForCurrentTransaction()
    {
        if (in_array($this->transaction_type, ['return_project', 'transfer_project'], true)) {
            if (! $this->from_project_id) {
                return collect();
            }

            $materialIds = collect($this->projectStockSummary((int) $this->from_project_id))
                ->pluck('id')
                ->filter()
                ->unique()
                ->values();

            return Material::whereIn('id', $materialIds)
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        if ($this->transaction_type === 'return_account') {
            $materialIds = collect($this->borrowedStockSummary())
                ->pluck('material_id')
                ->filter()
                ->unique()
                ->values();

            return Material::whereIn('id', $materialIds)
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        return Material::where('active', true)
            ->orderBy('name')
            ->get();
    }

    public function selectBorrowedStockForReturn(
        string $borrowerName,
        ?string $borrowerPhone,
        int $materialId,
        float $balance
    ): void {
        $material = Material::find($materialId);

        if (! $material) {
            return;
        }

        $this->account_holder_name = $borrowerName;
        $this->account_holder_phone = $borrowerPhone;

        $this->transactionLines = [[
            'material_id' => $material->id,
            'material_code' => $material->material_code,
            'description' => $material->description ?: $material->name,
            'unit' => $material->unit,
            'quantity' => $balance,
            'unit_cost' => $material->standard_price ?? 0,
            'line_total' => round($balance * (float) ($material->standard_price ?? 0), 2),
            'available_stock' => $balance,
        ]];
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

        if ($transaction->waybill) {
            $this->editWaybill($transaction->waybill->id);
            return;
        }

        $this->activeTab = 'waybills';
        $this->waybill_transaction_id = $transaction->id;
        $this->delivery_location = $transaction->project?->location;
    }

    public function editWaybill(int $id): void
    {
        $waybill = MaterialWaybill::findOrFail($id);

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
        ]);

        MaterialWaybill::updateOrCreate(
            ['id' => $this->editingWaybillId],
            [
                'waybill_no' => $this->editingWaybillId
                    ? MaterialWaybill::find($this->editingWaybillId)?->waybill_no
                    : $this->generateWaybillNo(),
                'transaction_id' => $this->waybill_transaction_id,
                'transporter_name' => $this->transporter_name,
                'driver_name' => $this->driver_name,
                'driver_phone' => $this->driver_phone,
                'vehicle_number' => $this->vehicle_number,
                'delivery_location' => $this->delivery_location,
                'loaded_by' => $this->loaded_by,
                'received_by' => $this->received_by,
                'status' => 'issued',
            ]
        );

        $this->clearWaybillForm();

        session()->flash('success', 'Waybill saved successfully.');
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

    public function clearTransactionForm(): void
    {
        $this->reset([
            'isEditingTransaction',
            'editingTransactionId',
            'transaction_type',
            'project_id',
            'payment_voucher_id',
            'receipt_voucher_id',
            'from_project_id',
            'to_project_id',
            'account_holder_name',
            'account_holder_phone',
            'expected_return_date',
            'reference',
            'remarks',
            'transaction_status',
            'transporter_name',
            'driver_name',
            'driver_phone',
            'vehicle_number',
            'delivery_location',
            'loaded_by',
            'received_by',
            'sourceProjectStock',
            'borrowedReturnMaterials',
        ]);

        $this->transaction_type = 'receive';
        $this->transaction_status = 'draft';
        $this->transaction_date = now()->toDateString();
        $this->transactionLines = [$this->blankTransactionLine()];
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

        $allMaterials = Material::where('active', true)->orderBy('name')->get();

        $transactionMaterials = $this->materialsForCurrentTransaction();

        $availableStocks = [];

        foreach ($transactionMaterials as $material) {
            $availableStocks[$material->id] = $this->availableForTransaction($material->id);
        }

        $stockBalances = [];

        foreach ($materials as $material) {
            $stockBalances[$material->id] = $this->stockQuantity($material->id);
        }

        $projects = Project::orderBy('project_name')->get();

        $issuedProjects = $this->projectsWithStock();

        $sourceProjectStock = in_array($this->transaction_type, ['return_project', 'transfer_project'], true)
            && $this->from_project_id
                ? $this->projectStockSummary((int) $this->from_project_id)
                : [];

        $borrowedStock = $this->borrowedStockSummary();

        $paymentVouchers = collect();

        if (class_exists(\App\Models\PaymentVoucher::class)) {
            $paymentVouchers = \App\Models\PaymentVoucher::latest()
                ->take(100)
                ->get();
        }

        $receiptVouchers = collect();

        if (class_exists(\App\Models\ReceiptVoucher::class)) {
            $receiptVouchers = \App\Models\ReceiptVoucher::latest()
                ->take(100)
                ->get();
        }

        $transactions = MaterialTransaction::with([
            'project',
            'fromProject',
            'toProject',
            'paymentVoucher',
            'receiptVoucher',
            'lines.material',
            'waybill',
            'approvedBy',
        ])
            ->when($this->transactionSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('transaction_no', 'like', "%{$this->transactionSearch}%")
                        ->orWhere('transaction_type', 'like', "%{$this->transactionSearch}%")
                        ->orWhere('reference', 'like', "%{$this->transactionSearch}%")
                        ->orWhere('status', 'like', "%{$this->transactionSearch}%")
                        ->orWhere('account_holder_name', 'like', "%{$this->transactionSearch}%")
                        ->orWhereHas('project', function ($projectQuery) {
                            $projectQuery->where('project_name', 'like', "%{$this->transactionSearch}%");
                        })
                        ->orWhereHas('fromProject', function ($projectQuery) {
                            $projectQuery->where('project_name', 'like', "%{$this->transactionSearch}%");
                        })
                        ->orWhereHas('toProject', function ($projectQuery) {
                            $projectQuery->where('project_name', 'like', "%{$this->transactionSearch}%");
                        });
                });
            })
            ->latest()
            ->take(150)
            ->get();

        $receiptTransactions = MaterialTransaction::with([
            'project',
            'fromProject',
            'paymentVoucher',
            'lines.material',
        ])
            ->whereIn('transaction_type', [
                'receive',
                'purchase_resale',
                'return_project',
                'return_account',
            ])
            ->when($this->receiptSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('transaction_no', 'like', "%{$this->receiptSearch}%")
                        ->orWhere('reference', 'like', "%{$this->receiptSearch}%")
                        ->orWhere('status', 'like', "%{$this->receiptSearch}%");
                });
            })
            ->latest()
            ->take(150)
            ->get();

        $issueTransactions = MaterialTransaction::with([
            'project',
            'fromProject',
            'toProject',
            'receiptVoucher',
            'lines.material',
            'waybill',
        ])
            ->whereIn('transaction_type', [
                'issue_project',
                'issue_sale',
                'issue_account',
                'transfer_project',
            ])
            ->when($this->issueSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('transaction_no', 'like', "%{$this->issueSearch}%")
                        ->orWhere('reference', 'like', "%{$this->issueSearch}%")
                        ->orWhere('status', 'like', "%{$this->issueSearch}%")
                        ->orWhere('account_holder_name', 'like', "%{$this->issueSearch}%");
                });
            })
            ->latest()
            ->take(150)
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
                            $projectQuery->where('project_name', 'like', "%{$this->waybillSearch}%");
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
            ->take(150)
            ->get();

        return view('livewire.projects.project-materials-page', compact(
            'categories',
            'materials',
            'allMaterials',
            'transactionMaterials',
            'availableStocks',
            'stockBalances',
            'projects',
            'issuedProjects',
            'sourceProjectStock',
            'borrowedStock',
            'paymentVouchers',
            'receiptVouchers',
            'transactions',
            'receiptTransactions',
            'issueTransactions',
            'waybillTransactions'
        ))->layout('layouts.erp');
    }
}