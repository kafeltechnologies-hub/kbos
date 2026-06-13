<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_number',
        'journal_date',
        'reference_no',
        'narration',
        'total_debit',
        'total_credit',
        'status',
        'prepared_by',
        'approved_by',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }
}