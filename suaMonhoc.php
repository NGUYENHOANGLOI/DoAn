<?php
require 'connect.php';

// Lấy mã môn từ URL
if (isset($_GET['mamon'])) {
    $mamon = $_GET['mamon'];

    // Lấy danh sách khoa để làm dropdown
    $khoaResult = $conn->query("SELECT * FROM Khoa");

    // Nếu form sửa đã gửi
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $tenmon = $_POST['tenmon'];
        $sotiet = $_POST['sotiet'];
        $sotinchi = $_POST['sotinchi'];
        $makhoa = $_POST['makhoa'];

        $sql = "UPDATE MonHoc SET 
                    TenMon = '$tenmon',
                    Sotiet = $sotiet,
                    SoTinChi = $sotinchi,
                    MaKhoa = " . ($makhoa ? "'$makhoa'" : "NULL") . "
                WHERE MaMon = '$mamon'";

        if ($conn->query($sql) === TRUE) {
            header("Location: quanlyMonhoc.php");
            exit;
        } else {
            $message = "Lỗi: " . $conn->error;
        }
    }

    // Truy vấn thông tin môn học cần sửa
    $result = $conn->query("SELECT * FROM MonHoc WHERE MaMon = '$mamon'");
    $row = $result->fetch_assoc();
} else {
    header("Location: quanlyMonhoc.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Môn Học</title>
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
            <h2>Sửa thông tin Môn học</h2>
        </div>

        <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

        <div class="form-section">
            <form method="post" action="">
                <div class="form-group">
                    Mã Môn:
                    <input type="text" value="<?= $row['MaMon'] ?>" disabled>
                    <div class="help-text">Mã môn không thể thay đổi</div>
                </div>

                <div class="form-group">
                    Tên Môn:
                    <input type="text" name="tenmon" value="<?= $row['TenMon'] ?>" required>

                    Số Tiết:
                    <input type="number" name="sotiet" value="<?= $row['Sotiet'] ?>" required>

                    Số Tín Chỉ:
                    <input type="number" name="sotinchi" value="<?= $row['SoTinChi'] ?>" required>

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
                    <a href="quanlyMonhoc.php"><button type="button" class="btn-secondary">Hủy</button></a>
                </div>
            </form>
        </div>
    </div>
</body>