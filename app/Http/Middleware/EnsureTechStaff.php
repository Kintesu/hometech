<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTechStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'StaffTech') {
            Auth::logout();

            return redirect('/quantri/login')
                ->withErrors(['error' => 'Tài khoản không có quyền truy cập trang lắp đặt.']);
        }

        return $next($request);
    }
}
