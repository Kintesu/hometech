@extends('layouts.client')

@section('title', 'Đăng nhập - HomeTech')

@section('css')
<style>
    .auth-section {
        background: #f5f5f5;
        padding: 45px 0;
    }

    .auth-box {
        max-width: 460px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e4e7ed;
        padding: 28px;
    }

    .auth-box h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .auth-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 20px;
    }

    .auth-actions .primary-btn {
        border: 0;
    }
</style>
@endsection

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="breadcrumb-header">Đăng nhập</h3>
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="active">Đăng nhập</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="auth-section">
    <div class="container">
        <div class="auth-box">
            <h3>Tài khoản khách hàng</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="/dang-nhap">
                @csrf

                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input id="username"
                           type="text"
                           name="username"
                           class="input"
                           value="{{ old('username') }}"
                           required
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input id="password"
                           type="password"
                           name="password"
                           class="input"
                           required>
                </div>

                <div class="auth-actions">
                    <button type="submit" class="primary-btn order-submit">Đăng nhập</button>
                    <a href="/dang-ky">Tạo tài khoản mới</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
