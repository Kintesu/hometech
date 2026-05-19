@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo Phiếu Nhập Kho</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="/quantri/kho/nhap-kho" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Thông tin phiếu</h6></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nhà cung cấp</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block mt-4"><i class="fas fa-save"></i> Hoàn tất nhập kho</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Chi tiết mặt hàng</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-row"><i class="fas fa-plus"></i> Thêm dòng</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="import-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th width="20%">Số lượng</th>
                                    <th width="30%">Giá nhập (VNĐ)</th>
                                    <th width="10%">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="product_id[]" class="form-control product-select" required>
                                            <option value="" data-price="" data-supplier="">-- Chọn sản phẩm --</option>
                                            @foreach($products as $sp)
                                                <option value="{{ $sp->id }}" data-price="{{ $sp->price }}" data-supplier="{{ $sp->supplier_id }}">{{ $sp->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                                    <td>
                                        <input type="number" name="import_price[]" class="form-control import-price-input" min="0" required placeholder="Nhập giá...">
                                        <small class="text-muted suggest-price" style="display: none; font-style: italic; font-size: 11px; margin-top: 5px;"></small>
                                    </td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var rowHtml = $('#import-table tbody tr:first').html();
        var originalOptions = $('select[name="product_id[]"]').first().html();

        // Xóa sạch các lựa chọn sản phẩm ban đầu (Bắt người dùng phải chọn NCC trước)
        $('select[name="product_id[]"]').find('option:not(:first)').remove();

        // 2. Hàm xử lý Lọc sản phẩm theo ID Nhà cung cấp (Chính xác 100%)
        function filterProducts() {
            var supplierId = $('select[name="supplier_id"]').val(); // Lấy ID của NCC

            $('select[name="product_id[]"]').each(function() {
                var currentSelected = $(this).val(); 
                
                $(this).html(originalOptions); // Khôi phục lại toàn bộ options

                if (supplierId !== '') {
                    // Nếu đã chọn NCC, chỉ giữ lại các option có data-supplier khớp với ID đó
                    $(this).find('option:not(:first)').each(function() {
                        if ($(this).attr('data-supplier') != supplierId) {
                            $(this).remove(); 
                        }
                    });
                } else {
                    // Nếu chưa chọn NCC, xóa hết (trừ option mặc định)
                    $(this).find('option:not(:first)').remove();
                }
                
                $(this).val(currentSelected);
            });
        }

        // 3. Kích hoạt Lọc khi người dùng thay đổi Nhà cung cấp
        $('select[name="supplier_id"]').change(function() {
            filterProducts();
            $('select[name="product_id[]"]').val('');
            
            $('.suggest-price').hide();
            $('.import-price-input').attr('placeholder', 'Nhập giá...');
        });

        // 4. Bấm thêm dòng
        $('#btn-add-row').click(function() {
            // Kiểm tra xem đã chọn Nhà cung cấp chưa, nếu chưa thì cảnh báo
            if ($('select[name="supplier_id"]').val() === '') {
                alert('Vui lòng chọn Nhà cung cấp trước khi thêm mặt hàng!');
                return;
            }

            $('#import-table tbody').append('<tr>' + rowHtml + '</tr>');
            filterProducts(); 
        });

        // 5. Xóa dòng
        $(document).on('click', '.btn-remove-row', function() {
            if ($('#import-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Phải có ít nhất 1 mặt hàng để nhập kho!');
            }
        });

        // 6. GỢI Ý GIÁ NHẬP MỜ (Giữ nguyên như cũ)
        $(document).on('change', '.product-select', function() {
            var basePrice = $(this).find('option:selected').attr('data-price');
            
            var row = $(this).closest('tr'); 
            var priceInput = row.find('.import-price-input');
            var suggestText = row.find('.suggest-price');

            if (basePrice && basePrice !== "") {
                var formattedPrice = new Intl.NumberFormat('vi-VN').format(basePrice);
                suggestText.text('Giá bán hiện tại: ' + formattedPrice + ' đ').show();
                priceInput.attr('placeholder', '< ' + formattedPrice);
            } else {
                suggestText.hide();
                priceInput.attr('placeholder', 'Nhập giá...');
            }
        });
    });
</script>
@endsection