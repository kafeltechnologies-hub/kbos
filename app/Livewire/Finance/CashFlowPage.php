<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use Illuminate\Support\Facades\Schema;

class CashFlowPage extends FinanceBasePage
{
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $search = null;

    public bool $material_only = false;
    public bool $hide_zero_lines = true;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function clearFilters(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
        $this->search = null;
        $this->material_only = false;
        $this->hide_zero_lines = true;
    }

    private function ledgerRows()
    {
        if (! Schema::hasTable('general_ledgers')) {
            return collect();
        }

        return GeneralLedger::query()
            ->where(function ($q) {
                $q->whereNotNull('account_name')
                    ->orWhereNotNull('account');
            })
            ->when($this->date_from, fn ($q) => $q->whereDate('entry_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('entry_date', '<=', $this->date_to))
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
                        ->orWhere('account', 'ilike', "%{$this->search}%")
                        ->orWhere('reference', 'ilike', "%{$this->search}%")
                        ->orWhere('description', 'ilike', "%{$this->search}%")
                        ->orWhere('narration', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('entry_date')
            ->get()
            ->map(function ($entry) {
                $entry->display_account = $entry->account_name ?? $entry->account;
                $entry->cash_section = $this->classifyCashFlowSection($entry->display_account, $entry->description ?? $entry->narration);
                $entry->cash_effect = $this->cashEffect($entry);

                return $entry;
            });
    }

    private function classifyCashFlowSection(?string $account, ?string $narration = null): string
    {
        $text = strtolower((string) $account . ' ' . (string) $narration);

        if (
            str_contains($text, 'asset') ||
            str_contains($text, 'vehicle') ||
            str_contains($text, 'equipment') ||
            str_contains($text, 'computer') ||
            str_contains($text, 'building') ||
            str_contains($text, 'land') ||
            str_contains($text, 'depreciation')
        ) {
            return 'investing';
        }

        if (
            str_contains($text, 'capital') ||
            str_contains($text, 'equity') ||
            str_contains($text, 'loan') ||
            str_contains($text, 'share') ||
            str_contains($text, 'retained earnings')
        ) {
            return 'financing';
        }

        return 'operating';
    }

    private function cashEffect($entry): float
    {
        $account = strtolower((string) ($entry->account_name ?? $entry->account));
        $debit = (float) ($entry->debit ?? $entry->debit_amount ?? 0);
        $credit = (float) ($entry->credit ?? $entry->credit_amount ?? 0);

        if (str_contains($account, 'cash') || str_contains($account, 'bank')) {
            return $debit - $credit;
        }

        if (
            str_contains($account, 'revenue') ||
            str_contains($account, 'income') ||
            str_contains($account, 'payable') ||
            str_contains($account, 'liability') ||
            str_contains($account, 'loan') ||
            str_contains($account, 'capital')
        ) {
            return $credit - $debit;
        }

        return $debit - $credit;
    }

    private function sectionSummary($rows, string $section): array
    {
        $sectionRows = $rows->where('cash_section', $section);

        $inflow = (float) $sectionRows->sum(fn ($row) => $row->cash_effect > 0 ? $row->cash_effect : 0);
        $outflow = (float) $sectionRows->sum(fn ($row) => $row->cash_effect < 0 ? abs($row->cash_effect) : 0);
        $net = $inflow - $outflow;

        return [
            'rows' => $sectionRows->filter(function ($row) {
                return ! $this->hide_zero_lines || round((float) $row->cash_effect, 2) != 0;
            })->values(),
            'inflow' => $inflow,
            'outflow' => $outflow,
            'net' => $net,
        ];
    }

    public function render()
    {
        $rows = $this->ledgerRows();

        $operating = $this->sectionSummary($rows, 'operating');
        $investing = $this->sectionSummary($rows, 'investing');
        $financing = $this->sectionSummary($rows, 'financing');

        $totalInflows = $operating['inflow'] + $investing['inflow'] + $financing['inflow'];
        $totalOutflows = $operating['outflow'] + $investing['outflow'] + $financing['outflow'];
        $netCashFlow = $operating['net'] + $investing['net'] + $financing['net'];

        $cashAndBankBalance = $this->cashAndBankBalance();

        return view('livewire.finance.cash-flow-page', compact(
            'rows',
            'operating',
            'investing',
            'financing',
            'totalInflows',
            'totalOutflows',
            'netCashFlow',
            'cashAndBankBalance'
        ) + [
            'financeNavLinks' => $this->financeNavLinks(),
        ])->layout($this->layoutName());
    }

    private function cashAndBankBalance(): float
    {
        if (! Schema::hasTable('general_ledgers')) {
            return 0;
        }

        $debit = (float) GeneralLedger::query()
            ->where(function ($q) {
                $q->where('account_name', 'ilike', '%cash%')
                    ->orWhere('account_name', 'ilike', '%bank%')
                    ->orWhere('account', 'ilike', '%cash%')
                    ->orWhere('account', 'ilike', '%bank%');
            })
            ->sum('debit');

        $credit = (float) GeneralLedger::query()
            ->where(function ($q) {
                $q->where('account_name', 'ilike', '%cash%')
                    ->orWhere('account_name', 'ilike', '%bank%')
                    ->orWhere('account', 'ilike', '%cash%')
                    ->orWhere('account', 'ilike', '%bank%');
            })
            ->sum('credit');

        return $debit - $credit;
    }
}