<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

abstract class FinanceBasePage extends Component
{
    protected function layoutName(): string { return 'layouts.erp'; }

    protected function debit(string|array $accounts): float
    {
        if (! Schema::hasTable('general_ledgers')) return 0;
        $accounts = is_array($accounts) ? $accounts : [$accounts];
        return (float) GeneralLedger::whereIn('account_name', $accounts)->sum('debit')
             - (float) GeneralLedger::whereIn('account_name', $accounts)->sum('credit');
    }

    protected function credit(string|array $accounts): float
    {
        if (! Schema::hasTable('general_ledgers')) return 0;
        $accounts = is_array($accounts) ? $accounts : [$accounts];
        return (float) GeneralLedger::whereIn('account_name', $accounts)->sum('credit')
             - (float) GeneralLedger::whereIn('account_name', $accounts)->sum('debit');
    }

    protected function financeNavLinks(): array
    {
        return [
            ['label'=>'Finance Centre','route'=>'finance.centre'],
            ['label' => 'Operations','route' => 'finance.operations'],
            ['label'=>'Dashboard','route'=>'finance.dashboard'],
            ['label'=>'Chart of Accounts','route'=>'finance.chart-of-accounts'],
            ['label'=>'Journals','route'=>'finance.journal-entries'],
            ['label'=>'Payments','route'=>'finance.payment-centre'],
            ['label'=>'Receipts','route'=>'finance.receipt-centre'],
            ['label'=>'Invoices','route'=>'finance.invoice-centre'],
            ['label'=>'General Ledger','route'=>'finance.general-ledger'],
            //['label'=>'Trial Balance','route'=>'finance.trial-balance'],
            //['label'=>'Profit & Loss','route'=>'finance.profit-loss'],
            //['label'=>'Balance Sheet','route'=>'finance.balance-sheet'],
            //['label'=>'Cash Flow','route'=>'finance.cash-flow'],
            ['label'=>'Budgets','route'=>'finance.budgets'],
            ['label'=>'Fixed Assets','route'=>'finance.fixed-assets'],
            ['label'=>'Tax Centre','route'=>'finance.tax-centre'],
            //['label'=>'Bank Reconciliation','route'=>'finance.bank-reconciliation'],
            ['label'=>'Reports','route'=>'finance.accounting-reports'],
            ['label'=>'Settings','route'=>'finance.settings'],
            ['label'=>'Materials','route'=>'projects.materials'],
            ['label'=>'Projects','route'=>'projects.index'],
            
            ];
    }

    protected function materialPostings(int $limit = 50)
    {
        if (! Schema::hasTable('general_ledgers')) return collect();
        return GeneralLedger::query()
            ->where(function ($q) {
                $q->where('source_module','materials')
                  ->orWhere('source_type','material_transaction')
                  ->orWhere('reference','ilike','%GRN%')
                  ->orWhere('reference','ilike','%MIV%')
                  ->orWhere('reference','ilike','%RTN%')
                  ->orWhere('reference','ilike','%TRF%')
                  ->orWhere('reference','ilike','%PRS%')
                  ->orWhere('description','ilike','%material%')
                  ->orWhere('narration','ilike','%material%')
                  ->orWhere('description','ilike','%inventory%')
                  ->orWhere('narration','ilike','%inventory%');
            })
            ->latest('entry_date')
            ->limit($limit)
            ->get();
    }

    protected function accountSummary(): array
    {
        $accounts=['Inventory Asset','Project Material Cost','Material Receivables','Cost of Goods Sold','Material Sales Revenue','Supplier Payable','Payment Voucher Clearing','Receipt Voucher Clearing'];
        return collect($accounts)->map(function($account){
            $debit = Schema::hasTable('general_ledgers') ? (float) GeneralLedger::where('account_name',$account)->sum('debit') : 0;
            $credit = Schema::hasTable('general_ledgers') ? (float) GeneralLedger::where('account_name',$account)->sum('credit') : 0;
            return ['account'=>$account,'debit'=>$debit,'credit'=>$credit,'balance'=>$debit-$credit];
        })->toArray();
    }
}
