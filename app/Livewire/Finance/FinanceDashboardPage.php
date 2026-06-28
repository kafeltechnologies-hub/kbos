<?php

namespace App\Livewire\Finance;

use App\Models\MaterialTransaction;
use Illuminate\Support\Facades\Schema;

class FinanceDashboardPage extends FinanceBasePage
{
    public function render()
    {
        $inventoryAsset = $this->debit(['Inventory Asset','Inventory','Materials Inventory']);
        $projectMaterialCost = $this->debit(['Project Material Cost','Materials Issued to Projects','Material Cost']);
        $materialReceivables = $this->debit(['Material Receivables','Materials Receivable']);
        $costOfGoodsSold = $this->debit(['COGS','Cost of Goods Sold','Material COGS']);
        $materialRevenue = $this->credit(['Material Revenue','Materials Revenue','Sales Revenue','Material Sales Revenue']);
        $netMaterialPosition = $inventoryAsset + $materialReceivables + $projectMaterialCost + $costOfGoodsSold - $materialRevenue;
        $materialTransactionsPending = Schema::hasTable('material_transactions') ? MaterialTransaction::whereIn('status',['pending','draft','posted'])->count() : 0;
        $materialTransactionsApproved = Schema::hasTable('material_transactions') ? MaterialTransaction::where('status','approved')->count() : 0;
        $recentMaterialPostings = $this->materialPostings(20);
        $materialPostingsCount = $recentMaterialPostings->count();
        $postingSummary = $this->accountSummary();
        return view('livewire.finance.finance-dashboard-page', compact('inventoryAsset','projectMaterialCost','materialReceivables','costOfGoodsSold','materialRevenue','netMaterialPosition','materialTransactionsPending','materialTransactionsApproved','materialPostingsCount','recentMaterialPostings','postingSummary') + ['financeNavLinks'=>$this->financeNavLinks()])->layout($this->layoutName());
    }
}
