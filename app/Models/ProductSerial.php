<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    protected $table = 'product_serials';

    protected $fillable = [
        'product_id',
        'serial_number',
        'distributed_by',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
