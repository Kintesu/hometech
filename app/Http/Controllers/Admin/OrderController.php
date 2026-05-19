<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index()
    {
        // Lấy danh sách đơn, kèm thông tin User, sắp xếp đơn mới nhất lên đầu
        $orders = Order::with('user')->orderBy('id', 'desc')->paginate(10);
        return view('admin.order.index', compact('orders'));
    }

    // 2. Xem chi tiết 1 đơn hàng
    public function show($id)
    {
        $order = Order::with(['user', 'details.product'])->find($id);
        
        if (!$order) {
            return redirect('/quantri/don-hang')->with('error', 'Không tìm thấy đơn hàng!');
        }

        return view('admin.order.show', compact('order'));
    }

    // 3. Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = $request->status;
            $order->save();
            return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #DH-' . $id);
        }
        return back()->with('error', 'Lỗi cập nhật trạng thái!');
    }
}