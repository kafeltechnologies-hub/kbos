<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ReceiptVoucher;

class ReceiptVoucherPrintController extends Controller
{
    public function show(ReceiptVoucher $voucher)
    {
        $voucher->load(['project', 'category']);

        return view('prints.receipt-voucher', compact('voucher'));
    }
}