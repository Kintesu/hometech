@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Lịch sử Nhập Kho</h1>
    <p class="mb-4">Danh sách các phiếu nhập hàng vào hệ thống HomeTech.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách phiếu nhập</h6>
            <div>
                <a href="/quantri/kho/ton-kho" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Trở về Tồn kho
                </a>
                <a href="/quantri/kho/nhap-kho" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tạo phiếu nhập mới
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="15%" class="text-center">Mã Phiếu</th>
                            <th width="30%">Ngày nhập</th>
                            <th width="30%">Tổng giá trị</th>
                            <th width="25%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($receipts->count() > 0)
                            @foreach($receipts as $phieu)
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-primary">PN-{{ $phieu->id }}</td>
                                <td class="align-middle">{{ date('d/m/Y H:i', strtotime($phieu->import_date)) }}</td>
                                <td class="text-danger font-weight-bold align-middle">{{ number_format($phieu->total_value, 0, ',', '.') }} đ</td>
                                <td class="text-center align-middle">
                                    <a href="/quantri/kho/chi-tiet/{{ $phieu->id }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">Chưa có phiếu nhập kho nào trong dữ liệu.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection