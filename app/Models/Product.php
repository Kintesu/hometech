<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Chỉ định đúng tên bảng trong MySQL
    protected $table = 'products';

    // Tắt tự động quản lý thời gian nếu bảng không có created_at, updated_at
    public $timestamps = false; 

    // 1. Hàm tự động tính Phần trăm giảm giá
    public function getDiscountPercentAttribute()
    {
        $stock = $this->stock_quantity; // Số lượng tồn kho
        $salesLast2Months = 0; // Mặc định số lượng bán là 0 nếu chưa có hệ thống đơn hàng

        // Dùng try-catch để chặn lỗi đỏ màn hình nếu Database chưa hoàn thiện
        try {
            $salesLast2Months = \DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.product_id', $this->id)
                // CHÚ Ý: Nếu cột ngày tháng bảng orders của bạn tên khác, hãy sửa chữ 'created_at' ở dưới
                ->where('orders.created_at', '>=', now()->subMonths(2))
                ->sum('order_details.quantity');
        } catch (\Exception $e) {
            // Nếu bảng chưa có hoặc thiếu cột, hệ thống sẽ bỏ qua và coi như bán được 0 sản phẩm
        }

        // LOGIC KHUYẾN MÃI THEO YÊU CẦU:
        
        // Mốc 1: Tồn rất nhiều, bán cực chậm -> Xả hàng giảm 45%
        if ($stock >= 100 && $salesLast2Months <= 10) {
            return 45;
        }
        // Mốc 2: Tồn nhiều, bán chậm -> Giảm 35%
        elseif ($stock >= 85 && $salesLast2Months <= 15) {
            return 35;
        }
        // Mốc 3: Tồn khá, bán hơi chậm -> Giảm 25% 
        elseif ($stock >= 70 && $salesLast2Months <= 8) {
            return 25;
        }
        // Mốc 4: Tồn trung bình, bán túc tắc -> Giảm 15%
        elseif ($stock >= 50 && $salesLast2Months <= 20) {
            return 15;
        }
        // Mốc 5: Tồn ít, nhưng bán đang chạy -> Kích cầu nhẹ giảm 5%
        elseif ($stock < 50 && $salesLast2Months >= 10) {
            return 5;
        }

        // Nếu không thuộc mốc nào thì không giảm giá (0%)
        return 0; 
    }

    // 2. Hàm tự động tính Giá mới sau khi trừ khuyến mãi
    public function getDiscountedPriceAttribute()
    {
        $discount = $this->discount_percent; // Gọi hàm bên trên
        
        if ($discount > 0) {
            // Công thức: Giá gốc * (1 - Phần trăm/100)
            return $this->price * (1 - $discount / 100);
        }
        
        return $this->price; // Giữ nguyên giá gốc nếu không giảm
    }
}