<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use Illuminate\Support\Facades\Schema;

class BankReconciliationPage extends FinanceBasePage
{
    public string $search = '';

    public function render()
    {
        $entries = Schema::hasTable('general_ledgers')
            ? GeneralLedger::query()
                ->when($this->search, fn($q) => $q->where(fn($qq) =>
                    $qq->where('reference','ilike',"%{$this->search}%")
                       ->orWhere('account_name','ilike',"%{$this->search}%")
                       ->orWhere('description','ilike',"%{$this->search}%")
                       ->orWhere('narration','ilike',"%{$this->search}%")
                ))
                ->latest('entry_date')
                ->take(300)
                ->get()
            : collect();

        $postingSummary = $this->accountSummary();
        $totalDebit = (float) $entries->sum(fn($e) => (float)($e->debit ?? $e->debit_amount ?? 0));
        $totalCredit = (float) $entries->sum(fn($e) => (float)($e->credit ?? $e->credit_amount ?? 0));

        return view('livewire.finance.bank-reconciliation-page', compact('entries','postingSummary','totalDebit','totalCredit') + [
            'financeNavLinks'=>$this->financeNavLinks(),
        ])->layout($this->layoutName());
    }
}
