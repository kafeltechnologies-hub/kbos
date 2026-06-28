<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BudgetCentrePage extends FinanceBasePage
{
    public string $search = '';
    public ?string $financial_year = null;
    public ?string $period = null;
    public ?string $status = null;
    public ?int $project_id = null;
    public ?int $account_id = null;

    public ?int $editingId = null;

    public string $budget_code = '';
    public string $budget_name = '';
    public ?int $form_project_id = null;
    public ?int $form_account_id = null;
    public string $form_financial_year = '';
    public string $form_period = 'annual';
    public float|int|string $budget_amount = 0;
    public float|int|string $alert_threshold = 80;
    public string $description = '';
    public string $form_status = 'active';

    public array $periods = [
        'annual' => 'Annual',
        'q1' => 'Quarter 1',
        'q2' => 'Quarter 2',
        'q3' => 'Quarter 3',
        'q4' => 'Quarter 4',
        'jan' => 'January',
        'feb' => 'February',
        'mar' => 'March',
        'apr' => 'April',
        'may' => 'May',
        'jun' => 'June',
        'jul' => 'July',
        'aug' => 'August',
        'sep' => 'September',
        'oct' => 'October',
        'nov' => 'November',
        'dec' => 'December',
    ];

    public array $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'closed' => 'Closed',
        'cancelled' => 'Cancelled',
    ];

    public function mount(): void
    {
        $this->financial_year = now()->format('Y');
        $this->form_financial_year = now()->format('Y');
    }

    public function rules(): array
    {
        return [
            'budget_name' => ['required', 'string', 'max:255'],
            'form_account_id' => ['required', 'integer'],
            'form_financial_year' => ['required', 'string', 'max:20'],
            'form_period' => ['required', 'string', 'max:50'],
            'budget_amount' => ['required', 'numeric', 'min:0'],
            'alert_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'form_status' => ['required', 'string', 'max:50'],
        ];
    }

    private function budgetsTable(): string
    {
        return 'finance_budgets';
    }

    private function ensureBudgetsTable(): bool
    {
        return Schema::hasTable($this->budgetsTable());
    }

    public function generateBudgetCode(): string
    {
        $prefix = 'BUD' . now()->format('Y');

        if (! $this->ensureBudgetsTable()) {
            return $prefix . '0001';
        }

        $lastId = DB::table($this->budgetsTable())->max('id') ?? 0;

        return $prefix . str_pad((string) ($lastId + 1), 4, '0', STR_PAD_LEFT);
    }

    public function saveBudget(): void
        {
            if (! $this->ensureBudgetsTable()) {
                $this->addError('budget_name', 'Missing table finance_budgets. Create the migration first.');
                return;
            }

            $this->validate();

            $account = $this->accountOptions()->firstWhere('id', $this->form_account_id);
            $project = $this->projectOptions()->firstWhere('id', $this->form_project_id);

            $payload = [
                'budget_code' => $this->budget_code ?: $this->generateBudgetCode(),
                'budget_name' => $this->budget_name,
                'project_id' => $this->form_project_id,
                'project_name' => $project?->project_name,
                'account_id' => $this->form_account_id,
                'account_code' => $account?->account_code,
                'account_name' => $account?->account_name,
                'financial_year' => $this->form_financial_year,
                'period' => $this->form_period,
                'budget_amount' => (float) $this->budget_amount,
                'alert_threshold' => (float) $this->alert_threshold,
                'description' => $this->description,
                'status' => $this->form_status,
                'updated_at' => now(),
            ];

            if ($this->editingId) {
                DB::table($this->budgetsTable())
                    ->where('id', $this->editingId)
                    ->update($payload);
            } else {
                $payload['created_at'] = now();

                DB::table($this->budgetsTable())
                    ->insert($payload);
            }

            $this->clearForm();

            session()->flash('success', 'Budget item saved successfully.');
        }

    public function editBudget(int $id): void
    {
        if (! $this->ensureBudgetsTable()) {
            return;
        }

        $budget = DB::table($this->budgetsTable())->where('id', $id)->first();

        if (! $budget) {
            return;
        }

        $this->editingId = $budget->id;
        $this->budget_code = $budget->budget_code ?? '';
        $this->budget_name = $budget->budget_name ?? '';
        $this->form_project_id = $budget->project_id ?? null;
        $this->form_account_id = $budget->account_id ?? null;
        $this->form_financial_year = $budget->financial_year ?? now()->format('Y');
        $this->form_period = $budget->period ?? 'annual';
        $this->budget_amount = $budget->budget_amount ?? 0;
        $this->alert_threshold = $budget->alert_threshold ?? 80;
        $this->description = $budget->description ?? '';
        $this->form_status = $budget->status ?? 'active';
    }

    public function deleteBudget(int $id): void
    {
        if (! $this->ensureBudgetsTable()) {
            return;
        }

        DB::table($this->budgetsTable())->where('id', $id)->delete();

        session()->flash('success', 'Budget item deleted successfully.');
    }

    public function clearForm(): void
    {
        $this->editingId = null;
        $this->budget_code = '';
        $this->budget_name = '';
        $this->form_project_id = null;
        $this->form_account_id = null;
        $this->form_financial_year = now()->format('Y');
        $this->form_period = 'annual';
        $this->budget_amount = 0;
        $this->alert_threshold = 80;
        $this->description = '';
        $this->form_status = 'active';
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->financial_year = now()->format('Y');
        $this->period = null;
        $this->status = null;
        $this->project_id = null;
        $this->account_id = null;
    }

    private function accountOptions()
    {
        if (class_exists(ChartOfAccount::class) && Schema::hasTable('chart_of_accounts')) {
            return ChartOfAccount::query()
                ->where(function ($q) {
                    $q->where('active', true)
                        ->orWhere('active', 1)
                        ->orWhereNull('active');
                })
                ->orderBy('account_code')
                ->get();
        }

        return collect();
    }

    private function projectOptions()
    {
        if (Schema::hasTable('projects')) {
            return Project::orderBy('project_name')->get();
        }

        return collect();
    }

    private function budgetActual(float|int|string|null $accountId, ?int $projectId, ?string $year, ?string $period): float
    {
        if (! Schema::hasTable('general_ledgers')) {
            return 0;
        }

        $account = $this->accountOptions()->firstWhere('id', (int) $accountId);

        if (! $account) {
            return 0;
        }

        $query = GeneralLedger::query()
            ->where(function ($q) use ($account) {
                $q->where('account_name', $account->account_name)
                    ->orWhere('account', $account->account_name)
                    ->orWhere('account_code', $account->account_code);
            })
            ->when($projectId, function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('entry_date', $year);
            });

        $monthMap = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        if (isset($monthMap[$period])) {
            $query->whereMonth('entry_date', $monthMap[$period]);
        }

        if ($period === 'q1') {
            $query->whereMonth('entry_date', '>=', 1)->whereMonth('entry_date', '<=', 3);
        }

        if ($period === 'q2') {
            $query->whereMonth('entry_date', '>=', 4)->whereMonth('entry_date', '<=', 6);
        }

        if ($period === 'q3') {
            $query->whereMonth('entry_date', '>=', 7)->whereMonth('entry_date', '<=', 9);
        }

        if ($period === 'q4') {
            $query->whereMonth('entry_date', '>=', 10)->whereMonth('entry_date', '<=', 12);
        }

        $debit = (float) $query->clone()->sum('debit');
        $credit = (float) $query->clone()->sum('credit');

        return abs($debit - $credit);
    }

    private function budgetRows()
    {
        if (! $this->ensureBudgetsTable()) {
            return collect();
        }

        return DB::table($this->budgetsTable())
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('budget_code', 'ilike', "%{$this->search}%")
                        ->orWhere('budget_name', 'ilike', "%{$this->search}%")
                        ->orWhere('account_name', 'ilike', "%{$this->search}%")
                        ->orWhere('project_name', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->financial_year, fn ($q) => $q->where('financial_year', $this->financial_year))
            ->when($this->period, fn ($q) => $q->where('period', $this->period))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->project_id, fn ($q) => $q->where('project_id', $this->project_id))
            ->when($this->account_id, fn ($q) => $q->where('account_id', $this->account_id))
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                $actual = $this->budgetActual(
                    $row->account_id,
                    $row->project_id,
                    $row->financial_year,
                    $row->period
                );

                $budget = (float) $row->budget_amount;
                $variance = $budget - $actual;
                $usage = $budget > 0 ? ($actual / $budget) * 100 : 0;

                $row->actual_amount = $actual;
                $row->variance_amount = $variance;
                $row->usage_percent = $usage;
                $row->is_over_budget = $actual > $budget && $budget > 0;
                $row->is_warning = $usage >= (float) $row->alert_threshold && ! $row->is_over_budget;

                return $row;
            });
    }

    public function render()
    {
        $accounts = $this->accountOptions();
        $projects = $this->projectOptions();
        $budgets = $this->budgetRows();

        $totalBudget = (float) $budgets->sum('budget_amount');
        $totalActual = (float) $budgets->sum('actual_amount');
        $totalVariance = $totalBudget - $totalActual;
        $overallUsage = $totalBudget > 0 ? ($totalActual / $totalBudget) * 100 : 0;

        $overBudgetCount = $budgets->where('is_over_budget', true)->count();
        $warningCount = $budgets->where('is_warning', true)->count();

        return view('livewire.finance.budget-centre-page', compact(
            'accounts',
            'projects',
            'budgets',
            'totalBudget',
            'totalActual',
            'totalVariance',
            'overallUsage',
            'overBudgetCount',
            'warningCount'
        ) + [
            'financeNavLinks' => $this->financeNavLinks(),
            'periods' => $this->periods,
            'statuses' => $this->statuses,
            'hasBudgetTable' => $this->ensureBudgetsTable(),
        ])->layout($this->layoutName());
    }
}