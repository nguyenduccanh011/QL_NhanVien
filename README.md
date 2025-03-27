# QL_NhanVien

# Dự án Quản Lý Nhân Sự (QLNS) Đơn giản - PHP/MySQL

Một ứng dụng web PHP/MySQL cơ bản để quản lý thông tin nhân viên theo phòng ban. Ứng dụng bao gồm chức năng đăng nhập, phân quyền người dùng (admin/user) và các thao tác quản lý nhân viên (Thêm, Sửa, Xóa) dành riêng cho quản trị viên.

## Tính năng chính

* **Quản lý Nhân viên:**
    * Hiển thị danh sách nhân viên với thông tin chi tiết (Mã NV, Tên NV, Giới tính, Nơi Sinh, Tên Phòng, Lương).
    * Hiển thị hình ảnh đại diện theo giới tính (nam/nữ).
    * Phân trang danh sách nhân viên (5 nhân viên/trang).
* **Quản lý Phòng ban:**
    * Lưu trữ thông tin phòng ban (Mã Phòng, Tên Phòng).
    * Liên kết nhân viên với phòng ban tương ứng.
* **Quản lý Người dùng & Phân quyền:**
    * Đăng nhập / Đăng xuất người dùng.
    * Lưu trữ mật khẩu an toàn bằng `password_hash()`.
    * Phân quyền người dùng:
        * `admin`: Có toàn quyền xem, thêm, sửa, xóa nhân viên.
        * `user`: Chỉ có quyền xem danh sách nhân viên.
* **Bảo mật:**
    * Sử dụng Prepared Statements để chống tấn công SQL Injection.
    * Sử dụng `htmlspecialchars()` để chống tấn công XSS khi hiển thị dữ liệu.

## Công nghệ sử dụng

* **Ngôn ngữ:** PHP (>= 7.0 khuyến nghị do sử dụng `password_hash`)
* **Cơ sở dữ liệu:** MySQL (hoặc MariaDB)
* **Giao diện:** HTML, CSS (cơ bản, inline/internal)
* **Web Server:** Apache, Nginx (hoặc bất kỳ web server nào hỗ trợ PHP)

## Điều kiện tiên quyết

* Đã cài đặt một môi trường phát triển web như XAMPP, WAMP, MAMP hoặc một web server có PHP và MySQL.
* Trình duyệt web (Chrome, Firefox, Edge,...).
* Công cụ quản lý CSDL (như phpMyAdmin) để thiết lập cơ sở dữ liệu ban đầu.

## Cài đặt và Chạy dự án

1.  **Clone hoặc Tải mã nguồn:**
    * Đặt toàn bộ mã nguồn dự án vào thư mục gốc của web server (ví dụ: `htdocs` trong XAMPP, `www` trong WAMP).

2.  **Tạo Cơ sở dữ liệu:**
    * Sử dụng công cụ quản lý CSDL (như phpMyAdmin), tạo một cơ sở dữ liệu mới. Tên khuyến nghị: `QL_NhanSu`.
    * Chọn collation `utf8mb4_unicode_ci` cho CSDL để hỗ trợ tốt tiếng Việt.

3.  **Cấu hình Kết nối CSDL:**
    * Mở file `db_connect.php`.
    * Chỉnh sửa các thông tin sau cho phù hợp với môi trường MySQL của bạn:
        ```php
        $servername = "localhost";  // Hoặc địa chỉ IP/hostname của máy chủ MySQL
        $username   = "root";       // Tên người dùng MySQL
        $password   = "";           // Mật khẩu MySQL
        $dbname     = "QL_NhanSu";  // Tên cơ sở dữ liệu đã tạo ở bước 2
        ```

4.  **Tạo Bảng trong CSDL:**
    * Chạy đoạn mã SQL sau trong CSDL `QL_NhanSu` đã tạo để tạo các bảng `PHONGBAN`, `NHANVIEN`, và `user`:

    ```sql
    -- Tạo bảng PHONGBAN
    CREATE TABLE PHONGBAN (
        Ma_Phong VARCHAR(2) NOT NULL,
        Ten_Phong NVARCHAR(30) NOT NULL,
        CONSTRAINT PK_PHONGBAN PRIMARY KEY (Ma_Phong)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Tạo bảng NHANVIEN
    CREATE TABLE NHANVIEN (
        Ma_NV VARCHAR(3) NOT NULL,
        Ten_NV NVARCHAR(100) NOT NULL,
        Phai NVARCHAR(3),
        Noi_Sinh NVARCHAR(200),
        Ma_Phong VARCHAR(2),
        Luong INT,
        CONSTRAINT PK_NHANVIEN PRIMARY KEY (Ma_NV),
        CONSTRAINT FK_NHANVIEN_PHONGBAN FOREIGN KEY (Ma_Phong) REFERENCES PHONGBAN(Ma_Phong)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Tạo bảng user
    CREATE TABLE user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname NVARCHAR(100),
        email VARCHAR(100) UNIQUE,
        role VARCHAR(20) NOT NULL DEFAULT 'user'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Chèn dữ liệu mẫu cho PHONGBAN (Tùy chọn nhưng cần thiết cho dữ liệu nhân viên mẫu)
    INSERT INTO PHONGBAN (Ma_Phong, Ten_Phong) VALUES
    ('QT', N'Quản Trị'),
    ('TC', N'Tài Chính'),
    ('KT', N'Kỹ Thuật');

    -- Chèn dữ liệu mẫu cho NHANVIEN (Tùy chọn)
    INSERT INTO NHANVIEN (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) VALUES
    ('A01', N'Nguyễn Thị Hải', N'NU', N'Hà Nội', 'TC', 600),
    ('A02', N'Trần Văn Chính', N'NAM', N'Bình Định', 'QT', 500),
    ('A03', N'Lê Trần Bạch Yến', N'NU', N'TP HCM', 'TC', 700),
    ('A04', N'Trần Anh Tuấn', N'NAM', N'Hà Nội', 'KT', 800),
    ('B01', N'Trần Thanh Mai', N'NU', N'Hải Phòng', 'TC', 800),
    ('B02', N'Trần Thị Thu Thùy', N'NU', N'TP HCM', 'KT', 700),
    ('B03', N'Nguyễn Thị Nở', N'NU', N'Ninh Bình', 'KT', 400);
    ```

5.  **Tạo Tài khoản Admin Ban đầu:**
    * Trong thư mục dự án, bạn sẽ thấy file `create_admin.php`.
    * (Tùy chọn) Mở file này và chỉnh sửa thông tin `$admin_username`, `$admin_password`, `$admin_fullname`, `$admin_email` nếu bạn muốn thay đổi tài khoản admin mặc định.
    * Chạy file `create_admin.php` bằng cách truy cập nó qua trình duyệt (ví dụ: `http://localhost/ten_thu_muc_du_an/create_admin.php`).
    * **QUAN TRỌNG:** Sau khi chạy thành công và thấy thông báo "Tạo tài khoản admin thành công!", hãy **XÓA** file `create_admin.php` khỏi dự án để đảm bảo an toàn.
    * Tài khoản admin mặc định (nếu không sửa):
        * Username: `admin`
        * Password: `password123`

6.  **Chuẩn bị Thư mục Hình ảnh:**
    * Đảm bảo có một thư mục tên là `images` trong thư mục gốc của dự án.
    * Đặt các file hình ảnh sau vào thư mục `images`:
        * `man.jpg` (Hình đại diện cho giới tính Nam)
        * `woman.jpg` (Hình đại diện cho giới tính Nữ)
        * `edit.png` (Biểu tượng nút Sửa)
        * `delete.png` (Biểu tượng nút Xóa)

7.  **Chạy Dự án:**
    * Khởi động Web Server (Apache) và MySQL Server của bạn.
    * Mở trình duyệt web và truy cập vào thư mục chứa dự án (ví dụ: `http://localhost/ten_thu_muc_du_an/`).
    * Bạn sẽ được tự động chuyển đến trang đăng nhập (`login.php`).

## Hướng dẫn sử dụng

1.  **Đăng nhập:**
    * Sử dụng tài khoản đã tạo (mặc định: `admin` / `password123`) để đăng nhập.
    * Nếu đăng nhập sai, thông báo lỗi sẽ hiển thị.
2.  **Xem Danh sách Nhân viên:**
    * Sau khi đăng nhập thành công, bạn sẽ thấy danh sách nhân viên.
    * Sử dụng các liên kết phân trang ở cuối bảng để xem các trang khác.
3.  **Chức năng Admin:**
    * Nếu bạn đăng nhập bằng tài khoản `admin`:
        * Bạn sẽ thấy nút **"THÊM NHÂN VIÊN"** ở trên bảng. Click vào đây để đi đến form thêm nhân viên mới.
        * Trong bảng danh sách, cột cuối cùng là **"Hành động"** với các biểu tượng:
            * Bút chì (`edit.png`): Click để sửa thông tin nhân viên tương ứng.
            * Thùng rác (`delete.png`): Click để xóa nhân viên tương ứng (sẽ có hộp thoại xác nhận).
4.  **Thêm/Sửa Nhân viên:**
    * Điền đầy đủ thông tin vào form và nhấn nút "Thêm Nhân viên" hoặc "Cập nhật".
5.  **Đăng xuất:**
    * Click vào link "Đăng xuất" ở góc trên bên phải để kết thúc phiên làm việc và quay lại trang đăng nhập.

## Cấu trúc File (Sơ lược)

## Cấu trúc File (Sơ lược)

Dưới đây là mô tả các file chính trong dự án:

* **`db_connect.php`**: Chứa thông tin cấu hình và mã lệnh để kết nối đến cơ sở dữ liệu MySQL.
* **`index.php`**: Trang chính của ứng dụng. Hiển thị danh sách nhân viên (có phân trang), kiểm tra trạng thái đăng nhập và vai trò người dùng để hiển thị các tùy chọn tương ứng (Xem, Thêm, Sửa, Xóa).
* **`login.php`**: Hiển thị giao diện form đăng nhập cho người dùng nhập tên đăng nhập và mật khẩu.
* **`process_login.php`**: Tiếp nhận và xử lý thông tin từ form đăng nhập. Xác thực thông tin với CSDL, tạo session nếu thành công và chuyển hướng người dùng.
* **`logout.php`**: Xử lý yêu cầu đăng xuất bằng cách hủy session và chuyển hướng người dùng về trang đăng nhập.
* **`add_nhanvien.php`**: Form dành cho quản trị viên (`admin`) để nhập thông tin của một nhân viên mới.
* **`process_add.php`**: Xử lý dữ liệu được gửi từ `add_nhanvien.php`. Thực hiện lệnh `INSERT` vào CSDL để thêm nhân viên mới (chỉ `admin`).
* **`edit_nhanvien.php`**: Form dành cho `admin` để chỉnh sửa thông tin của một nhân viên hiện có. Form này sẽ được điền sẵn dữ liệu cũ của nhân viên.
* **`process_edit.php`**: Xử lý dữ liệu được gửi từ `edit_nhanvien.php`. Thực hiện lệnh `UPDATE` trong CSDL (chỉ `admin`).
* **`delete_nhanvien.php`**: Xử lý yêu cầu xóa một nhân viên dựa trên `Ma_NV` được gửi đến. Thực hiện lệnh `DELETE` trong CSDL (chỉ `admin`, thường được gọi sau khi người dùng xác nhận).
* **`create_admin.php`**: Script tiện ích, chạy **một lần duy nhất** để tạo tài khoản `admin` ban đầu trong CSDL. **Cần xóa file này sau khi chạy thành công.**
* **`README.md`**: Chính là file này, cung cấp thông tin tổng quan, hướng dẫn cài đặt và sử dụng dự án.
* **`images/`**: Thư mục chứa các file hình ảnh cần thiết cho giao diện:
    * `man.jpg`: Hình đại diện giới tính nam.
    * `woman.jpg`: Hình đại diện giới tính nữ.
    * `edit.png`: Biểu tượng cho chức năng sửa.
    * `delete.png`: Biểu tượng cho chức năng xóa.
---
Chúc bạn cài đặt và sử dụng dự án thành công!