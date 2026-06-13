<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_no',
        'company_id',
        'branch_id',
        'department_id',
        'position_id',
        'cost_center_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'ghana_card_no',
        'ssnit_no',
        'tin',
        'hire_date',
        'employment_type',
        'monthly_salary',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}