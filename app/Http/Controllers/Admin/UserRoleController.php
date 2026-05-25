<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    private array $roles = ['Admin', 'StaffSales', 'StaffWarehouse', 'StaffTech', 'Customer'];

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);
        $roles = $this->roles;

        return view('admin.users.roles', compact('users', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in($this->roles)],
        ]);

        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'Không tìm thấy tài khoản.');
        }

        if ((int) $user->id === (int) Auth::id() && $validated['role'] !== 'Admin') {
            return back()->with('error', 'Bạn không thể tự bỏ quyền Admin của chính mình.');
        }

        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', 'Đã cập nhật quyền cho tài khoản ' . $user->username . '.');
    }
}
