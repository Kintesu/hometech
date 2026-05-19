<?php

use Illuminate\Support\Facades\Route;

// Import các Controller đang sử dụng
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| ROUTES GIAO DIỆN KHÁCH HÀNG (FRONTEND)
|--------------------------------------------------------------------------
*/
// Gọi hàm index của HomeController khi người dùng vào trang chủ
Route::get('/', [HomeController::class, 'index']);


/*
|--------------------------------------------------------------------------
| ROUTES QUẢN TRỊ VIÊN (BACKEND)
|--------------------------------------------------------------------------
*/
// Các route không cần bảo vệ (Trang đăng nhập, xử lý đăng nhập, đăng xuất)
Route::get('/quantri/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/quantri/login', [AuthController::class, 'login']);
Route::get('/quantri/logout', [AuthController::class, 'logout']);

// BỌC MIDDLEWARE: Các route bắt buộc phải đăng nhập mới vào được
Route::middleware(['auth'])->group(function () {
    
        Route::get('/quantri', function () {
        $now = Carbon::now();

        /* =======================================================
        1. THỐNG KÊ TỔNG QUAN
        ======================================================= */
        $doanhThuThang = DB::table('orders')
            ->whereMonth('order_date', $now->month) // Đã sửa thành order_date
            ->whereYear('order_date', $now->year)
            ->sum('total_price'); // Đã sửa thành total_price

        $doanhThuNam = DB::table('orders')
            ->whereYear('order_date', $now->year)
            ->sum('total_price');

        $donHangCho = DB::table('orders')
            ->where('status', 'Pending') // Đã sửa thành chữ P viết hoa theo DB
            ->count();

        $stats = [
            'doanh_thu_thang' => $doanhThuThang,
            'doanh_thu_nam' => $doanhThuNam,
            'don_hang_cho' => $donHangCho
        ];

        /* =======================================================
        2. BIỂU ĐỒ DOANH THU (ĐƯỜNG)
        ======================================================= */
        // Lấy doanh thu từng tháng trong năm nay
        $monthlyData = array_fill(1, 12, 0); 
        $monthlyRevenues = DB::table('orders')
            ->select(DB::raw('MONTH(order_date) as month'), DB::raw('SUM(total_price) as total'))
            ->whereYear('order_date', $now->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($monthlyRevenues as $month => $total) {
            $monthlyData[$month] = $total;
        }

        // Lấy doanh thu 5 năm gần nhất
        $yearlyData = [];
        $yearlyLabels = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = (string)$year;
            $total = DB::table('orders')->whereYear('order_date', $year)->sum('total_price');
            $yearlyData[] = $total;
        }

        $chartData = [
            'thang' => [
                'labels' => ['Th.1', 'Th.2', 'Th.3', 'Th.4', 'Th.5', 'Th.6', 'Th.7', 'Th.8', 'Th.9', 'Th.10', 'Th.11', 'Th.12'],
                'data' => array_values($monthlyData)
            ],
            'nam' => [
                'labels' => $yearlyLabels,
                'data' => $yearlyData
            ]
        ];

        /* =======================================================
        3. BIỂU ĐỒ TỈ TRỌNG SẢN PHẨM BÁN RA (TRÒN)
        ======================================================= */
        $categorySales = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.category_id', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.category_id')
            ->pluck('total_sold', 'category_id')
            ->toArray();

        $catNames = [
            1 => 'Tủ lạnh', 2 => 'Điều hòa', 3 => 'Máy giặt',
            4 => 'Tivi', 5 => 'Máy lọc nước', 6 => 'Gia dụng khác'
        ];

        $pieLabels = [];
        $pieDataValues = [];

        foreach ($catNames as $id => $name) {
            $pieLabels[] = $name;
            $pieDataValues[] = $categorySales[$id] ?? 0;
        }

        $pieData = [
            'labels' => $pieLabels,
            'data' => $pieDataValues,
            'colors' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
        ];

        return view('admin.dashboard', compact('stats', 'chartData', 'pieData'));
    });

    // Quản lý sản phẩm
    Route::get('/quantri/san-pham', [ProductController::class, 'index']);
    // Thêm sản phẩm
    Route::get('/quantri/san-pham/them', [ProductController::class, 'create']);
    Route::post('/quantri/san-pham/them', [ProductController::class, 'store']);
    // Sửa sản phẩm
    Route::get('/quantri/san-pham/sua/{id}', [ProductController::class, 'edit']);
    Route::post('/quantri/san-pham/sua/{id}', [ProductController::class, 'update']);
    // Xóa sản phẩm
    Route::get('/quantri/san-pham/xoa/{id}', [ProductController::class, 'destroy']);

    // Quản lý kho hàng
    Route::get('/quantri/kho/ton-kho', [WarehouseController::class, 'inventory']); // Màn hình chính: Tồn kho
    Route::get('/quantri/kho/lich-su', [WarehouseController::class, 'index']);     // Lịch sử nhập kho
    Route::get('/quantri/kho/nhap-kho', [WarehouseController::class, 'create']);   // Form tạo phiếu
    Route::post('/quantri/kho/nhap-kho', [WarehouseController::class, 'store']);   // Xử lý lưu phiếu
    Route::get('/quantri/kho/chi-tiet/{id}', [WarehouseController::class, 'show']);

    // Quản lý Đơn hàng
    Route::get('/quantri/don-hang', [OrderController::class, 'index']);
    Route::get('/quantri/don-hang/chi-tiet/{id}', [OrderController::class, 'show']);
    Route::post('/quantri/don-hang/cap-nhat/{id}', [OrderController::class, 'updateStatus']);

    // TRANG KHÁCH HÀNG (FRONTEND)
    Route::get('/', [HomeController::class, 'index']);
    // THÊM DÒNG NÀY: Dùng {id} để biết khách đang bấm vào sản phẩm số mấy
    Route::get('/san-pham/{id}', [HomeController::class, 'detail']);

    // ROUTE GIỎ HÀNG
    Route::post('/gio-hang/them/{id}', [CartController::class, 'add']); // Bấm nút thêm vào giỏ
    Route::get('/gio-hang', [CartController::class, 'index']);          // Xem trang giỏ hàng
    Route::get('/gio-hang/xoa/{id}', [CartController::class, 'remove']);// Xóa 1 sản phẩm khỏi giỏ

    // Route Tìm kiếm sản phẩm
    Route::get('/tim-kiem', [App\Http\Controllers\HomeController::class, 'search']);

    // Sau này các route Quản lý sản phẩm, đơn hàng... sẽ đặt hết ở trong khối này
});