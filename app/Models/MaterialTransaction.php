<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\MaterialTransactionLine;
use App\Models\MaterialWaybill;
use App\Models\User;

class MaterialTransaction extends Model
{
    protected $fillable = [
        'transaction_no',
        'transaction_type',
        'project_id',
        'transaction_date',
        'reference',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
        'payment_voucher_id',
        'from_project_id',
        'to_project_id',
        'account_holder_name',
        'account_holder_phone',
        'expected_return_date',
        'receipt_voucher_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function lines()
    {
        return $this->hasMany(MaterialTransactionLine::class, 'transaction_id');
    }

    public function waybill()
    {
        return $this->hasOne(MaterialWaybill::class, 'transaction_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paymentVoucher()
    {
        return $this->belongsTo(\App\Models\PaymentVoucher::class, 'payment_voucher_id');
    }

    public function receiptVoucher()
    {
        return $this->belongsTo(\App\Models\ReceiptVoucher::class, 'receipt_voucher_id');
    }

    public function fromProject()
    {
        return $this->belongsTo(Project::class, 'from_project_id');
    }

    public function toProject()
    {
        return $this->belongsTo(Project::class, 'to_project_id');
    }
}