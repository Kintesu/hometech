@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Quản lý Sản phẩm</h1>
    <p class="mb-4">Danh sách các đồ điện gia dụng hiện có trong hệ thống HomeTech</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm tên sản phẩm..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-control">
                            <option value="">-- Tất cả danh mục --</option>
                            
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc dữ liệu</button>
                        <a href="/quantri/san-pham" class="btn btn-secondary">Tải lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
            <a href="/quantri/san-pham/them" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">ID</th>
                            <th width="10%" class="text-center">Hình ảnh</th>
                            <th width="20%">Tên sản phẩm</th>
                            
                            <th width="30%">Mô tả ngắn</th>
                            
                            <th width="15%">Giá bán</th>
                            <th width="20%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->count() > 0)
                            @foreach($products as $sp)
                            <tr>
                                <td class="text-center align-middle">{{ $sp->id }}</td>
                                
                                <td class="text-center align-middle">
                                    @if($sp->image)
                                        <img src="{{ asset('uploads/products/' . $sp->image) }}" alt="{{ $sp->name }}" width="80" class="img-thumbnail">
                                    @else
                                        <span class="text-muted small">Chưa có ảnh</span>
                                    @endif
                                </td>

                                <td class="align-middle font-weight-bold">{{ $sp->name }}</td>
                                
                                <td class="align-middle text-muted" style="font-size: 13px;">
                                    {{ \Illuminate\Support\Str::limit($sp->description, 70, '...') }}
                                </td>

                                <td class="text-danger font-weight-bold align-middle">{{ number_format($sp->price, 0, ',', '.') }} đ</td>
                                <td class="text-center align-middle">
                                    <a href="/quantri/san-pham/sua/{{ $sp->id }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="/quantri/san-pham/xoa/{{ $sp->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Chưa có sản phẩm nào trong dữ liệu.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $products->appends(request()->all())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection