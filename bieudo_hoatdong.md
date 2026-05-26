# Biểu đồ hoạt động cho project HomeTech

## 1. Đăng ký khách hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Khách hàng mở trang Đăng ký]
    B --> C[Nhập họ tên, username, mật khẩu, xác nhận mật khẩu]
    C --> D[Gửi form đăng ký]
    D --> E{Dữ liệu hợp lệ?}
    E -- Không --> F[Hiển thị lỗi validate]
    F --> C
    E -- Có --> G{Username đã tồn tại?}
    G -- Có --> H[Thông báo username đã tồn tại]
    H --> C
    G -- Không --> I[Mã hóa mật khẩu]
    I --> J[Tạo user với role Customer]
    J --> K[Đăng nhập user vừa tạo]
    K --> L[Chuyển về trang chủ]
    L --> M([Kết thúc])
```

## 2. Đăng nhập khách hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Khách hàng mở trang Đăng nhập]
    B --> C[Nhập username và mật khẩu]
    C --> D[Gửi form đăng nhập]
    D --> E{Thông tin hợp lệ?}
    E -- Không --> F[Hiển thị lỗi thiếu dữ liệu]
    F --> C
    E -- Có --> G{Auth attempt thành công?}
    G -- Không --> H[Thông báo sai username hoặc mật khẩu]
    H --> C
    G -- Có --> I{Role là Customer?}
    I -- Không --> J[Logout và báo lỗi tài khoản không phải khách hàng]
    J --> B
    I -- Có --> K[Regenerate session]
    K --> L[Chuyển về trang chủ hoặc trang dự định]
    L --> M([Kết thúc])
```

## 3. Xem chi tiết sản phẩm

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Khách hàng chọn sản phẩm]
    B --> C[Gửi request /san-pham/{id}]
    C --> D[HomeController tìm Product theo id]
    D --> E{Sản phẩm tồn tại?}
    E -- Không --> F[Redirect về trang chủ]
    F --> G[Thông báo sản phẩm không tồn tại]
    G --> H([Kết thúc])
    E -- Có --> I[Lấy thông tin sản phẩm]
    I --> J[Render view client.detail]
    J --> K[Hiển thị chi tiết sản phẩm]
    K --> H
```

## 4. Tìm kiếm sản phẩm

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Khách hàng nhập từ khóa hoặc chọn danh mục]
    B --> C[Gửi request /tim-kiem]
    C --> D[Lấy keyword và category_id]
    D --> E[Khởi tạo query Product, sắp xếp mới nhất]
    E --> F{Có keyword?}
    F -- Có --> G[Lọc tên sản phẩm theo keyword]
    F -- Không --> H[Bỏ qua lọc keyword]
    G --> I{Có category_id hợp lệ?}
    H --> I
    I -- Có --> J[Lọc theo danh mục]
    I -- Không --> K[Bỏ qua lọc danh mục]
    J --> L[Phân trang 12 sản phẩm]
    K --> L
    L --> M[Render view client.search]
    M --> N[Hiển thị kết quả tìm kiếm]
    N --> O([Kết thúc])
```

## 5. Quản lý giỏ hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Khách hàng thao tác với giỏ hàng]
    B --> C{Chọn hành động}
    C -- Thêm sản phẩm --> D[Gửi POST /gio-hang/them/{id}]
    D --> E[Tìm Product theo id]
    E --> F{Sản phẩm tồn tại?}
    F -- Không --> G[Thông báo sản phẩm không tồn tại]
    F -- Có --> H[Lấy giỏ hàng từ session]
    H --> I{Sản phẩm đã có trong giỏ?}
    I -- Có --> J[Cộng dồn số lượng]
    I -- Không --> K[Thêm item mới vào giỏ]
    J --> L[Lưu giỏ hàng vào session]
    K --> L
    L --> M[Redirect đến trang giỏ hàng]

    C -- Xem giỏ hàng --> N[Lấy cart từ session]
    N --> O[Tính tổng tiền]
    O --> P[Render view client.cart]

    C -- Xóa sản phẩm --> Q[Gửi request xóa item]
    Q --> R[Lấy cart từ session]
    R --> S{Item tồn tại?}
    S -- Có --> T[Xóa item và lưu session]
    S -- Không --> U[Không thay đổi giỏ hàng]
    T --> V[Quay lại trang giỏ hàng]
    U --> V

    G --> W([Kết thúc])
    M --> W
    P --> W
    V --> W
```

## 6. Tạo đơn hàng bán tại quầy POS

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Nhân viên POS đăng nhập]
    B --> C{Role hợp lệ là StaffPOS?}
    C -- Không --> D[Từ chối truy cập]
    D --> Z([Kết thúc])
    C -- Có --> E[Mở màn hình POS]
    E --> F[Tìm sản phẩm theo keyword]
    F --> G[Hệ thống trả về sản phẩm còn tồn kho]
    G --> H[Thêm sản phẩm và số lượng vào hóa đơn]
    H --> I[Nhập thông tin khách hàng nếu có]
    I --> J[Chọn trạng thái Completed hoặc Pending]
    J --> K[Nhập tiền khách đưa và địa chỉ nếu cần]
    K --> L[Gửi form tạo đơn POS]
    L --> M[Validate dữ liệu]
    M --> N{Hợp lệ?}
    N -- Không --> O[Hiển thị lỗi]
    O --> E
    N -- Có --> P[Bắt đầu transaction]
    P --> Q[Khóa và lấy danh sách sản phẩm]
    Q --> R{Tất cả sản phẩm đủ tồn kho?}
    R -- Không --> S[Rollback và báo lỗi tồn kho]
    S --> E
    R -- Có --> T[Tính tổng tiền và kiểm tra sản phẩm cần lắp đặt]
    T --> U{Cần địa chỉ giao/lắp đặt?}
    U -- Có --> V{Đã nhập địa chỉ?}
    V -- Không --> W[Rollback và báo lỗi thiếu địa chỉ]
    W --> E
    V -- Có --> X[Kiểm tra tiền khách đưa]
    U -- Không --> X
    X --> Y{Tiền khách đưa đủ?}
    Y -- Không --> AA[Rollback và báo lỗi thanh toán]
    AA --> E
    Y -- Có --> AB[Tạo Order và OrderDetail]
    AB --> AC{Trạng thái Completed?}
    AC -- Có --> AD[Trừ tồn kho sản phẩm]
    AC -- Không --> AE[Giữ đơn ở trạng thái Pending]
    AD --> AF[Commit transaction]
    AE --> AF
    AF --> AG[Chuyển đến trang in hóa đơn]
    AG --> Z
```

## 7. Quản lý sản phẩm của admin

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Admin đăng nhập]
    B --> C{Có quyền admin/staff?}
    C -- Không --> D[Từ chối truy cập]
    D --> Z([Kết thúc])
    C -- Có --> E[Mở màn hình quản lý sản phẩm]
    E --> F{Chọn chức năng}

    F -- Xem danh sách --> G[Lấy keyword và category nếu có]
    G --> H[Lọc Product theo điều kiện]
    H --> I[Phân trang danh sách]
    I --> J[Render admin.product.index]

    F -- Thêm mới --> K[Mở form thêm sản phẩm]
    K --> L[Nhập thông tin, danh mục, nhà cung cấp, ảnh, cờ cần lắp đặt]
    L --> M[Upload ảnh nếu có]
    M --> N[Lưu Product mới]
    N --> J

    F -- Cập nhật --> O[Mở form sửa sản phẩm]
    O --> P[Cập nhật thông tin sản phẩm]
    P --> Q{Có ảnh mới?}
    Q -- Có --> R[Xóa ảnh cũ nếu tồn tại và upload ảnh mới]
    Q -- Không --> S[Giữ ảnh hiện tại]
    R --> T[Lưu thay đổi Product]
    S --> T
    T --> J

    F -- Xóa --> U[Chọn sản phẩm cần xóa]
    U --> V[Xóa ảnh sản phẩm nếu có]
    V --> W[Xóa Product]
    W --> J

    J --> Z
```

## 8. Nhập kho

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Admin mở chức năng nhập kho]
    B --> C[Lấy danh sách nhà cung cấp và sản phẩm]
    C --> D[Hiển thị form tạo phiếu nhập]
    D --> E[Chọn nhà cung cấp]
    E --> F[Chọn sản phẩm, số lượng, giá nhập]
    F --> G[Gửi form nhập kho]
    G --> H[Bắt đầu transaction]
    H --> I[Tạo WarehouseReceipt]
    I --> J[Lặp qua từng dòng sản phẩm]
    J --> K[Tạo WarehouseReceiptDetail]
    K --> L[Cộng stock_quantity cho Product]
    L --> M{Còn dòng sản phẩm?}
    M -- Có --> J
    M -- Không --> N[Cập nhật tổng giá trị phiếu nhập]
    N --> O[Commit transaction]
    O --> P[Redirect lịch sử nhập kho]
    P --> Q([Kết thúc])

    H --> R{Có lỗi?}
    R -- Có --> S[Rollback transaction]
    S --> T[Thông báo lỗi]
    T --> Q
```

## 9. Xuất kho cho đơn hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Nhân viên kho đăng nhập]
    B --> C{Role là StaffWarehouse?}
    C -- Không --> D[Từ chối truy cập]
    D --> Z([Kết thúc])
    C -- Có --> E[Xem danh sách đơn Pending]
    E --> F[Chọn đơn cần xuất kho]
    F --> G[Lấy chi tiết đơn và sản phẩm]
    G --> H{Đơn tồn tại và đang Pending?}
    H -- Không --> I[Thông báo không tìm thấy đơn hợp lệ]
    I --> Z
    H -- Có --> J{Chọn hành động}

    J -- Xác nhận xuất kho --> K[Bắt đầu transaction]
    K --> L[Kiểm tra từng sản phẩm trong đơn]
    L --> M{Đủ tồn kho thực tế?}
    M -- Không --> N[Rollback và báo lỗi thiếu hàng]
    N --> E
    M -- Có --> O{Đơn có sản phẩm cần lắp đặt?}
    O -- Có --> P{Đã chọn nhân viên kỹ thuật?}
    P -- Không --> Q[Rollback và yêu cầu chọn kỹ thuật]
    Q --> F
    P -- Có --> R[Trừ tồn kho sản phẩm]
    O -- Không --> R
    R --> S[Cập nhật Order sang Shipping]
    S --> T[Gán nhân viên kỹ thuật nếu có]
    T --> U[Ghi OrderStatusHistory]
    U --> V[Commit transaction]
    V --> W[Thông báo xuất kho thành công]
    W --> Z

    J -- Từ chối xuất kho --> X[Nhập lý do từ chối]
    X --> Y[Cập nhật Order sang Canceled]
    Y --> AA[Ghi OrderStatusHistory]
    AA --> AB[Thông báo đã từ chối xuất kho]
    AB --> Z
```

## 10. Quản lý trạng thái đơn hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Admin mở danh sách đơn hàng]
    B --> C[Nhập từ khóa hoặc chọn trạng thái lọc]
    C --> D[Lấy danh sách Order kèm User và OrderDetail]
    D --> E[Hiển thị danh sách đơn]
    E --> F[Admin chọn một đơn hàng]
    F --> G[Xem chi tiết đơn hàng]
    G --> H[Chọn trạng thái mới]
    H --> I[Gửi request cập nhật trạng thái]
    I --> J{Trạng thái mới hợp lệ?}
    J -- Không --> K[Hiển thị lỗi validate]
    K --> G
    J -- Có --> L[Tìm Order theo id]
    L --> M{Order tồn tại?}
    M -- Không --> N[Thông báo lỗi cập nhật]
    N --> Z([Kết thúc])
    M -- Có --> O{Pending sang Shipping?}
    O -- Có --> P[Từ chối và yêu cầu dùng chức năng xuất kho]
    P --> G
    O -- Không --> Q[Cập nhật status của Order]
    Q --> R[Lưu Order]
    R --> S[Thông báo cập nhật thành công]
    S --> Z
```

## 11. Xem báo cáo doanh thu

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Admin đăng nhập]
    B --> C[Truy cập dashboard /quantri]
    C --> D{Có quyền admin/staff?}
    D -- Không --> E[Từ chối truy cập]
    E --> Z([Kết thúc])
    D -- Có --> F[Lấy thời gian hiện tại]
    F --> G[Tính doanh thu tháng từ đơn Completed]
    G --> H[Tính doanh thu năm từ đơn Completed]
    H --> I[Lấy doanh thu từng tháng trong năm]
    I --> J[Lấy doanh thu 5 năm gần nhất]
    J --> K[Đóng gói chartData]
    K --> L[Render admin.dashboard]
    L --> M[Hiển thị biểu đồ doanh thu tháng/năm]
    M --> Z
```

## 12. Thống kê đơn hàng

```mermaid
flowchart TD
    A([Bắt đầu]) --> B[Admin truy cập dashboard]
    B --> C{Có quyền admin/staff?}
    C -- Không --> D[Từ chối truy cập]
    D --> Z([Kết thúc])
    C -- Có --> E[Truy vấn bảng orders]
    E --> F[Đếm đơn hàng Pending]
    F --> G[Truy vấn order_details kết hợp products]
    G --> H[Tính tổng số lượng bán theo category_id]
    H --> I[Gán nhãn danh mục sản phẩm]
    I --> J[Đóng gói pieData gồm label, data, color]
    J --> K[Render admin.dashboard]
    K --> L[Hiển thị thống kê đơn chờ và tỉ trọng sản phẩm bán ra]
    L --> Z
```
