<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MaterialTransaction;

class MaterialReceiptPrintController extends Controller
{
    public function show(MaterialTransaction $transaction)
    {
        $transaction->load([
            'project',
            'fromProject',
            'toProject',
            'paymentVoucher',
            'receiptVoucher',
            'lines.material',
            'approvedBy',
        ]);

        $company = Company::first();

        return view('projects.prints.material-receipt', compact(
            'transaction',
            'company'
        ));
    }
}