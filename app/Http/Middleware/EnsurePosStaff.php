<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePosStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'StaffSales') {
            Auth::logout();

            return redirect(url('/pos/login'))
                ->withErrors(['error' => 'Tài khoản không có quyền truy cập POS.']);
        }

        return $next($request);
    }
}
