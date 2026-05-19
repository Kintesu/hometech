@extends('layouts.client')

@section('title', 'Trang chủ - HomeTech')

@section('content')
<div class="section">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="section-title">
                    <h3 class="title">Sản Phẩm Mới Nhất</h3>
                    <div class="section-nav">
                        <ul class="section-tab-nav tab-nav">
                            <li class="active"><a href="#">Tất cả</a></li>
                            <li><a href="#">Tủ lạnh</a></li>
                            <li><a href="#">Máy giặt</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row" style="display: flex; flex-wrap: wrap;">
                    
                    @forelse($newProducts as $sp)
                        <div class="col-md-3 col-xs-6 mb-4" style="display: flex; flex-direction: column;">
                            <div class="product" style="flex: 1; display: flex; flex-direction: column; height: 100%;">
                                <div class="product-img">
                                    <a href="/san-pham/{{ $sp->id }}">
                                        @if($sp->image && file_exists(public_path('uploads/products/' . $sp->image)))
                                            <img src="{{ asset('uploads/products/' . $sp->image) }}" alt="{{ $sp->name }}" style="height: 200px; width: 100%; object-fit: contain; background-color: #fff; padding: 5px;">
                                        @else
                                            <img src="{{ asset('img/product01.png') }}" alt="No Image" style="height: 200px; width: 100%; object-fit: contain; background-color: #fff; opacity: 0.3; padding: 5px;">
                                        @endif
                                    </a>
                                    
                                    <div class="product-label">
                                        @if($sp->discount_percent > 0)
                                            <span class="sale" style="background-color: #D10024; color: white; padding: 2px 8px; font-size: 12px; font-weight: bold; border-radius: 2px;">
                                                -{{ $sp->discount_percent }}%
                                            </span>
                                        @else
                                            <span class="new">MỚI</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="product-body" style="flex: 1;">
                                    <p class="product-category">Điện máy</p>
                                    
                                    <h3 class="product-name">
                                        <a href="/san-pham/{{ $sp->id }}">{{ $sp->name }}</a>
                                    </h3>
                                    
                                    @if($sp->discount_percent > 0)
                                        <h4 class="product-price text-danger">
                                            {{ number_format($sp->discounted_price, 0, ',', '.') }} đ
                                            <br>
                                            <del class="product-old-price text-muted" style="font-size: 14px; font-weight: normal;">
                                                {{ number_format($sp->price, 0, ',', '.') }} đ
                                            </del>
                                        </h4>
                                    @else
                                        <h4 class="product-price text-danger">
                                            {{ number_format($sp->price, 0, ',', '.') }} đ
                                            <br>
                                            <span style="visibility: hidden; font-size: 14px;">0 đ</span>
                                        </h4>
                                    @endif
                                    
                                    <p class="text-muted" style="font-size: 12px; margin-top: 5px;">
                                        Kho: {{ $sp->stock_quantity }} sản phẩm
                                    </p>
                                </div>
                                
                                <form action="/gio-hang/them/{{ $sp->id }}" method="POST" class="add-to-cart" style="margin-top: auto;">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> THÊM VÀO GIỎ</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12 text-center">
                            <p>Hiện chưa có sản phẩm nào trong cửa hàng.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection