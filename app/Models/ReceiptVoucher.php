<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'receipt_type',
        'project_id',
        'income_category_id',
        'payer_name',
        'receipt_method',
        'reference_no',
        'project_value',
        'previous_receipts',
        'outstanding_before_receipt',
        'amount_received',
        'balance_after_receipt',
        'amount_in_words',
        'narration',
        'status',
        'prepared_by',
        'checked_by',
        'approved_by',
        'received_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }
}