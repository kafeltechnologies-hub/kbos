<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'transaction_category',
        'transaction_subtype',
        'reference_no',
        'reference_date',
        'finance_document_id',
        'party_id',
        'party_name',
        'project_id',
        'budget_id',
        'from_account_id',
        'to_account_id',
        'cash_account_id',
        'gross_amount',
        'discount_amount',
        'tax_amount',
        'wht_amount',
        'net_amount',
        'currency',
        'exchange_rate',
        'payment_method',
        'external_reference',
        'lender_name',
        'interest_rate',
        'interest_period',
        'loan_start_date',
        'loan_due_date',
        'amount_in_words',
        'narration',
        'status',
        'prepared_by',
        'prepared_at',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'posted_by',
        'posted_at',
        'reversed_by',
        'reversed_at',
        'reverse_reason',
        'print_count',
        'last_printed_at',
        'last_printed_by',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'loan_start_date' => 'date',
        'loan_due_date' => 'date',
        'prepared_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
        'last_printed_at' => 'datetime',
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'wht_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'interest_rate' => 'decimal:2',
    ];

    public function document()
    {
        return $this->belongsTo(FinanceDocument::class, 'finance_document_id');
    }

    public function party()
    {
        return $this->belongsTo(FinanceParty::class, 'party_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }
}