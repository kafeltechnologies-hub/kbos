<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $fillable = [

        'voucher_number',
        'voucher_date',

        'payment_type',

        'project_id',
        'expense_category_id',

        'payee_name',

        'payment_method',
        'reference_no',

        'narration',

        'gross_amount',

        'vat_applicable',
        'vat_amount',
        'getfund_amount',
        'nhil_amount',

        'withholding_tax',

        'net_payment',

        'status',

        'prepared_by',
        'approved_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }
}