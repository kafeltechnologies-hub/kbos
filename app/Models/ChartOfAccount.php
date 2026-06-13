<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'company_id',
        'account_code',
        'account_name',
        'account_type',
        'active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}