<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MaterialTransaction;

class MaterialIssuePrintController extends Controller
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
            'waybill',
            'approvedBy',
        ]);

        $company = Company::first();

        return view('projects.prints.material-issue', compact(
            'transaction',
            'company'
        ));
    }
}