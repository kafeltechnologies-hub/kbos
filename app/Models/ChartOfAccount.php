<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
        protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'description',
        'active',

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}