<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentVoucher;

class PaymentVoucherPrintController extends Controller
{
    public function show(PaymentVoucher $voucher)
    {
        $voucher->load(['project', 'category']);

        return view('prints.payment-voucher', compact('voucher'));
    }
}