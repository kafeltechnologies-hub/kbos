<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = [
        'category_code',
        'name',
        'description',
        'active',
    ];

    public function receiptVouchers()
    {
        return $this->hasMany(ReceiptVoucher::class);
    }
}