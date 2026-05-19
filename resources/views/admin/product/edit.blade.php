@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Cập nhật Sản Phẩm</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/quantri/san-pham/sua/{{ $product->id }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                
                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                </div>

                <div class="form-group">
                    <label>Loại đồ gia dụng (Danh mục)</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhà cung cấp</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ $product->supplier_id == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
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
                    <label>Hình ảnh hiện tại</label><br>
                    @if($product->image)
                        <img src="{{ asset('uploads/products/' . $product->image) }}" width="150" class="mb-2 img-thumbnail">
                    @else
                        <span class="text-danger">Chưa có ảnh</span><br>
                    @endif
                    <br>
                    <label>Chọn ảnh mới (để trống nếu không muốn đổi)</label>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                <a href="/quantri/san-pham" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>
@endsection