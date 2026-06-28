<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\FinanceDocument;
use App\Models\FinancePayment;
use App\Models\GeneralLedger;
use App\Models\Project;
use App\Services\Finance\FinanceCoordinator;
use Illuminate\Support\Facades\Schema;

class GeneralLedgerPage extends FinanceBasePage
{
    public string $activeTab = 'pending';

    public string $search = '';
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $source_filter = null;
    public ?string $account_name = null;
    public ?int $project_id = null;

    public ?string $posting_date = null;
    public ?string $posting_reference = null;
    public ?string $posting_narration = null;
    public ?string $source_module = null;
    public ?string $source_type = null;
    public ?int $source_id = null;
    public ?int $posting_project_id = null;

    public array $journalLines = [];

    public float $journalDebit = 0;
    public float $journalCredit = 0;

    public function mount(): void
    {
        $this->posting_date = now()->toDateString();
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();

        $this->journalLines = [
            $this->blankLine(),
            $this->blankLine(),
        ];

        $this->calculateTotals();
    }

    public function go(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function blankLine(): array
    {
        return [
            'account_id' => null,
            'account_code' => '',
            'account_name' => '',
            'description' => '',
            'debit' => 0,
            'credit' => 0,
        ];
    }

    public function addLine(): void
    {
        $this->journalLines[] = $this->blankLine();
        $this->calculateTotals();
    }

    public function removeLine(int $index): void
    {
        unset($this->journalLines[$index]);
        $this->journalLines = array_values($this->journalLines);

        if (count($this->journalLines) < 2) {
            $this->journalLines[] = $this->blankLine();
        }

        $this->calculateTotals();
    }

    public function updatedJournalLines(): void
    {
        foreach ($this->journalLines as $index => $line) {
            if (! empty($line['account_id'])) {
                $account = ChartOfAccount::find($line['account_id']);

                if ($account) {
                    $this->journalLines[$index]['account_code'] = $account->account_code;
                    $this->journalLines[$index]['account_name'] = $account->account_name;
                }
            }
        }

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->journalDebit = collect($this->journalLines)->sum(fn ($line) => (float) ($line['debit'] ?? 0));
        $this->journalCredit = collect($this->journalLines)->sum(fn ($line) => (float) ($line['credit'] ?? 0));
    }

    public function loadDocument(int $id): void
    {
        $document = FinanceDocument::findOrFail($id);

        $this->activeTab = 'posting';
        $this->source_module = 'finance_operations';
        $this->source_type = $document->document_type;
        $this->source_id = $document->id;
        $this->posting_date = optional($document->document_date)->toDateString() ?: now()->toDateString();
        $this->posting_reference = $document->document_no;
        $this->posting_project_id = $document->project_id;
        $this->posting_narration = $document->narration ?: ucfirst($document->document_type) . ' - ' . $document->customer_name;

        $this->journalLines = [
            [
                'account_id' => null,
                'account_code' => '',
                'account_name' => '',
                'description' => 'Amount due from ' . $document->customer_name,
                'debit' => (float) $document->grand_total,
                'credit' => 0,
            ],
            [
                'account_id' => null,
                'account_code' => '',
                'account_name' => '',
                'description' => 'Revenue from invoice',
                'debit' => 0,
                'credit' => (float) $document->grand_total,
            ],
        ];

        $this->calculateTotals();
    }

    public function loadPayment(int $id): void
    {
        $payment = FinancePayment::findOrFail($id);

        $this->activeTab = 'posting';
        $this->source_module = 'finance_operations';
        $this->source_type = $payment->payment_type;
        $this->source_id = $payment->id;
        $this->posting_date = optional($payment->payment_date)->toDateString() ?: now()->toDateString();
        $this->posting_reference = $payment->payment_no;
        $this->posting_project_id = $payment->project_id;
        $this->posting_narration = $payment->narration ?: ucfirst($payment->payment_type) . ' - ' . $payment->party_name;

        if ($payment->payment_type === 'receipt') {
            $this->journalLines = [
                [
                    'account_id' => $payment->cash_account_id,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Funds received from ' . $payment->party_name,
                    'debit' => (float) $payment->gross_amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => null,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Receipt allocation',
                    'debit' => 0,
                    'credit' => (float) $payment->gross_amount,
                ],
            ];
        } elseif ($payment->payment_type === 'transfer') {
            $this->journalLines = [
                [
                    'account_id' => $payment->credit_account_id,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Transfer in',
                    'debit' => (float) $payment->gross_amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => $payment->cash_account_id,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Transfer out',
                    'debit' => 0,
                    'credit' => (float) $payment->gross_amount,
                ],
            ];
        } else {
            $this->journalLines = [
                [
                    'account_id' => null,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Payment to ' . $payment->party_name,
                    'debit' => (float) $payment->gross_amount,
                    'credit' => 0,
                ],
                [
                    'account_id' => $payment->cash_account_id,
                    'account_code' => '',
                    'account_name' => '',
                    'description' => 'Funds paid out',
                    'debit' => 0,
                    'credit' => (float) $payment->gross_amount,
                ],
            ];
        }

        $this->updatedJournalLines();
    }

    public function postJournal(): void
    {
        $this->updatedJournalLines();

        $this->validate([
            'posting_date' => ['required', 'date'],
            'posting_reference' => ['required', 'string'],
            'posting_narration' => ['required', 'string'],
            'journalLines' => ['required', 'array', 'min:2'],
            'journalLines.*.account_id' => ['required', 'integer'],
        ]);

        if (round($this->journalDebit, 2) <= 0 || round($this->journalCredit, 2) <= 0) {
            $this->addError('journalLines', 'Debit and credit totals must be greater than zero.');
            return;
        }

        if (round($this->journalDebit, 2) !== round($this->journalCredit, 2)) {
            $this->addError('journalLines', 'Journal is not balanced.');
            return;
        }

        foreach ($this->journalLines as $line) {
            if ((float) ($line['debit'] ?? 0) > 0 && (float) ($line['credit'] ?? 0) > 0) {
                $this->addError('journalLines', 'A line cannot have both debit and credit.');
                return;
            }
        }

        app(FinanceCoordinator::class)->postDoubleEntry([
            'date' => $this->posting_date,
            'reference' => $this->posting_reference,
            'narration' => $this->posting_narration,
            'source_module' => $this->source_module ?: 'general_ledger',
            'source_type' => $this->source_type ?: 'manual_journal',
            'source_id' => $this->source_id,
            'project_id' => $this->posting_project_id,
            'lines' => collect($this->journalLines)
                ->filter(fn ($line) => ((float) ($line['debit'] ?? 0) > 0) || ((float) ($line['credit'] ?? 0) > 0))
                ->map(function ($line) {
                    $account = ChartOfAccount::find($line['account_id']);

                    return [
                        'account_code' => $account?->account_code,
                        'account_name' => $account?->account_name,
                        'description' => $line['description'] ?: $this->posting_narration,
                        'debit' => (float) ($line['debit'] ?? 0),
                        'credit' => (float) ($line['credit'] ?? 0),
                        'project_id' => $this->posting_project_id,
                    ];
                })
                ->values()
                ->all(),
        ]);

        $this->markSourcePosted();

        $this->clearPostingForm();

        session()->flash('success', 'Transaction posted to General Ledger successfully.');
    }

    public function markSourcePosted(): void
    {
        if ($this->source_type === 'invoice' && $this->source_id) {
            FinanceDocument::where('id', $this->source_id)->update(['status' => 'posted']);
        }

        if (in_array($this->source_type, ['receipt', 'payment', 'transfer'], true) && $this->source_id) {
            FinancePayment::where('id', $this->source_id)->update(['status' => 'posted']);
        }
    }

    public function clearPostingForm(): void
    {
        $this->posting_date = now()->toDateString();
        $this->posting_reference = null;
        $this->posting_narration = null;
        $this->source_module = null;
        $this->source_type = null;
        $this->source_id = null;
        $this->posting_project_id = null;

        $this->journalLines = [
            $this->blankLine(),
            $this->blankLine(),
        ];

        $this->calculateTotals();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
        $this->source_filter = null;
        $this->account_name = null;
        $this->project_id = null;
    }

    private function accounts()
    {
        return Schema::hasTable('chart_of_accounts')
            ? ChartOfAccount::orderBy('account_code')->get()
            : collect();
    }

    private function projects()
    {
        return Schema::hasTable('projects')
            ? Project::orderBy('project_name')->get()
            : collect();
    }

    private function pendingDocuments()
    {
        return Schema::hasTable('finance_documents')
            ? FinanceDocument::with('project')
                ->where('document_type', 'invoice')
                ->whereIn('status', ['submitted', 'approved'])
                ->latest()
                ->take(100)
                ->get()
            : collect();
    }

    private function pendingPayments()
    {
        return Schema::hasTable('finance_payments')
            ? FinancePayment::with(['document', 'project'])
                ->whereIn('payment_type', ['receipt', 'payment', 'transfer'])
                ->whereIn('status', ['submitted', 'approved'])
                ->latest()
                ->take(100)
                ->get()
            : collect();
    }

    private function ledgerEntries()
    {
        if (! Schema::hasTable('general_ledgers')) {
            return collect();
        }

        return GeneralLedger::query()
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('reference', 'ilike', "%{$this->search}%")
                    ->orWhere('account_name', 'ilike', "%{$this->search}%")
                    ->orWhere('account_code', 'ilike', "%{$this->search}%")
                    ->orWhere('description', 'ilike', "%{$this->search}%")
                    ->orWhere('narration', 'ilike', "%{$this->search}%");
            }))
            ->when($this->source_filter, fn ($q) => $q->where('source_type', $this->source_filter))
            ->when($this->account_name, fn ($q) => $q->where('account_name', $this->account_name))
            ->when($this->project_id, fn ($q) => $q->where('project_id', $this->project_id))
            ->when($this->date_from, fn ($q) => $q->whereDate('entry_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('entry_date', '<=', $this->date_to))
            ->latest('entry_date')
            ->latest('id')
            ->take(700)
            ->get();
    }

    public function render()
    {
        $entries = $this->ledgerEntries();

        return view('livewire.finance.general-ledger-page', [
            'financeNavLinks' => $this->financeNavLinks(),
            'accounts' => $this->accounts(),
            'projects' => $this->projects(),
            'pendingDocuments' => $this->pendingDocuments(),
            'pendingPayments' => $this->pendingPayments(),
            'entries' => $entries,
            'totalDebit' => (float) $entries->sum('debit'),
            'totalCredit' => (float) $entries->sum('credit'),
            'journalDebit' => $this->journalDebit,
            'journalCredit' => $this->journalCredit,
        ])->layout($this->layoutName());
    }
}