<?php

namespace App\Livewire\Finance;

use App\Models\IncomeCategory;
use App\Models\Project;
use App\Models\ReceiptVoucher;
use Livewire\Component;
use App\Services\Accounting\AccountingPostingService;

class ReceiptCentrePage extends Component
{
    public string $search = '';

    public ?int $editingReceiptId = null;
    public bool $isEditing = false;

    public ?string $receipt_date = null;
    public string $receipt_type = 'project_receipt';
    public string $status = 'draft';

    public ?int $project_id = null;
    public ?int $income_category_id = null;

    public ?string $payer_name = null;
    public ?string $receipt_method = null;
    public ?string $reference_no = null;
    public ?string $narration = null;

    public $project_value = 0;
    public $previous_receipts = 0;
    public $outstanding_before_receipt = 0;
    public $amount_received = 0;
    public $balance_after_receipt = 0;

    public string $amount_in_words = '';

    public ?string $prepared_by = null;
    public ?string $checked_by = null;
    public ?string $approved_by = null;
    public ?string $received_by = null;

    public array $receiptTypes = [
        'project_receipt' => 'Project Receipt',
        'consultancy_income' => 'Consultancy Income',
        'ict_income' => 'ICT Income',
        'maintenance_income' => 'Maintenance Income',
        'sale_of_materials' => 'Sale of Materials',
        'asset_disposal' => 'Asset Disposal',
        'loan_received' => 'Loan Received',
        'owner_capital' => 'Owner Capital',
        'miscellaneous_income' => 'Miscellaneous Income',
    ];

    public array $receiptMethods = [
        'Cash',
        'Cheque',
        'Bank Transfer',
        'Mobile Money',
        'POS',
        'Direct Deposit',
    ];

    public array $statuses = [
        'draft',
        'prepared',
        'approved',
        'received',
        'posted',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->receipt_date = now()->toDateString();
    }

    public function updatedReceiptType(): void
    {
        $this->income_category_id = null;

        if ($this->receipt_type !== 'project_receipt') {
            $this->project_id = null;
            $this->project_value = 0;
            $this->outstanding_before_receipt = 0;
            $this->balance_after_receipt = 0;
            $this->previous_receipts = $this->getPreviousReceiptsByType();
        }

        $this->calculateTotals();
    }

    public function updatedProjectId(): void
    {
        $this->loadProjectFinancials();
        $this->calculateTotals();
    }

    public function updatedIncomeCategoryId(): void
    {
        $this->previous_receipts = $this->getPreviousReceiptsByCategory();
    }

    public function updatedAmountReceived(): void
    {
        $this->calculateTotals();
    }

    public function categoryPrefixesForReceiptType(): array
    {
        return match ($this->receipt_type) {
            'project_receipt' => ['REV001', 'REV002', 'REV005', 'REV006'],
            'consultancy_income' => ['REV004'],
            'ict_income' => ['REV003'],
            'maintenance_income' => ['REV005'],
            'sale_of_materials' => ['REV007'],
            'asset_disposal' => ['AST'],
            'loan_received' => ['CAP002', 'CAP003'],
            'owner_capital' => ['CAP001'],
            'miscellaneous_income' => ['REV010', 'MISC'],
            default => [],
        };
    }

    public function loadProjectFinancials(): void
    {
        if (! $this->project_id) {
            $this->project_value = 0;
            $this->previous_receipts = 0;
            $this->outstanding_before_receipt = 0;
            $this->balance_after_receipt = 0;
            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            return;
        }

        $this->project_value = (float) ($project->contract_amount ?? 0);

        $this->previous_receipts = (float) ReceiptVoucher::where('project_id', $project->id)
            ->where('receipt_type', 'project_receipt')
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingReceiptId, function ($query) {
                $query->where('id', '!=', $this->editingReceiptId);
            })
            ->sum('amount_received');

        $this->outstanding_before_receipt =
            (float) $this->project_value - (float) $this->previous_receipts;
    }

    public function getPreviousReceiptsByType(): float
    {
        return (float) ReceiptVoucher::where('receipt_type', $this->receipt_type)
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingReceiptId, function ($query) {
                $query->where('id', '!=', $this->editingReceiptId);
            })
            ->sum('amount_received');
    }

    public function getPreviousReceiptsByCategory(): float
    {
        if (! $this->income_category_id) {
            return $this->getPreviousReceiptsByType();
        }

        return (float) ReceiptVoucher::where('income_category_id', $this->income_category_id)
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingReceiptId, function ($query) {
                $query->where('id', '!=', $this->editingReceiptId);
            })
            ->sum('amount_received');
    }

    public function calculateTotals(): void
    {
        if ($this->receipt_type === 'project_receipt') {
            $this->balance_after_receipt = round(
                (float) $this->outstanding_before_receipt - (float) $this->amount_received,
                2
            );
        } else {
            $this->balance_after_receipt = 0;
        }

        $this->amount_in_words = $this->amountToWords((float) $this->amount_received);
    }

    public function amountToWords(float $amount): string
    {
        if ($amount <= 0) {
            return '';
        }

        if (! class_exists(\NumberFormatter::class)) {
            return number_format($amount, 2) . ' Ghana Cedis Only';
        }

        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

        $cedis = floor($amount);
        $pesewas = round(($amount - $cedis) * 100);

        $words = ucfirst($formatter->format($cedis)) . ' Ghana Cedis';

        if ($pesewas > 0) {
            $words .= ' and ' . ucfirst($formatter->format($pesewas)) . ' Pesewas';
        }

        return $words . ' Only';
    }

    public function generateReceiptNumber(): string
    {
        $last = ReceiptVoucher::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'RV' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();
        session()->flash('info', 'New receipt voucher buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();
        session()->flash('info', 'Receipt voucher buffer cleared.');
    }

    public function sync(): void
    {
        if ($this->receipt_type === 'project_receipt') {
            $this->loadProjectFinancials();
        } else {
            $this->previous_receipts = $this->getPreviousReceiptsByCategory();
        }

        $this->calculateTotals();

        session()->flash('info', 'Receipt centre synchronized.');
    }

    public function editReceipt(int $receiptId): void
    {
        $receipt = ReceiptVoucher::findOrFail($receiptId);

        $this->editingReceiptId = $receipt->id;
        $this->isEditing = true;

        $this->receipt_date = $receipt->receipt_date;
        $this->receipt_type = $receipt->receipt_type;
        $this->status = $receipt->status;

        $this->project_id = $receipt->project_id;
        $this->income_category_id = $receipt->income_category_id;

        $this->payer_name = $receipt->payer_name;
        $this->receipt_method = $receipt->receipt_method;
        $this->reference_no = $receipt->reference_no;
        $this->narration = $receipt->narration;

        $this->project_value = $receipt->project_value;
        $this->previous_receipts = $receipt->previous_receipts;
        $this->outstanding_before_receipt = $receipt->outstanding_before_receipt;
        $this->amount_received = $receipt->amount_received;
        $this->balance_after_receipt = $receipt->balance_after_receipt;
        $this->amount_in_words = $receipt->amount_in_words ?? '';

        $this->prepared_by = $receipt->prepared_by;
        $this->checked_by = $receipt->checked_by;
        $this->approved_by = $receipt->approved_by;
        $this->received_by = $receipt->received_by;

        if ($this->receipt_type === 'project_receipt') {
            $this->loadProjectFinancials();
            $this->amount_received = $receipt->amount_received;
        } else {
            $this->previous_receipts = $this->getPreviousReceiptsByCategory();
        }

        $this->calculateTotals();

        session()->flash('info', 'Receipt voucher loaded for editing.');
    }

    public function approveReceipt(int $receiptId): void
    {
        ReceiptVoucher::findOrFail($receiptId)->update([
            'status' => 'approved',
            'approved_by' => $this->approved_by ?: 'System Approver',
        ]);

        session()->flash('success', 'Receipt voucher approved.');
    }

   public function markReceived(int $receiptId): void
        {
            $receipt = ReceiptVoucher::with(['project', 'category'])->findOrFail($receiptId);

            $receipt->update([
                'status' => 'received',
            ]);

            app(AccountingPostingService::class)->postReceipt($receipt);

            session()->flash('success', 'Receipt voucher marked as received and posted to General Ledger.');
        }

    public function cancelReceipt(int $receiptId): void
    {
        ReceiptVoucher::findOrFail($receiptId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Receipt voucher cancelled.');
    }

    public function clearForm(): void
    {
        $this->reset([
            'editingReceiptId',
            'isEditing',
            'receipt_type',
            'status',
            'project_id',
            'income_category_id',
            'payer_name',
            'receipt_method',
            'reference_no',
            'narration',
            'project_value',
            'previous_receipts',
            'outstanding_before_receipt',
            'amount_received',
            'balance_after_receipt',
            'amount_in_words',
            'prepared_by',
            'checked_by',
            'approved_by',
            'received_by',
        ]);

        $this->receipt_date = now()->toDateString();
        $this->receipt_type = 'project_receipt';
        $this->status = 'draft';

        $this->project_value = 0;
        $this->previous_receipts = 0;
        $this->outstanding_before_receipt = 0;
        $this->amount_received = 0;
        $this->balance_after_receipt = 0;
        $this->amount_in_words = '';
    }

    public function save(): void
    {
        if ($this->receipt_type === 'project_receipt') {
            $this->loadProjectFinancials();
        } else {
            $this->previous_receipts = $this->getPreviousReceiptsByCategory();
            $this->project_value = 0;
            $this->outstanding_before_receipt = 0;
        }

        $this->calculateTotals();

        $rules = [
            'receipt_date' => ['required', 'date'],
            'receipt_type' => ['required', 'string'],
            'payer_name' => ['required', 'string', 'max:255'],
            'receipt_method' => ['required', 'string', 'max:255'],
            'amount_received' => ['required', 'numeric', 'min:0.01'],
            'narration' => ['required', 'string'],
            'status' => ['required', 'string'],
        ];

        if ($this->receipt_type === 'project_receipt') {
            $rules['project_id'] = ['required', 'exists:projects,id'];
        } else {
            $rules['income_category_id'] = ['required', 'exists:income_categories,id'];
        }

        $this->validate($rules);

        $payload = [
            'receipt_date' => $this->receipt_date,
            'receipt_type' => $this->receipt_type,

            'project_id' => $this->receipt_type === 'project_receipt'
                ? $this->project_id
                : null,

            'income_category_id' => $this->receipt_type !== 'project_receipt'
                ? $this->income_category_id
                : null,

            'payer_name' => $this->payer_name,
            'receipt_method' => $this->receipt_method,
            'reference_no' => $this->reference_no,
            'narration' => $this->narration,

            'project_value' => $this->project_value,
            'previous_receipts' => $this->previous_receipts,
            'outstanding_before_receipt' => $this->outstanding_before_receipt,
            'amount_received' => $this->amount_received,
            'balance_after_receipt' => $this->balance_after_receipt,
            'amount_in_words' => $this->amount_in_words,

            'status' => $this->status,
            'prepared_by' => $this->prepared_by,
            'checked_by' => $this->checked_by,
            'approved_by' => $this->approved_by,
            'received_by' => $this->received_by,
        ];

        $wasEditing = $this->isEditing;

        if ($this->isEditing && $this->editingReceiptId) {
            ReceiptVoucher::findOrFail($this->editingReceiptId)->update($payload);
        } else {
            ReceiptVoucher::create(array_merge($payload, [
                'receipt_number' => $this->generateReceiptNumber(),
            ]));
        }

        $this->clearForm();

        session()->flash(
            'success',
            $wasEditing ? 'Receipt voucher updated successfully.' : 'Receipt voucher posted successfully.'
        );
    }

    public function render()
    {
        $projects = Project::orderBy('project_name')->get();

        $prefixes = $this->categoryPrefixesForReceiptType();

        $categories = IncomeCategory::where('active', true)
            ->when(count($prefixes) > 0, function ($query) use ($prefixes) {
                $query->where(function ($q) use ($prefixes) {
                    foreach ($prefixes as $prefix) {
                        $q->orWhere('category_code', 'like', $prefix . '%');
                    }
                });
            })
            ->orderBy('name')
            ->get();

        $receipts = ReceiptVoucher::with(['project', 'category'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('receipt_number', 'like', "%{$this->search}%")
                        ->orWhere('payer_name', 'like', "%{$this->search}%")
                        ->orWhere('receipt_type', 'like', "%{$this->search}%")
                        ->orWhere('receipt_method', 'like', "%{$this->search}%")
                        ->orWhere('reference_no', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.finance.receipt-centre-page', compact(
            'projects',
            'categories',
            'receipts'
        ))->layout('layouts.erp');
    }
}