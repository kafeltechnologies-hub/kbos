<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceDocument extends Model
{
    protected $fillable = [
        'document_type',
        'document_no',
        'document_date',
        'project_id',
        'customer_name',
        'service_description',
        'materials_total',
        'labour_cost',
        'transport_cost',
        'other_cost',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'grand_total',
        'source_quotation_no',
        'status',
        'narration',
        'created_by',
        'approved_by',
        'approved_at',
        'customer_address',
        'customer_phone',
        'customer_email',
        'contact_person',
        'amount_in_words',
        'currency',
        'valid_until',
        'reviewed_by',
        'reviewed_at',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'approved_at' => 'datetime',
        'materials_total' => 'decimal:2',
        'labour_cost' => 'decimal:2',
        'transport_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'valid_until' => 'date',
        'reviewed_at' => 'datetime',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(FinanceDocumentLine::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function payments()
    {
        return $this->hasMany(FinancePayment::class);
    }

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'finance_document_id');
    }
}