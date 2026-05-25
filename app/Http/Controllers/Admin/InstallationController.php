<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstallationController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'details.product'])
            ->where('status', 'Shipping')
            ->where('assigned_staff_tech_id', Auth::id())
            ->whereDate('installation_assigned_at', now()->toDateString())
            ->orderBy('installation_assigned_at')
            ->paginate(10);

        return view('installations.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'details.product'])
            ->where('assigned_staff_tech_id', Auth::id())
            ->find($id);

        if (!$order) {
            return redirect('/lap-dat')->with('error', 'Không tìm thấy đơn lắp đặt được phân công.');
        }

        return view('installations.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Completed,InstallationFailed'],
            'reason' => ['required_if:status,InstallationFailed', 'nullable', 'string', 'max:500'],
        ], [
            'reason.required_if' => 'Vui lòng nhập lý do lắp đặt thất bại.',
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                $order = Order::where('assigned_staff_tech_id', Auth::id())
                    ->lockForUpdate()
                    ->find($id);

                if (!$order) {
                    throw new \RuntimeException('Không tìm thấy đơn lắp đặt được phân công.');
                }

                if ($order->status !== 'Shipping') {
                    throw new \RuntimeException('Chỉ có thể cập nhật đơn đang giao/lắp đặt.');
                }

                $fromStatus = $order->status;
                $order->status = $validated['status'];

                if ($validated['status'] === 'Completed') {
                    $order->installation_completed_at = now();
                }

                $order->save();

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'changed_by' => Auth::id(),
                    'from_status' => $fromStatus,
                    'to_status' => $validated['status'],
                    'reason' => $validated['reason'] ?? null,
                    'created_at' => now(),
                ]);
            });

            $message = $validated['status'] === 'Completed'
                ? 'Đã cập nhật đơn hàng hoàn thành.'
                : 'Đã ghi nhận lắp đặt thất bại.';

            return redirect('/lap-dat')->with('success', $message);
        } catch (\Throwable $e) {
            return redirect('/lap-dat')->with('error', $e->getMessage());
        }
    }
}
