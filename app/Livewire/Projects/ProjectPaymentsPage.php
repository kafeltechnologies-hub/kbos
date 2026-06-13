<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectPayment;
use Livewire\Component;
use App\Models\Material;

class ProjectPaymentsPage extends Component
{
    public string $search = '';

    public string $voucher_type = 'payment_voucher';
    public ?int $project_id = null;
    public $amount_paid = 0;

    /*
    |--------------------------------------------------------------------------
    | Project Financial Summary
    |--------------------------------------------------------------------------
    | Contract Value and Gross Amount come from projects.contract_amount.
    | Project Cost means total payments already made to that selected project.
    */
    public float|int|string $project_value = 0;
    public float|int|string $gross_amount = 0;
    public float|int|string $project_cost = 0;
    public float|int|string $outstanding_balance = 0;

    /*
    |--------------------------------------------------------------------------
    | Current Voucher Amount
    |--------------------------------------------------------------------------
    */

    public bool $vat_applicable = false;

    public float|int|string $vat_amount = 0;
    public float|int|string $getfund_amount = 0;
    public float|int|string $nhil_amount = 0;
    public float|int|string $net_amount = 0;

    /*
    |--------------------------------------------------------------------------
    | Voucher Details
    |--------------------------------------------------------------------------
    */
    public ?string $payment_date = null;
    public ?string $payment_method = null;
    public ?string $payment_narration = null;
    public ?string $remarks = null;

    public ?string $payee_name = null;
    public ?string $payee_type = null;
    public ?string $payee_account = null;
    public ?string $payee_phone = null;
    public ?string $payee_tin = null;

    /*
    |--------------------------------------------------------------------------
    | Payment Method Details
    |--------------------------------------------------------------------------
    */
    public ?string $bank_name = null;
    public ?string $bank_account = null;
    public ?string $cheque_number = null;
    public ?string $momo_number = null;
    public ?string $transaction_reference = null;

    /*
    |--------------------------------------------------------------------------
    | Approval Details
    |--------------------------------------------------------------------------
    */
    public ?string $prepared_by = null;
    public ?string $checked_by = null;
    public ?string $approved_by = null;
    public ?string $payment_purpose = null;

    public array $voucherTypes = [
        'payment_voucher' => 'Payment Voucher',
        'receipt_voucher' => 'Receipt Voucher',
        'journal_voucher' => 'Journal Voucher',
    ];

    public array $payeeTypes = [
        'Client',
        'Supplier',
        'Subcontractor',
        'Staff',
        'Consultant',
        'Government Agency',
        'Other',
    ];

    public array $paymentMethods = [
        'Cash',
        'Bank Transfer',
        'Cheque',
        'Mobile Money',
        'Credit Note',
        'Debit Note',
        'Direct Debit',
        'Standing Order',
    ];

    public function mount(): void
    {
        $this->payment_date = now()->toDateString();
    }

    public function updatedProjectId(): void
    {
        $this->loadProjectFinancials();
        $this->calculateTaxes();
    }

    public function updatedAmountPaid(): void
    {
        $this->calculateTaxes();
    }

    public function updatedVatApplicable(): void
    {
        $this->calculateTaxes();
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New voucher buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Voucher buffer cleared successfully.');
    }

    public function sync(): void
    {
        $this->loadProjectFinancials();

        session()->flash('info', 'Voucher ledger synchronized successfully.');
    }

    public function loadProjectFinancials(): void
    {
        if (! $this->project_id) {
            $this->project_value = 0;
            $this->gross_amount = 0;
            $this->project_cost = 0;
            $this->outstanding_balance = 0;

            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            $this->project_value = 0;
            $this->gross_amount = 0;
            $this->project_cost = 0;
            $this->outstanding_balance = 0;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Contract Amount from Project Table
        |--------------------------------------------------------------------------
        */
        $this->project_value = (float) ($project->contract_amount ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Gross Amount = Project Contract Amount
        |--------------------------------------------------------------------------
        */
        $this->gross_amount = $this->project_value;

        /*
        |--------------------------------------------------------------------------
        | Project Cost = Sum of All Payments Already Made to This Project
        |--------------------------------------------------------------------------
        */
        $this->project_cost = (float) ProjectPayment::where('project_id', $project->id)
            ->sum('amount_paid');

        /*
        |--------------------------------------------------------------------------
        | Outstanding Balance
        |--------------------------------------------------------------------------
        */
        $this->outstanding_balance =
            (float) $this->project_value - (float) $this->project_cost;
    }

    public function calculateTaxes(): void
    {
        $base = (float) $this->amount_paid;

        if ((bool) $this->vat_applicable) {
            $this->vat_amount = round($base * 0.15, 2);
            $this->getfund_amount = round($base * 0.025, 2);
            $this->nhil_amount = round($base * 0.025, 2);
        } else {
            $this->vat_amount = 0;
            $this->getfund_amount = 0;
            $this->nhil_amount = 0;
        }

        $this->net_amount = round(
            $base +
            (float) $this->vat_amount +
            (float) $this->getfund_amount +
            (float) $this->nhil_amount,
            2
        );
    }

    public function generatePaymentCode(): string
    {
        $lastPayment = ProjectPayment::latest('id')->first();
        $nextNumber = $lastPayment ? $lastPayment->id + 1 : 1;

        return 'PAY' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function generateVoucherNumber(): string
    {
        $lastPayment = ProjectPayment::latest('id')->first();
        $nextNumber = $lastPayment ? $lastPayment->id + 1 : 1;

        return 'PV' . date('Y') . '-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function clearForm(): void
    {
        $this->reset([
            'voucher_type',
            'project_id',

            'project_value',
            'gross_amount',
            'project_cost',
            'outstanding_balance',

            'amount_paid',
            'vat_applicable',
            'vat_amount',
            'getfund_amount',
            'nhil_amount',
            'net_amount',

            'payment_method',
            'payment_narration',
            'remarks',

            'payee_name',
            'payee_type',
            'payee_account',
            'payee_phone',
            'payee_tin',

            'bank_name',
            'bank_account',
            'cheque_number',
            'momo_number',
            'transaction_reference',

            'prepared_by',
            'checked_by',
            'approved_by',
            'payment_purpose',
        ]);

        $this->voucher_type = 'payment_voucher';

        $this->project_value = 0;
        $this->gross_amount = 0;
        $this->project_cost = 0;
        $this->outstanding_balance = 0;

        $this->amount_paid = 0;
        $this->vat_applicable = false;

        $this->vat_amount = 0;
        $this->getfund_amount = 0;
        $this->nhil_amount = 0;
        $this->net_amount = 0;

        $this->payment_date = now()->toDateString();
    }

    public function save(): void
    {
        $this->loadProjectFinancials();
        $this->calculateTaxes();

        $this->validate([
            'voucher_type' => ['required', 'string'],
            'project_id' => ['required', 'exists:projects,id'],
            'payee_name' => ['required', 'string', 'max:255'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payment_narration' => ['nullable', 'string', 'max:255'],
        ]);

        ProjectPayment::create([
            'project_id' => $this->project_id,

            'payment_code' => $this->generatePaymentCode(),
            'voucher_number' => $this->generateVoucherNumber(),
            'voucher_type' => $this->voucher_type,

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */
            'gross_amount' => $this->gross_amount,
            'amount_paid' => $this->amount_paid,

            'vat_applicable' => $this->vat_applicable,
            'vat_amount' => $this->vat_amount,
            'getfund_amount' => $this->getfund_amount,
            'nhil_amount' => $this->nhil_amount,

            /*
            |--------------------------------------------------------------------------
            | Old withholding field retained for compatibility
            |--------------------------------------------------------------------------
            */
            'withholding_tax' => 0,

            'net_amount' => $this->net_amount,

            /*
            |--------------------------------------------------------------------------
            | Payment Details
            |--------------------------------------------------------------------------
            */
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'payment_narration' => $this->payment_narration,

            /*
            |--------------------------------------------------------------------------
            | Payee Details
            |--------------------------------------------------------------------------
            */
            'payee_name' => $this->payee_name,
            'payee_type' => $this->payee_type,
            'payee_account' => $this->payee_account,
            'payee_phone' => $this->payee_phone,
            'payee_tin' => $this->payee_tin,

            /*
            |--------------------------------------------------------------------------
            | Payment Method Details
            |--------------------------------------------------------------------------
            */
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'cheque_number' => $this->cheque_number,
            'momo_number' => $this->momo_number,
            'transaction_reference' => $this->transaction_reference,

            /*
            |--------------------------------------------------------------------------
            | Approval Details
            |--------------------------------------------------------------------------
            */
            'prepared_by' => $this->prepared_by,
            'checked_by' => $this->checked_by,
            'approved_by' => $this->approved_by,
            'payment_purpose' => $this->payment_purpose,

            'remarks' => $this->remarks,
        ]);

        $this->clearForm();

        session()->flash('success', 'Voucher posted successfully.');
    }

    public function render()
    {
        $projects = Project::orderBy('project_name')->get();

        $payments = ProjectPayment::with('project')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_code', 'like', "%{$this->search}%")
                        ->orWhere('voucher_number', 'like', "%{$this->search}%")
                        ->orWhere('payee_name', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('transaction_reference', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.projects.project-payments-page', compact(
            'projects',
            'payments'
        ))->layout('layouts.erp');
    }
}