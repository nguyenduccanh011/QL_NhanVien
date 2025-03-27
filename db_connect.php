<?php
// --- db_connect.php ---

$servername = "localhost";  // Hoặc địa chỉ IP/hostname của máy chủ MySQL
$username   = "root";       // Tên người dùng MySQL (thường là root nếu dùng XAMPP mặc định)
$password   = "";           // Mật khẩu MySQL (thường là trống nếu dùng XAMPP mặc định)
$dbname     = "QL_NhanSu";  // Tên cơ sở dữ liệu

// Tạo kết nối bằng MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    // Ngừng thực thi và hiển thị lỗi nếu kết nối thất bại
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt bộ mã ký tự kết nối thành UTF-8 để hiển thị tiếng Việt đúng
// Quan trọng khi làm việc với tiếng Việt!
if (!$conn->set_charset("utf8mb4")) {
    printf("Lỗi khi đặt bộ ký tự utf8mb4: %s\n", $conn->error);
    exit();
}
// Biến $conn bây giờ đã sẵn sàng để sử dụng cho các truy vấn
?>