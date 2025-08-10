<?php
require 'connect.php';

// Xử lý thêm khoa nếu form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "themkhoa") {
    $makhoa = $_POST['makhoa'];
    $tenkhoa = $_POST['tenkhoa'];

    if (strlen($makhoa) > 5) {
        $message = "❌ Mã khoa chỉ được phép tối đa 5 ký tự.";
    } else {
        // Kiểm tra mã khoa đã tồn tại chưa
        $check = $conn->query("SELECT * FROM Khoa WHERE MaKhoa = '$makhoa'");
        if ($check->num_rows > 0) {
            $message = "❌ Mã khoa $makhoa đã tồn tại.";
        } else {
            // Thêm khoa mới
            $sqlInsert = "INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES ('$makhoa', '$tenkhoa')";
            if ($conn->query($sqlInsert) === TRUE) {
                $message = "✅ Thêm khoa thành công.";
            } else {
                $message = "❌ Lỗi: " . $conn->error;
            }
        }
    }
}

// Truy vấn danh sách khoa
$sql = "SELECT * FROM Khoa";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khoa</title>
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

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"] {
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
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            padding: 6px 12px;
            font-size: 12px;
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
            width: 120px;
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

        <a href="quanlyKhoa.php" class="menu-item active">
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

        <a href="quanlyPhonghoc.php" class="menu-item">
            <i class="fas fa-door-open"></i>
            Quản lý phòng học
        </a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <a href="DashboardSV.php" class="back-link"><i class="fas fa-home"></i> Trang chủ</a>
                <h2>🏛️ Quản lý Khoa</h2>
            </div>

            <?php if (isset($message)): ?>
                <div id="notification" class="notification <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>Thêm khoa mới</h3>
                <form method="post" action="">
                    <input type="hidden" name="action" value="themkhoa">
                    Mã Khoa:<input type="text" name="makhoa" id="makhoa" placeholder="Nhập mã khoa có 5 kí tự" required maxlength="5">
                    Tên khoa:<input type="text" name="tenkhoa" placeholder="Nhập tên khoa" required>

                    <div style="display:flex; gap:10px;">
                        <button type="submit">Thêm khoa</button>
                        <a href=""><button type="button" class="btn-secondary">Làm mới</button></a>
                    </div>
                </form>
            </div>
            <h3> <i class="fas fa-list"></i>
                Danh sách Khoa</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Khoa</th>
                            <th>Tên Khoa</th>
                            <th class="action-cell">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['MaKhoa']) ?></td>
                                    <td><?= htmlspecialchars($row['TenKhoa']) ?></td>
                                    <td class="action-cell">
                                        <form style="display:inline" method="get" action="suaKhoa.php">
                                            <input type="hidden" name="makhoa" value="<?= htmlspecialchars($row['MaKhoa']) ?>">
                                            <button type="submit" class="btn-warning">Sửa</button>
                                        </form>

                                        <form style="display:inline" method="get" action="xoaKhoa.php" onsubmit="return confirm('Bạn có chắc muốn xóa khoa này không?');">
                                            <input type="hidden" name="makhoa" value="<?= htmlspecialchars($row['MaKhoa']) ?>">
                                            <button type="submit" class="btn-danger">Xóa</button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Không có khoa nào.</td>
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