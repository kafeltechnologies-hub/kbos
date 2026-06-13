<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\ProjectReceipt;

class ReceiptPrintController extends Controller
{
    public function show(ProjectReceipt $receipt)
    {
        $receipt->load('project');

        return view('prints.project-receipt', compact('receipt'));
    }
}