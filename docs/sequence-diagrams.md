# Bieu Do Tuan Tu

## 1. Nhan vien/Admin dang nhap va dieu huong theo vai tro

```mermaid
sequenceDiagram
    actor User as Nhan vien/Admin
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant AuthCtrl as AuthController
    participant Auth as Laravel Auth
    participant DB as Database

    User->>Browser: Nhap username, password
    Browser->>Route: POST /quantri/login
    Route->>AuthCtrl: login(request)
    AuthCtrl->>Auth: attempt(credentials)
    Auth->>DB: Tim user va kiem tra password
    DB-->>Auth: Tra ve ket qua xac thuc

    alt Dang nhap thanh cong
        Auth-->>AuthCtrl: User da xac thuc
        AuthCtrl->>AuthCtrl: Kiem tra role
        alt role = Admin
            AuthCtrl-->>Browser: Redirect /quantri
        else role = StaffSales
            AuthCtrl-->>Browser: Redirect /pos
        else role = StaffWarehouse
            AuthCtrl-->>Browser: Redirect /kho/xuat-kho
        else role = StaffTech
            AuthCtrl-->>Browser: Redirect /lap-dat
        else Khong co quyen quan tri
            AuthCtrl->>Auth: logout()
            AuthCtrl-->>Browser: Quay lai login kem loi
        end
    else Dang nhap that bai
        Auth-->>AuthCtrl: False
        AuthCtrl-->>Browser: Quay lai login kem loi
    end
```

## 2. Khach hang dang ky tai khoan

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant CustomerAuth as CustomerAuthController
    participant User as User Model
    participant Hash as Hash
    participant Auth as Laravel Auth
    participant DB as Database

    Customer->>Browser: Nhap thong tin dang ky
    Browser->>Route: POST /dang-ky
    Route->>CustomerAuth: register(request)
    CustomerAuth->>CustomerAuth: Validate full_name, username, password

    alt Du lieu hop le
        CustomerAuth->>Hash: make(password)
        Hash-->>CustomerAuth: Mat khau da ma hoa
        CustomerAuth->>User: create(data, role=Customer)
        User->>DB: INSERT users
        DB-->>User: User moi
        User-->>CustomerAuth: Tra ve user
        CustomerAuth->>Auth: login(user)
        CustomerAuth->>Browser: Regenerate session
        CustomerAuth-->>Browser: Redirect /
    else Du lieu khong hop le
        CustomerAuth-->>Browser: Quay lai form kem loi validate
    end
```

## 3. Khach hang dang nhap

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant CustomerAuth as CustomerAuthController
    participant Auth as Laravel Auth
    participant DB as Database

    Customer->>Browser: Nhap username, password
    Browser->>Route: POST /dang-nhap
    Route->>CustomerAuth: login(request)
    CustomerAuth->>CustomerAuth: Validate username, password
    CustomerAuth->>Auth: attempt(credentials)
    Auth->>DB: Tim user va kiem tra password
    DB-->>Auth: Ket qua xac thuc

    alt Sai thong tin dang nhap
        Auth-->>CustomerAuth: False
        CustomerAuth-->>Browser: Quay lai form kem loi
    else Dung thong tin dang nhap
        Auth-->>CustomerAuth: User da xac thuc
        alt role = Customer
            CustomerAuth->>Browser: Regenerate session
            CustomerAuth-->>Browser: Redirect /
        else role khong phai Customer
            CustomerAuth->>Auth: logout()
            CustomerAuth->>Browser: Invalidate session
            CustomerAuth-->>Browser: Quay lai form kem loi
        end
    end
```

## 4. Khach hang tim kiem san pham

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant HomeCtrl as HomeController
    participant Product as Product Model
    participant DB as Database
    participant View as client.search

    Customer->>Browser: Nhap tu khoa / chon danh muc
    Browser->>Route: GET /tim-kiem?keyword=&category_id=
    Route->>HomeCtrl: search(request)
    HomeCtrl->>Product: orderBy(id desc)

    opt Co keyword
        Product->>Product: where name like keyword
    end

    opt Co category_id
        Product->>Product: where category_id
    end

    HomeCtrl->>Product: paginate(12)
    Product->>DB: SELECT products
    DB-->>Product: Danh sach san pham
    Product-->>HomeCtrl: products
    HomeCtrl->>View: compact(products, keyword)
    View-->>Browser: Hien thi ket qua tim kiem
```

## 5. Khach hang xem chi tiet san pham

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant HomeCtrl as HomeController
    participant Product as Product Model
    participant DB as Database
    participant View as client.detail

    Customer->>Browser: Chon san pham
    Browser->>Route: GET /san-pham/{id}
    Route->>HomeCtrl: detail(id)
    HomeCtrl->>Product: find(id)
    Product->>DB: SELECT product by id
    DB-->>Product: Product hoac null
    Product-->>HomeCtrl: Ket qua

    alt Tim thay san pham
        HomeCtrl->>View: compact(product)
        View-->>Browser: Hien thi chi tiet san pham
    else Khong tim thay san pham
        HomeCtrl-->>Browser: Redirect / kem thong bao loi
    end
```

## 6. Khach hang them san pham vao gio hang

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant CartCtrl as CartController
    participant Product as Product Model
    participant DB as Database
    participant Session as Session

    Customer->>Browser: Bam them vao gio hang
    Browser->>Route: POST /gio-hang/them/{id}
    Route->>CartCtrl: add(request, id)
    CartCtrl->>Product: find(id)
    Product->>DB: SELECT product by id
    DB-->>Product: Product hoac null
    Product-->>CartCtrl: Ket qua

    alt Khong tim thay san pham
        CartCtrl-->>Browser: Redirect back kem loi
    else Tim thay san pham
        CartCtrl->>Session: get('cart', [])
        Session-->>CartCtrl: Gio hang hien tai
        alt San pham da co trong gio
            CartCtrl->>CartCtrl: Cong don so luong
        else San pham chua co trong gio
            CartCtrl->>CartCtrl: Tao item moi tu product
        end
        CartCtrl->>Session: put('cart', cart)
        CartCtrl-->>Browser: Redirect /gio-hang kem thong bao thanh cong
    end
```

## 7. Khach hang xem gio hang

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant CartCtrl as CartController
    participant Session as Session
    participant View as client.cart

    Customer->>Browser: Mo trang gio hang
    Browser->>Route: GET /gio-hang
    Route->>CartCtrl: index()
    CartCtrl->>Session: get('cart', [])
    Session-->>CartCtrl: Danh sach item
    CartCtrl->>CartCtrl: Tinh tong tien
    CartCtrl->>View: compact(cart, total)
    View-->>Browser: Hien thi gio hang
```

## 8. Khach hang xoa san pham khoi gio hang

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant CartCtrl as CartController
    participant Session as Session

    Customer->>Browser: Bam xoa san pham
    Browser->>Route: GET /gio-hang/xoa/{id}
    Route->>CartCtrl: remove(id)
    CartCtrl->>Session: get('cart')
    Session-->>CartCtrl: Gio hang hien tai

    alt San pham ton tai trong gio
        CartCtrl->>CartCtrl: unset(cart[id])
        CartCtrl->>Session: put('cart', cart)
        CartCtrl-->>Browser: Redirect back kem thong bao thanh cong
    else San pham khong ton tai
        CartCtrl-->>Browser: Redirect back
    end
```

## 9. Admin quan ly san pham: them san pham

```mermaid
sequenceDiagram
    actor Admin as Admin
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant ProductCtrl as ProductController
    participant File as File Upload
    participant Product as Product Model
    participant DB as Database

    Admin->>Browser: Nhap thong tin san pham
    Browser->>Route: POST /quantri/san-pham/them
    Route->>ProductCtrl: store(request)
    ProductCtrl->>ProductCtrl: Gan name, price, category_id, supplier_id
    ProductCtrl->>ProductCtrl: Gan requires_installation

    opt Co anh san pham
        ProductCtrl->>File: Lay file upload
        ProductCtrl->>ProductCtrl: Tao ten file an toan
        ProductCtrl->>File: move(public/uploads/products)
        File-->>ProductCtrl: Luu file thanh cong
        ProductCtrl->>ProductCtrl: Gan product.image
    end

    ProductCtrl->>Product: save()
    Product->>DB: INSERT products
    DB-->>Product: Thanh cong
    ProductCtrl-->>Browser: Redirect /quantri/san-pham kem thong bao
```

## 10. Admin quan ly san pham: sua san pham

```mermaid
sequenceDiagram
    actor Admin as Admin
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant ProductCtrl as ProductController
    participant Product as Product Model
    participant DB as Database
    participant File as File System

    Admin->>Browser: Cap nhat thong tin san pham
    Browser->>Route: POST /quantri/san-pham/sua/{id}
    Route->>ProductCtrl: update(request, id)
    ProductCtrl->>Product: find(id)
    Product->>DB: SELECT product by id
    DB-->>Product: Product
    Product-->>ProductCtrl: Product hien tai
    ProductCtrl->>ProductCtrl: Cap nhat name, price, category, supplier
    ProductCtrl->>ProductCtrl: Cap nhat requires_installation, description, specifications

    opt Co anh moi
        ProductCtrl->>File: Kiem tra anh cu
        ProductCtrl->>File: Xoa anh cu neu ton tai
        ProductCtrl->>File: Luu anh moi vao public/uploads/products
        ProductCtrl->>ProductCtrl: Gan product.image moi
    end

    ProductCtrl->>Product: save()
    Product->>DB: UPDATE products
    DB-->>Product: Thanh cong
    ProductCtrl-->>Browser: Redirect ve danh sach san pham kem thong bao
```

## 11. Nhan vien POS tao don ban hang

```mermaid
sequenceDiagram
    actor StaffSales as Nhan vien POS
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant PosCtrl as PosController
    participant DB as Database Transaction
    participant Product as Product Model
    participant User as User Model
    participant Order as Order Model
    participant Detail as OrderDetail Model

    StaffSales->>Browser: Tao don POS
    Browser->>Route: POST /pos/orders
    Route->>PosCtrl: store(request)
    PosCtrl->>PosCtrl: Validate items, customer, status, received_amount
    PosCtrl->>DB: Begin transaction
    PosCtrl->>Product: whereIn(product_id).lockForUpdate()
    Product->>DB: SELECT products FOR UPDATE
    DB-->>Product: Danh sach san pham
    Product-->>PosCtrl: products

    loop Moi san pham trong don
        PosCtrl->>PosCtrl: Kiem tra ton tai va ton kho
        PosCtrl->>PosCtrl: Tinh lineTotal va total
        PosCtrl->>PosCtrl: Kiem tra requires_installation
    end

    alt Can dia chi nhung chua nhap
        PosCtrl->>DB: Rollback
        PosCtrl-->>Browser: Tra loi loi delivery_address
    else Tien nhan khong du
        PosCtrl->>DB: Rollback
        PosCtrl-->>Browser: Tra loi loi received_amount
    else Du lieu hop le
        PosCtrl->>User: resolveCustomerId(customer_phone)
        User->>DB: Tim hoac tao khach hang POS
        DB-->>User: customer_id hoac null
        User-->>PosCtrl: customer_id
        PosCtrl->>Order: save(order)
        Order->>DB: INSERT orders
        DB-->>Order: order_id

        loop Moi item hop le
            PosCtrl->>Detail: save(order_id, product_id, quantity, unit_price)
            Detail->>DB: INSERT order_details
            alt status = Completed
                PosCtrl->>Product: Giam stock_quantity
                Product->>DB: UPDATE products
            else status = Pending
                PosCtrl->>PosCtrl: Khong tru kho
            end
        end

        PosCtrl->>DB: Commit
        PosCtrl-->>Browser: Redirect /pos/orders/{id}/receipt
    end
```

## 12. Nhan vien kho xac nhan / tu choi xuat kho

```mermaid
sequenceDiagram
    actor StaffWarehouse as Nhan vien kho
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant ShipCtrl as WarehouseShipmentController
    participant DB as Database Transaction
    participant Order as Order Model
    participant Product as Product Model
    participant User as User Model
    participant History as OrderStatusHistory

    StaffWarehouse->>Browser: Mo don cho xuat kho
    Browser->>Route: GET /kho/xuat-kho/{id}
    Route->>ShipCtrl: show(id)
    ShipCtrl->>Order: with(user, details.product).where(status=Pending).find(id)
    Order-->>ShipCtrl: Don hang Pending
    ShipCtrl->>User: Lay danh sach StaffTech
    User-->>ShipCtrl: staffTechs
    ShipCtrl-->>Browser: Hien thi chi tiet don

    alt Xac nhan xuat kho
        StaffWarehouse->>Browser: Bam xac nhan xuat kho
        Browser->>Route: POST /kho/xuat-kho/{id}
        Route->>ShipCtrl: confirm(request, id)
        ShipCtrl->>DB: Begin transaction
        ShipCtrl->>Order: lockForUpdate().find(id)
        Order->>DB: SELECT order FOR UPDATE
        DB-->>Order: Order va details
        Order-->>ShipCtrl: Order

        ShipCtrl->>ShipCtrl: Kiem tra status Pending va co san pham
        ShipCtrl->>ShipCtrl: Kiem tra san pham can lap dat

        alt Can lap dat nhung chua chon nhan vien ky thuat
            ShipCtrl->>DB: Rollback
            ShipCtrl-->>Browser: Redirect back kem loi
        else Nhan vien ky thuat khong hop le
            ShipCtrl->>User: Kiem tra role StaffTech
            User-->>ShipCtrl: Khong hop le
            ShipCtrl->>DB: Rollback
            ShipCtrl-->>Browser: Redirect back kem loi
        else Hop le
            loop Moi chi tiet don hang
                ShipCtrl->>Product: lockForUpdate(product_id)
                Product->>DB: SELECT product FOR UPDATE
                DB-->>Product: Product
                Product-->>ShipCtrl: Product
                alt Khong du ton kho
                    ShipCtrl->>DB: Rollback
                    ShipCtrl-->>Browser: Redirect back kem loi
                else Du ton kho
                    ShipCtrl->>Product: Giam stock_quantity
                    Product->>DB: UPDATE products
                end
            end

            ShipCtrl->>Order: Cap nhat status=Shipping, assigned_staff_tech_id
            Order->>DB: UPDATE orders
            ShipCtrl->>History: create(Pending -> Shipping)
            History->>DB: INSERT order_status_histories
            ShipCtrl->>DB: Commit
            ShipCtrl-->>Browser: Redirect /kho/xuat-kho kem thong bao
        end
    else Tu choi xuat kho
        StaffWarehouse->>Browser: Nhap ly do tu choi
        Browser->>Route: POST /kho/tu-choi-xuat-kho/{id}
        Route->>ShipCtrl: reject(request, id)
        ShipCtrl->>ShipCtrl: Validate reason
        ShipCtrl->>Order: find(id)
        Order-->>ShipCtrl: Order
        alt Order dang Pending
            ShipCtrl->>Order: status = Canceled
            Order->>DB: UPDATE orders
            ShipCtrl->>History: create(Pending -> Canceled, reason)
            History->>DB: INSERT order_status_histories
            ShipCtrl-->>Browser: Redirect /kho/xuat-kho kem thong bao
        else Order khong hop le
            ShipCtrl-->>Browser: Redirect back kem loi
        end
    end
```

## 13. Nhan vien ky thuat cap nhat trang thai lap dat

```mermaid
sequenceDiagram
    actor StaffTech as Nhan vien ky thuat
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant InstallCtrl as InstallationController
    participant DB as Database Transaction
    participant Order as Order Model
    participant History as OrderStatusHistory
    participant Auth as Laravel Auth

    StaffTech->>Browser: Mo danh sach lap dat
    Browser->>Route: GET /lap-dat
    Route->>InstallCtrl: index()
    InstallCtrl->>Auth: id()
    Auth-->>InstallCtrl: staff_tech_id
    InstallCtrl->>Order: Lay don Shipping duoc phan cong hom nay
    Order-->>InstallCtrl: Danh sach don
    InstallCtrl-->>Browser: Hien thi danh sach lap dat

    StaffTech->>Browser: Cap nhat trang thai don
    Browser->>Route: POST /lap-dat/{id}/trang-thai
    Route->>InstallCtrl: updateStatus(request, id)
    InstallCtrl->>InstallCtrl: Validate status va reason

    alt InstallationFailed nhung thieu reason
        InstallCtrl-->>Browser: Quay lai form kem loi reason
    else Du lieu hop le
        InstallCtrl->>DB: Begin transaction
        InstallCtrl->>Auth: id()
        Auth-->>InstallCtrl: staff_tech_id
        InstallCtrl->>Order: where assigned_staff_tech_id = Auth::id lockForUpdate find(id)
        Order->>DB: SELECT order FOR UPDATE
        DB-->>Order: Order hoac null
        Order-->>InstallCtrl: Ket qua

        alt Khong tim thay hoac khong duoc phan cong
            InstallCtrl->>DB: Rollback
            InstallCtrl-->>Browser: Redirect /lap-dat kem loi
        else Order khong phai Shipping
            InstallCtrl->>DB: Rollback
            InstallCtrl-->>Browser: Redirect /lap-dat kem loi
        else Cap nhat hop le
            InstallCtrl->>Order: status = Completed hoac InstallationFailed
            opt status = Completed
                InstallCtrl->>Order: installation_completed_at = now()
            end
            Order->>DB: UPDATE orders
            InstallCtrl->>History: create(Shipping -> status, reason)
            History->>DB: INSERT order_status_histories
            InstallCtrl->>DB: Commit
            InstallCtrl-->>Browser: Redirect /lap-dat kem thong bao
        end
    end
```

## 14. Thong ke don hang

```mermaid
sequenceDiagram
    actor Admin as Admin
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant OrderCtrl as OrderController
    participant Order as Order Model
    participant DB as Database
    participant View as admin.order.index

    Admin->>Browser: Mo trang quan ly don hang
    Browser->>Route: GET /quantri/don-hang?status=&q=
    Route->>OrderCtrl: index(request)
    OrderCtrl->>OrderCtrl: Tao danh sach status hop le
    OrderCtrl->>Order: with(user, details.product)

    opt Co status hop le
        Order->>Order: where status
    end

    opt Co tu khoa tim kiem
        Order->>Order: Loc theo ma don, dia chi, khach hang, san pham
    end

    OrderCtrl->>Order: orderBy(id desc).paginate(10)
    Order->>DB: SELECT orders va relations
    DB-->>Order: Danh sach don hang
    Order-->>OrderCtrl: orders
    OrderCtrl->>View: compact(orders, statuses, status, keyword)
    View-->>Browser: Hien thi thong ke/danh sach don hang
```

## 15. Xem bao cao doanh thu

```mermaid
sequenceDiagram
    actor Admin as Admin
    participant Browser as Trinh duyet
    participant Route as Web Routes
    participant Dashboard as Closure /quantri
    participant DB as Database
    participant View as admin.dashboard

    Admin->>Browser: Mo dashboard quan tri
    Browser->>Route: GET /quantri
    Route->>Dashboard: Xu ly dashboard
    Dashboard->>Dashboard: Lay thoi gian hien tai

    Dashboard->>DB: SUM total_price thang hien tai where status=Completed
    DB-->>Dashboard: doanhThuThang
    Dashboard->>DB: SUM total_price nam hien tai where status=Completed
    DB-->>Dashboard: doanhThuNam
    Dashboard->>DB: COUNT orders where status=Pending
    DB-->>Dashboard: donHangCho

    Dashboard->>DB: SUM doanh thu theo tung thang trong nam
    DB-->>Dashboard: monthlyRevenues

    loop 5 nam gan nhat
        Dashboard->>DB: SUM doanh thu theo nam where status=Completed
        DB-->>Dashboard: yearlyTotal
    end

    Dashboard->>DB: SUM so luong ban theo category tu order_details join products
    DB-->>Dashboard: categorySales
    Dashboard->>Dashboard: Tao stats, chartData, pieData
    Dashboard->>View: compact(stats, chartData, pieData)
    View-->>Browser: Hien thi bao cao doanh thu va bieu do
```
