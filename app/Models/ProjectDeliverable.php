<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDeliverable extends Model
{
    protected $fillable = [
        'project_id',
        'deliverable_name',
        'description',
        'due_date',
        'owner',
        'acceptance_criteria',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}