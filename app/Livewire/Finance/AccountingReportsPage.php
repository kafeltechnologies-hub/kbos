<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\GeneralLedger;
use App\Models\InvoiceVoucher;
use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\ReceiptVoucher;
use Illuminate\Support\Facades\Schema;

class AccountingReportsPage extends FinanceBasePage
{
    public string $search = '';

    public string $reportType = 'general_ledger';

    public $fromDate;
    public $toDate;
    public $projectId = '';
    public $accountId = '';

    public string $reportTitle = 'General Ledger';
    public array $reportRows = [];

    public array $summary = [
        'total_debit' => 0,
        'total_credit' => 0,
        'total_receipts' => 0,
        'total_payments' => 0,
        'total_tax' => 0,
        'net_position' => 0,
    ];

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');

        $this->generateReport();
    }

    public function updatedReportType(): void
    {
        $this->generateReport();
    }

    public function updatedFromDate(): void
    {
        $this->generateReport();
    }

    public function updatedToDate(): void
    {
        $this->generateReport();
    }

    public function updatedProjectId(): void
    {
        $this->generateReport();
    }

    public function updatedAccountId(): void
    {
        $this->generateReport();
    }

    public function updatedSearch(): void
    {
        $this->generateReport();
    }

    public function generateReport(): void
    {
        $this->resetReport();

        match ($this->reportType) {
            'project_finance_summary',
            'project_profitability',
            'project_cost_statement',
            'project_payment_report',
            'project_receipt_report',
            'project_wip_report' => $this->generateProjectReport(),

            'vat_report',
            'withholding_tax_report',
            'nhil_getfund_report',
            'paye_report',
            'ssnit_report',
            'tax_payment_summary' => $this->generateTaxReport(),

            'accounts_receivable',
            'accounts_payable',
            'customer_statement',
            'supplier_statement',
            'aging_receivables',
            'aging_payables' => $this->generateReceivablePayableReport(),

            'cash_position',
            'daily_finance_summary',
            'budget_variance',
            'expense_analysis',
            'fixed_asset_report',
            'audit_trail' => $this->generateManagementReport(),

            default => $this->generateAccountingReport(),
        };
    }

    protected function generateAccountingReport(): void
    {
        $this->reportTitle = $this->labelReport($this->reportType ?: 'general_ledger');

        if (! Schema::hasTable('general_ledgers')) {
            return;
        }

        $query = GeneralLedger::query();

        if ($this->search !== '') {
            $query->where(function ($qq) {
                foreach (['reference', 'reference_no', 'account_name', 'account', 'description', 'narration'] as $column) {
                    if (Schema::hasColumn('general_ledgers', $column)) {
                        $qq->orWhere($column, 'ilike', '%' . $this->search . '%');
                    }
                }
            });
        }

        if ($this->accountId && Schema::hasColumn('general_ledgers', 'account_id')) {
            $query->where('account_id', $this->accountId);
        }

        $dateColumn = $this->firstExistingColumn('general_ledgers', [
            'posting_date',
            'entry_date',
            'transaction_date',
            'created_at',
        ]);

        if ($dateColumn) {
            $query->when($this->fromDate, fn ($q) => $q->whereDate($dateColumn, '>=', $this->fromDate));
            $query->when($this->toDate, fn ($q) => $q->whereDate($dateColumn, '<=', $this->toDate));
            $query->orderByDesc($dateColumn);
        } else {
            $query->latest();
        }

        $this->reportRows = $query->take(500)->get()->map(function ($entry) {
            $debit = (float) ($entry->debit ?? $entry->debit_amount ?? 0);
            $credit = (float) ($entry->credit ?? $entry->credit_amount ?? 0);

            return [
                'id' => $entry->id,
                'source' => 'general_ledger',
                'date' => $entry->posting_date ?? $entry->entry_date ?? $entry->transaction_date ?? $entry->created_at ?? null,
                'reference' => $entry->reference ?? $entry->reference_no ?? 'GL-' . $entry->id,
                'project_account' => $entry->account_name ?? $entry->account ?? 'Ledger Account',
                'description' => $entry->description ?? $entry->narration ?? 'Ledger entry',
                'debit' => $debit,
                'credit' => $credit,
                'tax' => 0,
                'balance' => $debit - $credit,
            ];
        })->values()->all();

        $this->calculateSummary();
    }

    protected function generateProjectReport(): void
    {
        $this->reportTitle = $this->labelReport($this->reportType ?: 'project_finance_summary');

        $rows = collect();

        if (Schema::hasTable('payment_vouchers')) {
            $dateColumn = $this->firstExistingColumn('payment_vouchers', ['payment_date', 'voucher_date', 'created_at']);

            $payments = PaymentVoucher::query()
                ->when($this->projectId && Schema::hasColumn('payment_vouchers', 'project_id'), fn ($q) => $q->where('project_id', $this->projectId))
                ->when($dateColumn && $this->fromDate, fn ($q) => $q->whereDate($dateColumn, '>=', $this->fromDate))
                ->when($dateColumn && $this->toDate, fn ($q) => $q->whereDate($dateColumn, '<=', $this->toDate))
                ->get()
                ->map(function ($item) {
                    $amount = (float) ($item->amount_paid ?? $item->gross_amount ?? $item->amount ?? 0);
                    $tax = (float) (($item->vat_amount ?? 0) + ($item->withholding_tax ?? 0) + ($item->wht_amount ?? 0) + ($item->nhil_amount ?? 0) + ($item->getfund_amount ?? 0));

                    return [
                        'id' => $item->id,
                        'source' => 'payment_voucher',
                        'date' => $item->payment_date ?? $item->voucher_date ?? $item->created_at ?? null,
                        'reference' => $item->voucher_no ?? $item->reference ?? $item->reference_no ?? 'PAY-' . $item->id,
                        'project_account' => $item->project->project_name ?? $item->project_name ?? 'Project Payment',
                        'description' => $item->narration ?? $item->description ?? 'Payment voucher',
                        'debit' => 0,
                        'credit' => $amount,
                        'tax' => $tax,
                        'balance' => 0 - $amount,
                    ];
                });

            $rows = $rows->merge($payments);
        }

        if (Schema::hasTable('receipt_vouchers')) {
            $dateColumn = $this->firstExistingColumn('receipt_vouchers', ['date_received', 'receipt_date', 'created_at']);

            $receipts = ReceiptVoucher::query()
                ->when($this->projectId && Schema::hasColumn('receipt_vouchers', 'project_id'), fn ($q) => $q->where('project_id', $this->projectId))
                ->when($dateColumn && $this->fromDate, fn ($q) => $q->whereDate($dateColumn, '>=', $this->fromDate))
                ->when($dateColumn && $this->toDate, fn ($q) => $q->whereDate($dateColumn, '<=', $this->toDate))
                ->get()
                ->map(function ($item) {
                    $amount = (float) ($item->amount_received ?? $item->amount ?? 0);

                    return [
                        'id' => $item->id,
                        'source' => 'receipt_voucher',
                        'date' => $item->date_received ?? $item->receipt_date ?? $item->created_at ?? null,
                        'reference' => $item->receipt_no ?? $item->reference ?? $item->reference_no ?? 'RCT-' . $item->id,
                        'project_account' => $item->project->project_name ?? $item->project_name ?? 'Project Receipt',
                        'description' => $item->narration ?? $item->description ?? 'Receipt voucher',
                        'debit' => $amount,
                        'credit' => 0,
                        'tax' => 0,
                        'balance' => $amount,
                    ];
                });

            $rows = $rows->merge($receipts);
        }

        $this->reportRows = $rows->sortByDesc('date')->values()->all();
        $this->calculateSummary();
    }

    protected function generateTaxReport(): void
    {
        $this->reportTitle = $this->labelReport($this->reportType ?: 'tax_payment_summary');

        $rows = collect();

        if (Schema::hasTable('payment_vouchers')) {
            $rows = PaymentVoucher::query()
                ->when($this->projectId && Schema::hasColumn('payment_vouchers', 'project_id'), fn ($q) => $q->where('project_id', $this->projectId))
                ->get()
                ->map(function ($item) {
                    $tax = (float) (
                        ($item->vat_amount ?? 0)
                        + ($item->withholding_tax ?? 0)
                        + ($item->wht_amount ?? 0)
                        + ($item->nhil_amount ?? 0)
                        + ($item->getfund_amount ?? 0)
                    );

                    $amount = (float) ($item->amount_paid ?? $item->gross_amount ?? $item->amount ?? 0);

                    return [
                        'id' => $item->id,
                        'source' => 'tax_payment',
                        'date' => $item->payment_date ?? $item->created_at ?? null,
                        'reference' => $item->voucher_no ?? $item->reference ?? 'TAX-' . $item->id,
                        'project_account' => $item->project->project_name ?? 'Tax Payment',
                        'description' => $item->narration ?? 'Tax deductions / statutory payment',
                        'debit' => 0,
                        'credit' => $amount,
                        'tax' => $tax,
                        'balance' => $tax,
                    ];
                });
        }

        $this->reportRows = $rows->values()->all();
        $this->calculateSummary();
    }

    protected function generateReceivablePayableReport(): void
    {
        $this->reportTitle = $this->labelReport($this->reportType ?: 'accounts_receivable');

        $rows = collect();

        if (Schema::hasTable('invoice_vouchers')) {
            $dateColumn = $this->firstExistingColumn('invoice_vouchers', ['invoice_date', 'document_date', 'created_at']);

            $rows = InvoiceVoucher::query()
                ->when($this->projectId && Schema::hasColumn('invoice_vouchers', 'project_id'), fn ($q) => $q->where('project_id', $this->projectId))
                ->when($dateColumn && $this->fromDate, fn ($q) => $q->whereDate($dateColumn, '>=', $this->fromDate))
                ->when($dateColumn && $this->toDate, fn ($q) => $q->whereDate($dateColumn, '<=', $this->toDate))
                ->get()
                ->map(function ($item) {
                    $amount = (float) ($item->grand_total ?? $item->total_amount ?? $item->amount ?? 0);
                    $tax = (float) ($item->vat_amount ?? $item->tax_amount ?? 0);

                    return [
                        'id' => $item->id,
                        'source' => 'invoice_voucher',
                        'date' => $item->invoice_date ?? $item->document_date ?? $item->created_at ?? null,
                        'reference' => $item->invoice_no ?? $item->reference ?? $item->reference_no ?? 'INV-' . $item->id,
                        'project_account' => $item->client_name ?? $item->customer_name ?? 'Customer',
                        'description' => $item->description ?? $item->narration ?? 'Invoice',
                        'debit' => $amount,
                        'credit' => 0,
                        'tax' => $tax,
                        'balance' => $amount,
                    ];
                });
        }

        $this->reportRows = $rows->values()->all();
        $this->calculateSummary();
    }

    protected function generateManagementReport(): void
    {
        $this->reportTitle = $this->labelReport($this->reportType ?: 'daily_finance_summary');
        $this->generateProjectReport();
    }

    protected function calculateSummary(): void
    {
        $rows = collect($this->reportRows);

        $totalDebit = $rows->sum(fn ($row) => (float) ($row['debit'] ?? 0));
        $totalCredit = $rows->sum(fn ($row) => (float) ($row['credit'] ?? 0));
        $totalTax = $rows->sum(fn ($row) => (float) ($row['tax'] ?? 0));

        $this->summary = [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'total_receipts' => $totalDebit,
            'total_payments' => $totalCredit,
            'total_tax' => $totalTax,
            'net_position' => $totalDebit - $totalCredit - $totalTax,
        ];
    }

    protected function resetReport(): void
    {
        $this->reportRows = [];

        $this->summary = [
            'total_debit' => 0,
            'total_credit' => 0,
            'total_receipts' => 0,
            'total_payments' => 0,
            'total_tax' => 0,
            'net_position' => 0,
        ];
    }

    public function printReport()
    {
        return redirect()->route('reports.print', [
            'reportType' => $this->reportType,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'projectId' => $this->projectId,
            'accountId' => $this->accountId,
        ]);
    }

    public function printSingleTransaction($transactionId)
    {
        return redirect()->route('reports.transaction.print', [
            'transactionId' => $transactionId,
            'reportType' => $this->reportType,
        ]);
    }

    public function exportPdf()
    {
        return $this->printReport();
    }

    public function exportExcel(): void
    {
        session()->flash('success', 'Excel export will be connected later.');
    }

    public function resetFilters(): void
    {
        $this->reportType = 'general_ledger';
        $this->projectId = '';
        $this->accountId = '';
        $this->search = '';
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');

        $this->generateReport();
    }

    protected function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    protected function labelReport(string $type): string
    {
        return str($type)->replace('_', ' ')->title()->toString();
    }

    public function render()
    {
        $entries = collect();

        if (Schema::hasTable('general_ledgers')) {
            $entries = GeneralLedger::query()
                ->when($this->search, function ($q) {
                    $q->where(function ($qq) {
                        foreach (['reference', 'reference_no', 'account_name', 'account', 'description', 'narration'] as $column) {
                            if (Schema::hasColumn('general_ledgers', $column)) {
                                $qq->orWhere($column, 'ilike', '%' . $this->search . '%');
                            }
                        }
                    });
                })
                ->latest($this->firstExistingColumn('general_ledgers', ['entry_date', 'posting_date', 'transaction_date', 'created_at']) ?? 'id')
                ->take(300)
                ->get();
        }

        $postingSummary = $this->accountSummary();

        $totalDebit = (float) $entries->sum(fn ($e) => (float) ($e->debit ?? $e->debit_amount ?? 0));
        $totalCredit = (float) $entries->sum(fn ($e) => (float) ($e->credit ?? $e->credit_amount ?? 0));

        $projects = Schema::hasTable('projects')
            ? Project::query()
                ->orderBy(Schema::hasColumn('projects', 'project_name') ? 'project_name' : 'id')
                ->get()
            : collect();

        $accounts = Schema::hasTable('chart_of_accounts')
            ? ChartOfAccount::query()
                ->orderBy('account_code')
                ->orderBy('account_name')
                ->get()
            : collect();

        $company = Schema::hasTable('companies')
            ? Company::query()->first()
            : null;

        return view('livewire.finance.accounting-reports-page', [
            'entries' => $entries,
            'postingSummary' => $postingSummary,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,

            'projects' => $projects,
            'accounts' => $accounts,
            'company' => $company,

            'summary' => $this->summary,
            'reportRows' => $this->reportRows,
            'reportTitle' => $this->reportTitle,
            'reportType' => $this->reportType,

            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'projectId' => $this->projectId,
            'accountId' => $this->accountId,

            'financeNavLinks' => $this->financeNavLinks(),
        ])->layout($this->layoutName());
    }
}