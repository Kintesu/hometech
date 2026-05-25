@extends('layouts.tech')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cập nhật lắp đặt #DH-{{ $order->id }}</h1>
    <a href="/lap-dat" class="btn btn-sm btn-secondary">
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
                <h6 class="m-0 font-weight-bold text-primary">Thông tin giao/lắp</h6>
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
                        <td><strong>Địa chỉ:</strong></td>
                        <td>{{ $order->delivery_address ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Trạng thái:</strong></td>
                        <td><span class="badge badge-primary">Đang giao/lắp</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kết quả lắp đặt</h6>
            </div>
            <div class="card-body">
                @if($order->status === 'Shipping')
                    <form action="/lap-dat/{{ $order->id }}/trang-thai" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="status" value="Completed">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Cập nhật trạng thái: Hoàn thành
                        </button>
                    </form>

                    <form action="/lap-dat/{{ $order->id }}/trang-thai" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="InstallationFailed">
                        <div class="input-group">
                            <input
                                type="text"
                                name="reason"
                                class="form-control"
                                placeholder="Lý do thất bại: khách vắng nhà, hàng lỗi..."
                                required
                            >
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i> Lắp đặt thất bại
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info mb-0">Đơn hàng này đã được cập nhật trạng thái.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã SP</th>
                        <th class="text-left">Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Ghi chú</th>
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
                                @if($item->product && $item->product->requires_installation)
                                    <span class="badge badge-warning">Cần lắp đặt</span>
                                @else
                                    <span class="badge badge-secondary">Giao hàng</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Đơn hàng chưa có sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
