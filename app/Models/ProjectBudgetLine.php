<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBudgetLine extends Model
{
    protected $fillable = [
        'project_id',
        'budget_category',
        'description',
        'estimated_amount',
        'actual_amount',
        'variance_amount',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}