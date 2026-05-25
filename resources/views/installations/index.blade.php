@extends('layouts.tech')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Đơn lắp đặt hôm nay</h1>
    <span class="badge badge-primary px-3 py-2">{{ $orders->total() }} đơn được phân công</span>
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
        <h6 class="m-0 font-weight-bold text-primary">Danh sách cần giao/lắp</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Mã ĐH</th>
                        <th class="text-left">Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Địa chỉ</th>
                        <th>Phân công</th>
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
                            <td>{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                            <td class="text-left">{{ $order->delivery_address ?: 'N/A' }}</td>
                            <td>{{ $order->installation_assigned_at ? date('d/m/Y H:i', strtotime($order->installation_assigned_at)) : 'N/A' }}</td>
                            <td>
                                <a href="/lap-dat/{{ $order->id }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-clipboard-check"></i> Cập nhật
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Không có đơn lắp đặt được phân công hôm nay.</td>
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
