<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseShipmentController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'details.product'])
            ->where('status', 'Pending')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('warehouse.shipments.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'details.product'])
            ->where('status', 'Pending')
            ->find($id);

        if (!$order) {
            return redirect('/kho/xuat-kho')->with('error', 'Không tìm thấy đơn hàng chờ xuất kho.');
        }

        $staffTechs = User::where('role', 'StaffTech')
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return view('warehouse.shipments.show', compact('order', 'staffTechs'));
    }

    public function confirm(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $order = Order::with('details.product')->lockForUpdate()->find($id);

                if (!$order) {
                    throw new \RuntimeException('Không tìm thấy đơn hàng!');
                }

                if ($order->status !== 'Pending') {
                    throw new \RuntimeException('Chỉ có thể xuất kho đơn hàng đang Chờ xử lý.');
                }

                if ($order->details->isEmpty()) {
                    throw new \RuntimeException('Đơn hàng chưa có sản phẩm để xuất kho.');
                }

                $requiresInstallation = $order->details->contains(function ($detail) {
                    return $detail->product && (bool) $detail->product->requires_installation;
                });

                $staffTechId = $request->input('assigned_staff_tech_id');

                if ($requiresInstallation) {
                    if (!$staffTechId) {
                        throw new \RuntimeException('Vui lòng chọn nhân viên lắp đặt cho đơn hàng này.');
                    }

                    $staffTechExists = User::whereKey($staffTechId)
                        ->where('role', 'StaffTech')
                        ->exists();

                    if (!$staffTechExists) {
                        throw new \RuntimeException('Nhân viên lắp đặt không hợp lệ.');
                    }
                }

                foreach ($order->details as $detail) {
                    $product = Product::whereKey($detail->product_id)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \RuntimeException('Sản phẩm SP-' . $detail->product_id . ' không tồn tại.');
                    }

                    if ((int) $product->stock_quantity < (int) $detail->quantity) {
                        throw new \RuntimeException('Sản phẩm "' . $product->name . '" không đủ tồn kho thực tế.');
                    }

                    $product->stock_quantity = (int) $product->stock_quantity - (int) $detail->quantity;
                    $product->save();
                }

                $fromStatus = $order->status;
                $order->status = 'Shipping';
                $order->assigned_staff_tech_id = $staffTechId ?: null;
                $order->installation_assigned_at = $staffTechId ? now() : null;
                $order->save();

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'changed_by' => Auth::id(),
                    'from_status' => $fromStatus,
                    'to_status' => 'Shipping',
                    'reason' => $staffTechId ? 'Xuất kho và phân công lắp đặt' : 'Xuất kho',
                    'created_at' => now(),
                ]);
            });

            return redirect('/kho/xuat-kho')->with('success', 'Đã xác nhận xuất kho đơn hàng #DH-' . $id . '.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do từ chối xuất kho.',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        if ($order->status !== 'Pending') {
            return back()->with('error', 'Chỉ có thể từ chối xuất kho đơn hàng đang Chờ xử lý.');
        }

        $order->status = 'Canceled';
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'changed_by' => Auth::id(),
            'from_status' => 'Pending',
            'to_status' => 'Canceled',
            'reason' => $validated['reason'],
            'created_at' => now(),
        ]);

        return redirect('/kho/xuat-kho')->with(
            'success',
            'Đã từ chối xuất kho đơn hàng #DH-' . $id . '. Lý do: ' . $validated['reason']
        );
    }
}
