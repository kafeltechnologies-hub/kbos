<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWbsItem extends Model
{
    protected $fillable = [
        'project_id',
        'wbs_code',
        'title',
        'description',
        'start_date',
        'end_date',
        'responsible_person',
        'budget_amount',
        'progress_percent',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}