<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTransactionLine extends Model
{
    protected $fillable = [
        'transaction_id',
        'material_id',
        'quantity',
        'unit_cost',
        'line_total',
    ];

    public function transaction()
    {
        return $this->belongsTo(MaterialTransaction::class, 'transaction_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}