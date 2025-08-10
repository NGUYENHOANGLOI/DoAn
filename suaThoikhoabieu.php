<?php
require 'connect.php';

// Lấy mã TKB từ URL
if (isset($_GET['matkb'])) {
    $matkb = $_GET['matkb'];

    // Nếu form sửa đã được gửi
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)
            AND MaTKB != $matkb");

            // Kiểm tra trùng GV
            $check_gv = $conn->query("SELECT * FROM ThoiKhoaBieu 
            WHERE MaGV = '$maGV' AND Thu = $thu AND NgayHoc = '$ngayHoc' 
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)
            AND MaTKB != $matkb");

            // Kiểm tra trùng phòng
            $check_phong = $conn->query("SELECT * FROM ThoiKhoaBieu 
            WHERE MaPhong = '$maPhong' AND Thu = $thu AND NgayHoc = '$ngayHoc' 
            AND NOT (TietKetThuc < $tietBD OR TietBatDau > $tietKT)
            AND MaTKB != $matkb");

            if ($check_lop->num_rows > 0) {
                $message = "❌ Trùng lịch: Lớp học đã có lịch.";
            } elseif ($check_gv->num_rows > 0) {
                $message = "❌ Trùng lịch: Giảng viên đã có tiết dạy.";
            } elseif ($check_phong->num_rows > 0) {
                $message = "❌ Trùng lịch: Phòng học đã có lớp khác.";
            } else {
                // Cập nhật dữ liệu
                $sql = "UPDATE ThoiKhoaBieu 
                    SET MaLop='$maLop', MaMon='$maMon', MaGV='$maGV', MaPhong='$maPhong',
                        NgayHoc='$ngayHoc', Thu=$thu, 
                        TietBatDau=$tietBD, TietKetThuc=$tietKT, GhiChu='$ghiChu'
                    WHERE MaTKB = $matkb";
                if ($conn->query($sql) === TRUE) {
                    header("Location: quanlyThoikhoabieu.php");
                    exit;
                } else {
                    $message = "Lỗi: " . $conn->error;
                }
            }
        }
    }

    // Lấy dữ liệu cũ
    $result = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaTKB = $matkb");
    $row = $result->fetch_assoc();

    $lops = $conn->query("SELECT MaLop, TenLop FROM LopHoc");
    $mons = $conn->query("SELECT MaMon, TenMon FROM MonHoc");
    $gvs = $conn->query("SELECT MaGV, TenGV FROM GiangVien");
    $phongs = $conn->query("SELECT MaPhong, TenPhong FROM PhongHoc");
} else {
    header("Location: quanlyThoikhoabieu.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thời khóa biểu</title>
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
        input[type="number"],
        input[type="date"],
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
            <h2>Sửa Thời khóa biểu</h2>
        </div>

        <?php if (isset($message)) echo "<p style='color: red;'><strong>$message</strong></p>"; ?>

        <div class="form-section">
            <form method="post" action="">

                <div class="form-group">
                    Lớp:
                    <select name="maLop" required>
                        <?php while ($lop = $lops->fetch_assoc()): ?>
                            <option value="<?= $lop['MaLop'] ?>" <?= $lop['MaLop'] == $row['MaLop'] ? 'selected' : '' ?>>
                                <?= $lop['TenLop'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    Môn học:
                    <select name="maMon" required>
                        <?php while ($mon = $mons->fetch_assoc()): ?>
                            <option value="<?= $mon['MaMon'] ?>" <?= $mon['MaMon'] == $row['MaMon'] ? 'selected' : '' ?>>
                                <?= $mon['TenMon'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    Giảng viên:
                    <select name="maGV" required>
                        <?php while ($gv = $gvs->fetch_assoc()): ?>
                            <option value="<?= $gv['MaGV'] ?>" <?= $gv['MaGV'] == $row['MaGV'] ? 'selected' : '' ?>>
                                <?= $gv['TenGV'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    Phòng học:
                    <select name="maPhong" required>
                        <?php while ($phong = $phongs->fetch_assoc()): ?>
                            <option value="<?= $phong['MaPhong'] ?>" <?= $phong['MaPhong'] == $row['MaPhong'] ? 'selected' : '' ?>>
                                <?= $phong['TenPhong'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    Ngày học:
                    <input type="date" name="ngayHoc" value="<?= $row['NgayHoc'] ?>" required>

                    Thứ:
                    <select name="thu" id="thu" required>
                        <option value="">Chọn thứ</option>
                        <option value="2" <?= $row['Thu'] == 2 ? 'selected' : '' ?>>Thứ Hai</option>
                        <option value="3" <?= $row['Thu'] == 3 ? 'selected' : '' ?>>Thứ Ba</option>
                        <option value="4" <?= $row['Thu'] == 4 ? 'selected' : '' ?>>Thứ Tư</option>
                        <option value="5" <?= $row['Thu'] == 5 ? 'selected' : '' ?>>Thứ Năm</option>
                        <option value="6" <?= $row['Thu'] == 6 ? 'selected' : '' ?>>Thứ Sáu</option>
                        <option value="7" <?= $row['Thu'] == 7 ? 'selected' : '' ?>>Thứ Bảy</option>
                        <option value="8" <?= $row['Thu'] == 8 ? 'selected' : '' ?>>Chủ Nhật</option>
                    </select>
                    <div class="help-text">Tự động cập nhật khi chọn ngày học</div>

                    Tiết bắt đầu:
                    <input type="number" name="tietBD" value="<?= $row['TietBatDau'] ?>" min="1" max="12" required>

                    Tiết kết thúc:
                    <input type="number" name="tietKT" value="<?= $row['TietKetThuc'] ?>" min="1" max="12" required>

                    Ghi chú:
                    <input type="text" name="ghiChu" value="<?= htmlspecialchars($row['GhiChu']) ?>">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    <a href="quanlyThoikhoabieu.php"><button type="button" class="btn-secondary">Hủy</button></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tự động tính thứ từ ngày học
        document.querySelector('input[name="ngayHoc"]').addEventListener('change', function() {
            const date = new Date(this.value);
            if (!isNaN(date)) {
                let day = date.getDay(); // 0 = CN
                let thu = (day === 0) ? 8 : day + 1;
                document.querySelector('select[name="thu"]').value = thu;
            }
        });
    </script>
</body>

</html>