<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralLedger extends Model
{
    protected $fillable = [
        'posting_date',
        'entry_date',
        'transaction_date',
        'date',

        'account_id',
        'account_code',
        'account_name',
        'account',

        'reference',
        'reference_no',
        'reference_type',

        'description',
        'narration',

        'debit',
        'credit',
        'debit_amount',
        'credit_amount',
        'amount',

        'project_id',

        'source_module',
        'source_type',
        'source_id',

        'status',
        'created_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'entry_date' => 'date',
        'transaction_date' => 'date',
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function chartAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}