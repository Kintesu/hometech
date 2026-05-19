<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // 1. Thêm sản phẩm vào giỏ hàng
    public function add(Request $request, $id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // Lấy giỏ hàng hiện tại từ Session (nếu chưa có thì tạo mảng rỗng [])
        $cart = session()->get('cart', []);
        
        // Lấy số lượng khách hàng chọn (mặc định là 1 nếu không truyền lên)
        $quantity = $request->quantity ?? 1;

        // Nếu sản phẩm ĐÃ CÓ trong giỏ -> Cộng dồn số lượng
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } 
        // Nếu sản phẩm CHƯA CÓ trong giỏ -> Thêm mới vào mảng
        else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        // Lưu mảng $cart ngược lại vào Session
        session()->put('cart', $cart);

        return redirect('/gio-hang')->with('success', 'Đã thêm ' . $product->name . ' vào giỏ hàng!');
    }

    // 2. Hiển thị Trang Giỏ hàng
    public function index()
    {
        // Lấy giỏ hàng từ Session
        $cart = session()->get('cart', []);
        
        // Tính tổng tiền
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('client.cart', compact('cart', 'total'));
    }

    // 3. Xóa sản phẩm khỏi giỏ hàng
    public function remove($id)
    {
        $cart = session()->get('cart');

        // Nếu tồn tại sản phẩm đó trong session thì dùng lệnh unset để xóa
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}