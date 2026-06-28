<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceParty extends Model
{
    protected $fillable = [
        'party_code',
        'party_type',
        'name',
        'contact_person',
        'phone',
        'phone2',
        'email',
        'tin',
        'vat_number',
        'registration_number',
        'gps_address',
        'postal_address',
        'physical_address',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'momo_number',
        'opening_balance',
        'currency',
        'active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'party_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}