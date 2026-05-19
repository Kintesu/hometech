<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseReceiptDetail extends Model
{
    // Thêm dòng này để tắt tính năng lưu thời gian tự động
    public $timestamps = false; 
    
    // (Bên dưới là hàm product() bạn đã thêm lúc trước, cứ giữ nguyên nhé)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}