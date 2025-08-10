<?php
require 'connect.php';

// Hàm tạo mã sinh viên tự động
function generateStudentCode($conn, $malop)
{
    // Lấy năm hiện tại
    $currentYear = date('Y');
    $shortYear = substr($currentYear, -2); // Lấy 2 chữ số cuối của năm

    // Tạo prefix từ năm (ví dụ: 2025 -> 25)
    $prefix = $shortYear;

    // Tìm số thứ tự cao nhất với prefix này
    $sql = "SELECT MaSV FROM SinhVien WHERE MaSV LIKE '$prefix%' ORDER BY MaSV DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Nếu có mã sinh viên tồn tại, lấy mã đó
        $row = $result->fetch_assoc();
        $lastCode = $row['MaSV'];
        // Tách 4 số cuối và chuyển thành số nguyên
        $lastNumber = intval(substr($lastCode, -4));
        // Tăng số đó lên 1
        $newNumber = $lastNumber + 1;
    } else {
        // Nếu chưa có mã nào, bắt đầu từ 1
        $newNumber = 1;
    }

    // Tạo mã sinh viên mới, ghép prefix với 4 số, đủ 4 chữ số (thêm số 0 ở đầu nếu cần)
    $newCode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return $newCode;
}

// Xử lý thêm sinh viên nếu form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "themsinhvien") {
    $malop = $_POST['malop'];
    $masv = generateStudentCode($conn, $malop);
    $tensv = $_POST['tensv'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];
    $noisinh = $_POST['noisinh'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];

    // Kiểm tra email đã tồn tại
    $checkEmail = $conn->query("SELECT * FROM SinhVien WHERE Email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $message = "❌ Email $email đã được sử dụng.";
    } else {
        // Thêm sinh viên
        $sqlInsert = "INSERT INTO SinhVien 
            (MaSV, TenSV, GioiTinh, NgaySinh, NoiSinh, Email, SDT, MaLop) 
            VALUES 
            ('$masv', '$tensv', '$gioitinh', '$ngaysinh', '$noisinh', '$email', '$sdt', '$malop')";
        if ($conn->query($sqlInsert) === TRUE) {
            $message = "✅ Thêm sinh viên thành công.";
        } else {
            $message = "❌ Lỗi: " . $conn->error;
        }
    }
}

// Xử lý preview mã sinh viên
if (isset($_GET['preview_malop'])) {
    $preview_malop = $_GET['preview_malop'];
    $preview_masv = generateStudentCode($conn, $preview_malop);
    echo json_encode(['masv' => $preview_masv]);
    exit;
}

// Lấy danh sách lớp cho dropdown chọn lớp
$lopResult = $conn->query("SELECT MaLop, TenLop FROM LopHoc");

// Truy vấn danh sách Sinhvien
$sql = "SELECT * FROM SinhVien ORDER BY MaSV DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sinh viên</title>
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

        .code-preview {
            background: #e8f5e8;
            border: 2px solid #28a745;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            font-weight: bold;
            color: #155724;
        }

        .code-preview-placeholder {
            background: #f8f9fa;
            border: 1px dashed #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            color: #666;
            font-style: italic;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"],
        input[type="email"],
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
        <a href="quanlySinhvien.php" class="menu-item active">
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

        <a href="quanlyPhonghoc.php" class="menu-item">
            <i class="fas fa-door-open"></i>
            Quản lý phòng học
        </a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <a href="DashboardSV.php" class="back-link"><i class="fas fa-home"></i> Trang chủ</a>
                <h2>👨‍🎓 Quản lý Sinh viên</h2>
            </div>

            <?php if (isset($message)): ?>
                <div id="notification" class="notification <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h3>Thêm sinh viên mới</h3>
                <form method="post" action="">
                    <input type="hidden" name="action" value="themsinhvien">
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="malop">Lớp:</label>
                            <select name="malop" id="malop" required onchange="previewStudentCode()">
                                <option value="">Chọn lớp</option>
                                <?php if ($lopResult->num_rows > 0): ?>
                                    <?php while ($row = $lopResult->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['MaLop']) ?>">
                                            <?= htmlspecialchars($row['TenLop']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <div id="code-preview" class="code-preview-placeholder">
                                Mã sinh viên sẽ được tạo tự động
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tensv">Tên SV:</label>
                            <input type="text" name="tensv" id="tensv" required>
                        </div>

                        <div class="form-group">
                            <label for="gioitinh">Giới tính:</label>
                            <select name="gioitinh" id="gioitinh" required>
                                <option value="">Chọn giới tính</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ngaysinh">Ngày sinh:</label>
                            <input type="date" name="ngaysinh" id="ngaysinh" required>
                        </div>

                        <div class="form-group">
                            <label for="noisinh">Nơi sinh:</label>
                            <input type="text" name="noisinh" id="noisinh" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" required>
                        </div>

                        <div class="form-group">
                            <label for="sdt">SĐT:</label>
                            <input type="text" name="sdt" id="sdt" required maxlength="15">
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="submit">Thêm sinh viên</button>
                        <button type="reset" class="btn-secondary" onclick="resetPreview()">Làm mới</button>
                    </div>
                </form>
            </div>
            <h3> <i class="fas fa-list"></i>
                Danh sách Sinh viên</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Tên SV</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh</th>
                            <th>Nơi sinh</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Mã Lớp</th>
                            <th class="action-cell">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['MaSV']) ?></td>
                                    <td><?= htmlspecialchars($row['TenSV']) ?></td>
                                    <td><?= htmlspecialchars($row['GioiTinh']) ?></td>
                                    <td><?= htmlspecialchars($row['NgaySinh']) ?></td>
                                    <td><?= htmlspecialchars($row['NoiSinh']) ?></td>
                                    <td><?= htmlspecialchars($row['Email']) ?></td>
                                    <td><?= htmlspecialchars($row['SDT']) ?></td>
                                    <td><?= htmlspecialchars($row['MaLop']) ?></td>
                                    <td class="action-cell">
                                        <form style="display:inline;" method="get" action="suaSinhvien.php">
                                            <input type="hidden" name="masv" value="<?= htmlspecialchars($row['MaSV']) ?>">
                                            <button class="btn-warning">Sửa</button>
                                        </form>
                                        <form style="display:inline;" method="get" action="xoaSinhvien.php" onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này không?');">
                                            <input type="hidden" name="masv" value="<?= htmlspecialchars($row['MaSV']) ?>">
                                            <button class="btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">Không có sinh viên nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function previewStudentCode() {
            const malop = document.getElementById('malop').value;
            const previewDiv = document.getElementById('code-preview');

            if (!malop) {
                previewDiv.className = 'code-preview-placeholder';
                previewDiv.textContent = 'Mã sinh viên sẽ được tạo tự động';
                return;
            }

            // Gọi AJAX để lấy mã sinh viên preview
            fetch(`?preview_malop=${malop}`)
                .then(response => response.json())
                .then(data => {
                    previewDiv.className = 'code-preview';
                    previewDiv.innerHTML = `<i class="fas fa-id-card"></i> Mã sinh viên sẽ được tạo: <strong>${data.masv}</strong>`;
                })
                .catch(error => {
                    previewDiv.className = 'code-preview-placeholder';
                    previewDiv.textContent = 'Lỗi khi tạo mã sinh viên';
                });
        }

        function resetPreview() {
            const previewDiv = document.getElementById('code-preview');
            previewDiv.className = 'code-preview-placeholder';
            previewDiv.textContent = 'Mã sinh viên sẽ được tạo tự động';
        }

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