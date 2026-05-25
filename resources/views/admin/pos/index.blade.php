@extends('layouts.pos')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bán hàng tại quầy (POS)</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tìm sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" id="productSearch" class="form-control" placeholder="Nhập tên sản phẩm...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="searchButton">
                                <i class="fas fa-search"></i> Tìm
                            </button>
                        </div>
                    </div>

                    <div id="searchResults" class="list-group"></div>
                    <div id="emptySearch" class="text-muted text-center py-4">Nhập tên sản phẩm để bắt đầu bán hàng.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <form method="POST" action="{{ url('/pos/orders') }}" id="posForm">
                @csrf
                <div id="cartInputs"></div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng hiện tại</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th width="85">SL</th>
                                        <th width="110">Thành tiền</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Chưa có sản phẩm.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between h5">
                            <span>Tổng tiền</span>
                            <strong class="text-danger" id="totalText">0 đ</strong>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Số điện thoại khách hàng</label>
                            <input type="text" name="customer_phone" class="form-control" maxlength="15" placeholder="Bỏ trống nếu khách vãng lai">
                        </div>

                        <div class="form-group">
                            <label>Tên khách hàng</label>
                            <input type="text" name="customer_name" class="form-control" maxlength="100" placeholder="Nhập khi cần tạo khách mới">
                        </div>

                        <div class="form-group d-none" id="deliveryGroup">
                            <label>Địa chỉ giao hàng/lắp đặt</label>
                            <textarea name="delivery_address" id="deliveryAddress" class="form-control" rows="3" placeholder="Bắt buộc khi đơn cần ship hàng hoặc lắp đặt">{{ old('delivery_address') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Trạng thái đơn hàng</label>
                            <select name="status" id="orderStatus" class="form-control" required>
                                <option value="Completed" {{ old('status', 'Completed') === 'Completed' ? 'selected' : '' }}>1 - Hoàn thành</option>
                                <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>2 - Chờ xử lý</option>
                            </select>
                            <small class="form-text text-muted">
                                Chọn Hoàn thành khi khách đã nhận hàng tại quầy để trừ tồn kho ngay; chọn Chờ xử lý cho đơn cần ship hàng hoặc lắp đặt.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Tiền khách đưa</label>
                            <input type="text" id="receivedAmountDisplay" class="form-control" inputmode="numeric" autocomplete="off" required>
                            <input type="hidden" name="received_amount" id="receivedAmount" value="{{ old('received_amount') }}">
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Tiền thối</span>
                            <strong id="changeText">0 đ</strong>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-cash-register"></i> Xác nhận thanh toán
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cart = new Map();
    const searchInput = document.getElementById('productSearch');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    const emptySearch = document.getElementById('emptySearch');
    const cartBody = document.getElementById('cartBody');
    const cartInputs = document.getElementById('cartInputs');
    const totalText = document.getElementById('totalText');
    const receivedAmount = document.getElementById('receivedAmount');
    const receivedAmountDisplay = document.getElementById('receivedAmountDisplay');
    const changeText = document.getElementById('changeText');
    const deliveryGroup = document.getElementById('deliveryGroup');
    const deliveryAddress = document.getElementById('deliveryAddress');
    const orderStatus = document.getElementById('orderStatus');
    const posForm = document.getElementById('posForm');
    const productsUrl = @json(url('/pos/products'));
    let searchTimer = null;

    const money = new Intl.NumberFormat('vi-VN');

    function formatMoney(value) {
        return money.format(Math.max(0, value)) + ' đ';
    }

    function cartTotal() {
        let total = 0;
        cart.forEach(item => total += item.price * item.quantity);
        return total;
    }

    function cartRequiresInstallation() {
        let requiresInstallation = false;
        cart.forEach(item => {
            requiresInstallation = requiresInstallation || item.requires_installation;
        });

        return requiresInstallation;
    }

    function syncDeliveryGroup() {
        const needsDeliveryAddress = cartRequiresInstallation() || orderStatus.value === 'Pending';

        deliveryGroup.classList.toggle('d-none', !needsDeliveryAddress);
        deliveryAddress.required = needsDeliveryAddress;
    }

    function renderCart() {
        cartBody.innerHTML = '';
        cartInputs.innerHTML = '';

        if (cart.size === 0) {
            cartBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Chưa có sản phẩm.</td></tr>';
        }

        let index = 0;

        cart.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="font-weight-bold">${item.name}</div>
                    <small class="text-muted">${formatMoney(item.price)} | Tồn: ${item.stock_quantity}</small>
                </td>
                <td>
                    <input type="number" min="1" max="${item.stock_quantity}" value="${item.quantity}" class="form-control form-control-sm cart-quantity" data-id="${item.id}">
                </td>
                <td class="text-danger font-weight-bold">${formatMoney(item.price * item.quantity)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            cartBody.appendChild(row);

            cartInputs.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `);
            index++;
        });

        syncDeliveryGroup();
        totalText.textContent = formatMoney(cartTotal());
        updateChange();
    }

    function updateChange() {
        const received = Number(receivedAmount.value || 0);
        changeText.textContent = formatMoney(received - cartTotal());
    }

    function rawMoney(value) {
        return String(value || '').replace(/\D/g, '');
    }

    function syncReceivedAmount() {
        const rawValue = rawMoney(receivedAmountDisplay.value);
        receivedAmount.value = rawValue;
        receivedAmountDisplay.value = rawValue ? money.format(Number(rawValue)) : '';
        updateChange();
    }

    function addToCart(product) {
        const current = cart.get(product.id);

        if (current) {
            current.quantity = Math.min(current.quantity + 1, current.stock_quantity);
        } else {
            cart.set(product.id, {
                id: product.id,
                name: product.name,
                price: Number(product.price),
                stock_quantity: Number(product.stock_quantity),
                requires_installation: Boolean(Number(product.requires_installation)),
                quantity: 1
            });
        }

        renderCart();
    }

    async function searchProducts() {
        const keyword = searchInput.value.trim();
        searchResults.innerHTML = '';

        if (!keyword) {
            emptySearch.textContent = 'Nhập tên sản phẩm để bắt đầu bán hàng.';
            emptySearch.classList.remove('d-none');
            return;
        }

        emptySearch.textContent = 'Đang tìm...';
        emptySearch.classList.remove('d-none');

        let products = [];

        try {
            const response = await fetch(productsUrl + '?keyword=' + encodeURIComponent(keyword), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            products = await response.json();
        } catch (error) {
            searchResults.innerHTML = '';
            emptySearch.textContent = 'Không thể tải danh sách sản phẩm. Vui lòng kiểm tra đăng nhập POS hoặc đường dẫn ứng dụng.';
            emptySearch.classList.remove('d-none');
            return;
        }

        searchResults.innerHTML = '';
        emptySearch.classList.toggle('d-none', products.length > 0);
        emptySearch.textContent = products.length ? '' : 'Không tìm thấy sản phẩm còn hàng.';

        products.forEach(product => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            button.innerHTML = `
                <span>
                    <strong>${product.name}</strong>
                    ${Number(product.requires_installation) ? '<span class="badge badge-warning ml-2">Lắp đặt</span>' : ''}
                    <br><small class="text-muted">Tồn: ${product.stock_quantity}</small>
                </span>
                <span class="text-danger font-weight-bold">${formatMoney(Number(product.price))}</span>
            `;
            button.addEventListener('click', () => addToCart(product));
            searchResults.appendChild(button);
        });
    }

    searchButton.addEventListener('click', searchProducts);
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(searchProducts, 200);
    });
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(searchTimer);
            searchProducts();
        }
    });

    cartBody.addEventListener('input', function (event) {
        if (!event.target.classList.contains('cart-quantity')) {
            return;
        }

        const item = cart.get(Number(event.target.dataset.id));
        const quantity = Math.max(1, Math.min(Number(event.target.value || 1), item.stock_quantity));
        item.quantity = quantity;
        event.target.value = quantity;
        renderCart();
    });

    cartBody.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-item');

        if (!button) {
            return;
        }

        cart.delete(Number(button.dataset.id));
        renderCart();
    });

    receivedAmountDisplay.addEventListener('input', syncReceivedAmount);
    orderStatus.addEventListener('change', syncDeliveryGroup);
    posForm.addEventListener('submit', function (event) {
        if (cart.size === 0) {
            event.preventDefault();
            alert('Vui lòng thêm ít nhất một sản phẩm vào đơn hàng.');
        }
    });

    if (receivedAmount.value) {
        receivedAmountDisplay.value = money.format(Number(receivedAmount.value));
    }

    syncDeliveryGroup();
    updateChange();
});
</script>
@endsection
