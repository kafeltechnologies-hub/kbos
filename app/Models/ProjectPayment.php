<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $fillable = [
    'project_id',
    'payment_code',
    'voucher_number',
    'voucher_type',
    'gross_amount',
    'amount_paid',
    'vat_applicable',
    'vat_amount',
    'getfund_amount',
    'nhil_amount',
    'net_amount',
    'payment_date',
    'payment_method',
    'payment_narration',
    'payee_name',
    'payee_type',
    'payee_account',
    'payee_phone',
    'payee_tin',
    'bank_name',
    'bank_account',
    'cheque_number',
    'momo_number',
    'transaction_reference',
    'prepared_by',
    'checked_by',
    'approved_by',
    'payment_purpose',
    'remarks',
];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}