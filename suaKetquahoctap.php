<?php
require 'connect.php';

// Lấy mã sinh viên và mã môn học từ URL
if (isset($_GET['masv']) && isset($_GET['mamon'])) {
    $masv = $_GET['masv'];
    $mamon = $_GET['mamon'];

    // Nếu form sửa đã gửi
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $diemtrenlop = $_POST['diemtrenlop'];
        $diemgiuaky = $_POST['diemgiuaky'];
        $diemcuoiky = $_POST['diemcuoiky'];

        if (
            $diemtrenlop < 0 || $diemtrenlop > 10 ||
            $diemgiuaky < 0 || $diemgiuaky > 10 ||
            $diemcuoiky < 0 || $diemcuoiky > 10
        ) {
            $message = "Điểm phải nằm trong khoảng từ 0 đến 10!";
        } else {
            // Chỉ cập nhật 3 cột điểm, DiemTongKet sẽ tự động tính
            $sql = "UPDATE KetQuaHocTap SET 
                    DiemTrenLop = '$diemtrenlop', 
                    DiemGiuaKy = '$diemgiuaky', 
                    DiemCuoiKy = '$diemcuoiky'
                    WHERE MaSV = '$masv' AND MaMon = '$mamon'";

            if ($conn->query($sql) === TRUE) {
                header("Location: quanlyKetquahoctap.php");
                exit;
            } else {
                $message = "Lỗi: " . $conn->error;
            }
        }
    }

    // Lấy dữ liệu kết quả học tập cần sửa
    $result = $conn->query("SELECT * FROM KetQuaHocTap WHERE MaSV = '$masv' AND MaMon = '$mamon'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: quanlyKetquahoctap.php");
        exit;
    }
} else {
    header("Location: quanlyKetquahoctap.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Kết Quả Học Tập</title>
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
        input[type="number"] {
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
            <h2>Sửa Kết Quả Học Tập</h2>
        </div>

        <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

        <div class="form-section">
            <form method="post" action="">
                <div class="form-group">
                    Mã Sinh Viên:
                    <input type="text" name="masv" value="<?= $row['MaSV'] ?>" disabled>
                    <div class="help-text">Không thể thay đổi mã sinh viên</div>
                </div>

                <div class="form-group">
                    Mã Môn Học:
                    <input type="text" name="mamon" value="<?= $row['MaMon'] ?>" disabled>
                    <div class="help-text">Không thể thay đổi mã môn học</div>
                </div>

                <div class="form-group">
                    Điểm Trên Lớp:
                    <input type="number" step="0.01" name="diemtrenlop" value="<?= $row['DiemTrenLop'] ?>" required>

                    Điểm Giữa Kỳ:
                    <input type="number" step="0.01" name="diemgiuaky" value="<?= $row['DiemGiuaKy'] ?>" required>

                    Điểm Cuối Kỳ:
                    <input type="number" step="0.01" name="diemcuoiky" value="<?= $row['DiemCuoiKy'] ?>" required>

                    Điểm Tổng Kết:
                    <input type="text" value="<?= $row['DiemTongKet'] ?>" disabled>
                    <div class="help-text">Tự động tính theo công thức: 10% Trên lớp + 30% Giữa kỳ + 60% Cuối kỳ</div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    <a href="quanlyKetquahoctap.php"><button type="button" class="btn-secondary">Hủy</button></a>
                </div>
            </form>
        </div>
    </div>
</body>