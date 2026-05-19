<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Gọi Model Product vào để lấy dữ liệu

class ProductController extends Controller
{
    // 1. API Lấy danh sách tất cả sản phẩm
    public function index()
    {
        // Lấy toàn bộ sản phẩm từ DB
        $products = Product::all();

        // Trả về dữ liệu định dạng JSON kèm mã trạng thái 200 (Thành công)
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách sản phẩm thành công',
            'data' => $products
        ], 200);
    }

    // 2. API Lấy chi tiết 1 sản phẩm theo ID
    public function show($id)
    {
        // Tìm sản phẩm có id tương ứng
        $product = Product::find($id);

        // Nếu không tìm thấy sản phẩm
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm này'
            ], 404); // Mã 404 Not Found
        }

        // Nếu tìm thấy, trả về chi tiết sản phẩm
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy chi tiết sản phẩm thành công',
            'data' => $product
        ], 200);
    }
}