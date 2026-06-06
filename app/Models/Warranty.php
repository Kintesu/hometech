<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $table = 'warranties';

    protected $fillable = [
        'product_serial_id',
        'order_id',
        'user_id',
        'customer_phone',
        'purchase_date',
        'activation_date',
        'warranty_months',
        'status',
        'service_status',
        'service_received_at',
    ];

    public function productSerial()
    {
        return $this->belongsTo(ProductSerial::class, 'product_serial_id');
    }
}
