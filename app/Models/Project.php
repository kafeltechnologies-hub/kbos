<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'client_id',
        'cost_center_id',
        'project_code',
        'project_name',
        'project_type',
        'location',
        'contract_amount',
        'budget_amount',
        'expected_end_date',
        'actual_end_date',
        'status',
        'description',
        'project_type',
        'company_id',
        'client_id',
        'start_date',
        'end_date',
        'duration_days',
        'contract_amount',
        'estimated_cost',
        'budget_amount',
        'expected_profit',
        'profit_margin',
        'priority',
        'scope_summary',
        'objectives',
        'location',
        'notes',
        'project_manager',
        'site_engineer',
        'client_representative',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function phases()
    {
        return $this->hasMany(ProjectPhase::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function wbs()
    {
        return $this->hasMany(ProjectWbs::class);
    }

    public function costs()
    {
        return $this->hasMany(CostEntry::class);
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }

    public function wbsItems()
    {
        return $this->hasMany(ProjectWbsItem::class);
    }

    public function deliverables()
    {
        return $this->hasMany(ProjectDeliverable::class);
    }

    public function projectMaterials()
    {
        return $this->hasMany(ProjectMaterial::class);
    }

    public function budgetLines()
    {
        return $this->hasMany(ProjectBudgetLine::class);
    }
    }