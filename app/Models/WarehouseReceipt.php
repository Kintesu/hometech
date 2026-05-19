<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WarehouseReceipt extends Model
{
    // Thêm dòng này để tắt tính năng tự động lưu thời gian
    public $timestamps = false; 

    // Bổ sung 2 hàm này vào dưới dòng public $timestamps = false;
    
    // Liên kết 1 Phiếu nhập -> 1 Nhà cung cấp
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Liên kết 1 Phiếu nhập -> Nhiều Chi tiết phiếu nhập
    public function details()
    {
        return $this->hasMany(WarehouseReceiptDetail::class, 'receipt_id');
    }
}