@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Quản lý Đơn hàng</h1>
    <p class="mb-4">Theo dõi và cập nhật trạng thái đơn hàng của khách.</p>

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
        <div class="card-body">
            <form action="/quantri/don-hang" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label for="order-search" class="small font-weight-bold text-gray-700">Tìm kiếm</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input
                                type="text"
                                id="order-search"
                                name="q"
                                class="form-control"
                                placeholder="Mã đơn, tên khách, SĐT, địa chỉ..."
                                value="{{ $keyword }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label for="order-status" class="small font-weight-bold text-gray-700">Trạng thái</label>
                        <select id="order-status" name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <a href="/quantri/don-hang" class="btn btn-secondary">Tải lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
            <span class="small text-muted">{{ $orders->total() }} kết quả</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Tên đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Cập nhật</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $don)
                        <tr>
                            <td class="font-weight-bold text-primary">DH-{{ $don->id }}</td>
                            <td class="text-left">
                                @php
                                    $firstItem = $don->details->first();
                                    $otherItemsCount = max($don->details->count() - 1, 0);
                                @endphp

                                @if($firstItem && $firstItem->product)
                                    <span class="font-weight-bold">{{ $firstItem->product->name }}</span>
                                    @if($otherItemsCount > 0)
                                        <div class="small text-muted">+{{ $otherItemsCount }} sản phẩm khác</div>
                                    @endif
                                @else
                                    <span class="text-muted">Chưa có sản phẩm</span>
                                @endif
                            </td>
                            <td>{{ $don->user ? $don->user->full_name : 'Khách ID: '.$don->user_id }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($don->order_date)) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($don->total_price, 0, ',', '.') }} đ</td>
                            
                            <td>
                                @if($don->status == 'Pending') <span class="badge badge-warning">Chờ xử lý</span>
                                @elseif($don->status == 'Shipping') <span class="badge badge-primary">Đang giao hàng</span>
                                @elseif($don->status == 'Completed') <span class="badge badge-success">Đã hoàn thành</span>
                                @elseif($don->status == 'InstallationFailed') <span class="badge badge-danger">Lắp đặt thất bại</span>
                                @elseif($don->status == 'Canceled') <span class="badge badge-danger">Đã hủy</span>
                                @else <span class="badge badge-secondary">{{ $don->status }}</span>
                                @endif
                            </td>

                            <td>
                                <form action="/quantri/don-hang/cap-nhat/{{ $don->id }}" method="POST" class="d-flex justify-content-center">
                                    @csrf
                                    <select name="status" class="form-control form-control-sm mr-1" style="width: 140px;">
                                        <option value="Pending" {{ $don->status == 'Pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        @if($don->status != 'Pending')
                                            <option value="Shipping" {{ $don->status == 'Shipping' ? 'selected' : '' }}>Đang giao</option>
                                        @endif
                                        <option value="Completed" {{ $don->status == 'Completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="InstallationFailed" {{ $don->status == 'InstallationFailed' ? 'selected' : '' }}>Lắp đặt thất bại</option>
                                        <option value="Canceled" {{ $don->status == 'Canceled' ? 'selected' : '' }}>Hủy đơn</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                </form>
                            </td>

                            <td>
                                <a href="/quantri/don-hang/chi-tiet/{{ $don->id }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Không tìm thấy đơn hàng phù hợp.</td>
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
</div>
@endsection
