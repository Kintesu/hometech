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
        
        @yield('css')

    </head>
    <body>
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
            <div id="top-header">
                <div class="container">
                    <ul class="header-links pull-left">
                        <li><a href="#"><i class="fa fa-phone"></i> +021-95-51-84</a></li>
                        <li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
                        <li><a href="#"><i class="fa fa-map-marker"></i> 1734 Stonecoal Road</a></li>
                    </ul>
                    <ul class="header-links pull-right">
                        <li><a href="#"><i class="fa fa-dollar"></i> VNĐ</a></li>
                        <li><a href="#"><i class="fa fa-user-o"></i> Tài khoản</a></li>
                    </ul>
                </div>
            </div>
            <div id="header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="header-logo">
                                <a href="/" class="logo">
                                    <img src="{{ asset('img/logo2.png') }}" alt="HomeTech Logo">
                                </a>
                            </div>
                        </div>
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
                                    <input class="input" placeholder="Tìm kiếm sản phẩm..." name="keyword" value="{{ request('keyword') }}">
                                    <button type="submit" class="search-btn">Tìm kiếm</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-3 clearfix">
                            <div class="header-ctn">
                                <div>
                                    <a href="/yeu-thich">
                                        <i class="fa fa-heart-o"></i>
                                        <span>Yêu thích</span>
                                        <div class="qty">0</div>
                                    </a>
                                </div>
                                
                                <div class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="cursor: pointer;">
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
                                                                <img src="{{ asset('uploads/products/' . $item['image']) }}" alt="{{ $item['name'] }}" style="object-fit: cover; height: 100%;">
                                                            @else
                                                                <img src="{{ asset('img/product01.png') }}" alt="No Image" style="opacity: 0.5;">
                                                            @endif
                                                        </div>
                                                        <div class="product-body">
                                                            <h3 class="product-name"><a href="/san-pham/{{ $id }}">{{ $item['name'] }}</a></h3>
                                                            <h4 class="product-price"><span class="qty">{{ $item['quantity'] }}x</span>{{ number_format($item['price'], 0, ',', '.') }} đ</h4>
                                                        </div>
                                                        <a href="/gio-hang/xoa/{{ $id }}" class="delete"><i class="fa fa-close"></i></a>
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
                                            <a href="/thanh-toan">Thanh toán <i class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-toggle">
                                    <a href="#">
                                        <i class="fa fa-bars"></i>
                                        <span>Menu</span>
                                    </a>
                                </div>
                                </div>
                        </div>
                        </div>
                </div>
            </div>
            </header>
        <nav id="navigation">
            <div class="container">
                <div id="responsive-nav">
                    <ul class="main-nav nav navbar-nav">
                        <li class="{{ request()->is('/') ? 'active' : '' }}">
                            <a href="/">Trang chủ</a>
                        </li>
                        
                        <li><a href="#">Khuyến mãi</a></li> <li class="{{ request('category_id') == 1 ? 'active' : '' }}">
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
        
        @yield('content')
        
        <div id="newsletter" class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="newsletter">
                            <p>Đăng ký nhận <strong>KHUYẾN MÃI MỚI NHẤT</strong></p>
                            <form>
                                <input class="input" type="email" placeholder="Nhập email của bạn">
                                <button class="newsletter-btn"><i class="fa fa-envelope"></i> Đăng ký</button>
                            </form>
                            <ul class="newsletter-follow">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer id="footer">
            <div class="section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="footer">
                                <h3 class="footer-title">Về chúng tôi</h3>
                                <p>Cửa hàng đồ điện gia dụng HomeTech luôn mang đến các sản phẩm tốt nhất.</p>
                                <ul class="footer-links">
                                    <li><a href="#"><i class="fa fa-map-marker"></i>Hà Nội, Việt Nam</a></li>
                                    <li><a href="#"><i class="fa fa-phone"></i>+021-95-51-84</a></li>
                                    <li><a href="#"><i class="fa fa-envelope-o"></i>hometech@email.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/slick.min.js') }}"></script>
        <script src="{{ asset('js/nouislider.min.js') }}"></script>
        <script src="{{ asset('js/jquery.zoom.min.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        
        @yield('script')

    </body>
</html>