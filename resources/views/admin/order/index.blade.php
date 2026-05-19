@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Quản lý Đơn hàng</h1>
    <p class="mb-4">Theo dõi và cập nhật trạng thái đơn hàng của khách.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng mới nhất</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Mã ĐH</th>
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
                            <td>{{ $don->user ? $don->user->name : 'Khách ID: '.$don->user_id }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($don->order_date)) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($don->total_price, 0, ',', '.') }} đ</td>
                            
                            <td>
                                @if($don->status == 'Pending') <span class="badge badge-warning">Chờ xử lý</span>
                                @elseif($don->status == 'Confirmed') <span class="badge badge-info">Đã xác nhận</span>
                                @elseif($don->status == 'Shipping') <span class="badge badge-primary">Đang giao hàng</span>
                                @elseif($don->status == 'Completed') <span class="badge badge-success">Đã hoàn thành</span>
                                @elseif($don->status == 'Canceled') <span class="badge badge-danger">Đã hủy</span>
                                @else <span class="badge badge-secondary">{{ $don->status }}</span>
                                @endif
                            </td>

                            <td>
                                <form action="/quantri/don-hang/cap-nhat/{{ $don->id }}" method="POST" class="d-flex justify-content-center">
                                    @csrf
                                    <select name="status" class="form-control form-control-sm mr-1" style="width: 140px;">
                                        <option value="Pending" {{ $don->status == 'Pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="Confirmed" {{ $don->status == 'Confirmed' ? 'selected' : '' }}>Xác nhận</option>
                                        <option value="Shipping" {{ $don->status == 'Shipping' ? 'selected' : '' }}>Đang giao</option>
                                        <option value="Completed" {{ $don->status == 'Completed' ? 'selected' : '' }}>Hoàn thành</option>
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
                            <td colspan="7" class="text-center">Chưa có đơn hàng nào trong hệ thống.</td>
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