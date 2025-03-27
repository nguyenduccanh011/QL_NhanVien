<?php
// --- create_admin.php ---
require_once 'db_connect.php'; // Dùng lại file kết nối

// Thông tin tài khoản admin mẫu
$admin_username = 'admin';
$admin_password = 'password123'; // Mật khẩu gốc (sẽ được băm)
$admin_fullname = 'Quản Trị Viên';
$admin_email = 'admin@example.com';
$admin_role = 'admin';

// Thông tin tài khoản user mẫu
$user_username = 'nhanvien01';
$user_password = 'password456'; // Mật khẩu gốc
$user_fullname = 'Nhân Viên A';
$user_email = 'user@example.com';
$user_role = 'user'; // Hoặc bỏ qua vì đã có DEFAULT 'user'

// --- Băm mật khẩu ---
// Sử dụng thuật toán mặc định (hiện tại là BCRYPT), an toàn và được khuyến nghị
$hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);
$hashed_user_password = password_hash($user_password, PASSWORD_DEFAULT);

// --- Chuẩn bị câu lệnh INSERT (sử dụng Prepared Statement) ---
$sql = "INSERT INTO users (username, password, fullname, email, role) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

// --- Thêm tài khoản admin ---
$stmt->bind_param("sssss", $admin_username, $hashed_admin_password, $admin_fullname, $admin_email, $admin_role);
if ($stmt->execute()) {
    echo "Tạo tài khoản admin thành công!<br>";
} else {
    echo "Lỗi khi tạo tài khoản admin: " . $stmt->error . "<br>";
}

// --- Thêm tài khoản user ---
$stmt->bind_param("sssss", $user_username, $hashed_user_password, $user_fullname, $user_email, $user_role);
if ($stmt->execute()) {
    echo "Tạo tài khoản user thành công!<br>";
} else {
    echo "Lỗi khi tạo tài khoản user: " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();

?>