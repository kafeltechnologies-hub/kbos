<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\ProjectQuotation;

class QuotationPrintController extends Controller
{
    public function show(ProjectQuotation $quotation)
    {
        $quotation->load(['company', 'client', 'project', 'items']);

        return view('prints.project-quotation', compact('quotation'));
    }
}