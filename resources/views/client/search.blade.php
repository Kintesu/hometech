@extends('layouts.client')

@section('title', 'Kết quả tìm kiếm - HomeTech')

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="active">Tìm kiếm</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h3 class="title">
                        {{ $discount ? 'Sản phẩm khuyến mãi' : 'Kết quả tìm kiếm' }}
                        @if(!empty($keyword)) cho: "<span class="text-danger">{{ $keyword }}</span>" @endif
                    </h3>
                    <p>Tìm thấy {{ $products->total() }} sản phẩm phù hợp.</p>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    @forelse($products as $sp)
                        <div class="col-md-3 col-xs-6 mb-4" style="margin-bottom: 30px;">
                            <div class="product">
                                <div class="product-img">
                                    <a href="/san-pham/{{ $sp->id }}">
                                        @if($sp->image && file_exists(public_path('uploads/products/' . $sp->image)))
                                            <img src="{{ asset('uploads/products/' . $sp->image) }}" alt="{{ $sp->name }}" style="height: 200px; width: 100%; object-fit: contain; background-color: #fff; padding: 5px;">
                                        @else
                                            <img src="{{ asset('img/product01.png') }}" alt="No Image" style="height: 200px; width: 100%; object-fit: contain; background-color: #fff; padding: 5px; opacity: 0.3;">
                                        @endif
                                    </a>

                                    <div class="product-label">
                                        @if($sp->discount_percent > 0)
                                            <span class="sale" style="background-color: #D10024; color: white; padding: 2px 8px; font-size: 12px; font-weight: bold; border-radius: 2px;">
                                                -{{ $sp->discount_percent }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="product-body">
                                    <h3 class="product-name"><a href="/san-pham/{{ $sp->id }}">{{ $sp->name }}</a></h3>
                                    
                                    @if($sp->discount_percent > 0)
                                        <h4 class="product-price text-danger">
                                            {{ number_format($sp->discounted_price, 0, ',', '.') }} đ
                                            <br>
                                            <del class="product-old-price text-muted" style="font-size: 14px; font-weight: normal;">
                                                {{ number_format($sp->price, 0, ',', '.') }} đ
                                            </del>
                                        </h4>
                                    @else
                                        <h4 class="product-price text-danger">{{ number_format($sp->price, 0, ',', '.') }} đ</h4>
                                    @endif

                                </div>
                                <form action="/gio-hang/them/{{ $sp->id }}" method="POST" class="add-to-cart">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> THÊM VÀO GIỎ</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12 text-center" style="padding: 50px 0;">
                            <i class="fa fa-search" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                            <h4>Rất tiếc, không tìm thấy sản phẩm nào phù hợp với tìm kiếm của bạn!</h4>
                            <a href="/" class="btn btn-danger mt-3">Quay lại trang chủ</a>
                        </div>
                    @endforelse
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-center mt-4">
                        {{ $products->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
