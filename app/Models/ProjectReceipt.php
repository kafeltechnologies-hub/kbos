<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReceipt extends Model
{
    protected $fillable = [
    'project_id',
    'receipt_code',
    'receipt_number',
    'contract_value',
    'total_received_before',
    'amount_received',
    'outstanding_balance',
    'date_received',
    'receipt_method',
    'bank_name',
    'bank_account',
    'cheque_number',
    'momo_number',
    'transaction_reference',
    'received_from',
    'payer_phone',
    'payer_tin',
    'receipt_narration',
    'remarks',
    'prepared_by',
    'approved_by',
    'status',
    'amount_in_words',

    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}