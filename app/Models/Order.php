<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = false; // Tắt created_at, updated_at

    // 1 Đơn hàng thuộc về 1 Khách hàng (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 1 Đơn hàng có nhiều Chi tiết đơn hàng
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}