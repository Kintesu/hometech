<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index(Request $request)
    {
        $statuses = [
            'Pending' => 'Chờ xử lý',
            'Shipping' => 'Đang giao hàng',
            'Completed' => 'Đã hoàn thành',
            'InstallationFailed' => 'Lắp đặt thất bại',
            'Canceled' => 'Đã hủy',
        ];

        $status = $request->query('status');
        $keyword = trim((string) $request->query('q', ''));

        $orders = Order::with(['user', 'details.product'])
            ->when(is_string($status) && array_key_exists($status, $statuses), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($keyword !== '', function ($query) use ($keyword) {
                $orderId = preg_replace('/\D+/', '', $keyword);

                $query->where(function ($subQuery) use ($keyword, $orderId) {
                    if ($orderId !== '') {
                        $subQuery->where('id', $orderId);
                    }

                    $subQuery->orWhere('delivery_address', 'like', '%' . $keyword . '%')
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->where('full_name', 'like', '%' . $keyword . '%')
                                ->orWhere('username', 'like', '%' . $keyword . '%')
                                ->orWhere('phone', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('details.product', function ($productQuery) use ($keyword) {
                            $productQuery->where('name', 'like', '%' . $keyword . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.order.index', compact('orders', 'statuses', 'status', 'keyword'));
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
        $request->validate([
            'status' => ['required', 'in:Pending,Shipping,Completed,InstallationFailed,Canceled'],
        ]);

        $order = Order::find($id);
        if ($order) {
            if ($order->status === 'Pending' && $request->status === 'Shipping') {
                return back()->with('error', 'Vui lòng dùng chức năng Xác nhận xuất kho để chuyển đơn sang Đang giao.');
            }

            $order->status = $request->status;
            $order->save();
            return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #DH-' . $id);
        }
        return back()->with('error', 'Lỗi cập nhật trạng thái!');
    }

}
