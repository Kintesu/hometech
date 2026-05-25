@extends('layouts.pos')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hóa đơn POS #DH-{{ $order->id }}</h1>
        <div>
            <a href="{{ url('/pos') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Tạo đơn mới
            </a>
            <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()">
                <i class="fas fa-print fa-sm text-white-50"></i> In hóa đơn
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%"><strong>Ngày bán:</strong></td>
                            <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Trạng thái:</strong></td>
                            <td>
                                @if($order->status == 'Completed')
                                    <span class="badge badge-success">Đã hoàn thành</span>
                                @elseif($order->status == 'Pending')
                                    <span class="badge badge-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge badge-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Phương thức:</strong></td>
                            <td>{{ $order->payment_method }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tiền khách đưa:</strong></td>
                            <td>{{ number_format($order->received_amount ?? 0, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr>
                            <td><strong>Tiền thối:</strong></td>
                            <td>{{ number_format($order->change_amount ?? 0, 0, ',', '.') }} đ</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Khách hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="35%"><strong>Họ tên:</strong></td>
                            <td>{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Điện thoại:</strong></td>
                            <td>{{ $order->user ? $order->user->phone : 'N/A' }}</td>
                        </tr>
                        @if(!empty($order->delivery_address))
                        <tr>
                            <td><strong>Địa chỉ giao/lắp:</strong></td>
                            <td>{{ $order->delivery_address }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sản phẩm</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>STT</th>
                            <th class="text-left">Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-left">{{ $item->product ? $item->product->name : 'Sản phẩm không tồn tại' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                            <td class="text-danger font-weight-bold">{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right h5 mb-0">TỔNG CỘNG:</th>
                            <th class="text-danger h5 mb-0">{{ number_format($order->total_price, 0, ',', '.') }} đ</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
