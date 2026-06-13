<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'code',
        'name',
        'registration_number',
        'tin',
        'vat_number',
        'ssnit_number',
        'email',
        'phone',
        'address',
        'country',
        'active',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}