<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectQuotation extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'project_id',
        'quotation_code',
        'quotation_number',
        'quotation_date',
        'valid_until',
        'client_name',
        'client_phone',
        'client_email',
        'client_tin',
        'client_address',
        'project_title',
        'scope_of_work',
        'subtotal',
        'vat_applicable',
        'vat_amount',
        'getfund_amount',
        'nhil_amount',
        'grand_total',
        'amount_in_words',
        'terms_and_conditions',
        'notes',
        'prepared_by',
        'approved_by',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(ProjectQuotationItem::class);
    }
}