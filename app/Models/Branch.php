<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'region',
        'phone',
        'address',
        'active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}