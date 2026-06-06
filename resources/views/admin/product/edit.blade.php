@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Cập nhật Sản phẩm</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">Vui lòng kiểm tra lại thông tin sản phẩm.</div>
            @endif

            <form action="/quantri/san-pham/sua/{{ $product->id }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Loại đồ gia dụng (Danh mục)</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Nhà cung cấp</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="requires_installation" value="1" class="form-check-input" id="requiresInstallation" {{ old('requires_installation', $product->requires_installation) ? 'checked' : '' }}>
                    <label class="form-check-label" for="requiresInstallation">Sản phẩm yêu cầu giao hàng/lắp đặt</label>
                </div>

                <div class="form-group">
                    <label>Mô tả sản phẩm</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả giới thiệu...">{{ old('description', $product->description) }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Thông số kỹ thuật</label>
                    <textarea name="specifications" class="form-control" rows="4" placeholder="Ví dụ: Kích thước, Công suất, Trọng lượng...">{{ old('specifications', $product->specifications) }}</textarea>
                    @error('specifications') <small class="text-danger">{{ $message }}</small> @enderror
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
                    @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                <a href="/quantri/san-pham" class="btn btn-secondary">Hủy bỏ</a>
            </form>
        </div>
    </div>
</div>
@endsection
