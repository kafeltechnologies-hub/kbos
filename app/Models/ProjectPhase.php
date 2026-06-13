<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id',
        'phase_code',
        'phase_name',
        'description',
        'start_date',
        'end_date',
        'responsible_person',
        'budget_amount',
        'actual_cost',
        'progress_percent',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
