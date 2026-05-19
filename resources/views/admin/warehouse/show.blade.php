@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Chi tiết Phiếu Nhập Kho: #PN-{{ $receipt->id }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice"></i> Thông tin phiếu nhập</h6>
            <button class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> In phiếu</button>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="mb-2"><strong>Nhà cung cấp:</strong></h6>
                    <div>Tên: <strong>{{ $receipt->supplier ? $receipt->supplier->name : 'Không xác định' }}</strong></div>
                    <div>Điện thoại: {{ $receipt->supplier ? $receipt->supplier->phone : 'N/A' }}</div>
                    <div>Địa chỉ: {{ $receipt->supplier ? $receipt->supplier->address : 'N/A' }}</div>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <h6 class="mb-2"><strong>Chi tiết chứng từ:</strong></h6>
                    <div>Mã phiếu: <strong>PN-{{ $receipt->id }}</strong></div>
                    <div>Ngày lập: {{ date('d/m/Y H:i', strtotime($receipt->import_date)) }}</div>
                    <div>Người lập: ID {{ $receipt->staff_id }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="15%">Mã SP</th>
                            <th width="35%" class="text-left">Tên hàng hóa</th>
                            <th width="15%">Số lượng</th>
                            <th width="15%">Đơn giá nhập</th>
                            <th width="15%">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $stt = 1; @endphp
                        @foreach($receipt->details as $item)
                        <tr>
                            <td>{{ $stt++ }}</td>
                            <td>SP-{{ $item->product_id }}</td>
                            <td class="text-left font-weight-bold">{{ $item->product ? $item->product->name : 'Sản phẩm đã bị xóa' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->import_price, 0, ',', '.') }} đ</td>
                            <td class="text-danger font-weight-bold">{{ number_format($item->quantity * $item->import_price, 0, ',', '.') }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right h5 mb-0">Tổng giá trị phiếu nhập:</th>
                            <th class="text-danger h5 mb-0">{{ number_format($receipt->total_value, 0, ',', '.') }} đ</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection