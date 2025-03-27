<?php
session_start();
require_once 'db_connect.php';

// --- KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này.");
}

// --- Lấy và validate Ma_NV từ URL ---
$ma_nv_delete = trim($_GET['id'] ?? '');
if (empty($ma_nv_delete)) {
    die("Không tìm thấy mã nhân viên cần xóa.");
}

// --- Chuẩn bị và thực thi DELETE (Prepared Statement) ---
// KHÔNG cần hiển thị form xác nhận ở đây vì đã có confirm JS ở index.php
$sql = "DELETE FROM NHANVIEN WHERE Ma_NV = ?";
$stmt = $conn->prepare($sql);
 if (!$stmt) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}
$stmt->bind_param("s", $ma_nv_delete);

if ($stmt->execute()) {
    // Kiểm tra xem có dòng nào bị ảnh hưởng không (để chắc chắn đã xóa)
    if ($stmt->affected_rows > 0) {
        header("Location: index.php?status=delete_success");
        exit();
    } else {
         // Không có dòng nào bị xóa (có thể do mã NV không tồn tại)
         header("Location: index.php?status=delete_notfound");
         exit();
    }
} else {
     die("Lỗi khi xóa nhân viên: " . $stmt->error);
}

$stmt->close();
$conn->close();

?>