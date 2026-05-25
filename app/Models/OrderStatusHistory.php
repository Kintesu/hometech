<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'changed_by',
        'from_status',
        'to_status',
        'reason',
        'created_at',
    ];
}
