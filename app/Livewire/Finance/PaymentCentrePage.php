<?php

namespace App\Livewire\Finance;

use App\Models\ExpenseCategory;
use App\Models\PaymentVoucher;
use App\Models\Project;
use Livewire\Component;
use App\Services\Accounting\AccountingPostingService;

class PaymentCentrePage extends Component
{
    public string $search = '';

    public ?int $editingVoucherId = null;
    public bool $isEditing = false;

    public ?string $voucher_date = null;
    public string $payment_type = 'operating_expense';
    public string $status = 'draft';

    public ?int $project_id = null;
    public ?int $expense_category_id = null;

    public ?string $payee_name = null;
    public ?string $payment_method = null;
    public ?string $reference_no = null;
    public ?string $narration = null;

    public $project_value = 0;
    public $previous_payments = 0;
    public $outstanding_balance = 0;

    public $gross_amount = 0;
    public bool $vat_applicable = false;
    public $vat_amount = 0;
    public $getfund_amount = 0;
    public $nhil_amount = 0;
    public $withholding_tax = 0;
    public $net_payment = 0;

    public ?string $prepared_by = null;
    public ?string $approved_by = null;

    public array $paymentTypes = [
        'project_payment' => 'Project Payment',
        'operating_expense' => 'Operating Expense',
        'payroll_payment' => 'Payroll Payment',
        'statutory_payment' => 'Statutory Payment',
        'asset_purchase' => 'Asset Purchase',
        'loan_repayment' => 'Loan Repayment',
        'director_transaction' => 'Director Transaction',
        'miscellaneous' => 'Miscellaneous',
    ];

    public array $paymentMethods = [
        'Cash',
        'Cheque',
        'Bank Transfer',
        'Mobile Money',
        'POS',
        'Direct Debit',
    ];

    public array $statuses = [
        'draft',
        'prepared',
        'approved',
        'paid',
        'posted',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->voucher_date = now()->toDateString();
    }

    public function updatedPaymentType(): void
    {
        $this->expense_category_id = null;

        if ($this->payment_type !== 'project_payment') {
            $this->project_id = null;
            $this->project_value = 0;
            $this->previous_payments = $this->getPreviousPaymentsByType();
            $this->outstanding_balance = 0;
            $this->gross_amount = 0;
        }

        $this->calculateTotals();
    }

    public function updatedProjectId(): void
    {
        $this->loadProjectFinancials();
        $this->calculateTotals();
    }

    public function updatedExpenseCategoryId(): void
    {
        $this->previous_payments = $this->getPreviousPaymentsByCategory();
    }

    public function updatedGrossAmount(): void
    {
        $this->calculateTotals();
    }

    public function updatedVatApplicable(): void
    {
        $this->calculateTotals();
    }

    public function updatedWithholdingTax(): void
    {
        $this->calculateTotals();
    }

    public function categoryPrefixesForPaymentType(): array
    {
        return match ($this->payment_type) {
            'project_payment' => ['PROJ', 'LABR'],
            'operating_expense' => ['OFF', 'TRANS', 'ICT', 'EQP', 'PROF', 'MKT', 'TRN', 'FIN', 'PROC', 'MGT', 'MISC'],
            'payroll_payment' => ['PAY'],
            'statutory_payment' => ['STAT'],
            'asset_purchase' => ['EQP', 'ICT'],
            'loan_repayment' => ['FIN'],
            'director_transaction' => ['MGT'],
            'miscellaneous' => ['MISC'],
            default => [],
        };
    }

    public function loadProjectFinancials(): void
    {
        if (! $this->project_id) {
            $this->project_value = 0;
            $this->previous_payments = 0;
            $this->outstanding_balance = 0;
            $this->gross_amount = 0;
            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            return;
        }

        $this->project_value = (float) ($project->contract_amount ?? 0);

        $this->previous_payments = (float) PaymentVoucher::where('project_id', $project->id)
            ->where('payment_type', 'project_payment')
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingVoucherId, function ($query) {
                $query->where('id', '!=', $this->editingVoucherId);
            })
            ->sum('gross_amount');

        $this->outstanding_balance =
            (float) $this->project_value - (float) $this->previous_payments;

        $this->gross_amount = $this->project_value;
    }

    public function getPreviousPaymentsByType(): float
    {
        return (float) PaymentVoucher::where('payment_type', $this->payment_type)
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingVoucherId, function ($query) {
                $query->where('id', '!=', $this->editingVoucherId);
            })
            ->sum('gross_amount');
    }

    public function getPreviousPaymentsByCategory(): float
    {
        if (! $this->expense_category_id) {
            return $this->getPreviousPaymentsByType();
        }

        return (float) PaymentVoucher::where('expense_category_id', $this->expense_category_id)
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingVoucherId, function ($query) {
                $query->where('id', '!=', $this->editingVoucherId);
            })
            ->sum('gross_amount');
    }

    public function calculateTotals(): void
    {
        $base = (float) $this->gross_amount;

        if ((bool) $this->vat_applicable) {
            $this->vat_amount = round($base * 0.15, 2);
            $this->getfund_amount = round($base * 0.025, 2);
            $this->nhil_amount = round($base * 0.025, 2);
        } else {
            $this->vat_amount = 0;
            $this->getfund_amount = 0;
            $this->nhil_amount = 0;
        }

        $this->net_payment = round(
            $base
            + (float) $this->vat_amount
            + (float) $this->getfund_amount
            + (float) $this->nhil_amount
            - (float) $this->withholding_tax,
            2
        );
    }

    public function generateVoucherNumber(): string
    {
        $last = PaymentVoucher::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'PV' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New payment voucher buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Payment voucher buffer cleared.');
    }

    public function sync(): void
    {
        if ($this->payment_type === 'project_payment') {
            $this->loadProjectFinancials();
        } else {
            $this->previous_payments = $this->getPreviousPaymentsByCategory();
        }

        session()->flash('info', 'Payment centre synchronized.');
    }

    public function editVoucher(int $voucherId): void
    {
        $voucher = PaymentVoucher::findOrFail($voucherId);

        $this->editingVoucherId = $voucher->id;
        $this->isEditing = true;

        $this->voucher_date = $voucher->voucher_date;
        $this->payment_type = $voucher->payment_type;
        $this->status = $voucher->status;

        $this->project_id = $voucher->project_id;
        $this->expense_category_id = $voucher->expense_category_id;

        $this->payee_name = $voucher->payee_name;
        $this->payment_method = $voucher->payment_method;
        $this->reference_no = $voucher->reference_no;
        $this->narration = $voucher->narration;

        $this->gross_amount = $voucher->gross_amount;
        $this->vat_applicable = (bool) $voucher->vat_applicable;
        $this->vat_amount = $voucher->vat_amount;
        $this->getfund_amount = $voucher->getfund_amount;
        $this->nhil_amount = $voucher->nhil_amount;
        $this->withholding_tax = $voucher->withholding_tax;
        $this->net_payment = $voucher->net_payment;

        $this->prepared_by = $voucher->prepared_by;
        $this->approved_by = $voucher->approved_by;

        if ($this->payment_type === 'project_payment') {
            $this->loadProjectFinancials();

            $this->gross_amount = $voucher->gross_amount;
        } else {
            $this->project_value = 0;
            $this->outstanding_balance = 0;
            $this->previous_payments = $this->getPreviousPaymentsByCategory();
        }

        session()->flash('info', 'Payment voucher loaded for editing.');
    }

    public function approveVoucher(int $voucherId): void
    {
        PaymentVoucher::findOrFail($voucherId)->update([
            'status' => 'approved',
            'approved_by' => $this->approved_by ?: 'System Approver',
        ]);

        session()->flash('success', 'Payment voucher approved.');
    }

    public function markPaid(int $voucherId): void
    {
        $voucher = PaymentVoucher::with(['category.account', 'project'])->findOrFail($voucherId);

        $voucher->update([
            'status' => 'paid',
        ]);

        app(AccountingPostingService::class)->postPayment($voucher);

        session()->flash('success', 'Payment voucher marked as paid and posted to General Ledger.');
    }

    public function cancelVoucher(int $voucherId): void
    {
        PaymentVoucher::findOrFail($voucherId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Payment voucher cancelled.');
    }

    public function clearForm(): void
    {
        $this->reset([
            'editingVoucherId',
            'isEditing',
            'payment_type',
            'status',
            'project_id',
            'expense_category_id',
            'payee_name',
            'payment_method',
            'reference_no',
            'narration',
            'project_value',
            'previous_payments',
            'outstanding_balance',
            'gross_amount',
            'vat_applicable',
            'vat_amount',
            'getfund_amount',
            'nhil_amount',
            'withholding_tax',
            'net_payment',
            'prepared_by',
            'approved_by',
        ]);

        $this->voucher_date = now()->toDateString();
        $this->payment_type = 'operating_expense';
        $this->status = 'draft';

        $this->project_value = 0;
        $this->previous_payments = 0;
        $this->outstanding_balance = 0;

        $this->gross_amount = 0;
        $this->vat_applicable = false;
        $this->vat_amount = 0;
        $this->getfund_amount = 0;
        $this->nhil_amount = 0;
        $this->withholding_tax = 0;
        $this->net_payment = 0;
    }

    public function save(): void
    {
        if ($this->payment_type === 'project_payment') {
            $this->loadProjectFinancials();

            if ($this->gross_amount <= 0) {
                $this->gross_amount = $this->project_value;
            }
        } else {
            $this->previous_payments = $this->getPreviousPaymentsByCategory();
        }

        $this->calculateTotals();

        $rules = [
            'voucher_date' => ['required', 'date'],
            'payment_type' => ['required', 'string'],
            'payee_name' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'string', 'max:255'],
            'gross_amount' => ['required', 'numeric', 'min:0.01'],
            'withholding_tax' => ['nullable', 'numeric', 'min:0'],
            'narration' => ['required', 'string'],
            'status' => ['required', 'string'],
        ];

        if ($this->payment_type === 'project_payment') {
            $rules['project_id'] = ['required', 'exists:projects,id'];
        } else {
            $rules['expense_category_id'] = ['required', 'exists:expense_categories,id'];
        }

        $this->validate($rules);

        $payload = [
            'voucher_date' => $this->voucher_date,
            'payment_type' => $this->payment_type,

            'project_id' => $this->payment_type === 'project_payment'
                ? $this->project_id
                : null,

            'expense_category_id' => $this->payment_type !== 'project_payment'
                ? $this->expense_category_id
                : null,

            'payee_name' => $this->payee_name,
            'payment_method' => $this->payment_method,
            'reference_no' => $this->reference_no,
            'narration' => $this->narration,

            'gross_amount' => $this->gross_amount,
            'vat_applicable' => $this->vat_applicable,
            'vat_amount' => $this->vat_amount,
            'getfund_amount' => $this->getfund_amount,
            'nhil_amount' => $this->nhil_amount,
            'withholding_tax' => $this->withholding_tax,
            'net_payment' => $this->net_payment,

            'status' => $this->status,
            'prepared_by' => $this->prepared_by,
            'approved_by' => $this->approved_by,
        ];

        $wasEditing = $this->isEditing;

        if ($this->isEditing && $this->editingVoucherId) {
            PaymentVoucher::findOrFail($this->editingVoucherId)->update($payload);
        } else {
            PaymentVoucher::create(array_merge($payload, [
                'voucher_number' => $this->generateVoucherNumber(),
            ]));
        }

        $this->clearForm();

        session()->flash(
            'success',
            $wasEditing ? 'Payment voucher updated successfully.' : 'Payment voucher posted successfully.'
        );
    }

    public function render()
    {
        $projects = Project::orderBy('project_name')->get();

        $prefixes = $this->categoryPrefixesForPaymentType();

        $categories = ExpenseCategory::where('active', true)
            ->when(count($prefixes) > 0, function ($query) use ($prefixes) {
                $query->where(function ($q) use ($prefixes) {
                    foreach ($prefixes as $prefix) {
                        $q->orWhere('category_code', 'like', $prefix . '%');
                    }
                });
            })
            ->orderBy('name')
            ->get();

        $vouchers = PaymentVoucher::with(['project', 'category'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('voucher_number', 'like', "%{$this->search}%")
                        ->orWhere('payee_name', 'like', "%{$this->search}%")
                        ->orWhere('payment_type', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('reference_no', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.finance.payment-centre-page', compact(
            'projects',
            'categories',
            'vouchers'
        ))->layout('layouts.erp');
    }
}