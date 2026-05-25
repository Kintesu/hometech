@extends('layouts.warehouse')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Đơn hàng chờ xuất kho</h1>
    <span class="badge badge-warning px-3 py-2">{{ $orders->total() }} đơn cần xử lý</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn cần soạn hàng</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Mã ĐH</th>
                        <th class="text-left">Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $firstItem = $order->details->first();
                            $otherItemsCount = max($order->details->count() - 1, 0);
                        @endphp
                        <tr>
                            <td class="font-weight-bold text-primary">DH-{{ $order->id }}</td>
                            <td class="text-left">
                                @if($firstItem && $firstItem->product)
                                    <span class="font-weight-bold">{{ $firstItem->product->name }}</span>
                                    @if($otherItemsCount > 0)
                                        <div class="small text-muted">+{{ $otherItemsCount }} sản phẩm khác</div>
                                    @endif
                                @else
                                    <span class="text-muted">Chưa có sản phẩm</span>
                                @endif
                            </td>
                            <td>{{ $order->user ? $order->user->full_name : 'Khách ID: '.$order->user_id }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            <td>
                                <a href="/kho/xuat-kho/{{ $order->id }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-clipboard-check"></i> Kiểm tra
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Không có đơn hàng chờ xuất kho.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
