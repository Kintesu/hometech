<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HomeTech - Lắp đặt</title>
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body id="page-top" class="bg-light">
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <a class="navbar-brand font-weight-bold text-primary" href="/lap-dat">
            <i class="fas fa-tools mr-2"></i>HomeTech Lắp đặt
        </a>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link text-gray-700">
                    {{ Auth::user()->full_name ?? 'Nhân viên lắp đặt' }}
                </span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/quantri/logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>

    <main class="container-fluid">
        @yield('content')
    </main>

    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
