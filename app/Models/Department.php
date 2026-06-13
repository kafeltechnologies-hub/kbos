<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
            protected $fillable = [
            'company_id',
            'branch_id',
            'code',
            'name',
            'description',
            'active',
        ];

        public function company()
        {
            return $this->belongsTo(Company::class);
        }

        public function branch()
        {
            return $this->belongsTo(Branch::class);
        }

        public function positions()
        {
            return $this->hasMany(Position::class);
        }
}