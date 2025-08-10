<?php
require 'connect.php';

// Lấy mã giảng viên từ URL
if (isset($_GET['magv'])) {
    $magv = $_GET['magv'];

    // Nếu form sửa đã gửi
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tengv = $_POST['tengv'];
        $hocvi = $_POST['hocvi'];
        $gioitinh = $_POST['gioitinh'];
        $dienthoai = $_POST['dienthoai'];
        $email = $_POST['email'];
        $makhoa = $_POST['makhoa'];

        $sql = "UPDATE GiangVien SET 
                    TenGV = '$tengv',
                    HocVi = '$hocvi',
                    GioiTinh = '$gioitinh',
                    DienThoai = '$dienthoai',
                    Email = '$email',
                    MaKhoa = '$makhoa'
                WHERE MaGV = '$magv'";

        if ($conn->query($sql) === TRUE) {
            header("Location: quanlyGiangvien.php");
            exit;
        } else {
            $message = "Lỗi: " . $conn->error;
        }
    }

    // Truy vấn thông tin giảng viên cần sửa
    $result = $conn->query("SELECT * FROM GiangVien WHERE MaGV = '$magv'");
    $row = $result->fetch_assoc();

    // Truy vấn danh sách khoa
    $khoaResult = $conn->query("SELECT MaKhoa, TenKhoa FROM Khoa");
} else {
    header("Location: quanlyGiangvienien.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Giảng viên</title>
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
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        input[type="text"]:disabled {
            background-color: #e9ecef;
            opacity: 1;
            cursor: not-allowed;
        }

        .help-text {
            color: #6c757d;
            font-size: 12px;
            font-style: italic;
            margin-bottom: 10px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        button {
            padding: 8px 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Sửa thông tin Giảng viên</h2>
        </div>

        <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

        <div class="form-section">
            <form method="post" action="">
                <div class="form-group">
                    Mã GV:
                    <input type="text" value="<?= htmlspecialchars($row['MaGV']) ?>" disabled>
                    <div class="help-text">Mã giảng viên không thể thay đổi</div>
                </div>

                <div class="form-group">
                    Tên GV:
                    <input type="text" name="tengv" value="<?= htmlspecialchars($row['TenGV']) ?>" required>

                    Học vị:
                    <select name="hocvi" required>
                        <option value="">Chọn học vị</option>
                        <option value="Thạc sĩ" <?= $row['HocVi'] == 'Thạc sĩ' ? 'selected' : '' ?>>Thạc sĩ</option>
                        <option value="Tiến sĩ" <?= $row['HocVi'] == 'Tiến sĩ' ? 'selected' : '' ?>>Tiến sĩ</option>
                        <option value="Phó Giáo sư" <?= $row['HocVi'] == 'Phó Giáo sư' ? 'selected' : '' ?>>Phó Giáo sư</option>
                        <option value="Giáo sư" <?= $row['HocVi'] == 'Giáo sư' ? 'selected' : '' ?>>Giáo sư</option>
                    </select>

                    Giới tính:
                    <select name="gioitinh" required>
                        <option value="">Chọn giới tính</option>
                        <option value="Nam" <?= $row['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= $row['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                    </select>

                    Điện thoại:
                    <input type="text" name="dienthoai" value="<?= htmlspecialchars($row['DienThoai']) ?>">

                    Email:
                    <input type="email" name="email" value="<?= htmlspecialchars($row['Email']) ?>">

                    Khoa:
                    <select name="makhoa" required>
                        <?php while ($khoa = $khoaResult->fetch_assoc()): ?>
                            <option value="<?= $khoa['MaKhoa'] ?>" <?= $row['MaKhoa'] == $khoa['MaKhoa'] ? 'selected' : '' ?>>
                                <?= $khoa['TenKhoa'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    <a href="quanlyGiangvien.php"><button type="button" class="btn-secondary">Hủy</button></a>
                </div>
            </form>
        </div>
    </div>
</body>