<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceVoucher extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'project_id',
        'client_id',
        'company_id',
        'project_quotation_id',
        'client_name',
        'client_phone',
        'client_email',
        'client_tin',
        'client_address',
        'project_title',
        'scope_of_work',
        'contract_value',
        'previous_invoices',
        'outstanding_before_invoice',
        'subtotal',
        'labor_charge',
        'transport_charge',
        'other_charges',
        'other_charges_description',
        'vat_applicable',
        'vat_amount',
        'getfund_amount',
        'nhil_amount',
        'grand_total',
        'balance_after_invoice',
        'amount_in_words',
        'terms_and_conditions',
        'notes',
        'status',
        'prepared_by',
        'checked_by',
        'approved_by',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceVoucherItem::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function quotation()
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }
}