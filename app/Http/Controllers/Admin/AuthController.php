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
        if (Auth::check() && Auth::user()->role === 'Admin') {
            return redirect('/quantri');
        }
        if (Auth::check() && Auth::user()->role === 'StaffWarehouse') {
            return redirect('/kho/xuat-kho');
        }
        if (Auth::check() && Auth::user()->role === 'StaffTech') {
            return redirect('/lap-dat');
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
            
            if ($user->role === 'Admin') {
                return redirect('/quantri');
            }

            if ($user->role === 'StaffSales') {
                return redirect(url('/pos'));
            }

            if ($user->role === 'StaffWarehouse') {
                return redirect('/kho/xuat-kho');
            }

            if ($user->role === 'StaffTech') {
                return redirect('/lap-dat');
            }

            Auth::logout();
            return back()->withErrors(['error' => 'Tài khoản không có quyền truy cập quản trị.']);
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

    public function showPosLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'StaffSales') {
            return redirect(url('/pos'));
        }

        return view('pos.login');
    }

    public function posLogin(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role === 'StaffSales') {
                return redirect(url('/pos'));
            }

            Auth::logout();
            return back()->withErrors(['error' => 'Tài khoản chưa được Admin phân quyền POS.']);
        }

        return back()->withErrors(['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }

    public function posLogout()
    {
        Auth::logout();
        return redirect(url('/pos/login'));
    }
}
