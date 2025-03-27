<?php
session_start();
require_once 'db_connect.php';

// --- KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Hoặc trang thông báo lỗi quyền
    exit();
}

// --- Lấy danh sách phòng ban để tạo dropdown ---
$phongban_sql = "SELECT Ma_Phong, Ten_Phong FROM PHONGBAN ORDER BY Ten_Phong";
$phongban_result = $conn->query($phongban_sql);
// Kiểm tra lỗi $conn->error

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Nhân viên</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Thêm Nhân viên mới</h1>
        <div class="form-container">
            <form action="process_add.php" method="post">
                <div class="form-group">
                    <label for="ma_nv">Mã NV:</label>
                    <input type="text" id="ma_nv" name="Ma_NV" required pattern="[A-Za-z0-9]{1,3}" title="Mã NV tối đa 3 ký tự chữ hoặc số">
                </div>
                <div class="form-group">
                    <label for="ten_nv">Tên NV:</label>
                    <input type="text" id="ten_nv" name="Ten_NV" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Phái:</label>
                    <div class="radio-group">
                        <input type="radio" id="phai_nu" name="Phai" value="NU" required>
                        <label for="phai_nu">Nữ</label>
                        <input type="radio" id="phai_nam" name="Phai" value="NAM">
                        <label for="phai_nam">Nam</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="noi_sinh">Nơi Sinh:</label>
                    <input type="text" id="noi_sinh" name="Noi_Sinh" maxlength="200">
                </div>
                <div class="form-group">
                    <label for="ma_phong">Phòng Ban:</label>
                    <select id="ma_phong" name="Ma_Phong" required>
                        <option value="">-- Chọn Phòng Ban --</option>
                        <?php
                        if ($phongban_result->num_rows > 0) {
                            while($pb_row = $phongban_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($pb_row['Ma_Phong']) . "'>"
                                     . htmlspecialchars($pb_row['Ten_Phong']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="luong">Lương:</label>
                    <input type="number" id="luong" name="Luong" min="0">
                </div>
                <div class="form-actions">
                    <button type="submit" class="add-button">Thêm Nhân viên</button>
                    <a href="index.php" class="cancel-button">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>