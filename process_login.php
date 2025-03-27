<?php
// --- process_login.php ---
session_start(); // Bắt đầu session để lưu thông tin nếu đăng nhập thành công
require_once 'db_connect.php'; // Kết nối CSDL

// 1. Kiểm tra phương thức gửi là POST chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Lấy dữ liệu từ form và làm sạch (cơ bản)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 3. Validate dữ liệu đầu vào (cơ bản)
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Vui lòng nhập tên đăng nhập và mật khẩu.";
        header("Location: login.php");
        exit();
    }

    // 4. Chuẩn bị truy vấn lấy thông tin user (Prepared Statement)
    $sql = "SELECT id, username, password, fullname, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        // Lưu lỗi vào session thay vì die() để thân thiện hơn
        $_SESSION['login_error'] = "Lỗi hệ thống, vui lòng thử lại sau.";
        error_log("Lỗi chuẩn bị truy vấn đăng nhập: " . $conn->error); // Ghi log lỗi
        header("Location: login.php");
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 5. Kiểm tra kết quả
    if ($result->num_rows === 1) {
        // Tìm thấy user
        $user = $result->fetch_assoc();

        // 6. Xác thực mật khẩu bằng password_verify()
        if (password_verify($password, $user['password'])) {
            // Mật khẩu chính xác -> Đăng nhập thành công

            // Xóa lỗi cũ (nếu có)
            unset($_SESSION['login_error']);

            // Lưu thông tin cần thiết vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role']; // Quan trọng để kiểm tra quyền

            // Chuyển hướng đến trang chính
            header("Location: index.php");
            exit();

        } else {
            // Sai mật khẩu
            $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Không tìm thấy user
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();

} else {
    // Nếu không phải POST, chuyển về trang login
    header("Location: login.php");
    exit();
}

$conn->close();
?>