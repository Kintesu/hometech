<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        return view('admin.pos.index');
    }

    public function searchProducts(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', ''));

        if ($keyword === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('name', 'like', '%' . $keyword . '%')
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'price', 'stock_quantity', 'image', 'requires_installation']);

        return response()->json($products);
    }

    public function receipt($id)
    {
        $order = Order::with(['user', 'details.product'])
            ->where('created_by', Auth::id())
            ->find($id);

        if (!$order) {
            return redirect(url('/pos'))->withErrors(['error' => 'Không tìm thấy hóa đơn POS.']);
        }

        return view('pos.receipt', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:15'],
            'status' => ['nullable', 'in:Completed,Pending,completed,pending'],
            'received_amount' => ['required', 'numeric', 'min:0'],
            'delivery_address' => ['nullable', 'string'],
        ], [
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm vào đơn hàng.',
            'items.min' => 'Vui lòng thêm ít nhất một sản phẩm vào đơn hàng.',
            'received_amount.required' => 'Vui lòng nhập số tiền khách đưa.',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            $requiresInstallation = false;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get((int) $item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => 'Có sản phẩm không tồn tại trong hệ thống.',
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if ((int) $product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => 'Sản phẩm "' . $product->name . '" không đủ tồn kho.',
                    ]);
                }

                $lineTotal = (float) $product->price * $quantity;
                $total += $lineTotal;
                $requiresInstallation = $requiresInstallation || (bool) $product->requires_installation;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ];
            }

            $orderStatus = ucfirst(strtolower($validated['status'] ?? 'Pending'));
            $needsDeliveryAddress = $requiresInstallation || $orderStatus === 'Pending';

            if ($needsDeliveryAddress && empty(trim((string) ($validated['delivery_address'] ?? '')))) {
                throw ValidationException::withMessages([
                    'delivery_address' => 'Vui lòng nhập địa chỉ giao hàng/lắp đặt.',
                ]);
            }

            $receivedAmount = (float) $validated['received_amount'];

            if ($receivedAmount < $total) {
                throw ValidationException::withMessages([
                    'received_amount' => 'Số tiền khách đưa không đủ để thanh toán.',
                ]);
            }

            $order = new Order();
            $order->user_id = $this->resolveCustomerId($validated);
            $order->order_date = now();
            $order->total_price = $total;
            $order->status = $orderStatus;
            $order->payment_method = 'Tiền mặt';
            $order->received_amount = $receivedAmount;
            $order->change_amount = $receivedAmount - $total;
            $order->delivery_address = $needsDeliveryAddress
                ? trim((string) ($validated['delivery_address'] ?? ''))
                : null;
            $order->created_by = Auth::id();
            $order->save();

            foreach ($items as $item) {
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->product_id = $item['product']->id;
                $detail->quantity = $item['quantity'];
                $detail->unit_price = $item['unit_price'];
                $detail->save();

                if ($orderStatus === 'Completed') {
                    $item['product']->stock_quantity = (int) $item['product']->stock_quantity - (int) $item['quantity'];
                    $item['product']->save();
                }
            }

            return $order;
        });

        return redirect(url('/pos/orders/' . $order->id . '/receipt'))
            ->with('success', 'Đã tạo đơn POS #DH-' . $order->id . ' thành công.');
    }

    private function resolveCustomerId(array $validated): ?int
    {
        $phone = trim((string) ($validated['customer_phone'] ?? ''));

        if ($phone === '') {
            return null;
        }

        $customer = User::where('phone', $phone)->first();

        if ($customer) {
            return $customer->id;
        }

        $customer = new User();
        $customer->username = $this->makeCustomerUsername($phone);
        $customer->password = Hash::make(Str::random(32));
        $customer->full_name = trim((string) ($validated['customer_name'] ?? '')) ?: 'Khách POS ' . $phone;
        $customer->role = 'Customer';
        $customer->phone = $phone;
        $customer->save();

        return $customer->id;
    }

    private function makeCustomerUsername(string $phone): string
    {
        $base = 'pos_' . preg_replace('/\D+/', '', $phone);

        if ($base === 'pos_') {
            $base = 'pos_customer';
        }

        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $suffix++;
        }

        return $username;
    }
}
