@extends('layouts.warehouse')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kiểm tra xuất kho #DH-{{ $order->id }}</h1>
    <a href="/kho/xuat-kho" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
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

<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="35%"><strong>Khách hàng:</strong></td>
                        <td>{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Điện thoại:</strong></td>
                        <td>{{ $order->user ? $order->user->phone : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Địa chỉ giao/lắp:</strong></td>
                        <td>{{ $order->delivery_address ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Trạng thái:</strong></td>
                        <td><span class="badge badge-warning">Chờ xử lý</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Xử lý xuất kho</h6>
            </div>
            <div class="card-body">
                <form action="/kho/xuat-kho/{{ $order->id }}" method="POST" class="mb-3">
                    @csrf
                    @php
                        $requiresInstallation = $order->details->contains(function ($detail) {
                            return $detail->product && $detail->product->requires_installation;
                        });
                    @endphp
                    <div class="form-group">
                        <label class="font-weight-bold" for="assigned_staff_tech_id">
                            Nhân viên lắp đặt
                            @if($requiresInstallation)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <select name="assigned_staff_tech_id" id="assigned_staff_tech_id" class="form-control">
                            <option value="">Chưa phân công</option>
                            @foreach($staffTechs as $staffTech)
                                <option value="{{ $staffTech->id }}">{{ $staffTech->full_name }}</option>
                            @endforeach
                        </select>
                        @if($requiresInstallation)
                            <small class="form-text text-muted">Đơn có sản phẩm cần lắp đặt nên phải chọn nhân viên phụ trách.</small>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-dolly"></i> Xác nhận xuất kho
                    </button>
                </form>

                <form action="/kho/tu-choi-xuat-kho/{{ $order->id }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input
                            type="text"
                            name="reason"
                            class="form-control"
                            placeholder="Nhập lý do từ chối: hàng thiếu, hàng lỗi..."
                            required
                        >
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Từ chối xuất kho
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm cần soạn</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã SP</th>
                        <th class="text-left">Tên sản phẩm</th>
                        <th>Số lượng xuất</th>
                        <th>Tồn kho hiện tại</th>
                        <th>Ghi nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->details as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>SP-{{ $item->product_id }}</td>
                            <td class="text-left font-weight-bold">{{ $item->product ? $item->product->name : 'Sản phẩm không tồn tại' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if($item->product)
                                    <span class="{{ $item->product->stock_quantity < $item->quantity ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">
                                        {{ $item->product->stock_quantity }}
                                    </span>
                                @else
                                    <span class="text-danger font-weight-bold">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(!$item->product || $item->product->stock_quantity < $item->quantity)
                                    <span class="badge badge-danger">Thiếu/lỗi</span>
                                @else
                                    <span class="badge badge-success">Đủ hàng</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Đơn hàng chưa có sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
