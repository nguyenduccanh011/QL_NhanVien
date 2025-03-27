<?php
session_start();
require_once 'db_connect.php';

// --- KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// --- Lấy và validate Ma_NV từ URL ---
$ma_nv_edit = trim($_GET['id'] ?? '');
if (empty($ma_nv_edit)) {
    die("Không tìm thấy mã nhân viên cần sửa.");
}

// --- Lấy thông tin nhân viên cần sửa ---
$sql_get = "SELECT * FROM NHANVIEN WHERE Ma_NV = ?";
$stmt_get = $conn->prepare($sql_get);
if(!$stmt_get) die("Lỗi chuẩn bị: ".$conn->error);
$stmt_get->bind_param("s", $ma_nv_edit);
$stmt_get->execute();
$result_get = $stmt_get->get_result();

if ($result_get->num_rows !== 1) {
    die("Không tìm thấy nhân viên với mã: " . htmlspecialchars($ma_nv_edit));
}
$nhanvien = $result_get->fetch_assoc();
$stmt_get->close();

// --- Lấy danh sách phòng ban ---
$phongban_sql = "SELECT Ma_Phong, Ten_Phong FROM PHONGBAN ORDER BY Ten_Phong";
$phongban_result = $conn->query($phongban_sql);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa thông tin Nhân viên</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Sửa thông tin Nhân viên</h1>
        <div class="form-container">
            <form action="process_edit.php" method="post">
                <input type="hidden" name="Ma_NV_Orig" value="<?php echo htmlspecialchars($nhanvien['Ma_NV']); ?>">

                <div class="form-group">
                    <label>Mã NV:</label>
                    <strong><?php echo htmlspecialchars($nhanvien['Ma_NV']); ?></strong>
                </div>
                <div class="form-group">
                    <label for="ten_nv">Tên NV:</label>
                    <input type="text" id="ten_nv" name="Ten_NV" required maxlength="100" value="<?php echo htmlspecialchars($nhanvien['Ten_NV']); ?>">
                </div>
                <div class="form-group">
                    <label>Phái:</label>
                    <div class="radio-group">
                        <input type="radio" id="phai_nu" name="Phai" value="NU" required <?php echo ($nhanvien['Phai'] == 'NU') ? 'checked' : ''; ?>>
                        <label for="phai_nu">Nữ</label>
                        <input type="radio" id="phai_nam" name="Phai" value="NAM" <?php echo ($nhanvien['Phai'] == 'NAM') ? 'checked' : ''; ?>>
                        <label for="phai_nam">Nam</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="noi_sinh">Nơi Sinh:</label>
                    <input type="text" id="noi_sinh" name="Noi_Sinh" maxlength="200" value="<?php echo htmlspecialchars($nhanvien['Noi_Sinh'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="ma_phong">Phòng Ban:</label>
                    <select id="ma_phong" name="Ma_Phong" required>
                        <option value="">-- Chọn Phòng Ban --</option>
                        <?php
                        if ($phongban_result && $phongban_result->num_rows > 0) {
                            while($pb_row = $phongban_result->fetch_assoc()) {
                                $selected = ($pb_row['Ma_Phong'] == $nhanvien['Ma_Phong']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($pb_row['Ma_Phong']) . "' $selected>"
                                     . htmlspecialchars($pb_row['Ten_Phong']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="luong">Lương:</label>
                    <input type="number" id="luong" name="Luong" min="0" value="<?php echo htmlspecialchars($nhanvien['Luong'] ?? 0); ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" class="add-button">Cập nhật</button>
                    <a href="index.php" class="cancel-button">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>