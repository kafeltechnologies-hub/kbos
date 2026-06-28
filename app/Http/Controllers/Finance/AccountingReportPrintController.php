<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\GeneralLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AccountingReportPrintController extends Controller
{
    public function show(Request $request)
    {
        $type = $request->get('type', 'general_ledger');

        $entries = Schema::hasTable('general_ledgers')
            ? GeneralLedger::query()
                ->when($type === 'inventory_asset', fn($q) => $q->where('account_name', 'Inventory Asset'))
                ->when($type === 'project_material_cost', fn($q) => $q->where('account_name', 'Project Material Cost'))
                ->when($type === 'material_receivables', fn($q) => $q->where('account_name', 'Material Receivables'))
                ->when($type === 'material_sales_revenue', fn($q) => $q->where('account_name', 'Material Sales Revenue'))
                ->when($type === 'cost_of_goods_sold', fn($q) => $q->where('account_name', 'Cost of Goods Sold'))
                ->when($type === 'material_finance_postings', fn($q) => $q->where('source_module', 'materials'))
                ->when($request->account_name, fn($q) => $q->where('account_name', $request->account_name))
                ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
                ->when($request->date_from, fn($q) => $q->whereDate('entry_date', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('entry_date', '<=', $request->date_to))
                ->latest()
                ->get()
            : collect();

        $company = Company::first();

        $reportTitle = match ($type) {
            'inventory_asset' => 'Inventory Asset Ledger',
            'project_material_cost' => 'Project Material Cost Ledger',
            'material_receivables' => 'Material Receivables Ledger',
            'material_sales_revenue' => 'Material Sales Revenue Report',
            'cost_of_goods_sold' => 'Cost of Goods Sold Report',
            'material_finance_postings' => 'Material Finance Postings Report',
            default => 'General Ledger',
        };

        $totalDebit = (float) $entries->sum(fn($e) => (float)($e->debit ?? $e->debit_amount ?? 0));
        $totalCredit = (float) $entries->sum(fn($e) => (float)($e->credit ?? $e->credit_amount ?? 0));

        return view('finance.prints.accounting-report', compact('entries','company','reportTitle','totalDebit','totalCredit'));
    }
}
