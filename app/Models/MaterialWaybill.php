<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialWaybill extends Model
{
    protected $fillable = [
        'waybill_no',
        'transaction_id',
        'transporter_name',
        'driver_name',
        'driver_phone',
        'vehicle_number',
        'delivery_location',
        'loaded_by',
        'received_by',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(MaterialTransaction::class, 'transaction_id');
    }
}