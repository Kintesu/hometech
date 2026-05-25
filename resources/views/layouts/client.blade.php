<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>@yield('title', 'HomeTech - Cửa Hàng Đồ Điện Gia Dụng')</title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
        <link type="text/css" rel="stylesheet" href="{{ asset('css/slick.css') }}"/>
        <link type="text/css" rel="stylesheet" href="{{ asset('css/slick-theme.css') }}"/>
        <link type="text/css" rel="stylesheet" href="{{ asset('css/nouislider.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}"/>

        <style>
            .floating-contact {
                position: fixed;
                right: 25px;
                top: 45%;
                transform: translateY(-50%);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .floating-contact .contact-item {
                width: 62px;
                height: 62px;
                border-radius: 50%;
                background-color: #D10024;
                color: #FFF;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 27px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.28);
                position: relative;
                transition: all 0.3s ease;
                text-decoration: none;
            }

            .floating-contact .contact-item:hover {
                background-color: #15161D;
                color: #FFF;
                transform: scale(1.12);
                text-decoration: none;
            }

            .floating-contact .contact-label {
                position: absolute;
                right: 75px;
                top: 50%;
                transform: translateY(-50%);
                background-color: #15161D;
                color: #FFF;
                padding: 8px 13px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .floating-contact .contact-item:hover .contact-label {
                opacity: 1;
                visibility: visible;
                right: 72px;
            }

            .floating-contact .phone {
                animation: phoneRing 1.6s infinite;
            }

            @keyframes phoneRing {
                0% {
                    transform: rotate(0deg);
                }

                10% {
                    transform: rotate(12deg);
                }

                20% {
                    transform: rotate(-12deg);
                }

                30% {
                    transform: rotate(8deg);
                }

                40% {
                    transform: rotate(-8deg);
                }

                50% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(0deg);
                }
            }

            .floating-contact .phone:hover {
                animation: none;
                transform: scale(1.12);
            }

            @media only screen and (max-width: 767px) {
                .floating-contact {
                    right: 15px;
                    bottom: 20px;
                    top: auto;
                    transform: none;
                }

                .floating-contact .contact-item {
                    width: 52px;
                    height: 52px;
                    font-size: 23px;
                }

                .floating-contact .contact-label {
                    display: none;
                }
            }
        </style>
        
        @yield('css')
    </head>

    <body>
        <!-- FLOATING CONTACT -->
        <div class="floating-contact">
            <a href="tel:+021955184" class="contact-item phone" title="Gọi tổng đài">
                <i class="fa fa-phone"></i>
                <span class="contact-label">Gọi tổng đài</span>
            </a>

            <a href="mailto:email@email.com?subject=Phản hồi thông tin&body=Xin chào HomeTech,"
               class="contact-item email"
               title="Gửi phản hồi qua email">
                <i class="fa fa-envelope-o"></i>
                <span class="contact-label">Gửi phản hồi</span>
            </a>

            <a href="#" class="contact-item address" title="Địa chỉ cửa hàng">
                <i class="fa fa-map-marker"></i>
                <span class="contact-label">Địa chỉ cửa hàng</span>
            </a>
        </div>
        <!-- /FLOATING CONTACT -->

        @php
            // LẤY TẤT CẢ DANH MỤC CHO THANH TÌM KIẾM
            $globalCategories = \App\Models\Category::all();

            $cart = session()->get('cart', []);
            $cartCount = 0;
            $cartTotal = 0;

            foreach($cart as $item) {
                $cartCount += $item['quantity'];
                $cartTotal += $item['price'] * $item['quantity'];
            }
        @endphp

        <header>
            <!-- MAIN HEADER -->
            <div id="header">
                <div class="container">
                    <div class="row">

                        <!-- LOGO -->
                        <div class="col-md-3">
                            <div class="header-logo">
                                <a href="/" class="logo">
                                    <img src="{{ asset('img/logo2.png') }}" alt="HomeTech Logo">
                                </a>
                            </div>
                        </div>
                        <!-- /LOGO -->

                        <!-- SEARCH BAR -->
                        <div class="col-md-6">
                            <div class="header-search">
                                <form action="/tim-kiem" method="GET">
                                    <select class="input-select" name="category_id" style="width: 150px;">
                                        <option value="0">Tất cả</option>

                                        @foreach($globalCategories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input class="input"
                                           placeholder="Tìm kiếm sản phẩm..."
                                           name="keyword"
                                           value="{{ request('keyword') }}">

                                    <button type="submit" class="search-btn">Tìm kiếm</button>
                                </form>
                            </div>
                        </div>
                        <!-- /SEARCH BAR -->

                        <!-- HEADER ACTIONS -->
                        <div class="col-md-3 clearfix">
                            <div class="header-ctn">

                                <!-- WISHLIST -->
                                <div class="dropdown">
                                    @auth
                                        <a href="#"
                                           class="dropdown-toggle"
                                           data-toggle="dropdown"
                                           role="button"
                                           aria-expanded="false"
                                           style="cursor: pointer;">
                                            <i class="fa fa-user-o"></i>
                                            <span>{{ Auth::user()->full_name }}</span>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 190px; padding: 8px 0;">
                                            <div style="padding: 8px 16px; color: #2b2d42; font-weight: 600;">
                                                <i class="fa fa-user-o"></i>
                                                {{ Auth::user()->full_name }}
                                            </div>
                                            <div class="divider" style="margin: 4px 0;"></div>
                                            <a href="/tai-khoan"
                                               class="dropdown-item"
                                               style="display: block; padding: 8px 16px; color: #2b2d42;">
                                                <i class="fa fa-id-card-o"></i> Thông tin cá nhân
                                            </a>
                                            <a href="{{ route('customer.logout') }}"
                                               class="dropdown-item"
                                               style="display: block; padding: 8px 16px; color: #2b2d42;"
                                               onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                                                <i class="fa fa-sign-out"></i> Đăng xuất
                                            </a>
                                            <form id="customer-logout-form" method="POST" action="{{ route('customer.logout') }}" style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    @else
                                        <a href="/dang-nhap">
                                            <i class="fa fa-user-o"></i>
                                            <span>Đăng nhập</span>
                                        </a>
                                    @endauth
                                </div>

                                <div>
                                    <a href="/yeu-thich">
                                        <i class="fa fa-heart-o"></i>
                                        <span>Yêu thích</span>
                                        <div class="qty">0</div>
                                    </a>
                                </div>
                                <!-- /WISHLIST -->

                                <!-- CART -->
                                <div class="dropdown">
                                    <a class="dropdown-toggle"
                                       data-toggle="dropdown"
                                       aria-expanded="true"
                                       style="cursor: pointer;">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Giỏ hàng</span>
                                        <div class="qty">{{ $cartCount }}</div>
                                    </a>

                                    <div class="cart-dropdown">
                                        <div class="cart-list">
                                            @if(count($cart) > 0)
                                                @foreach($cart as $id => $item)
                                                    <div class="product-widget">
                                                        <div class="product-img">
                                                            @if(isset($item['image']) && $item['image'] && file_exists(public_path('uploads/products/' . $item['image'])))
                                                                <img src="{{ asset('uploads/products/' . $item['image']) }}"
                                                                     alt="{{ $item['name'] }}"
                                                                     style="object-fit: cover; height: 100%;">
                                                            @else
                                                                <img src="{{ asset('img/product01.png') }}"
                                                                     alt="No Image"
                                                                     style="opacity: 0.5;">
                                                            @endif
                                                        </div>

                                                        <div class="product-body">
                                                            <h3 class="product-name">
                                                                <a href="/san-pham/{{ $id }}">
                                                                    {{ $item['name'] }}
                                                                </a>
                                                            </h3>

                                                            <h4 class="product-price">
                                                                <span class="qty">{{ $item['quantity'] }}x</span>
                                                                {{ number_format($item['price'], 0, ',', '.') }} đ
                                                            </h4>
                                                        </div>

                                                        <a href="/gio-hang/xoa/{{ $id }}" class="delete">
                                                            <i class="fa fa-close"></i>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-center mt-3">Giỏ hàng đang trống</p>
                                            @endif
                                        </div>

                                        <div class="cart-summary">
                                            <small>Đã chọn {{ count($cart) }} sản phẩm</small>
                                            <h5>TỔNG: {{ number_format($cartTotal, 0, ',', '.') }} đ</h5>
                                        </div>

                                        <div class="cart-btns">
                                            <a href="/gio-hang">Xem giỏ hàng</a>
                                            <a href="/thanh-toan">
                                                Thanh toán <i class="fa fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /CART -->

                                <!-- MENU TOGGLE -->
                                <div class="menu-toggle">
                                    <a href="#">
                                        <i class="fa fa-bars"></i>
                                        <span>Menu</span>
                                    </a>
                                </div>
                                <!-- /MENU TOGGLE -->

                            </div>
                        </div>
                        <!-- /HEADER ACTIONS -->

                    </div>
                </div>
            </div>
            <!-- /MAIN HEADER -->
        </header>

        <!-- NAVIGATION -->
        <nav id="navigation">
            <div class="container">
                <div id="responsive-nav">
                    <ul class="main-nav nav navbar-nav">
                        <li class="{{ request()->is('/') ? 'active' : '' }}">
                            <a href="/">Trang chủ</a>
                        </li>

                        <li>
                            <a href="#">Khuyến mãi</a>
                        </li>

                        <li class="{{ request('category_id') == 1 ? 'active' : '' }}">
                            <a href="/tim-kiem?category_id=1">Tủ lạnh</a>
                        </li>

                        <li class="{{ request('category_id') == 3 ? 'active' : '' }}">
                            <a href="/tim-kiem?category_id=3">Máy giặt</a>
                        </li>

                        <li class="{{ request('category_id') == 4 ? 'active' : '' }}">
                            <a href="/tim-kiem?category_id=4">Tivi</a>
                        </li>

                        <li class="{{ request('category_id') == 6 ? 'active' : '' }}">
                            <a href="/tim-kiem?category_id=6">Đồ gia dụng</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- /NAVIGATION -->
        
        @yield('content')
        
        <!-- NEWSLETTER -->
        <div id="newsletter" class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="newsletter">
                            <p>Đăng ký nhận <strong>KHUYẾN MÃI MỚI NHẤT</strong></p>

                            <form>
                                <input class="input" type="email" placeholder="Nhập email của bạn">

                                <button class="newsletter-btn">
                                    <i class="fa fa-envelope"></i> Đăng ký
                                </button>
                            </form>

                            <ul class="newsletter-follow">
                                <li>
                                    <a href="#"><i class="fa fa-facebook"></i></a>
                                </li>

                                <li>
                                    <a href="#"><i class="fa fa-twitter"></i></a>
                                </li>

                                <li>
                                    <a href="#"><i class="fa fa-instagram"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /NEWSLETTER -->

        <!-- FOOTER -->
        <footer id="footer">
            <div class="section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="footer">
                                <h3 class="footer-title">Về chúng tôi</h3>

                                <p>
                                    Cửa hàng đồ điện gia dụng HomeTech luôn mang đến các sản phẩm tốt nhất.
                                </p>

                                <ul class="footer-links">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-map-marker"></i>Hà Nội, Việt Nam
                                        </a>
                                    </li>

                                    <li>
                                        <a href="tel:+021955184">
                                            <i class="fa fa-phone"></i>+021-95-51-84
                                        </a>
                                    </li>

                                    <li>
                                        <a href="mailto:hometech@email.com?subject=Phản hồi thông tin&body=Xin chào HomeTech,">
                                            <i class="fa fa-envelope-o"></i>hometech@email.com
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /FOOTER -->

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/slick.min.js') }}"></script>
        <script src="{{ asset('js/nouislider.min.js') }}"></script>
        <script src="{{ asset('js/jquery.zoom.min.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        
        @yield('script')
    </body>
</html>
