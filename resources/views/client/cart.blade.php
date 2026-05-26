@extends('layouts.client')

@section('title', 'Giỏ hàng của bạn - HomeTech')

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="breadcrumb-header">Giỏ Hàng</h3>
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="active">Giỏ hàng</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="section">
    <div class="container">
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="15%">Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-center" width="15%">Đơn giá</th>
                            <th class="text-center" width="10%">Số lượng</th>
                            <th class="text-center" width="15%">Thành tiền</th>
                            <th class="text-center" width="10%">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(session('cart') && count(session('cart')) > 0)
                            @foreach(session('cart') as $id => $item)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ asset('uploads/products/' . $item['image']) }}" width="80" alt="{{ $item['name'] }}">
                                    </td>
                                    <td class="align-middle"><strong>{{ $item['name'] }}</strong></td>
                                    <td class="text-center align-middle text-danger">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                    <td class="text-center align-middle">
                                        {{ $item['quantity'] }}
                                    </td>
                                    <td class="text-center align-middle text-danger font-weight-bold">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="/gio-hang/xoa/{{ $id }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="text-right"><h4>Tổng cộng:</h4></td>
                                <td colspan="2" class="text-center text-danger"><h4>{{ number_format($total, 0, ',', '.') }} đ</h4></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6" class="text-center">
                                    <h4>Giỏ hàng của bạn đang trống!</h4>
                                    <a href="/" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if(session('cart') && count(session('cart')) > 0)
            <div class="col-md-12 text-right mt-4">
                <a href="/" class="btn btn-default" style="border: 1px solid #ccc; padding: 10px 20px;">TIẾP TỤC MUA SẮM</a>
                <a href="#"
                   class="btn btn-danger feature-pending"
                   style="padding: 10px 20px; font-weight: bold;"
                   title="Chức năng thanh toán đang cập nhật"
                   onclick="return false;">TIẾN HÀNH THANH TOÁN</a>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
