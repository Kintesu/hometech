@extends('layouts.client')

@section('title', 'Thông tin cá nhân - HomeTech')

@section('css')
<style>
    .profile-section {
        background: #f5f5f5;
        padding: 45px 0;
    }

    .profile-box {
        max-width: 720px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e4e7ed;
        padding: 28px;
    }

    .profile-box h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .profile-meta {
        background: #fbfbfc;
        border: 1px solid #e4e7ed;
        padding: 14px 16px;
        margin-bottom: 22px;
    }

    .profile-meta p {
        margin-bottom: 6px;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .profile-actions .primary-btn {
        border: 0;
    }
</style>
@endsection

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="breadcrumb-header">Thông tin cá nhân</h3>
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="active">Thông tin cá nhân</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="profile-section">
    <div class="container">
        <div class="profile-box">
            <h3>Tài khoản của bạn</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="profile-meta">
                <p><strong>Mã tài khoản:</strong> {{ $user->id }}</p>
                <p><strong>Vai trò:</strong> {{ $user->role }}</p>
                <p><strong>Ngày tạo:</strong> {{ $user->created_at ? date('d/m/Y H:i', strtotime($user->created_at)) : 'N/A' }}</p>
            </div>

            <form method="POST" action="/tai-khoan">
                @csrf

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="full_name">Họ tên</label>
                            <input id="full_name"
                                   type="text"
                                   name="full_name"
                                   class="input"
                                   value="{{ old('full_name', $user->full_name) }}"
                                   required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input id="username"
                                   type="text"
                                   name="username"
                                   class="input"
                                   value="{{ old('username', $user->username) }}"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input id="phone"
                           type="text"
                           name="phone"
                           class="input"
                           value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea id="address"
                              name="address"
                              class="input"
                              rows="3">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password">Mật khẩu mới</label>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   class="input"
                                   placeholder="Để trống nếu không đổi">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                            <input id="password_confirmation"
                                   type="password"
                                   name="password_confirmation"
                                   class="input"
                                   placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="primary-btn order-submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
