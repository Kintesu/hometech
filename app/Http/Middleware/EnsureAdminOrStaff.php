<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'Admin') {
            Auth::logout();

            return redirect('/quantri/login')
                ->withErrors(['error' => 'Tài khoản không có quyền truy cập quản trị.']);
        }

        return $next($request);
    }
}
