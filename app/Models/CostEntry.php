<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostEntry extends Model
{
    protected $fillable = [
        'company_id',
        'project_id',
        'cost_center_id',
        'cost_code',
        'cost_type',
        'source_type',
        'source_id',
        'description',
        'amount',
        'cost_date',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}