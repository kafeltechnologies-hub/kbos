<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\MaterialWaybill;

class MaterialWaybillPrintController extends Controller
{
    public function show(MaterialWaybill $waybill)
    {
        $waybill->load([
            'transaction.project',
            'transaction.lines.material',
        ]);

        return view(
            'projects.prints.material-waybill',
            compact('waybill')
        );
    }
}