<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WbsTemplate extends Model
{
    protected $fillable = [
        'project_type',
        'wbs_code',
        'title',
        'description',
        'sort_order',
        'active',
    ];
}