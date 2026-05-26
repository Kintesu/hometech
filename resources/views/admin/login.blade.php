<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HomeTech - Đăng nhập Quản trị</title>
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(20, 22, 29, 0.92), rgba(209, 0, 36, 0.82)),
                url("{{ asset('screenshot/Store.png') }}") center/cover no-repeat;
            color: #2b2d42;
        }

        .admin-login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 32px 0;
        }

        .login-shell {
            overflow: hidden;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.35);
        }

        .login-brand-panel {
            min-height: 520px;
            height: 100%;
            padding: 48px;
            background:
                linear-gradient(160deg, rgba(21, 22, 29, 0.96), rgba(21, 22, 29, 0.72)),
                url("{{ asset('images/TuLanh.jpg') }}") center/cover no-repeat;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-mark {
            width: 58px;
            height: 58px;
            border-radius: 8px;
            background: #d10024;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            box-shadow: 0 14px 30px rgba(209, 0, 36, 0.35);
        }

        .brand-title {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.15;
            margin: 28px 0 14px;
        }

        .brand-text {
            max-width: 360px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 15px;
            line-height: 1.7;
            margin: 0;
        }

        .brand-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 36px;
        }

        .brand-stat {
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.08);
        }

        .brand-stat strong {
            display: block;
            font-size: 18px;
            color: #fff;
        }

        .brand-stat span {
            display: block;
            color: rgba(255, 255, 255, 0.68);
            font-size: 12px;
            margin-top: 3px;
        }

        .login-form-panel {
            min-height: 520px;
            padding: 52px 48px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-kicker {
            color: #d10024;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .login-heading {
            color: #15161d;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #737686;
            margin-bottom: 30px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 18px;
        }

        .input-wrap i {
            position: absolute;
            top: 50%;
            left: 18px;
            transform: translateY(-50%);
            color: #9da2b3;
            z-index: 2;
        }

        .admin-login-input {
            height: 54px;
            border-radius: 8px !important;
            border: 1px solid #e3e6f0;
            padding: 0 18px 0 48px !important;
            font-size: 15px !important;
            background: #f8f9fc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .admin-login-input:focus {
            background: #fff;
            border-color: #d10024;
            box-shadow: 0 0 0 0.2rem rgba(209, 0, 36, 0.12);
        }

        .admin-login-btn {
            height: 52px;
            border-radius: 8px !important;
            background: #d10024;
            border-color: #d10024;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 12px 24px rgba(209, 0, 36, 0.22);
        }

        .admin-login-btn:hover,
        .admin-login-btn:focus {
            background: #b90020;
            border-color: #b90020;
        }

        .login-alert {
            border-radius: 8px;
            border: 0;
            background: #fff1f3;
            color: #9f001b;
        }

        .login-footer-note {
            margin-top: 24px;
            color: #9da2b3;
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 991px) {
            .login-brand-panel {
                min-height: auto;
                padding: 36px;
            }

            .brand-stats {
                grid-template-columns: 1fr;
            }

            .login-form-panel {
                min-height: auto;
                padding: 38px 28px;
            }
        }
    </style>
</head>
<body>
    <main class="admin-login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-11">
                    <div class="card login-shell">
                        <div class="row no-gutters">
                            <div class="col-lg-6 d-none d-lg-block">
                                <section class="login-brand-panel">
                                    <div>
                                        <div class="brand-mark">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <h1 class="brand-title">HomeTech Admin</h1>
                                        <p class="brand-text">
                                            Trung tâm quản trị vận hành sản phẩm, đơn hàng, kho và phân quyền nhân sự.
                                        </p>
                                    </div>

                                    <div class="brand-stats">
                                        <div class="brand-stat">
                                            <strong>POS</strong>
                                            <span>Bán hàng</span>
                                        </div>
                                        <div class="brand-stat">
                                            <strong>Kho</strong>
                                            <span>Tồn & xuất</span>
                                        </div>
                                        <div class="brand-stat">
                                            <strong>Đơn</strong>
                                            <span>Theo dõi</span>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-lg-6">
                                <section class="login-form-panel">
                                    <div class="login-kicker">Quản trị hệ thống</div>
                                    <h2 class="login-heading">Đăng nhập</h2>
                                    <p class="login-subtitle">Nhập tài khoản quản trị để tiếp tục.</p>

                                    @if($errors->any())
                                        <div class="alert login-alert">
                                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form class="user" method="POST" action="/quantri/login">
                                        @csrf
                                        <div class="input-wrap">
                                            <i class="fas fa-user"></i>
                                            <input type="text"
                                                   name="username"
                                                   class="form-control form-control-user admin-login-input"
                                                   placeholder="Tên đăng nhập"
                                                   autocomplete="username"
                                                   required
                                                   autofocus>
                                        </div>

                                        <div class="input-wrap">
                                            <i class="fas fa-lock"></i>
                                            <input type="password"
                                                   name="password"
                                                   class="form-control form-control-user admin-login-input"
                                                   placeholder="Mật khẩu"
                                                   autocomplete="current-password"
                                                   required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block admin-login-btn">
                                            <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                                        </button>
                                    </form>

                                    <div class="login-footer-note">
                                        HomeTech &copy; 2026
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
