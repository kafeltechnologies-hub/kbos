<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use App\Models\MaterialTransaction;
use Illuminate\Support\Facades\Schema;

class FinanceCentrePage extends FinanceBasePage
{
    public function render()
    {
        $pendingMaterials = Schema::hasTable('material_transactions') ? MaterialTransaction::whereIn('status',['draft','pending','posted'])->count() : 0;
        $postedGlEntries = Schema::hasTable('general_ledgers') ? GeneralLedger::count() : 0;
        $materialPostings = $this->materialPostings(10);
        return view('livewire.finance.finance-centre-page', [
            'financeNavLinks'=>$this->financeNavLinks(),
            'pendingMaterials'=>$pendingMaterials,
            'postedGlEntries'=>$postedGlEntries,
            'materialPostings'=>$materialPostings,
        ])->layout($this->layoutName());
    }
}
