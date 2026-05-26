# Mã Mermaid các biểu đồ tuần tự

## 1. Nhân viên/Admin đăng nhập và điều hướng theo vai trò

```mermaid
sequenceDiagram
    actor N as Nhân viên/Admin
    participant UI as Giao diện
    participant Auth as Hệ thống xác thực
    participant DB as CSDL

    N->>UI: Nhập email và mật khẩu
    UI->>Auth: Gửi thông tin đăng nhập
    Auth->>DB: Kiểm tra tài khoản
    DB-->>Auth: Trả về thông tin người dùng và vai trò

    alt Đăng nhập hợp lệ
        Auth-->>UI: Xác thực thành công
        alt Vai trò Admin
            UI-->>N: Điều hướng đến trang quản trị
        else Vai trò Nhân viên
            UI-->>N: Điều hướng đến trang nhân viên
        end
    else Đăng nhập thất bại
        Auth-->>UI: Thông báo lỗi
        UI-->>N: Hiển thị đăng nhập thất bại
    end
```

## 2. Khách hàng đăng ký tài khoản

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant Auth as Hệ thống tài khoản
    participant DB as CSDL

    KH->>UI: Nhập thông tin đăng ký
    UI->>Auth: Gửi yêu cầu đăng ký
    Auth->>DB: Kiểm tra email đã tồn tại

    alt Email chưa tồn tại
        Auth->>DB: Lưu tài khoản mới
        DB-->>Auth: Đăng ký thành công
        Auth-->>UI: Trả về kết quả thành công
        UI-->>KH: Hiển thị thông báo đăng ký thành công
    else Email đã tồn tại
        Auth-->>UI: Trả về lỗi
        UI-->>KH: Hiển thị email đã được sử dụng
    end
```

## 3. Khách hàng đăng nhập

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant Auth as Hệ thống xác thực
    participant DB as CSDL

    KH->>UI: Nhập email và mật khẩu
    UI->>Auth: Gửi thông tin đăng nhập
    Auth->>DB: Kiểm tra tài khoản

    alt Thông tin hợp lệ
        DB-->>Auth: Trả về thông tin khách hàng
        Auth-->>UI: Đăng nhập thành công
        UI-->>KH: Điều hướng đến trang chủ
    else Thông tin không hợp lệ
        Auth-->>UI: Đăng nhập thất bại
        UI-->>KH: Hiển thị thông báo lỗi
    end
```

## 4. Khách hàng tìm kiếm sản phẩm

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant SP as Hệ thống sản phẩm
    participant DB as CSDL

    KH->>UI: Nhập từ khóa tìm kiếm
    UI->>SP: Gửi yêu cầu tìm kiếm
    SP->>DB: Truy vấn sản phẩm theo từ khóa
    DB-->>SP: Trả về danh sách sản phẩm
    SP-->>UI: Gửi kết quả tìm kiếm
    UI-->>KH: Hiển thị danh sách sản phẩm phù hợp
```

## 5. Khách hàng xem chi tiết sản phẩm

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant SP as Hệ thống sản phẩm
    participant DB as CSDL

    KH->>UI: Chọn sản phẩm cần xem
    UI->>SP: Gửi yêu cầu xem chi tiết sản phẩm
    SP->>DB: Lấy thông tin chi tiết sản phẩm
    DB-->>SP: Trả về thông tin sản phẩm
    SP-->>UI: Gửi dữ liệu chi tiết
    UI-->>KH: Hiển thị chi tiết sản phẩm
```

## 6. Khách hàng thêm sản phẩm vào giỏ hàng

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant GH as Hệ thống giỏ hàng
    participant DB as CSDL

    KH->>UI: Nhấn thêm vào giỏ hàng
    UI->>GH: Gửi thông tin sản phẩm và số lượng
    GH->>DB: Kiểm tra sản phẩm trong giỏ hàng

    alt Sản phẩm đã có trong giỏ
        GH->>DB: Cập nhật số lượng
    else Sản phẩm chưa có trong giỏ
        GH->>DB: Thêm sản phẩm vào giỏ
    end

    DB-->>GH: Cập nhật thành công
    GH-->>UI: Trả về kết quả
    UI-->>KH: Hiển thị thông báo đã thêm vào giỏ hàng
```

## 7. Khách hàng xem giỏ hàng

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant GH as Hệ thống giỏ hàng
    participant DB as CSDL

    KH->>UI: Mở giỏ hàng
    UI->>GH: Gửi yêu cầu xem giỏ hàng
    GH->>DB: Lấy danh sách sản phẩm trong giỏ
    DB-->>GH: Trả về dữ liệu giỏ hàng
    GH-->>UI: Gửi danh sách sản phẩm và tổng tiền
    UI-->>KH: Hiển thị giỏ hàng
```

## 8. Khách hàng xóa sản phẩm khỏi giỏ hàng

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant UI as Giao diện
    participant GH as Hệ thống giỏ hàng
    participant DB as CSDL

    KH->>UI: Chọn xóa sản phẩm khỏi giỏ
    UI->>GH: Gửi yêu cầu xóa sản phẩm
    GH->>DB: Xóa sản phẩm khỏi giỏ hàng
    DB-->>GH: Xóa thành công
    GH-->>UI: Trả về giỏ hàng đã cập nhật
    UI-->>KH: Hiển thị giỏ hàng mới
```

## 9. Admin quản lý sản phẩm: thêm sản phẩm

```mermaid
sequenceDiagram
    actor A as Admin
    participant UI as Giao diện quản trị
    participant SP as Hệ thống sản phẩm
    participant DB as CSDL

    A->>UI: Nhập thông tin sản phẩm mới
    UI->>SP: Gửi yêu cầu thêm sản phẩm
    SP->>DB: Lưu sản phẩm mới
    DB-->>SP: Thêm thành công
    SP-->>UI: Trả về kết quả
    UI-->>A: Hiển thị thông báo thêm sản phẩm thành công
```

## 10. Admin quản lý sản phẩm: sửa sản phẩm

```mermaid
sequenceDiagram
    actor A as Admin
    participant UI as Giao diện quản trị
    participant SP as Hệ thống sản phẩm
    participant DB as CSDL

    A->>UI: Chọn sản phẩm cần sửa
    UI->>SP: Lấy thông tin sản phẩm
    SP->>DB: Truy vấn sản phẩm
    DB-->>SP: Trả về thông tin sản phẩm
    SP-->>UI: Hiển thị form chỉnh sửa

    A->>UI: Cập nhật thông tin sản phẩm
    UI->>SP: Gửi yêu cầu cập nhật
    SP->>DB: Cập nhật sản phẩm
    DB-->>SP: Cập nhật thành công
    SP-->>UI: Trả về kết quả
    UI-->>A: Hiển thị thông báo sửa sản phẩm thành công
```

## 11. Admin quản lý sản phẩm: xóa sản phẩm

```mermaid
sequenceDiagram
    actor A as Admin
    participant UI as Giao diện quản trị
    participant SP as Hệ thống sản phẩm
    participant DB as CSDL

    A->>UI: Chọn sản phẩm cần xóa
    UI->>A: Hiển thị xác nhận xóa
    A->>UI: Xác nhận xóa
    UI->>SP: Gửi yêu cầu xóa sản phẩm
    SP->>DB: Xóa sản phẩm khỏi CSDL
    DB-->>SP: Xóa thành công
    SP-->>UI: Trả về kết quả
    UI-->>A: Hiển thị thông báo xóa sản phẩm thành công
```

## 12. Nhân viên bán hàng tạo đơn hàng

```mermaid
sequenceDiagram
    actor NV as Nhân viên bán hàng
    participant UI as Giao diện bán hàng
    participant DH as Hệ thống đơn hàng
    participant SP as Hệ thống sản phẩm
    participant KH as Hệ thống khách hàng
    participant DB as CSDL

    NV->>UI: Chọn chức năng tạo đơn hàng
    UI-->>NV: Hiển thị form tạo đơn hàng

    NV->>UI: Nhập thông tin khách hàng
    UI->>KH: Kiểm tra thông tin khách hàng
    KH->>DB: Truy vấn khách hàng
    DB-->>KH: Trả về thông tin khách hàng

    alt Khách hàng đã tồn tại
        KH-->>UI: Gửi thông tin khách hàng
    else Khách hàng mới
        UI->>KH: Gửi yêu cầu tạo khách hàng mới
        KH->>DB: Lưu thông tin khách hàng
        DB-->>KH: Tạo khách hàng thành công
        KH-->>UI: Gửi thông tin khách hàng mới
    end

    NV->>UI: Chọn sản phẩm và số lượng
    UI->>SP: Kiểm tra sản phẩm và tồn kho
    SP->>DB: Truy vấn thông tin sản phẩm
    DB-->>SP: Trả về sản phẩm và số lượng tồn

    alt Đủ hàng
        SP-->>UI: Xác nhận sản phẩm hợp lệ
        UI->>DH: Gửi yêu cầu tạo đơn hàng
        DH->>DB: Lưu thông tin đơn hàng
        DB-->>DH: Tạo đơn hàng thành công
        DH-->>UI: Trả về mã đơn hàng
        UI-->>NV: Hiển thị thông báo tạo đơn hàng thành công
    else Không đủ hàng
        SP-->>UI: Thông báo không đủ số lượng tồn kho
        UI-->>NV: Hiển thị lỗi không đủ hàng
    end
```

## 13. Quản lý kho hàng / Xác nhận xuất kho

```mermaid
sequenceDiagram
    actor QK as Quản lý kho hàng
    participant UI as Giao diện kho hàng
    participant XK as Hệ thống xuất kho
    participant DH as Hệ thống đơn hàng
    participant KHO as Hệ thống kho hàng
    participant DB as CSDL

    QK->>UI: Chọn đơn hàng chờ xuất kho
    UI->>DH: Gửi yêu cầu xem chi tiết đơn hàng
    DH->>DB: Lấy thông tin đơn hàng
    DB-->>DH: Trả về chi tiết đơn hàng
    DH-->>UI: Hiển thị chi tiết đơn hàng

    QK->>UI: Xác nhận xuất kho
    UI->>XK: Gửi yêu cầu xuất kho
    XK->>KHO: Kiểm tra tồn kho
    KHO->>DB: Truy vấn số lượng tồn
    DB-->>KHO: Trả về số lượng tồn

    alt Đủ hàng để xuất kho
        XK->>KHO: Cập nhật số lượng tồn kho
        KHO->>DB: Trừ số lượng sản phẩm trong kho
        DB-->>KHO: Cập nhật kho thành công

        XK->>DH: Cập nhật trạng thái đơn hàng
        DH->>DB: Lưu trạng thái "Đã xuất kho"
        DB-->>DH: Cập nhật đơn hàng thành công

        XK-->>UI: Trả về kết quả xuất kho thành công
        UI-->>QK: Hiển thị thông báo đã xuất kho
    else Không đủ hàng
        XK-->>UI: Trả về lỗi không đủ hàng
        UI-->>QK: Hiển thị thông báo không thể xuất kho
    end
```

## 14. Nhân viên kỹ thuật cập nhật trạng thái lắp đặt

```mermaid
sequenceDiagram
    actor NV as Nhân viên kỹ thuật
    participant UI as Giao diện nhân viên
    participant LD as Hệ thống lắp đặt
    participant DB as CSDL

    NV->>UI: Chọn đơn lắp đặt
    UI->>LD: Gửi yêu cầu xem chi tiết đơn
    LD->>DB: Lấy thông tin đơn lắp đặt
    DB-->>LD: Trả về thông tin đơn
    LD-->>UI: Hiển thị chi tiết đơn

    NV->>UI: Cập nhật trạng thái lắp đặt
    UI->>LD: Gửi trạng thái mới
    LD->>DB: Lưu trạng thái lắp đặt
    DB-->>LD: Cập nhật thành công
    LD-->>UI: Trả về kết quả
    UI-->>NV: Hiển thị thông báo cập nhật thành công
```

## 15. Thống kê đơn hàng

```mermaid
sequenceDiagram
    actor A as Admin
    participant UI as Giao diện quản trị
    participant TK as Hệ thống thống kê
    participant DB as CSDL

    A->>UI: Chọn chức năng thống kê đơn hàng
    UI->>TK: Gửi yêu cầu thống kê
    TK->>DB: Truy vấn dữ liệu đơn hàng
    DB-->>TK: Trả về danh sách đơn hàng

    TK->>TK: Tính tổng số đơn, đơn hoàn thành, đơn hủy
    TK-->>UI: Trả về dữ liệu thống kê
    UI-->>A: Hiển thị biểu đồ và số liệu thống kê
```

## 16. Xem báo cáo doanh thu

```mermaid
sequenceDiagram
    actor A as Admin
    participant UI as Giao diện quản trị
    participant BC as Hệ thống báo cáo
    participant DB as CSDL

    A->>UI: Chọn báo cáo doanh thu
    UI->>BC: Gửi yêu cầu xem báo cáo
    BC->>DB: Truy vấn đơn hàng đã thanh toán
    DB-->>BC: Trả về dữ liệu doanh thu

    BC->>BC: Tính tổng doanh thu theo thời gian
    BC-->>UI: Trả về báo cáo doanh thu
    UI-->>A: Hiển thị bảng và biểu đồ doanh thu
```
