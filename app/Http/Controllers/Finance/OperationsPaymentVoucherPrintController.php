<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedger;

class OperationsPaymentVoucherPrintController extends Controller
{
    public function __invoke(string $reference)
    {
        $entries = GeneralLedger::query()
            ->where('reference', $reference)
            ->where('source_module', 'finance_operations')
            ->where('source_type', 'receipt')
            ->orderBy('id')
            ->get();

        abort_if($entries->isEmpty(), 404);

        return view('finance.print.operations-receipt', [
            'reference' => $reference,
            'entries' => $entries,
            'header' => $entries->first(),
            'total' => (float) $entries->sum('debit'),
        ]);
    }
}