@extends('layouts.client')

@section('title', 'Tra cứu bảo hành - HomeTech')

@section('css')
<style>
    .warranty-box {
        background: #fff;
        border: 1px solid #E4E7ED;
        padding: 28px;
        margin-bottom: 30px;
    }

    .warranty-methods {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .warranty-methods label {
        margin: 0;
        font-weight: 600;
        color: #2B2D42;
    }

    .warranty-result {
        border: 1px solid #E4E7ED;
        padding: 18px;
        margin-bottom: 15px;
        background: #fff;
    }

    .warranty-result h4 {
        margin-top: 0;
        margin-bottom: 14px;
        color: #2B2D42;
    }

    .warranty-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 9px 0;
        border-top: 1px solid #F1F1F1;
    }

    .warranty-row span:first-child {
        color: #8D99AE;
        font-weight: 600;
    }

    .warranty-status {
        display: inline-block;
        padding: 4px 10px;
        font-weight: 700;
        color: #fff;
        background: #1E9E5A;
    }

    .warranty-status.expired {
        background: #D10024;
    }

    .warranty-processing {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 10px;
        background: #F5A623;
        color: #fff;
        font-weight: 700;
    }

    @media only screen and (max-width: 767px) {
        .warranty-row {
            display: block;
        }

        .warranty-row span {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb-tree">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="active">Tra cứu bảo hành</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="section-title text-center">
                    <h3 class="title">Tra cứu bảo hành</h3>
                </div>

                <div class="warranty-box">
                    <form action="{{ route('warranty.lookup.search') }}" method="POST">
                        @csrf

                        <div class="warranty-methods">
                            <label>
                                <input type="radio" name="lookup_method" value="phone" {{ old('lookup_method', $lookupMethod) === 'phone' ? 'checked' : '' }}>
                                Theo Số điện thoại mua hàng
                            </label>

                            <label>
                                <input type="radio" name="lookup_method" value="serial" {{ old('lookup_method', $lookupMethod) === 'serial' ? 'checked' : '' }}>
                                Theo Mã Serial dán trên sản phẩm
                            </label>
                        </div>

                        <div class="input-group">
                            <input class="input form-control"
                                   name="keyword"
                                   value="{{ old('keyword', $keyword) }}"
                                   placeholder="Nhập số điện thoại hoặc mã serial">
                            <span class="input-group-btn">
                                <button type="submit" class="primary-btn">Tra cứu</button>
                            </span>
                        </div>

                        @error('lookup_method')
                            <div class="text-danger" style="margin-top: 10px;">{{ $message }}</div>
                        @enderror

                        @error('keyword')
                            <div class="text-danger" style="margin-top: 10px;">{{ $message }}</div>
                        @enderror
                    </form>
                </div>

                @if($results !== null)
                    @if($results->isEmpty())
                        <div class="alert alert-warning">
                            Không tìm thấy thông tin bảo hành cho dữ liệu bạn vừa nhập. Vui lòng kiểm tra lại!
                        </div>
                    @else
                        @foreach($results as $item)
                            <div class="warranty-result">
                                <h4>{{ $item['product_name'] }}</h4>

                                <div class="warranty-row">
                                    <span>Mã Serial</span>
                                    <strong>{{ $item['serial_number'] }}</strong>
                                </div>

                                <div class="warranty-row">
                                    <span>Ngày kích hoạt bảo hành / Ngày mua</span>
                                    <strong>{{ $item['start_date'] }}</strong>
                                </div>

                                <div class="warranty-row">
                                    <span>Thời hạn bảo hành còn lại</span>
                                    <strong>{{ $item['remaining'] }}</strong>
                                </div>

                                <div class="warranty-row">
                                    <span>Trạng thái hiện tại</span>
                                    <strong>
                                        <span class="warranty-status {{ $item['status_label'] === 'Đã hết hạn' ? 'expired' : '' }}">
                                            {{ $item['status_label'] }}
                                        </span>

                                        @if($item['is_processing'])
                                            <br>
                                            <span class="warranty-processing">Đang xử lý bảo hành tại trung tâm</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
