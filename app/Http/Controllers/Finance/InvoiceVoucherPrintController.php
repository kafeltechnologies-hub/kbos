<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\InvoiceVoucher;

class InvoiceVoucherPrintController extends Controller
{
    public function show(InvoiceVoucher $invoice)
    {
        $invoice->load([
            'items',
            'items.material',
            'project',
            'client',
            'company',
            'quotation',
        ]);

        return view('prints.invoice-voucher', compact('invoice'));
    }
}