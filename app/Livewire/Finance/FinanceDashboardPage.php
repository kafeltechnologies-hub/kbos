<?php

namespace App\Livewire\Finance;

use App\Models\GeneralLedger;
use App\Models\InvoiceVoucher;
use App\Models\PaymentVoucher;
use App\Models\Project;
use App\Models\ReceiptVoucher;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class FinanceDashboardPage extends Component
{
    public function render()
    {
        $totalProjects = Project::count();

        $contractValue = Project::sum('contract_amount');

        $totalPayments = PaymentVoucher::whereNotIn('status', ['cancelled', 'draft'])
            ->sum('gross_amount');

        $totalReceipts = ReceiptVoucher::whereNotIn('status', ['cancelled', 'draft'])
            ->sum('amount_received');

        $totalInvoices = InvoiceVoucher::whereNotIn('status', ['cancelled', 'draft'])
            ->sum('grand_total');

        $glEntries = Schema::hasTable('general_ledgers')
            ? GeneralLedger::count()
            : 0;

        $outstandingReceivables = max((float) $totalInvoices - (float) $totalReceipts, 0);

        $netCashPosition = (float) $totalReceipts - (float) $totalPayments;

        $outstandingProjectValue = max((float) $contractValue - (float) $totalReceipts, 0);

        return view('livewire.finance.finance-dashboard-page', compact(
            'totalProjects',
            'contractValue',
            'totalPayments',
            'totalReceipts',
            'totalInvoices',
            'outstandingReceivables',
            'netCashPosition',
            'outstandingProjectValue',
            'glEntries'
        ))->layout('layouts.erp');
    }
}