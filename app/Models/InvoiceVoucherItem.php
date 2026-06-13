<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceVoucherItem extends Model
{
    protected $fillable = [
        'invoice_voucher_id',
        'material_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'line_total',
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoiceVoucher::class, 'invoice_voucher_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}