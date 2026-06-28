<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BalanceSheetPage extends FinanceBasePage
{
    public ?string $as_at_date = null;
    public ?string $search = null;

    public bool $hide_zero_balances = true;
    public bool $material_only = false;
    public bool $fixed_assets_only = false;

    public function mount(): void
    {
        $this->as_at_date = now()->toDateString();
    }

    public function clearFilters(): void
    {
        $this->as_at_date = now()->toDateString();
        $this->search = null;
        $this->hide_zero_balances = true;
        $this->material_only = false;
        $this->fixed_assets_only = false;
    }

    private function accountMaster()
    {
        if (! class_exists(ChartOfAccount::class) || ! Schema::hasTable('chart_of_accounts')) {
            return collect();
        }

        $query = ChartOfAccount::query();

        if (Schema::hasColumn('chart_of_accounts', 'is_active')) {
            $query->where(function ($q) {
                $q->where('is_active', true)->orWhere('is_active', 1)->orWhereNull('is_active');
            });
        } elseif (Schema::hasColumn('chart_of_accounts', 'active')) {
            $query->where(function ($q) {
                $q->where('active', true)->orWhere('active', 1)->orWhereNull('active');
            });
        }

        return $query->orderBy('account_code')->get()
            ->keyBy(fn ($account) => strtolower(trim((string) $account->account_name)));
    }

    private function ledgerRows()
    {
        if (! Schema::hasTable('general_ledgers')) {
            return collect();
        }

        $master = $this->accountMaster();

        return GeneralLedger::query()
            ->selectRaw('account_name, MAX(account_code) as gl_account_code, SUM(COALESCE(debit,0)) as debit_total, SUM(COALESCE(credit,0)) as credit_total')
            ->whereNotNull('account_name')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', ['cancelled', 'reversed', 'void']);
            })
            ->when($this->as_at_date, fn ($q) => $q->whereDate('entry_date', '<=', $this->as_at_date))
            ->when($this->material_only, fn ($q) => $q->where('source_module', 'materials'))
            ->when($this->fixed_assets_only, fn ($q) => $q->where('source_module', 'fixed_assets'))
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('account_name', 'ilike', "%{$this->search}%")
                    ->orWhere('account_code', 'ilike', "%{$this->search}%")
                    ->orWhere('reference', 'ilike', "%{$this->search}%")
                    ->orWhere('description', 'ilike', "%{$this->search}%")
                    ->orWhere('narration', 'ilike', "%{$this->search}%");
            }))
            ->groupBy('account_name')
            ->orderBy('account_name')
            ->get()
            ->map(function ($row) use ($master) {
                $key = strtolower(trim((string) $row->account_name));
                $account = $master->get($key);

                $debit = (float) $row->debit_total;
                $credit = (float) $row->credit_total;

                $type = strtolower((string) ($account?->account_type ?? $this->classifyAccount($row->account_name)));
                $category = strtolower((string) ($account?->category ?? $this->classifyCategory($row->account_name, $type)));

                $isContraAsset = $this->isContraAsset($row->account_name);

                if ($isContraAsset) {
                    $type = 'asset';
                    $category = 'non_current_asset';
                    $balance = ($credit - $debit) * -1;
                } else {
                    $balance = match ($type) {
                        'liability', 'equity' => $credit - $debit,
                        default => $debit - $credit,
                    };
                }

                $row->account_code = $account?->account_code ?? $row->gl_account_code;
                $row->account_type = $type;
                $row->category = $category;
                $row->balance = $balance;
                $row->is_contra_asset = $isContraAsset;

                return $row;
            })
            ->filter(function ($row) {
                if (! in_array($row->account_type, ['asset', 'liability', 'equity'], true)) {
                    return false;
                }

                if ($this->hide_zero_balances && round((float) $row->balance, 2) == 0) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    private function classifyAccount(?string $accountName): string
    {
        $name = strtolower((string) $accountName);

        if (
            str_contains($name, 'asset') ||
            str_contains($name, 'inventory') ||
            str_contains($name, 'receivable') ||
            str_contains($name, 'cash') ||
            str_contains($name, 'bank') ||
            str_contains($name, 'prepaid') ||
            str_contains($name, 'vehicle') ||
            str_contains($name, 'equipment') ||
            str_contains($name, 'building') ||
            str_contains($name, 'land')
        ) {
            return 'asset';
        }

        if (
            str_contains($name, 'payable') ||
            str_contains($name, 'liability') ||
            str_contains($name, 'vat') ||
            str_contains($name, 'withholding') ||
            str_contains($name, 'loan') ||
            str_contains($name, 'clearing')
        ) {
            return 'liability';
        }

        if (
            str_contains($name, 'equity') ||
            str_contains($name, 'capital') ||
            str_contains($name, 'retained') ||
            str_contains($name, 'earnings')
        ) {
            return 'equity';
        }

        return 'unclassified';
    }

    private function classifyCategory(?string $accountName, string $type): string
    {
        $name = strtolower((string) $accountName);

        if ($type === 'asset') {
            if (
                str_contains($name, 'fixed') ||
                str_contains($name, 'vehicle') ||
                str_contains($name, 'equipment') ||
                str_contains($name, 'building') ||
                str_contains($name, 'land') ||
                str_contains($name, 'depreciation')
            ) {
                return 'non_current_asset';
            }

            return 'current_asset';
        }

        if ($type === 'liability') {
            return str_contains($name, 'loan') ? 'non_current_liability' : 'current_liability';
        }

        if ($type === 'equity') {
            return 'equity';
        }

        return 'unclassified';
    }

    private function isContraAsset(?string $accountName): bool
    {
        $name = strtolower((string) $accountName);

        return str_contains($name, 'accumulated depreciation')
            || str_contains($name, 'depreciation provision')
            || str_contains($name, 'allowance for depreciation');
    }

    private function fixedAssetSummary(): array
    {
        if (! Schema::hasTable('fixed_assets')) {
            return ['cost' => 0, 'current_value' => 0, 'depreciation' => 0, 'count' => 0, 'active_count' => 0];
        }

        $assets = DB::table('fixed_assets')
            ->whereNotIn('status', ['disposed', 'lost', 'retired'])
            ->get();

        $cost = (float) $assets->sum('purchase_cost');
        $currentValue = (float) $assets->sum('current_value');

        return [
            'cost' => $cost,
            'current_value' => $currentValue,
            'depreciation' => max(0, $cost - $currentValue),
            'count' => $assets->count(),
            'active_count' => $assets->where('status', 'active')->count(),
        ];
    }

    private function fixedAssetRows()
    {
        if (! Schema::hasTable('fixed_assets')) {
            return collect();
        }

        return DB::table('fixed_assets')
            ->leftJoin('projects', 'projects.id', '=', 'fixed_assets.project_id')
            ->select('fixed_assets.*', 'projects.project_name', 'projects.project_code')
            ->whereNotIn('fixed_assets.status', ['disposed', 'lost', 'retired'])
            ->orderBy('fixed_assets.asset_category')
            ->orderBy('fixed_assets.asset_name')
            ->get();
    }

    private function sectionRows($rows, string $type)
    {
        return $rows->where('account_type', $type)->values();
    }

    public function render()
    {
        $rows = $this->ledgerRows();

        $assets = $this->sectionRows($rows, 'asset');
        $liabilities = $this->sectionRows($rows, 'liability');
        $equity = $this->sectionRows($rows, 'equity');

        $currentAssets = $assets->where('category', 'current_asset')->values();
        $nonCurrentAssets = $assets->where('category', 'non_current_asset')->values();

        $currentLiabilities = $liabilities->where('category', 'current_liability')->values();
        $nonCurrentLiabilities = $liabilities->where('category', 'non_current_liability')->values();

        $totalCurrentAssets = (float) $currentAssets->sum('balance');
        $totalNonCurrentAssets = (float) $nonCurrentAssets->sum('balance');
        $totalAssets = $totalCurrentAssets + $totalNonCurrentAssets;

        $totalCurrentLiabilities = (float) $currentLiabilities->sum('balance');
        $totalNonCurrentLiabilities = (float) $nonCurrentLiabilities->sum('balance');
        $totalLiabilities = $totalCurrentLiabilities + $totalNonCurrentLiabilities;

        $totalEquity = (float) $equity->sum('balance');
        $liabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $difference = round($totalAssets - $liabilitiesAndEquity, 2);

        $workingCapital = $totalCurrentAssets - $totalCurrentLiabilities;
        $currentRatio = $totalCurrentLiabilities > 0 ? $totalCurrentAssets / $totalCurrentLiabilities : 0;

        $fixedAssetSummary = $this->fixedAssetSummary();
        $fixedAssetRows = $this->fixedAssetRows();

        $fixedAssetGLBalance = (float) $nonCurrentAssets
            ->filter(fn ($row) => str_contains(strtolower((string) $row->account_name), 'fixed')
                || str_contains(strtolower((string) $row->account_name), 'vehicle')
                || str_contains(strtolower((string) $row->account_name), 'equipment')
                || str_contains(strtolower((string) $row->account_name), 'building')
                || str_contains(strtolower((string) $row->account_name), 'land')
                || str_contains(strtolower((string) $row->account_name), 'depreciation'))
            ->sum('balance');

        $fixedAssetDifference = round($fixedAssetSummary['current_value'] - $fixedAssetGLBalance, 2);

        return view('livewire.finance.balance-sheet-page', compact(
            'rows',
            'currentAssets',
            'nonCurrentAssets',
            'currentLiabilities',
            'nonCurrentLiabilities',
            'equity',
            'totalCurrentAssets',
            'totalNonCurrentAssets',
            'totalAssets',
            'totalCurrentLiabilities',
            'totalNonCurrentLiabilities',
            'totalLiabilities',
            'totalEquity',
            'liabilitiesAndEquity',
            'difference',
            'workingCapital',
            'currentRatio',
            'fixedAssetSummary',
            'fixedAssetRows',
            'fixedAssetGLBalance',
            'fixedAssetDifference'
        ) + [
            'financeNavLinks' => $this->financeNavLinks(),
        ])->layout($this->layoutName());
    }
}