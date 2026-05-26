<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Đừng quên gọi Model Product vào nhé
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    // Nghiệp vụ 1: Hiển thị trang chủ khách hàng
    public function index()
    {
        // Lấy 8 sản phẩm mới nhất từ kho dữ liệu (sắp xếp ID giảm dần)
        $newProducts = Product::orderBy('id', 'desc')->take(8)->get();
        
        // Trả về giao diện và ném biến $newProducts sang cho View
        return view('client.home', compact('newProducts'));
    }
    
    // Nghiệp vụ 2: Xem chi tiết 1 sản phẩm
    public function detail($id)
    {
        // Vào database tìm sản phẩm có ID tương ứng
        $product = Product::find($id);

        // Nếu khách cố tình gõ ID bậy bạ trên thanh URL -> Đuổi về trang chủ
        if (!$product) {
            return redirect('/')->with('error', 'Sản phẩm không tồn tại!');
        }

        // Nếu tìm thấy, ném dữ liệu sang trang Giao diện Chi tiết
        return view('client.detail', compact('product'));
    }

    // XỬ LÝ TÌM KIẾM
    public function search(Request $request)
    {
        // 1. Lấy dữ liệu người dùng gửi lên từ thanh tìm kiếm
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $discount = $request->boolean('discount');

        // 2. Bắt đầu truy vấn
        $query = Product::orderBy('id', 'desc');

        // Lọc theo từ khóa (Nếu có nhập)
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // Lọc theo danh mục (Nếu có chọn danh mục khác "Tất cả" - tức là khác 0)
        if (!empty($category_id) && $category_id != 0) {
            $query->where('category_id', $category_id);
        }

        if ($discount) {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $discountedProducts = $query->get()->filter(function ($product) {
                return $product->discount_percent > 0;
            })->values();

            $products = new LengthAwarePaginator(
                $discountedProducts->forPage($page, 12),
                $discountedProducts->count(),
                12,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            // 3. Phân trang (Hiển thị 12 sản phẩm/trang)
            $products = $query->paginate(12);
        }

        // 4. Trả về view kết quả tìm kiếm
        return view('client.search', compact('products', 'keyword', 'discount'));
    }
}
