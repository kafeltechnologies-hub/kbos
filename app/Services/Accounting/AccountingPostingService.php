<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\GeneralLedger;
use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
use App\Models\InvoiceVoucher;
use Illuminate\Support\Facades\DB;

class AccountingPostingService
{
    public function postPayment(PaymentVoucher $voucher): void
    {
        DB::transaction(function () use ($voucher) {
            $this->deleteExisting($voucher->voucher_number, 'payment_voucher');

            $expenseAccount = $voucher->category?->account_id
                ? $voucher->category->account
                : Account::where('account_code', '6900')->first();

            $bankAccount = $this->cashAccount($voucher->payment_method);

            GeneralLedger::create([
                'posting_date' => $voucher->voucher_date,
                'account_id' => $expenseAccount->id,
                'reference_no' => $voucher->voucher_number,
                'reference_type' => 'payment_voucher',
                'description' => $voucher->narration,
                'debit' => $voucher->gross_amount,
                'credit' => 0,
            ]);

            GeneralLedger::create([
                'posting_date' => $voucher->voucher_date,
                'account_id' => $bankAccount->id,
                'reference_no' => $voucher->voucher_number,
                'reference_type' => 'payment_voucher',
                'description' => 'Payment to ' . $voucher->payee_name,
                'debit' => 0,
                'credit' => $voucher->net_payment,
            ]);
        });
    }

    public function postJournal(JournalEntry $journal): void
        {
            DB::transaction(function () use ($journal) {
                $this->deleteExisting($journal->journal_number, 'manual_journal');

                foreach ($journal->lines as $line) {
                    GeneralLedger::create([
                        'posting_date' => $journal->journal_date,
                        'account_id' => $line->account_id,
                        'reference_no' => $journal->journal_number,
                        'reference_type' => 'manual_journal',
                        'description' => $line->description ?: $journal->narration,
                        'debit' => $line->debit,
                        'credit' => $line->credit,
                    ]);
                }
            });
        }
        
    public function postReceipt(ReceiptVoucher $voucher): void
    {
        DB::transaction(function () use ($voucher) {
            $this->deleteExisting($voucher->receipt_number, 'receipt_voucher');

            $incomeAccount = Account::where('account_code', '4100')->first();
            $bankAccount = $this->cashAccount($voucher->receipt_method);

            GeneralLedger::create([
                'posting_date' => $voucher->receipt_date,
                'account_id' => $bankAccount->id,
                'reference_no' => $voucher->receipt_number,
                'reference_type' => 'receipt_voucher',
                'description' => 'Receipt from ' . $voucher->payer_name,
                'debit' => $voucher->amount_received,
                'credit' => 0,
            ]);

            GeneralLedger::create([
                'posting_date' => $voucher->receipt_date,
                'account_id' => $incomeAccount->id,
                'reference_no' => $voucher->receipt_number,
                'reference_type' => 'receipt_voucher',
                'description' => $voucher->narration,
                'debit' => 0,
                'credit' => $voucher->amount_received,
            ]);
        });
    }

    public function postInvoice(InvoiceVoucher $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $this->deleteExisting($invoice->invoice_number, 'invoice_voucher');

            $receivable = Account::where('account_code', '1200')->first();
            $revenue = Account::where('account_code', '4100')->first();

            GeneralLedger::create([
                'posting_date' => $invoice->invoice_date,
                'account_id' => $receivable->id,
                'reference_no' => $invoice->invoice_number,
                'reference_type' => 'invoice_voucher',
                'description' => 'Invoice to ' . $invoice->client_name,
                'debit' => $invoice->grand_total,
                'credit' => 0,
            ]);

            GeneralLedger::create([
                'posting_date' => $invoice->invoice_date,
                'account_id' => $revenue->id,
                'reference_no' => $invoice->invoice_number,
                'reference_type' => 'invoice_voucher',
                'description' => $invoice->project_title,
                'debit' => 0,
                'credit' => $invoice->grand_total,
            ]);
        });
    }

    private function cashAccount(?string $method): Account
    {
        return match ($method) {
            'Cash' => Account::where('account_code', '1110')->first(),
            'Mobile Money' => Account::where('account_code', '1130')->first(),
            default => Account::where('account_code', '1120')->first(),
        };
    }

    private function deleteExisting(string $referenceNo, string $referenceType): void
    {
        GeneralLedger::where('reference_no', $referenceNo)
            ->where('reference_type', $referenceType)
            ->delete();
    }
}