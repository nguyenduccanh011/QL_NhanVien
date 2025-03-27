<?php
session_start();
require_once 'db_connect.php';

// --- KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // --- Lấy và Validate dữ liệu từ $_POST ---
    $ma_nv_orig = trim($_POST['Ma_NV_Orig'] ?? ''); // Lấy mã gốc từ hidden input
    $ten_nv = trim($_POST['Ten_NV'] ?? '');
    $phai = trim($_POST['Phai'] ?? '');
    $noi_sinh = trim($_POST['Noi_Sinh'] ?? '');
    $ma_phong = trim($_POST['Ma_Phong'] ?? '');
    $luong = filter_input(INPUT_POST, 'Luong', FILTER_VALIDATE_INT, ["options" => ["min_range"=>0]]);

    // --- Kiểm tra các trường bắt buộc và mã NV gốc---
    if (empty($ma_nv_orig) || empty($ten_nv) || empty($phai) || empty($ma_phong) || $luong === false || $luong === null) {
         die("Vui lòng điền đầy đủ thông tin bắt buộc và lương hợp lệ.");
    }
    // Thêm validate khác nếu cần

    // --- Chuẩn bị và thực thi UPDATE (Prepared Statement) ---
    $sql = "UPDATE NHANVIEN SET Ten_NV = ?, Phai = ?, Noi_Sinh = ?, Ma_Phong = ?, Luong = ? WHERE Ma_NV = ?";
    $stmt = $conn->prepare($sql);
     if (!$stmt) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }
    // Lưu ý thứ tự bind phải khớp với thứ tự dấu ? trong câu SQL
    $stmt->bind_param("ssssis", $ten_nv, $phai, $noi_sinh, $ma_phong, $luong, $ma_nv_orig);

    if ($stmt->execute()) {
        // Cập nhật thành công
        header("Location: index.php?status=edit_success");
        exit();
    } else {
        die("Lỗi khi cập nhật nhân viên: " . $stmt->error);
    }
    $stmt->close();

} else {
    header("Location: index.php"); // Nếu truy cập trực tiếp, về trang chính
    exit();
}
$conn->close();
?>