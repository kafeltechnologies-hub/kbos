<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\CostEntry;
use App\Models\InvoiceVoucher;
use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\ReceiptVoucher;

class ProjectReportController extends Controller
{
    public function show(Project $project)
    {
        $project->load([
            'company',
            'client',
            'phases',
            'wbsItems',
            'deliverables',
            'projectMaterials.material',
            'budgetLines',
        ]);

        $costEntries = CostEntry::where('project_id', $project->id)
            ->latest()
            ->get();

        $payments = PaymentVoucher::where('project_id', $project->id)
            ->latest()
            ->get();

        $receipts = ReceiptVoucher::where('project_id', $project->id)
            ->latest()
            ->get();

        $invoices = InvoiceVoucher::where('project_id', $project->id)
            ->latest()
            ->get();

        $contractValue = (float) $project->contract_amount;
        $estimatedCost = (float) $project->estimated_cost;

        $materialTotal = $project->projectMaterials->sum('line_total');
        $budgetTotal = $project->budgetLines->sum('estimated_amount');
        $actualBudgetCost = $project->budgetLines->sum('actual_amount');
        $costEntryTotal = $costEntries->sum('amount');

        $totalPayments = $payments->sum('gross_amount');
        $totalReceipts = $receipts->sum('amount_received');
        $totalInvoices = $invoices->sum('grand_total');

        $profit = $contractValue - $costEntryTotal;
        $cashBalance = $totalReceipts - $totalPayments;
        $outstandingContractBalance = $contractValue - $totalReceipts;
        $receivableBalance = $totalInvoices - $totalReceipts;

        return view('projects.report-print', compact(
            'project',
            'costEntries',
            'payments',
            'receipts',
            'invoices',
            'contractValue',
            'estimatedCost',
            'materialTotal',
            'budgetTotal',
            'actualBudgetCost',
            'costEntryTotal',
            'totalPayments',
            'totalReceipts',
            'totalInvoices',
            'profit',
            'cashBalance',
            'outstandingContractBalance',
            'receivableBalance'
        ));
    }
}