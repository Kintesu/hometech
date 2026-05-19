@extends('layouts.client')

@section('title', $product->name . ' - HomeTech')

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="#">Sản phẩm</a></li>
                    <li class="active">{{ $product->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="section">
    <div class="container">
        <div class="row">
            
            <div class="col-md-5">
                <div id="product-main-img" style="display: flex; justify-content: center; align-items: center; min-height: 450px; background-color: #f9f9f9; border-radius: 5px;">
                    <div class="product-preview" style="display: flex; justify-content: center;">
                        @if($product->image && file_exists(public_path('uploads/products/' . $product->image)))
                            <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 320px; width: 100%; object-fit: contain; max-height: 400px;">
                        @else
                            <img src="{{ asset('img/product01.png') }}" alt="No Image" style="max-width: 320px; width: 100%; object-fit: contain; max-height: 400px; opacity: 0.3;">
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="product-details">
                    <h2 class="product-name">{{ $product->name }}</h2>
                    
                    <div>
                        <h3 class="product-price text-danger">{{ number_format($product->price, 0, ',', '.') }} VNĐ</h3>
                        <span class="product-available {{ $product->stock_quantity > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $product->stock_quantity > 0 ? 'Còn hàng ('.$product->stock_quantity.')' : 'Hết hàng' }}
                        </span>
                    </div>

                    <form action="/gio-hang/them/{{ $product->id }}" method="POST" class="add-to-cart mt-4" style="margin-bottom: 30px;">
                        @csrf
                        <div class="qty-label">
                            Số lượng
                            <div class="input-number" style="width: 100px; display: inline-block; margin-left: 10px;">
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                                <span class="qty-up">+</span>
                                <span class="qty-down">-</span>
                            </div>
                        </div>
                        <button type="submit" class="add-to-cart-btn" {{ $product->stock_quantity == 0 ? 'disabled' : '' }}>
                            <i class="fa fa-shopping-cart"></i> THÊM VÀO GIỎ
                        </button>
                    </form>

                    <div id="product-tab" style="margin-top: 20px; border-top: 1px solid #E4E7ED; padding-top: 20px;">
                        <ul class="tab-nav">
                            <li class="active"><a data-toggle="tab" href="#tab1" style="font-size: 15px; font-weight: bold;">Mô tả sản phẩm</a></li>
                            <li><a data-toggle="tab" href="#tab2" style="font-size: 15px; font-weight: bold;">Thông số kỹ thuật</a></li>
                        </ul>
                        
                        <div class="tab-content" style="max-height: 300px; overflow-y: auto; padding: 20px; background: #fbfbfb; border: 1px solid #eee; border-radius: 5px; margin-top: 15px;">
                            
                            <div id="tab1" class="tab-pane fade in active">
                                <p style="white-space: pre-line; line-height: 1.8; color: #555; text-align: justify;">
                                    {{ $product->description ?? 'Đang cập nhật mô tả cho sản phẩm này...' }}
                                </p>
                            </div>
                            
                            <div id="tab2" class="tab-pane fade in">
                                <p style="white-space: pre-line; line-height: 1.8; color: #555;">
                                    {{ $product->specifications ?? 'Đang cập nhật thông số kỹ thuật...' }}
                                </p>
                            </div>

                        </div>
                    </div>
                    </div>
            </div>
            
        </div>
    </div>
</div>
@endsection