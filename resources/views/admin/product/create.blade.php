@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Sản Phẩm Mới</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/quantri/san-pham/them" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" required placeholder="Nhập tên đồ gia dụng...">
                </div>
                
                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" class="form-control" required placeholder="Ví dụ: 8500000">
                </div>

                <div class="form-group">
                    <label>Loại đồ gia dụng (Danh mục)</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhà cung cấp</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Mô tả sản phẩm</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả giới thiệu...">{{ isset($product) ? $product->description : '' }}</textarea>
                </div>

                <div class="form-group">
                    <label>Thông số kỹ thuật</label>
                    <textarea name="specifications" class="form-control" rows="4" placeholder="Ví dụ: Kích thước, Công suất, Trọng lượng...">{{ isset($product) ? $product->specifications : '' }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Hình ảnh sản phẩm</label>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu sản phẩm</button>
                <a href="/quantri/san-pham" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>
@endsection