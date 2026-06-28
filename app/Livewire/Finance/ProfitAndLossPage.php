<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProfitAndLossPage extends Component
{
    public ?string $date_from = null;
    public ?string $date_to = null;
    public string $account_type_filter = '';
    public string $search = '';

    public float $totalRevenue = 0;
    public float $totalCogs = 0;
    public float $grossProfit = 0;
    public float $totalExpenses = 0;
    public float $netProfit = 0;
    public float $grossProfitMargin = 0;
    public float $netProfitMargin = 0;

    public function mount(): void
    {
        $this->date_from = now()->startOfYear()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
    }

    public function resetFilters(): void
    {
        $this->date_from = now()->startOfYear()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
        $this->account_type_filter = '';
        $this->search = '';
    }

    public function render()
    {
        $revenueAccounts = $this->accountSummary(['Income', 'Revenue', 'Sales']);
        $cogsAccounts = $this->accountSummary(['COGS', 'Cost of Sales']);
        $expenseAccounts = $this->accountSummary(['Expense', 'Expenses']);

        $this->totalRevenue = $revenueAccounts->sum('balance');
        $this->totalCogs = $cogsAccounts->sum('balance');
        $this->grossProfit = $this->totalRevenue - $this->totalCogs;
        $this->totalExpenses = $expenseAccounts->sum('balance');
        $this->netProfit = $this->grossProfit - $this->totalExpenses;

        $this->grossProfitMargin = $this->totalRevenue > 0
            ? ($this->grossProfit / $this->totalRevenue) * 100
            : 0;

        $this->netProfitMargin = $this->totalRevenue > 0
            ? ($this->netProfit / $this->totalRevenue) * 100
            : 0;

        $recentEntries = GeneralLedger::query()
            ->with('account')
            ->whereHas('account', function ($query) {
                $query->whereIn('account_type', [
                    'Income',
                    'Revenue',
                    'Sales',
                    'Expense',
                    'Expenses',
                    'COGS',
                    'Cost of Sales',
                ]);
            })
            ->when($this->date_from, fn ($q) => $q->whereDate('posting_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('posting_date', '<=', $this->date_to))
            ->latest('posting_date')
            ->limit(10)
            ->get();

        return view('livewire.finance.profit-and-loss-page', [
            'revenueAccounts' => $revenueAccounts,
            'cogsAccounts' => $cogsAccounts,
            'expenseAccounts' => $expenseAccounts,
            'recentEntries' => $recentEntries,
            'periodLabel' => $this->periodLabel(),
        ]);
    }

    private function accountSummary(array $types): Collection
    {
        return ChartOfAccount::query()
            ->whereIn('account_type', $types)
            ->when($this->account_type_filter, function ($query) {
                $query->where('account_type', $this->account_type_filter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('account_code', 'ilike', "%{$this->search}%")
                        ->orWhere('account_name', 'ilike', "%{$this->search}%")
                        ->orWhere('account_type', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) {
                $debit = GeneralLedger::query()
                    ->where('account_id', $account->id)
                    ->when($this->date_from, fn ($q) => $q->whereDate('posting_date', '>=', $this->date_from))
                    ->when($this->date_to, fn ($q) => $q->whereDate('posting_date', '<=', $this->date_to))
                    ->sum('debit');

                $credit = GeneralLedger::query()
                    ->where('account_id', $account->id)
                    ->when($this->date_from, fn ($q) => $q->whereDate('posting_date', '>=', $this->date_from))
                    ->when($this->date_to, fn ($q) => $q->whereDate('posting_date', '<=', $this->date_to))
                    ->sum('credit');

                $isIncome = in_array($account->account_type, ['Income', 'Revenue', 'Sales']);

                $account->debit_total = (float) $debit;
                $account->credit_total = (float) $credit;
                $account->balance = $isIncome
                    ? (float) $credit - (float) $debit
                    : (float) $debit - (float) $credit;

                return $account;
            });
    }

    private function periodLabel(): string
    {
        $from = $this->date_from
            ? Carbon::parse($this->date_from)->format('d M Y')
            : 'Beginning';

        $to = $this->date_to
            ? Carbon::parse($this->date_to)->format('d M Y')
            : 'Today';

        return "{$from} to {$to}";
    }
}