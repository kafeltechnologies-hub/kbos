<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectQuotationItem extends Model
{
    protected $fillable = [
        'project_quotation_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'line_total',
        'material_id',
    ];

    public function quotation()
    {
        return $this->belongsTo(ProjectQuotation::class, 'project_quotation_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }


}