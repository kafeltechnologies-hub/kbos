<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\MaterialTransaction;

class MaterialReceiptPrintController extends Controller
{
    public function show(MaterialTransaction $transaction)
    {
        $transaction->load([
            'project',
            'lines.material',
        ]);

        abort_if(
            $transaction->transaction_type !== 'receive',
            404
        );

        return view(
            'projects.prints.material-receipt',
            compact('transaction')
        );
    }
}