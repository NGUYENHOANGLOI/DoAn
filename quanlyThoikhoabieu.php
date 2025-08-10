<?php
require 'connect.php';

// Xử lý thêm thời khóa biểu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "themTKB") {
    $maLop = $_POST['maLop'];
    $maMon = $_POST['maMon'];
    $maGV = $_POST['maGV'];
    $maPhong = $_POST['maPhong'];
    $ngayHoc = $_POST['ngayHoc'];
    $thu = $_POST['thu'];
    $tietBD = $_POST['tietBD'];
    $tietKT = $_POST['tietKT'];
    $ghiChu = $_POST['ghiChu'];

    // Kiểm tra hợp lệ
    if ($tietKT <= $tietBD) {
        $message = "❌ Tiết kết thúc phải lớn hơn tiết bắt đầu.";
    } else {
        // Kiểm tra trùng lớp
        $check_lop = $conn->query("SELECT * FROM ThoiKhoaBieu 
            WHERE MaLop = '$maLop' AND Thu = $thu AND NgayHoc = '$ngayHoc' 
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)");

        // Kiểm tra trùng GV
        $check_gv = $conn->query("SELECT * FROM ThoiKhoaBieu 
            WHERE MaGV = '$maGV' AND Thu = $thu AND NgayHoc = '$ngayHoc' 
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)");

        // Kiểm tra trùng phòng
        $check_phong = $conn->query("SELECT * FROM ThoiKhoaBieu 
            WHERE MaPhong = '$maPhong' AND Thu = $thu AND NgayHoc = '$ngayHoc' 
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)");

        if ($check_lop->num_rows > 0) {
            $message = "❌ Trùng lịch: Lớp học đã có lịch.";
        } elseif ($check_gv->num_rows > 0) {
            $message = "❌ Trùng lịch: Giảng viên đã có tiết dạy.";
        } elseif ($check_phong->num_rows > 0) {
            $message = "❌ Trùng lịch: Phòng học đã có lớp khác.";
        } else {
            $sql = "INSERT INTO ThoiKhoaBieu (MaLop, MaMon, MaGV, MaPhong, Thu, TietBatDau, TietKetThuc, NgayHoc, GhiChu) 
                VALUES ('$maLop', '$maMon', '$maGV', '$maPhong', $thu, $tietBD, $tietKT, '$ngayHoc', '$ghiChu')";

            if ($conn->query($sql) === TRUE) {
                $message = "✅ Thêm thời khóa biểu thành công.";
            } else {
                $message = "❌ Lỗi khi thêm: " . $conn->error;
            }
        }
    }
}


// Lấy danh sách thời khóa biểu
$sqlTKB = "SELECT tkb.*, l.TenLop, m.TenMon, gv.TenGV, p.TenPhong 
           FROM ThoiKhoaBieu tkb 
           JOIN LopHoc l ON tkb.MaLop = l.MaLop 
           JOIN MonHoc m ON tkb.MaMon = m.MaMon 
           JOIN GiangVien gv ON tkb.MaGV = gv.MaGV 
           JOIN PhongHoc p ON tkb.MaPhong = p.MaPhong
           ORDER BY tkb.NgayHoc, tkb.Thu, tkb.TietBatDau";
$resultTKB = $conn->query($sqlTKB);

// Dữ liệu cho form
$lops = $conn->query("SELECT MaLop, TenLop FROM LopHoc");
$mons = $conn->query("SELECT MaMon, TenMon FROM MonHoc");
$gvs  = $conn->query("SELECT MaGV, TenGV FROM GiangVien");
$phongs = $conn->query("SELECT MaPhong, TenPhong FROM PhongHoc");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thời khóa biểu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
            color: #2c3e50;
            display: flex;
            padding: 10px;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: white;
            height: 100vh;
            border: 1px solid #ddd;
            padding: 20px 0;
            position: fixed;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.08);
        }

        .sidebar h3 {
            padding: 0 20px 15px;
            border-bottom: 1px solid #eee;
            font-size: 20px;
        }

        .menu-item {
            display: block;
            padding: 15px 20px;
            color: #666;
            text-decoration: none;
            border-bottom: 1px solid #f5f5f5;
        }

        .menu-item:hover {
            background: #f8f9fa;
        }

        .menu-item.active {
            background: #e3f2fd;
            color: #1976d2;
            border-left: 4px solid #3498db;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link i {
            margin-right: 5px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        /* Form grid layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            grid-column: span 2;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-warning {
            background: #ffc107;
            font-size: 12px;
            padding: 6px 12px;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            font-size: 12px;
            padding: 6px 12px;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container {
            max-height: 323px;
            /* chiều cao tối đa hiển thị khoảng 5 hàng */
            overflow-y: auto;
            margin: 20px 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .action-cell {
            text-align: center;
            width: 150px;
        }

        .notification {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 15px;
        }

        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>Menu Quản lý</h3>
        <a href="quanlySinhvien.php" class="menu-item">
            <i class="fas fa-user-graduate"></i>
            Quản lý sinh viên
        </a>

        <a href="quanlyLophoc.php" class="menu-item">
            <i class="fas fa-users"></i>
            Quản lý lớp học
        </a>

        <a href="quanlyGiangvien.php" class="menu-item">
            <i class="fas fa-chalkboard-teacher"></i>
            Quản lý giảng viên
        </a>

        <a href="quanlyMonhoc.php" class="menu-item">
            <i class="fas fa-book"></i>
            Quản lý môn học
        </a>

        <a href="quanlyKhoa.php" class="menu-item">
            <i class="fas fa-university"></i>
            Quản lý khoa
        </a>

        <a href="quanlyThoikhoabieu.php" class="menu-item active">
            <i class="fas fa-calendar-alt"></i>
            Quản lý TKB
        </a>

        <a href="quanlyKetquahoctap.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            Quản lý điểm
        </a>

        <a href="quanlyPhonghoc.php" class="menu-item">
            <i class="fas fa-door-open"></i>
            Quản lý phòng học
        </a>

    </div>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <a href="DashboardSV.php" class="back-link"><i class="fas fa-home"></i> Trang chủ</a>
                <h2>📅 Quản lý Thời khóa biểu</h2>
            </div>

            <?php if (isset($message)): ?>
                <div id="notification" class="notification <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <form method="post" action="">
                    <h3>Thêm thời khóa biểu</h3>
                    <input type="hidden" name="action" value="themTKB">
                    <div class="form-grid">
                        <!-- Hàng 1 -->
                        <div class="form-group">
                            <label for="maLop">Lớp:</label>
                            <select name="maLop" id="maLop" required>
                                <option value="">Chọn lớp</option>
                                <?php while ($lop = $lops->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($lop['MaLop']) ?>"><?= htmlspecialchars($lop['TenLop']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="maMon">Môn học:</label>
                            <select name="maMon" id="maMon" required>
                                <option value="">Chọn môn</option>
                                <?php while ($mon = $mons->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($mon['MaMon']) ?>"><?= htmlspecialchars($mon['TenMon']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Hàng 2 -->
                        <div class="form-group">
                            <label for="maGV">Giảng viên:</label>
                            <select name="maGV" id="maGV" required>
                                <option value="">Chọn giảng viên</option>
                                <?php while ($gv = $gvs->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($gv['MaGV']) ?>"><?= htmlspecialchars($gv['TenGV']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="maPhong">Phòng học:</label>
                            <select name="maPhong" id="maPhong" required>
                                <option value="">Chọn phòng</option>
                                <?php while ($phong = $phongs->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($phong['MaPhong']) ?>"><?= htmlspecialchars($phong['TenPhong']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Hàng 3 -->
                        <div class="form-group">
                            <label for="ngayHoc">Ngày học:</label>
                            <input type="date" name="ngayHoc" id="ngayHoc" required>
                        </div>

                        <div class="form-group">
                            <label for="thu">Thứ:</label>
                            <select name="thu" id="thu" required>
                                <option value="">Chọn thứ</option>
                                <option value="2">Thứ Hai</option>
                                <option value="3">Thứ Ba</option>
                                <option value="4">Thứ Tư</option>
                                <option value="5">Thứ Năm</option>
                                <option value="6">Thứ Sáu</option>
                                <option value="7">Thứ Bảy</option>
                                <option value="8">Chủ Nhật</option>
                            </select>
                        </div>

                        <!-- Hàng 4 -->
                        <div class="form-group">
                            <label for="tietBD">Tiết bắt đầu:</label>
                            <input type="number" name="tietBD" id="tietBD" min="1" max="12" required>
                        </div>

                        <div class="form-group">
                            <label for="tietKT">Tiết kết thúc:</label>
                            <input type="number" name="tietKT" id="tietKT" min="1" max="12" required>
                        </div>

                        <!-- Hàng 5 -->
                        <div class="form-group full-width">
                            <label for="ghiChu">Ghi chú:</label>
                            <input type="text" name="ghiChu" id="ghiChu">
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="form-buttons">
                        <button type="submit">Thêm TKB</button>
                        <a href=""><button type="button" class="btn-secondary">Làm mới</button></a>
                    </div>
                </form>
            </div>
            <h3> <i class="fas fa-list"></i>
                Danh sách Thời khóa biểu</h3>
            <div class="table-container">
                <table border="1" cellpadding="8" cellspacing="0">
                    <tr>
                        <th>Lớp</th>
                        <th>Môn</th>
                        <th>Giảng viên</th>
                        <th>Phòng</th>
                        <th>Ngày học</th>
                        <th>Thứ</th>
                        <th>Tiết</th>
                        <th>Ghi chú</th>
                        <th class="action-cell">Thao tác</th>
                    </tr>
                    <?php if ($resultTKB->num_rows > 0): ?>
                        <?php while ($row = $resultTKB->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['TenLop']) ?></td>
                                <td><?= htmlspecialchars($row['TenMon']) ?></td>
                                <td><?= htmlspecialchars($row['TenGV']) ?></td>
                                <td><?= htmlspecialchars($row['TenPhong']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['NgayHoc'])) ?></td>
                                <td><?= htmlspecialchars($row['Thu'] == 8 ? 'Chủ nhật' : 'Thứ ' . $row['Thu']) ?></td>
                                <td><?= htmlspecialchars($row['TietBatDau']) ?> - <?= htmlspecialchars($row['TietKetThuc']) ?></td>
                                <td><?= htmlspecialchars($row['GhiChu']) ?></td>
                                <td class="action-cell">
                                    <form method="get" action="suaThoikhoabieu.php" style="display:inline">
                                        <input type="hidden" name="matkb" value="<?= htmlspecialchars($row['MaTKB']) ?>">
                                        <button class="btn-warning">Sửa</button>
                                    </form>
                                    <form method="get" action="xoaThoikhoabieu.php" onsubmit="return confirm('Bạn có chắc muốn xóa mục này?');" style="display:inline">
                                        <input type="hidden" name="matkb" value="<?= htmlspecialchars($row['MaTKB']) ?>">
                                        <button class="btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">Không có thời khóa biểu nào.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('ngayHoc').addEventListener('change', function() {
            const date = new Date(this.value);
            if (!isNaN(date)) {
                let day = date.getDay(); // 0 = CN, 1 = T2,... 6 = T7
                let thu = (day === 0) ? 8 : day + 1; // Chủ nhật = 8, Thứ 2-7 = 2-7
                document.getElementById('thu').value = thu;
            }
        });

        // Tự động ẩn thông báo lỗi sau 1 giây
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 500); // sau khi hiệu ứng fade-out hoàn tất
            }, 1000); // hiển thị trong 1 giây
        }
    </script>
</body>

</html>