<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'category_id',
        'material_code',
        'name',
        'description',
        'unit',
        'standard_price',
        'selling_price',
        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'barcode',
        'image_path',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function transactionLines()
    {
        return $this->hasMany(MaterialTransactionLine::class);
    }
}
