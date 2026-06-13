<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\GeneralLedger;
use Livewire\Component;

class AccountingReportsPage extends Component
{
    public string $report_type = 'general_ledger';

    public ?string $date_from = null;
    public ?string $date_to = null;

    public ?int $account_id = null;

    public array $reportTypes = [
        'general_ledger' => 'General Ledger',
        'cashbook' => 'Cashbook',
        'trial_balance' => 'Trial Balance',
        'income_statement' => 'Income Statement',
        'balance_sheet' => 'Balance Sheet',
    ];

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function sync(): void
    {
        session()->flash('info', 'Accounting reports refreshed successfully.');
    }

    public function getLedgerEntries()
    {
        return GeneralLedger::with('account')
            ->when($this->date_from, fn ($q) => $q->whereDate('posting_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('posting_date', '<=', $this->date_to))
            ->when($this->account_id, fn ($q) => $q->where('account_id', $this->account_id))
            ->orderBy('posting_date')
            ->latest('id')
            ->get();
    }

    public function getCashbookEntries()
    {
        return GeneralLedger::with('account')
            ->whereHas('account', function ($q) {
                $q->whereIn('account_code', ['1110', '1120', '1130', '1140']);
            })
            ->when($this->date_from, fn ($q) => $q->whereDate('posting_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('posting_date', '<=', $this->date_to))
            ->orderBy('posting_date')
            ->latest('id')
            ->get();
    }

    public function getTrialBalance()
    {
        return Account::where('active', true)
            ->withSum(['ledgerEntries as debit_total' => function ($q) {
                $q->when($this->date_from, fn ($query) => $query->whereDate('posting_date', '>=', $this->date_from))
                  ->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'debit')
            ->withSum(['ledgerEntries as credit_total' => function ($q) {
                $q->when($this->date_from, fn ($query) => $query->whereDate('posting_date', '>=', $this->date_from))
                  ->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'credit')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) {
                $debit = (float) ($account->debit_total ?? 0);
                $credit = (float) ($account->credit_total ?? 0);
                $balance = $debit - $credit;

                return [
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'debit' => $balance > 0 ? $balance : 0,
                    'credit' => $balance < 0 ? abs($balance) : 0,
                ];
            });
    }

    public function getIncomeStatement()
    {
        return Account::whereIn('account_type', ['Revenue', 'Expense'])
            ->where('active', true)
            ->withSum(['ledgerEntries as debit_total' => function ($q) {
                $q->when($this->date_from, fn ($query) => $query->whereDate('posting_date', '>=', $this->date_from))
                  ->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'debit')
            ->withSum(['ledgerEntries as credit_total' => function ($q) {
                $q->when($this->date_from, fn ($query) => $query->whereDate('posting_date', '>=', $this->date_from))
                  ->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'credit')
            ->orderBy('account_code')
            ->get();
    }

    public function getBalanceSheet()
    {
        return Account::whereIn('account_type', ['Asset', 'Liability', 'Equity'])
            ->where('active', true)
            ->withSum(['ledgerEntries as debit_total' => function ($q) {
                $q->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'debit')
            ->withSum(['ledgerEntries as credit_total' => function ($q) {
                $q->when($this->date_to, fn ($query) => $query->whereDate('posting_date', '<=', $this->date_to));
            }], 'credit')
            ->orderBy('account_code')
            ->get();
    }

    public function render()
    {
        $accounts = Account::where('active', true)->orderBy('account_code')->get();

        $ledgerEntries = $this->getLedgerEntries();
        $cashbookEntries = $this->getCashbookEntries();
        $trialBalance = $this->getTrialBalance();
        $incomeStatement = $this->getIncomeStatement();
        $balanceSheet = $this->getBalanceSheet();

        return view('livewire.finance.accounting-reports-page', compact(
            'accounts',
            'ledgerEntries',
            'cashbookEntries',
            'trialBalance',
            'incomeStatement',
            'balanceSheet'
        ))->layout('layouts.erp');
    }
}