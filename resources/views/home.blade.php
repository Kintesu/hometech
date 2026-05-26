@extends('layouts.client')

@section('content')
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/TuLanh.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Tủ Lạnh<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/DieuHoa.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Điều Hòa<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/Quat.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Các đồ điện gia dụng khác<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/MayLocNuoc.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Máy Lọc Nước<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/MayGiat.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Máy Giặt<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="{{ asset('images/Tivi.jpg') }}" alt="">
                        </div>
                        <div class="shop-body">
                            <h3>Tivi<br></h3>
                            <a href="#" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Sản phẩm mới</h3>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <div id="tab1" class="tab-pane active">
                                <div class="products-slick" data-nav="#slick-nav-1">
                                    
                                    @foreach($products as $sp)
                                    <div class="product">
                                        <div class="product-img">
                                            <a href="/san-pham/{{ $sp->id }}">
                                                @if($sp->image && file_exists(public_path('uploads/products/' . $sp->image)))
                                                    <img src="{{ asset('uploads/products/' . $sp->image) }}" alt="{{ $sp->name }}" style="height: 200px; object-fit: cover; width: 100%;">
                                                @else
                                                    <img src="{{ asset('img/product01.png') }}" alt="No Image" style="height: 200px; object-fit: cover; width: 100%; opacity: 0.3;">
                                                @endif
                                            </a>
                                            <div class="product-label">
                                                <span class="new">MỚI</span>
                                            </div>
                                        </div>
                                        <div class="product-body">
                                            <p class="product-category">Đồ gia dụng</p>
                                            
                                            <h3 class="product-name"><a href="/san-pham/{{ $sp->id }}">{{ $sp->name }}</a></h3>
                                            
                                            <h4 class="product-price">{{ number_format($sp->price, 0, ',', '.') }} đ</h4>
                                            
                                            <div class="product-rating">
                                                <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                            </div>
                                            <div class="product-btns">
                                                <button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">so sánh</span></button>
                                                <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">xem nhanh</span></button>
                                            </div>
                                        </div>
                                        
                                        <form action="/gio-hang/them/{{ $sp->id }}" method="POST" class="add-to-cart">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Thêm vào giỏ</button>
                                        </form>
                                        
                                    </div>
                                    @endforeach
                                    </div>
                                <div id="slick-nav-1" class="products-slick-nav"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
