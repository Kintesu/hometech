<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        // Nếu đã đăng nhập rồi thì đẩy thẳng vào dashboard
        if (Auth::check() && in_array(Auth::user()->role, ['Admin', 'Staff'])) {
            return redirect('/quantri');
        }
        return view('admin.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        // Nhận dữ liệu từ form
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // Thực hiện kiểm tra với Database
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Kiểm tra quyền (Chỉ Admin hoặc Staff mới được vào)
            if ($user->role == 'Admin' || $user->role == 'Staff') {
                return redirect('/quantri');
            } else {
                Auth::logout();
                return back()->withErrors(['error' => 'Tài khoản không có quyền truy cập quản trị.']);
            }
        }

        // Đăng nhập thất bại
        return back()->withErrors(['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }

    // Đăng xuất
    public function logout()
    {
        Auth::logout();
        return redirect('/quantri/login');
    }
}