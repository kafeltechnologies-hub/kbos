<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceDocument;
use App\Models\FinancePayment;
use Illuminate\Support\Facades\Schema;

class FinanceOperationsPrintController extends Controller
{
    private function company()
    {
        if (class_exists(\App\Models\FinanceSetting::class) && Schema::hasTable('finance_settings')) {
            return \App\Models\FinanceSetting::first();
        }

        if (class_exists(\App\Models\Company::class) && Schema::hasTable('companies')) {
            return \App\Models\Company::first();
        }

        return (object) [
            'company_name' => config('app.name', 'Kafel ERP'),
            'postal_address' => '',
            'physical_address' => '',
            'telephone' => '',
            'telephone2' => '',
            'email' => '',
            'website' => '',
            'tax_number' => '',
            'vat_number' => '',
            'prepared_by' => auth()->user()->name ?? 'System User',
            'reviewed_by' => '',
            'approved_by' => '',
            'report_footer' => 'Generated from Kafel ERP Finance Operations Centre',
        ];
    }

    public function quotation(string $documentNo)
    {
        $document = FinanceDocument::with(['lines', 'project'])
            ->where('document_type', 'quotation')
            ->where('document_no', $documentNo)
            ->firstOrFail();

        return view('finance.prints.quotation', [
            'document' => $document,
            'company' => $this->company(),
        ]);
    }

    public function invoice(string $documentNo)
    {
        $document = FinanceDocument::with(['lines', 'project'])
            ->where('document_type', 'invoice')
            ->where('document_no', $documentNo)
            ->firstOrFail();

        return view('finance.prints.invoice', [
            'document' => $document,
            'company' => $this->company(),
        ]);
    }

    public function receipt(string $paymentNo)
    {
        $payment = FinancePayment::with(['document', 'project', 'cashAccount'])
            ->where('payment_type', 'receipt')
            ->where('payment_no', $paymentNo)
            ->firstOrFail();

        return view('finance.prints.receipt', [
            'payment' => $payment,
            'company' => $this->company(),
        ]);
    }

    public function paymentVoucher(string $paymentNo)
    {
        $payment = FinancePayment::with(['document', 'project', 'cashAccount'])
            ->where('payment_type', 'payment')
            ->where('payment_no', $paymentNo)
            ->firstOrFail();

        return view('finance.prints.payment-voucher', [
            'payment' => $payment,
            'company' => $this->company(),
        ]);
    }

    public function loanReceipt(string $paymentNo)
    {
        $payment = FinancePayment::with(['document', 'project', 'cashAccount'])
            ->where('payment_type', 'loan')
            ->where('payment_no', $paymentNo)
            ->firstOrFail();

        return view('finance.prints.loan-receipt', [
            'payment' => $payment,
            'company' => $this->company(),
        ]);
    }

    public function fundTransfer(string $paymentNo)
    {
        $payment = FinancePayment::with(['cashAccount', 'creditAccount'])
            ->where('payment_type', 'transfer')
            ->where('payment_no', $paymentNo)
            ->firstOrFail();

        return view('finance.prints.fund-transfer', [
            'payment' => $payment,
            'company' => $this->company(),
        ]);
    }
}