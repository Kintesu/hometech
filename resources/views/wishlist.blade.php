@extends('layouts.client')

@section('content')
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h3 class="title">Sản phẩm yêu thích</h3>
                </div>
            </div>

            @if(count($wishlist) > 0)
                @foreach($wishlist as $item)
                    <div class="col-md-3 col-xs-6">
                        <div class="product">
                            <div class="product-img">
                                <a href="/san-pham/{{ $item['id'] }}">
                                    @if(isset($item['image']) && $item['image'] && file_exists(public_path('uploads/products/' . $item['image'])))
                                        <img src="{{ asset('uploads/products/' . $item['image']) }}"
                                             alt="{{ $item['name'] }}"
                                             style="height: 200px; object-fit: cover; width: 100%;">
                                    @else
                                        <img src="{{ asset('img/product01.png') }}"
                                             alt="No Image"
                                             style="height: 200px; object-fit: cover; width: 100%; opacity: 0.3;">
                                    @endif
                                </a>
                            </div>

                            <div class="product-body">
                                <p class="product-category">Đồ gia dụng</p>

                                <h3 class="product-name">
                                    <a href="/san-pham/{{ $item['id'] }}">
                                        {{ $item['name'] }}
                                    </a>
                                </h3>

                                <h4 class="product-price">
                                    {{ number_format($item['price'], 0, ',', '.') }} đ
                                </h4>
                            </div>

                            <form action="/gio-hang/them/{{ $item['id'] }}" method="POST" class="add-to-cart">
                                @csrf
                                <input type="hidden" name="quantity" value="1">

                                <button type="submit" class="add-to-cart-btn">
                                    <i class="fa fa-shopping-cart"></i>
                                    Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-md-12">
                    <p>Bạn chưa có sản phẩm yêu thích nào.</p>
                    <a href="/" class="primary-btn">Tiếp tục mua sắm</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection