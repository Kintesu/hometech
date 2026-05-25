@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn hàng #DH-{{ $order->id }}</h1>
        <div>
            <a href="/quantri/don-hang" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại</a>
            <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print fa-sm text-white-50"></i> In hóa đơn</button>
        </div>
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
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%"><strong>Mã đơn hàng:</strong></td>
                            <td>DH-{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày đặt hàng:</strong></td>
                            <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phương thức thanh toán:</strong></td>
                            <td>
                                <span class="badge badge-info">{{ $order->payment_method ?? 'Chưa xác định' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Trạng thái:</strong></td>
                            <td>
                                @if($order->status == 'Pending') <span class="badge badge-warning">Chờ xử lý</span>
                                @elseif($order->status == 'Shipping') <span class="badge badge-primary">Đang giao hàng</span>
                                @elseif($order->status == 'Completed') <span class="badge badge-success">Đã hoàn thành</span>
                                @elseif($order->status == 'InstallationFailed') <span class="badge badge-danger">Lắp đặt thất bại</span>
                                @elseif($order->status == 'Canceled') <span class="badge badge-danger">Đã hủy</span>
                                @else <span class="badge badge-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @if(!is_null($order->received_amount))
                        <tr>
                            <td><strong>Tiền khách đưa:</strong></td>
                            <td>{{ number_format($order->received_amount, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr>
                            <td><strong>Tiền thối:</strong></td>
                            <td>{{ number_format($order->change_amount ?? 0, 0, ',', '.') }} đ</td>
                        </tr>
                        @endif
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

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="30%"><strong>Họ và tên:</strong></td>
                            <td>{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tên đăng nhập:</strong></td>
                            <td>{{ $order->user ? $order->user->username : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Điện thoại:</strong></td>
                            <td>{{ $order->user ? $order->user->phone : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ:</strong></td>
                            <td>{{ $order->user ? $order->user->address : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm mua</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="15%">Mã SP</th>
                            <th width="35%" class="text-left">Tên sản phẩm</th>
                            <th width="15%">Số lượng</th>
                            <th width="15%">Tồn kho hiện tại</th>
                            <th width="15%">Đơn giá</th>
                            <th width="15%">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($order->details && $order->details->count() > 0)
                            @php $stt = 1; @endphp
                            @foreach($order->details as $item)
                            <tr>
                                <td>{{ $stt++ }}</td>
                                <td>SP-{{ $item->product_id }}</td>
                                <td class="text-left font-weight-bold">{{ $item->product ? $item->product->name : 'Sản phẩm không tồn tại' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    @if($item->product)
                                        <span class="{{ $item->product->stock_quantity < $item->quantity ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $item->product->stock_quantity }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                <td class="text-danger font-weight-bold">{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">Chưa có dữ liệu chi tiết cho đơn hàng này.</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right h5 mb-0">TỔNG CỘNG:</th>
                            <th class="text-danger h5 mb-0">{{ number_format($order->total_price, 0, ',', '.') }} đ</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
