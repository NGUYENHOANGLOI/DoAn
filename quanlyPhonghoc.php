<?php
require 'connect.php';

// Xử lý thêm phòng học
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "themphong") {
    $maphong = $_POST['maphong'];
    $tenphong = $_POST['tenphong'];
    $daynha = $_POST['daynha'];
    $loaiphong = $_POST['loaiphong'];

    if (strlen($maphong) > 5) {
        $message = "❌ Mã phòng chỉ được phép tối đa 5 ký tự.";
    } else {
        // Kiểm tra mã phòng đã tồn tại chưa
        $check = $conn->query("SELECT * FROM PhongHoc WHERE MaPhong = '$maphong'");
        if ($check->num_rows > 0) {
            $message = "❌ Mã phòng $maphong đã tồn tại.";
        } else {
            $sqlInsert = "INSERT INTO PhongHoc (MaPhong, TenPhong, DayNha, LoaiPhong)
                          VALUES ('$maphong', '$tenphong', '$daynha', '$loaiphong')";
            $message = $conn->query($sqlInsert) ? "✅ Thêm phòng học thành công." : "❌ Lỗi: " . $conn->error;
        }
    }
}


// Truy vấn danh sách phòng học
$sql = "SELECT * FROM PhongHoc";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phòng học</title>
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

        <a href="quanlyThoikhoabieu.php" class="menu-item">
            <i class="fas fa-calendar-alt"></i>
            Quản lý TKB
        </a>

        <a href="quanlyKetquahoctap.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            Quản lý điểm
        </a>

        <a href="quanlyPhonghoc.php" class="menu-item active">
            <i class="fas fa-door-open"></i>
            Quản lý phòng học
        </a>

    </div>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <a href="DashboardSV.php" class="back-link"><i class="fas fa-home"></i> Trang chủ</a>
                <h2>🏫 Quản lý Phòng học</h2>
            </div>

            <?php if (isset($message)): ?>
                <div id="notification" class="notification <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>Thêm phòng học mới</h3>
                <form method="post" action="">
                    <input type="hidden" name="action" value="themphong">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="maphong">Mã phòng:</label>
                            <input type="text" id="maphong" name="maphong" id="maphong" placeholder="Nhập mã phòng có 5 kí tự" required maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="tenphong">Tên phòng:</label>
                            <input type="text" id="tenphong" name="tenphong" placeholder="Nhập tên phòng" required>
                        </div>
                        <div class="form-group">
                            <label for="daynha">Dãy nhà:</label>
                            <input type="text" id="daynha" name="daynha" placeholder="Nhập dãy nhà" required>
                        </div>
                        <div class="form-group">
                            <label for="loaiphong">Loại phòng:</label>
                            <select id="loaiphong" name="loaiphong" required>
                                <option value="">Chọn loại phòng</option>
                                <option value="Lý thuyết">Lý thuyết</option>
                                <option value="Thực hành">Thực hành</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="submit">Thêm phòng</button>
                        <a href=""><button type="button" class="btn-secondary">Làm mới</button></a>
                    </div>
                </form>

            </div>
            <h3> <i class="fas fa-list"></i>
                Danh sách Phòng học</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã phòng</th>
                            <th>Tên phòng</th>
                            <th>Dãy nhà</th>
                            <th>Loại phòng</th>
                            <th class="action-cell">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['MaPhong']) ?></td>
                                    <td><?= htmlspecialchars($row['TenPhong']) ?></td>
                                    <td><?= htmlspecialchars($row['DayNha']) ?></td>
                                    <td><?= htmlspecialchars($row['LoaiPhong']) ?></td>
                                    <td class="action-cell">
                                        <form style="display:inline;" method="get" action="suaPhonghoc.php">
                                            <input type="hidden" name="maphong" value="<?= htmlspecialchars($row['MaPhong']) ?>">
                                            <button class="btn-warning">Sửa</button>
                                        </form>
                                        <form style="display:inline;" method="get" action="xoaPhonghoc.php" onsubmit="return confirm('Bạn có chắc muốn xóa phòng học này không?');">
                                            <input type="hidden" name="maphong" value="<?= htmlspecialchars($row['MaPhong']) ?>">
                                            <button class="btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Không có phòng học nào.</td>
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