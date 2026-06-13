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

use App\Livewire\Finance\FinanceDashboardPage;
use App\Livewire\Finance\PaymentCentrePage;
use App\Livewire\Finance\ReceiptCentrePage;
use App\Livewire\Finance\InvoiceCentrePage;
use App\Livewire\Finance\JournalEntriesPage;
use App\Livewire\Finance\AccountingReportsPage;

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

Route::prefix('finance')->name('finance.')->group(function () {

    Route::get('/dashboard', FinanceDashboardPage::class)
        ->name('dashboard');

    Route::get('/payment-centre', PaymentCentrePage::class)
        ->name('payment-centre');

    Route::get('/payment-vouchers/{voucher}/print', [PaymentVoucherPrintController::class, 'show'])
        ->name('payment-vouchers.print');

    Route::get('/receipt-centre', ReceiptCentrePage::class)
        ->name('receipt-centre');

    Route::get('/receipt-vouchers/{voucher}/print', [ReceiptVoucherPrintController::class, 'show'])
        ->name('receipt-vouchers.print');

    Route::get('/invoice-centre', InvoiceCentrePage::class)
        ->name('invoice-centre');

    Route::get('/invoices/{invoice}/print', [InvoiceVoucherPrintController::class, 'show'])
        ->name('invoices.print');

    Route::get('/journal-entries', JournalEntriesPage::class)
        ->name('journal-entries');

    Route::get('/accounting-reports', AccountingReportsPage::class)
        ->name('accounting-reports');
});

/*
|--------------------------------------------------------------------------
| Project Routes
|--------------------------------------------------------------------------
*/

Route::prefix('projects')->name('projects.')->group(function () {

    Route::get('/', ProjectsPage::class)
        ->name('index');

    Route::get('/dashboard', ProjectDashboardPage::class)
        ->name('dashboard');

    Route::get('/project-centre/{projectId?}', ProjectCentrePage::class)
        ->name('project-centre');

    Route::get('/cost-entries', CostEntriesPage::class)
        ->name('cost-entries');

    Route::get('/payments', ProjectPaymentsPage::class)
        ->name('payments');

    Route::get('/receipts', ProjectReceiptsPage::class)
        ->name('receipts');

    Route::get('/receipts/{receipt}/print', [ReceiptPrintController::class, 'show'])
        ->name('receipts.print');

    Route::get('/quotations', ProjectQuotationsPage::class)
        ->name('quotations');

    Route::get('/quotations/{quotation}/print', [QuotationPrintController::class, 'show'])
        ->name('quotations.print');

    Route::get('/{project}/report', [ProjectReportController::class, 'show'])
        ->name('report');

    /*
    |--------------------------------------------------------------------------
    | Materials / Inventory Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/materials', ProjectMaterialsPage::class)
        ->name('materials');

    Route::get('/materials/transactions/{transaction}/receipt', [MaterialReceiptPrintController::class, 'show'])
        ->name('materials.receipt.print');

    Route::get('/materials/transactions/{transaction}/issue', [MaterialIssuePrintController::class, 'show'])
        ->name('materials.issue.print');

    Route::get('/materials/waybills/{waybill}/print', [MaterialWaybillPrintController::class, 'show'])
        ->name('materials.waybill.print');


    Route::get('/materials/reports/print', [InventoryReportPrintController::class, 'show'])
        ->name('materials.reports.print');
});