<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Project Module
|--------------------------------------------------------------------------
*/

use App\Livewire\Projects\ProjectsPage;
use App\Livewire\Projects\ProjectCentrePage;
use App\Livewire\Projects\CostEntriesPage;
use App\Livewire\Projects\ProjectPaymentsPage;
use App\Livewire\Projects\ProjectReceiptsPage;
use App\Livewire\Projects\ProjectQuotationsPage;
use App\Livewire\Projects\ProjectDashboardPage;
use App\Livewire\Projects\ProjectMaterialsPage;

use App\Http\Controllers\Projects\ProjectReportController;
use App\Http\Controllers\Projects\ReceiptPrintController;
use App\Http\Controllers\Projects\QuotationPrintController;
use App\Http\Controllers\Projects\MaterialReceiptPrintController;
use App\Http\Controllers\Projects\MaterialIssuePrintController;
use App\Http\Controllers\Projects\MaterialWaybillPrintController;
use App\Http\Controllers\Projects\InventoryReportPrintController;

/*
|--------------------------------------------------------------------------
| Finance Module
|--------------------------------------------------------------------------
*/

use App\Livewire\Finance\FinanceCentrePage;
use App\Livewire\Finance\FinanceDashboardPage;
use App\Livewire\Finance\FinanceOperationsPage;
use App\Livewire\Finance\GeneralLedgerPage;
use App\Livewire\Finance\TrialBalancePage;
use App\Livewire\Finance\ProfitAndLossPage;
use App\Livewire\Finance\BalanceSheetPage;
use App\Livewire\Finance\CashFlowPage;
use App\Livewire\Finance\BudgetCentrePage;
use App\Livewire\Finance\FixedAssetsPage;
use App\Livewire\Finance\TaxCentrePage;
use App\Livewire\Finance\BankReconciliationPage;
use App\Livewire\Finance\FinanceSettingsPage;
use App\Livewire\Finance\AccountingReportsPage;
use App\Livewire\Finance\PaymentCentrePage;
use App\Livewire\Finance\ReceiptCentrePage;
use App\Livewire\Finance\InvoiceCentrePage;
use App\Livewire\Finance\JournalEntriesPage;
use App\Livewire\Finance\ChartOfAccountsPage;

use App\Http\Controllers\Finance\FinancePrintController;
use App\Http\Controllers\Finance\FinanceOperationsPrintController;
use App\Http\Controllers\Finance\AccountingReportPrintController;
use App\Http\Controllers\Finance\PaymentVoucherPrintController;
use App\Http\Controllers\Finance\ReceiptVoucherPrintController;
use App\Http\Controllers\Finance\InvoiceVoucherPrintController;

/*
|--------------------------------------------------------------------------
| Default Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('finance.dashboard');
})->name('home');

/*
|--------------------------------------------------------------------------
| Finance Routes
|--------------------------------------------------------------------------
*/

Route::prefix('finance')
    ->name('finance.')
    ->group(function () {

        Route::get('/centre', FinanceCentrePage::class)->name('centre');
        Route::get('/dashboard', FinanceDashboardPage::class)->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Finance Operations Centre
        |--------------------------------------------------------------------------
        */

        Route::get('/operations', FinanceOperationsPage::class)->name('operations');

        Route::prefix('operations/print')
            ->name('operations.')
            ->group(function () {
                Route::get('/quotation/{documentNo}', [FinanceOperationsPrintController::class, 'quotation'])
                    ->name('quotation.print');

                Route::get('/invoice/{documentNo}', [FinanceOperationsPrintController::class, 'invoice'])
                    ->name('invoice.print');

                Route::get('/receipt/{paymentNo}', [FinanceOperationsPrintController::class, 'receipt'])
                    ->name('receipt.print');

                Route::get('/payment/{paymentNo}', [FinanceOperationsPrintController::class, 'paymentVoucher'])
                    ->name('payment.print');

                Route::get('/loan/{paymentNo}', [FinanceOperationsPrintController::class, 'loanReceipt'])
                    ->name('loan.print');

                Route::get('/transfer/{paymentNo}', [FinanceOperationsPrintController::class, 'fundTransfer'])
                    ->name('transfer.print');
            });

        /*
        |--------------------------------------------------------------------------
        | Core Finance Pages
        |--------------------------------------------------------------------------
        */

        Route::get('/general-ledger', GeneralLedgerPage::class)->name('general-ledger');
        Route::get('/trial-balance', TrialBalancePage::class)->name('trial-balance');
        Route::get('/profit-and-loss', ProfitAndLossPage::class)->name('profit-and-loss');
        Route::get('/balance-sheet', BalanceSheetPage::class)->name('balance-sheet');
        Route::get('/cash-flow', CashFlowPage::class)->name('cash-flow');
        Route::get('/budgets', BudgetCentrePage::class)->name('budgets');
        Route::get('/fixed-assets', FixedAssetsPage::class)->name('fixed-assets');
        Route::get('/tax-centre', TaxCentrePage::class)->name('tax-centre');
        Route::get('/bank-reconciliation', BankReconciliationPage::class)->name('bank-reconciliation');
        Route::get('/settings', FinanceSettingsPage::class)->name('settings');
        Route::get('/accounting-reports', AccountingReportsPage::class)->name('accounting-reports');

        /*
        |--------------------------------------------------------------------------
        | Legacy / Existing Finance Pages
        |--------------------------------------------------------------------------
        */

        Route::get('/payments', PaymentCentrePage::class)->name('payments');
        Route::get('/receipts', ReceiptCentrePage::class)->name('receipts');
        Route::get('/invoices', InvoiceCentrePage::class)->name('invoices');
        Route::get('/journals', JournalEntriesPage::class)->name('journals');
        Route::get('/chart-of-accounts', ChartOfAccountsPage::class)->name('chart-of-accounts');

        /*
        |--------------------------------------------------------------------------
        | Finance Print Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/print', [FinancePrintController::class, 'ledger'])->name('print');

        Route::get('/accounting-reports/print', [AccountingReportPrintController::class, 'show'])
            ->name('accounting-reports.print');

        Route::get('/payments/{voucher}/print', [PaymentVoucherPrintController::class, 'show'])
            ->name('payments.print');

        Route::get('/receipts/{receipt}/print', [ReceiptVoucherPrintController::class, 'show'])
            ->name('receipts.print');

        Route::get('/invoices/{invoice}/print', [InvoiceVoucherPrintController::class, 'show'])
            ->name('invoices.print');
    });

/*
|--------------------------------------------------------------------------
| Project Routes
|--------------------------------------------------------------------------
*/

Route::prefix('projects')
    ->name('projects.')
    ->group(function () {

        Route::get('/', ProjectsPage::class)->name('index');

        Route::get('/dashboard', ProjectDashboardPage::class)->name('dashboard');

        Route::get('/project-centre/{projectId?}', ProjectCentrePage::class)
            ->name('project-centre');

        Route::get('/cost-entries', CostEntriesPage::class)->name('cost-entries');

        Route::get('/payments', ProjectPaymentsPage::class)->name('payments');

        Route::get('/receipts', ProjectReceiptsPage::class)->name('receipts');

        Route::get('/receipts/{receipt}/print', [ReceiptPrintController::class, 'show'])
            ->name('receipts.print');

        Route::get('/quotations', ProjectQuotationsPage::class)->name('quotations');

        Route::get('/quotations/{quotation}/print', [QuotationPrintController::class, 'show'])
            ->name('quotations.print');

        Route::get('/{project}/report', [ProjectReportController::class, 'show'])
            ->name('report');

        /*
        |--------------------------------------------------------------------------
        | Materials / Inventory Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/materials', ProjectMaterialsPage::class)->name('materials');

        Route::get('/materials/transactions/{transaction}/receipt', [MaterialReceiptPrintController::class, 'show'])
            ->name('materials.receipt.print');

        Route::get('/materials/transactions/{transaction}/issue', [MaterialIssuePrintController::class, 'show'])
            ->name('materials.issue.print');

        Route::get('/materials/waybills/{waybill}/print', [MaterialWaybillPrintController::class, 'show'])
            ->name('materials.waybill.print');

        Route::get('/materials/reports/print', [InventoryReportPrintController::class, 'show'])
            ->name('materials.reports.print');
    });