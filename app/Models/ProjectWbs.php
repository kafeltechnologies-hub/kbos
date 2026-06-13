<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWbs extends Model
{
    protected $table = 'project_wbs';

    protected $fillable = [
        'project_id',
        'code',
        'name',
        'description',
        'budget_amount',
        'actual_cost',
        'start_date',
        'end_date',
        'progress_percentage',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}