<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [

        'account_code',
        'account_name',

        'account_type',
        'account_group',

        'parent_id',
        'active',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class,'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class,'parent_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(GeneralLedger::class);
    }

    
}