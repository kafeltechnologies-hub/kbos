<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\MaterialTransaction;

class MaterialIssuePrintController extends Controller
{
    public function show(MaterialTransaction $transaction)
    {
        $transaction->load([
            'project',
            'lines.material',
            'waybill',
        ]);

        return view(
            'projects.prints.material-issue',
            compact('transaction')
        );
    }
}