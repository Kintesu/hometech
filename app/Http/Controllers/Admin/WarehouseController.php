<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Thêm Auth để lấy thông tin người đăng nhập
use App\Models\Product;
use App\Models\Supplier;
use App\Models\WarehouseReceipt;
use App\Models\WarehouseReceiptDetail;

class WarehouseController extends Controller
{
    // Hiển thị màn hình Hàng tồn kho (Mới)
    public function inventory()
    {
        // Lấy tất cả sản phẩm, sắp xếp số lượng từ thấp đến cao (để cảnh báo hàng sắp hết lên đầu)
        $products = Product::orderBy('stock_quantity', 'asc')->get();
        return view('admin.warehouse.inventory', compact('products'));
    }
    // 1. Hiển thị danh sách phiếu nhập
    public function index()
    {
        // Lấy danh sách phiếu nhập, sắp xếp mới nhất lên đầu
        $receipts = WarehouseReceipt::orderBy('id', 'desc')->get();
        return view('admin.warehouse.index', compact('receipts'));
    }

    // 2. Hiển thị form tạo phiếu nhập mới
    public function create()
    {
        $suppliers = Supplier::all(); // Lấy danh sách nhà cung cấp
        $products = Product::all();   // Lấy danh sách sản phẩm để chọn
        return view('admin.warehouse.create', compact('suppliers', 'products'));
    }

    // 3. Xử lý lưu phiếu nhập và cộng dồn tồn kho
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Bước 1: Tạo Phiếu nhập kho (Bảng cha)
            $receipt = new WarehouseReceipt();
            $receipt->supplier_id = $request->supplier_id;
            $receipt->staff_id = Auth::id() ?? 1; // Lấy ID người dùng, nếu chưa login thì tạm để 1
            $receipt->total_value = 0; 
            $receipt->save();

            $total_money = 0;

            // Bước 2: Lưu chi tiết phiếu nhập (Bảng con)
            foreach ($request->product_id as $key => $pid) {
                
                $quantity = $request->quantity[$key];
                $price = $request->import_price[$key];

                $detail = new WarehouseReceiptDetail();
                $detail->receipt_id = $receipt->id; 
                $detail->product_id = $pid;
                $detail->quantity = $quantity;
                $detail->import_price = $price;
                $detail->save();

                $total_money += ($quantity * $price);

                // Bước 3: Cập nhật CỘNG DỒN SỐ LƯỢNG vào bảng Products
                $product = Product::find($pid);
                if ($product) {
                    // ĐÃ SỬA CHỖ NÀY: Dùng stock_quantity cho khớp với DB của bạn
                    $product->stock_quantity = $product->stock_quantity + $quantity; 
                    $product->save();
                }
            }

            // Bước 4: Cập nhật lại tổng tiền cho phiếu nhập
            $receipt->total_value = $total_money;
            $receipt->save();

            DB::commit();

            return redirect('/quantri/kho/lich-su')->with('success', 'Nhập kho thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    // 4. Xem chi tiết phiếu nhập
    public function show($id)
    {
        // Lấy phiếu nhập kèm theo THÔNG TIN NHÀ CUNG CẤP và CHI TIẾT SẢN PHẨM (Nhờ các hàm Relation vừa tạo ở Bước 1)
        $receipt = WarehouseReceipt::with(['supplier', 'details.product'])->find($id);

        if (!$receipt) {
            return redirect('/quantri/kho/lich-su')->with('error', 'Không tìm thấy phiếu nhập này!');
        }

        return view('admin.warehouse.show', compact('receipt'));
    }
}