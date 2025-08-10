<?php
require 'connect.php';

// Hàm xếp loại theo điểm trung bình tích lũy
function xepLoai($diem)
{
    if ($diem >= 9.0 && $diem <= 10.0) return "Xuất sắc";
    if ($diem >= 8.0 && $diem < 9.0) return "Giỏi";
    if ($diem >= 7.0 && $diem < 8.0) return "Khá";
    if ($diem >= 5.0 && $diem < 7.0) return "Trung bình";
    if ($diem >= 4.0 && $diem < 5.0) return "Yếu";
    return "Kém";
}

// Xử lý thêm kết quả học tập nếu form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "themketqua") {
    $masv = $conn->real_escape_string(trim($_POST['masv']));
    $mamon = $conn->real_escape_string(trim($_POST['mamon']));
    $diemTrenLop = floatval($_POST['diemTrenLop']);
    $diemGiuaKy = floatval($_POST['diemGiuaKy']);
    $diemCuoiKy = floatval($_POST['diemCuoiKy']);
    // Kiểm tra điểm có hợp lệ không
    if (
        $diemTrenLop < 0 || $diemTrenLop > 10 ||
        $diemGiuaKy < 0 || $diemGiuaKy > 10 ||
        $diemCuoiKy < 0 || $diemCuoiKy > 10
    ) {
        $message = "Điểm phải nằm trong khoảng từ 0 đến 10.";
    } else {
        // Kiểm tra xem đã có kết quả cho sinh viên + môn học chưa
        $check = $conn->query("SELECT * FROM KetQuaHocTap WHERE MaSV='$masv' AND MaMon='$mamon'");
        if ($check->num_rows > 0) {
            $message = "❌ Kết quả học tập cho sinh viên này và môn học này đã tồn tại.";
        } else {
            $sqlInsert = "INSERT INTO KetQuaHocTap (MaSV, MaMon, DiemTrenLop, DiemGiuaKy, DiemCuoiKy) 
              VALUES ('$masv', '$mamon', $diemTrenLop, $diemGiuaKy, $diemCuoiKy)";
            if ($conn->query($sqlInsert) === TRUE) {
                $message = "✅ Thêm kết quả học tập thành công.";
            } else {
                $message = "❌ Lỗi: " . $conn->error;
            }
        }
    }
}

// Lấy danh sách kết quả học tập kèm tên SV và tên môn
$sql = "SELECT kq.*, sv.TenSV, mh.TenMon, mh.SoTinChi
        FROM KetQuaHocTap kq
        JOIN SinhVien sv ON kq.MaSV = sv.MaSV
        JOIN MonHoc mh ON kq.MaMon = mh.MaMon
        ORDER BY kq.MaSV, kq.MaMon";
$result = $conn->query($sql);

// Tính điểm trung bình tích lũy từng sinh viên
$sql_avg = "SELECT sv.MaSV, sv.TenSV, ROUND(SUM(kq.DiemTongKet * mh.SoTinChi) / SUM(mh.SoTinChi), 2) AS DiemTB
    FROM
        KetQuaHocTap kq
    JOIN SinhVien sv ON kq.MaSV = sv.MaSV
    JOIN MonHoc mh ON kq.MaMon = mh.MaMon
    GROUP BY sv.MaSV
    ORDER BY sv.TenSV ";
$tb_result = $conn->query($sql_avg);

// Lấy danh sách sinh viên
$sv_list = $conn->query("SELECT MaSV, TenSV FROM SinhVien ORDER BY TenSV");
$mon_list = $conn->query("SELECT MaMon, TenMon FROM MonHoc ORDER BY TenMon");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Kết quả học tập</title>
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
            border-collapse: separate;
            border-spacing: 0;
        }

        .table1-container,
        .table2-container {
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

        .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
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

        <a href="quanlyThoikhoabieu.php" class="menu-item">
            <i class="fas fa-calendar-alt"></i>
            Quản lý TKB
        </a>

        <a href="quanlyKetquahoctap.php" class="menu-item active">
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
                <h2>📊 Quản lý Kết quả học tập</h2>
            </div>

            <?php if (isset($message)): ?>
                <div id="notification" class="notification <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>Thêm kết quả học tập</h3>
                <form method="post" action="">
                    <input type="hidden" name="action" value="themketqua">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="masv">Sinh viên:</label>
                            <select name="masv" id="masv" required>
                                <option value="">Chọn sinh viên</option>
                                <?php while ($sv = $sv_list->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($sv['MaSV']) ?>"><?= htmlspecialchars($sv['TenSV']) ?> (<?= htmlspecialchars($sv['MaSV']) ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mamon">Môn học:</label>
                            <select name="mamon" id="mamon" required>
                                <option value="">Chọn môn học</option>
                                <?php while ($mh = $mon_list->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($mh['MaMon']) ?>"><?= htmlspecialchars($mh['TenMon']) ?> (<?= htmlspecialchars($mh['MaMon']) ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="diemTrenLop">Điểm trên lớp:</label>
                            <input type="number" step="0.01" min="0" max="10" name="diemTrenLop" id="diemTrenLop" required>
                        </div>

                        <div class="form-group">
                            <label for="diemGiuaKy">Điểm giữa kỳ:</label>
                            <input type="number" step="0.01" min="0" max="10" name="diemGiuaKy" id="diemGiuaKy" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="diemCuoiKy">Điểm cuối kỳ:</label>
                            <input type="number" step="0.01" min="0" max="10" name="diemCuoiKy" id="diemCuoiKy" required>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="submit">Thêm kết quả</button>
                        <a href=""><button type="button" class="btn-secondary">Làm mới</button></a>
                    </div>
                </form>
            </div>

            <h3>
                <i class="fas fa-list"></i>
                Danh sách kết quả học tập
            </h3>
            <div class="table1-container">
                <table cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã SV</th>
                            <th>Tên SV</th>
                            <th>Mã môn</th>
                            <th>Tên môn</th>
                            <th>Số tín chỉ</th>
                            <th>Điểm trên lớp</th>
                            <th>Điểm giữa kỳ</th>
                            <th>Điểm cuối kỳ</th>
                            <th>Điểm tổng kết</th>
                            <th class="action-cell">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): $i = 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['MaSV']) ?></td>
                                    <td><?= htmlspecialchars($row['TenSV']) ?></td>
                                    <td><?= htmlspecialchars($row['MaMon']) ?></td>
                                    <td><?= htmlspecialchars($row['TenMon']) ?></td>
                                    <td><?= $row['SoTinChi'] ?></td>
                                    <td><?= $row['DiemTrenLop'] ?></td>
                                    <td><?= $row['DiemGiuaKy'] ?></td>
                                    <td><?= $row['DiemCuoiKy'] ?></td>
                                    <td><?= $row['DiemTongKet'] ?></td>
                                    <td class="action-cell">
                                        <form style="display:inline" method="get" action="suaKetquahoctap.php">
                                            <input type="hidden" name="masv" value="<?= htmlspecialchars($row['MaSV']) ?>">
                                            <input type="hidden" name="mamon" value="<?= htmlspecialchars($row['MaMon']) ?>">
                                            <button class="btn-warning">Sửa</button>
                                        </form>
                                        <form style="display:inline" method="get" action="xoaKetquahoctap.php" onsubmit="return confirm('Bạn có chắc muốn xóa kết quả này không?');">
                                            <input type="hidden" name="masv" value="<?= htmlspecialchars($row['MaSV']) ?>">
                                            <input type="hidden" name="mamon" value="<?= htmlspecialchars($row['MaMon']) ?>">
                                            <button class="btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align:center;">Chưa có kết quả học tập.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <h3 class="section-title">
                <i class="fas fa-trophy"></i>
                Điểm trung bình học kỳ và xếp loại
            </h3>
            <div class="table2-container">
                <table cellpadding="4" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Tên SV</th>
                            <th>Điểm trung bình học kỳ</th>
                            <th>Xếp loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tb_result->num_rows > 0): ?>
                            <?php while ($row = $tb_result->fetch_assoc()):
                                $xeploai = xepLoai($row['DiemTB']);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['MaSV']) ?></td>
                                    <td><?= htmlspecialchars($row['TenSV']) ?></td>
                                    <td><?= $row['DiemTB'] ?></td>
                                    <td><?= $xeploai ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Chưa có dữ liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
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