<?php
// --- index.php ---
session_start(); // LUÔN LUÔN bắt đầu session ở đầu file

// 1. KIỂM TRA ĐĂNG NHẬP
// Nếu chưa đăng nhập (không có user_id trong session), chuyển hướng về login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Dừng thực thi script hiện tại
}

// Lấy thông tin người dùng từ session
$loggedInUser = $_SESSION['fullname'] ?? $_SESSION['username']; // Lấy fullname, nếu ko có thì lấy username
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'); // Kiểm tra có phải admin không

// Bao gồm file kết nối CSDL (sau khi đã kiểm tra đăng nhập)
require_once 'db_connect.php';

// --- Phần code phân trang và truy vấn dữ liệu giữ nguyên như Phần 1 ---
$records_per_page = 5;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $records_per_page;

// Truy vấn tổng số
$total_sql = "SELECT COUNT(*) AS total FROM NHANVIEN";
$total_result = $conn->query($total_sql);
if (!$total_result) die("Lỗi truy vấn tổng số nhân viên: " . $conn->error);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $records_per_page;
} elseif ($current_page > 1 && $total_pages == 0) {
    $current_page = 1;
    $offset = 0;
}

// Truy vấn lấy dữ liệu
$sql = "SELECT nv.Ma_NV, nv.Ten_NV, nv.Phai, nv.Noi_Sinh, pb.Ten_Phong, nv.Luong
        FROM NHANVIEN nv
        JOIN PHONGBAN pb ON nv.Ma_Phong = pb.Ma_Phong
        ORDER BY nv.Ma_NV ASC -- Thêm ORDER BY để kết quả nhất quán
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Lỗi chuẩn bị truy vấn: " . $conn->error);
$stmt->bind_param("ii", $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Nhân viên</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>THÔNG TIN NHÂN VIÊN</h1>

        <div class="welcome-logout">
            Chào mừng, <?php echo htmlspecialchars($loggedInUser); ?>!
            <a href="logout.php">Đăng xuất</a>
        </div>

        <?php if ($isAdmin): ?>
            <a href="add_nhanvien.php" class="add-button">THÊM NHÂN VIÊN</a>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Mã Nhân Viên</th>
                        <th>Tên Nhân Viên</th>
                        <th>Giới tính</th>
                        <th>Nơi Sinh</th>
                        <th>Tên Phòng</th>
                        <th>Lương</th>
                        <?php if ($isAdmin): ?>
                            <th>Hành động</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["Ma_NV"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Ten_NV"]) . "</td>";
                            echo "<td style='text-align: center;'>";
                            if (strtoupper($row["Phai"]) === 'NU') {
                                echo "<img src='images/woman.png' alt='Nữ' class='gender-icon'>";
                            } elseif (strtoupper($row["Phai"]) === 'NAM') {
                                echo "<img src='images/man.png' alt='Nam' class='gender-icon'>";
                            } else {
                                echo htmlspecialchars($row["Phai"]);
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row["Noi_Sinh"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Ten_Phong"]) . "</td>";
                            echo "<td style='text-align: right;'>" . number_format($row["Luong"] ?? 0, 0, ',', '.') . "</td>";

                            if ($isAdmin) {
                                echo "<td class='action-links'>";
                                echo "<a href='edit_nhanvien.php?id=" . urlencode($row["Ma_NV"]) . "' title='Sửa'><img src='images/edit.png' alt='Sửa'></a>";
                                echo "<a href='delete_nhanvien.php?id=" . urlencode($row["Ma_NV"]) . "' title='Xóa' onclick='return confirm(\"Bạn có chắc chắn muốn xóa nhân viên này?\");'><img src='images/delete.png' alt='Xóa'></a>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        $colspan = $isAdmin ? 7 : 6;
                        echo "<tr><td colspan='$colspan' style='text-align:center;'>Không có dữ liệu nhân viên.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php
            if ($current_page > 1) echo "<a href='?page=" . ($current_page - 1) . "'>&laquo; Trước</a>";
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) echo "<span class='current-page'>" . $i . "</span>";
                else echo "<a href='?page=" . $i . "'>" . $i . "</a>";
            }
            if ($current_page < $total_pages) echo "<a href='?page=" . ($current_page + 1) . "'>Sau &raquo;</a>";
            ?>
        </div>
        <?php endif; ?>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>