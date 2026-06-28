<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrialBalancePage extends FinanceBasePage
{
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $account_name = null;
    public ?string $account_type = null;
    public ?int $project_id = null;

    public bool $material_only = false;
    public bool $hide_zero_balances = true;

    public string $search = '';

    public function mount(): void
    {
        $this->date_to = now()->toDateString();
    }

    public function clearFilters(): void
    {
        $this->date_from = null;
        $this->date_to = now()->toDateString();
        $this->account_name = null;
        $this->account_type = null;
        $this->project_id = null;
        $this->material_only = false;
        $this->hide_zero_balances = true;
        $this->search = '';
    }

    public function rows()
    {
        if (! Schema::hasTable('general_ledgers')) {
            return collect();
        }

        $query = GeneralLedger::query()
            ->select(
                'account_name',
                DB::raw('COALESCE(SUM(debit), 0) as debit_total'),
                DB::raw('COALESCE(SUM(credit), 0) as credit_total')
            )
            ->whereNotNull('account_name')
            ->when($this->date_from, function ($q) {
                $q->whereDate('entry_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($q) {
                $q->whereDate('entry_date', '<=', $this->date_to);
            })
            ->when($this->account_name, function ($q) {
                $q->where('account_name', $this->account_name);
            })
            ->when($this->project_id, function ($q) {
                $q->where('project_id', $this->project_id);
            })
            ->when($this->material_only, function ($q) {
                $q->where(function ($query) {
                    $query->where('source_module', 'materials')
                        ->orWhere('source_type', 'material_transaction')
                        ->orWhere('reference', 'ilike', '%GRN%')
                        ->orWhere('reference', 'ilike', '%MIV%')
                        ->orWhere('reference', 'ilike', '%RTN%')
                        ->orWhere('reference', 'ilike', '%TRF%')
                        ->orWhere('reference', 'ilike', '%PRS%')
                        ->orWhere('description', 'ilike', '%material%')
                        ->orWhere('description', 'ilike', '%inventory%')
                        ->orWhere('narration', 'ilike', '%material%')
                        ->orWhere('narration', 'ilike', '%inventory%');
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('account_name', 'ilike', "%{$this->search}%")
                        ->orWhere('reference', 'ilike', "%{$this->search}%")
                        ->orWhere('description', 'ilike', "%{$this->search}%")
                        ->orWhere('narration', 'ilike', "%{$this->search}%");
                });
            })
            ->groupBy('account_name')
            ->orderBy('account_name')
            ->get()
            ->map(function ($row) {
                $debit = (float) $row->debit_total;
                $credit = (float) $row->credit_total;
                $balance = $debit - $credit;

                $row->balance = $balance;
                $row->debit_balance = $balance > 0 ? $balance : 0;
                $row->credit_balance = $balance < 0 ? abs($balance) : 0;
                $row->classification = $this->classifyAccount($row->account_name);

                return $row;
            });

        if ($this->account_type) {
            $query = $query->filter(fn ($row) => $row->classification === $this->account_type);
        }

        if ($this->hide_zero_balances) {
            $query = $query->filter(fn ($row) => round((float) $row->balance, 2) != 0);
        }

        return $query->values();
    }

    private function classifyAccount(?string $accountName): string
    {
        $name = strtolower((string) $accountName);

        if (str_contains($name, 'asset') || str_contains($name, 'inventory') || str_contains($name, 'receivable') || str_contains($name, 'cash') || str_contains($name, 'bank')) {
            return 'asset';
        }

        if (str_contains($name, 'payable') || str_contains($name, 'liability') || str_contains($name, 'clearing')) {
            return 'liability';
        }

        if (str_contains($name, 'revenue') || str_contains($name, 'sales') || str_contains($name, 'income')) {
            return 'income';
        }

        if (str_contains($name, 'cost') || str_contains($name, 'expense') || str_contains($name, 'cogs')) {
            return 'expense';
        }

        if (str_contains($name, 'equity') || str_contains($name, 'capital')) {
            return 'equity';
        }

        return 'unclassified';
    }

    public function render()
    {
        $rows = $this->rows();

       $accounts = collect();

        if (class_exists(\App\Models\ChartOfAccount::class) && Schema::hasTable('chart_of_accounts')) {
            $accounts = \App\Models\ChartOfAccount::query()
                ->where(function ($q) {
                    $q->where('active', true)
                        ->orWhere('active', 1)
                        ->orWhereNull('active');
                })
                ->orderBy('account_code')
                ->get();
        } elseif (Schema::hasTable('general_ledgers')) {
            $accounts = GeneralLedger::selectRaw('NULL as id, NULL as account_code, account_name')
                ->whereNotNull('account_name')
                ->distinct()
                ->orderBy('account_name')
                ->get();
        }

        $projects = Schema::hasTable('projects')
            ? Project::orderBy('project_name')->get()
            : collect();

        $totalDebit = (float) $rows->sum('debit_total');
        $totalCredit = (float) $rows->sum('credit_total');
        $totalDebitBalance = (float) $rows->sum('debit_balance');
        $totalCreditBalance = (float) $rows->sum('credit_balance');
        $difference = round($totalDebitBalance - $totalCreditBalance, 2);

        $assetTotal = (float) $rows->where('classification', 'asset')->sum('debit_balance');
        $liabilityTotal = (float) $rows->where('classification', 'liability')->sum('credit_balance');
        $incomeTotal = (float) $rows->where('classification', 'income')->sum('credit_balance');
        $expenseTotal = (float) $rows->where('classification', 'expense')->sum('debit_balance');

        $accountTypes = [
            'asset' => 'Assets',
            'liability' => 'Liabilities',
            'equity' => 'Equity',
            'income' => 'Income',
            'expense' => 'Expenses',
            'unclassified' => 'Unclassified',
        ];

        return view('livewire.finance.trial-balance-page', compact(
            'rows',
            'accounts',
            'projects',
            'totalDebit',
            'totalCredit',
            'totalDebitBalance',
            'totalCreditBalance',
            'difference',
            'assetTotal',
            'liabilityTotal',
            'incomeTotal',
            'expenseTotal',
            'accountTypes'
        ) + [
            'financeNavLinks' => $this->financeNavLinks(),
        ])->layout($this->layoutName());
    }
}