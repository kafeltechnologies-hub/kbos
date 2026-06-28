<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancePayment extends Model
{
    protected $fillable = [
        'payment_type',
        'transaction_category',
        'transaction_subtype',
        'payment_no',
        'payment_date',
        'finance_document_id',
        'project_id',
        'budget_id',
        'party_name',
        'lender_name',
        'interest_rate',
        'interest_period',
        'loan_start_date',
        'loan_due_date',
        'purpose',
        'payment_method',
        'cash_account_id',
        'debit_account_id',
        'credit_account_id',
        'gross_amount',
        'apply_wht',
        'wht_rate',
        'wht_amount',
        'net_amount',
        'amount_in_words',
        'external_reference',
        'narration',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'loan_start_date' => 'date',
        'loan_due_date' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'gross_amount' => 'decimal:2',
        'wht_rate' => 'decimal:2',
        'wht_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'apply_wht' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(FinanceDocument::class, 'finance_document_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}