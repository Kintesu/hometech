@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Quản lý Tồn Kho</h1>
    <p class="mb-4">Theo dõi số lượng hàng hóa hiện có, cảnh báo hàng sắp hết.</p>

    <div class="mb-4">
        <a href="/quantri/kho/nhap-kho" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tạo phiếu nhập kho
        </a>
        <a href="/quantri/kho/lich-su" class="btn btn-info shadow-sm ml-2">
            <i class="fas fa-history fa-sm text-white-50"></i> Lịch sử nhập kho
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Hàng tồn kho</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="10%" class="text-center">Mã SP</th>
                            <th width="15%" class="text-center">Hình ảnh</th>
                            <th width="40%">Tên sản phẩm</th>
                            <th width="15%" class="text-center">Số lượng tồn</th>
                            <th width="20%" class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->count() > 0)
                            @foreach($products as $sp)
                            <tr>
                                <td class="text-center align-middle">{{ $sp->id }}</td>
                                <td class="text-center align-middle">
                                    @if($sp->image)
                                        <img src="{{ asset('uploads/products/' . $sp->image) }}" width="60" class="img-thumbnail">
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold">{{ $sp->name }}</td>
                                
                                <td class="text-center align-middle font-weight-bold" style="font-size: 1.1rem;">
                                    {{ $sp->stock_quantity ?? 0 }}
                                </td>

                                <td class="text-center align-middle">
                                    @if(empty($sp->stock_quantity) || $sp->stock_quantity == 0)
                                        <span class="badge badge-danger px-3 py-2">Hết hàng</span>
                                    @elseif($sp->stock_quantity <= 5)
                                        <span class="badge badge-warning px-3 py-2 text-dark">Sắp hết (Cần nhập)</span>
                                    @else
                                        <span class="badge badge-success px-3 py-2">Còn hàng</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Chưa có sản phẩm nào.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection