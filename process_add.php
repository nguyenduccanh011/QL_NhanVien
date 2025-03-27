<?php
session_start();
require_once 'db_connect.php';

// --- KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này."); // Hoặc redirect
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Lấy và Validate dữ liệu từ $_POST ---
    $ma_nv = trim($_POST['Ma_NV'] ?? '');
    $ten_nv = trim($_POST['Ten_NV'] ?? '');
    $phai = trim($_POST['Phai'] ?? '');
    $noi_sinh = trim($_POST['Noi_Sinh'] ?? '');
    $ma_phong = trim($_POST['Ma_Phong'] ?? '');
    $luong = filter_input(INPUT_POST, 'Luong', FILTER_VALIDATE_INT, ["options" => ["min_range"=>0]]); // Lấy và validate lương

    // --- Kiểm tra các trường bắt buộc ---
    if (empty($ma_nv) || empty($ten_nv) || empty($phai) || empty($ma_phong) || $luong === false || $luong === null) {
         die("Vui lòng điền đầy đủ thông tin bắt buộc và lương hợp lệ."); // Xử lý lỗi tốt hơn
    }
    // Thêm các validate khác nếu cần (độ dài, định dạng mã NV...)

    // --- Chuẩn bị và thực thi INSERT (Prepared Statement) ---
    $sql = "INSERT INTO NHANVIEN (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }
    $stmt->bind_param("sssssi", $ma_nv, $ten_nv, $phai, $noi_sinh, $ma_phong, $luong);

    if ($stmt->execute()) {
        // Thêm thành công, chuyển hướng về trang danh sách
        header("Location: index.php?status=add_success"); // Có thể thêm tham số để hiện thông báo
        exit();
    } else {
        // Xử lý lỗi (ví dụ: Mã NV đã tồn tại - lỗi unique key)
        if ($conn->errno == 1062) { // Mã lỗi MySQL cho duplicate entry
             die("Lỗi: Mã nhân viên '$ma_nv' đã tồn tại.");
        } else {
             die("Lỗi khi thêm nhân viên: " . $stmt->error);
        }
    }
    $stmt->close();

} else {
    header("Location: add_nhanvien.php"); // Nếu truy cập trực tiếp, về form thêm
    exit();
}
$conn->close();
?>