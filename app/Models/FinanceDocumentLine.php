<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceDocumentLine extends Model
{
    protected $fillable = [
        'finance_document_id',
        'material_id',
        'line_type',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function document()
    {
        return $this->belongsTo(FinanceDocument::class, 'finance_document_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}