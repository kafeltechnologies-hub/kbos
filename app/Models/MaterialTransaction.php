<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTransaction extends Model
{
    protected $fillable = [
        'transaction_no',
        'transaction_type',
        'project_id',
        'transaction_date',
        'reference',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function lines()
    {
        return $this->hasMany(MaterialTransactionLine::class, 'transaction_id');
    }

    public function waybill()
    {
        return $this->hasOne(MaterialWaybill::class, 'transaction_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}