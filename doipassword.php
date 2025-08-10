<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['role'])) {
    die("Bạn chưa đăng nhập.");
}

$message = "";

if ($_SESSION['role'] === 'sinhvien') {
    $username = $_SESSION['MaSV'];
    $user_column = "MaSV";
    $table = "SinhVien";
} elseif ($_SESSION['role'] === 'admin') {
    $username = $_SESSION['username'];
    $user_column = "username";
    $table = "users";
} else {
    die("Vai trò không hợp lệ.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $conn->real_escape_string($_POST['current_password']);
    $new_password = $conn->real_escape_string($_POST['new_password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Mật khẩu mới và xác nhận không khớp.";
    } else {
        if ($table === "SinhVien") {
            $sql = "SELECT * FROM SinhVien WHERE MaSV='$username'";
        } else {
            $sql = "SELECT * FROM users WHERE username='$username'";
        }

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            // Đối với sinh viên: cho phép mật khẩu cũ là MaSV hoặc đã cập nhật
            $valid_old_password = (
                ($table === "SinhVien" && ($current_password === $username || $current_password === $stored_password)) ||
                ($table === "users" && $current_password === $stored_password)
            );

            if ($valid_old_password) {
                $update_sql = "UPDATE $table SET password='$new_password' WHERE $user_column='$username'";
                if ($conn->query($update_sql) === TRUE) {
                    echo "<script>alert('Đổi mật khẩu thành công!'); window.location='View_DashboardSV.php';</script>";
                    exit();
                } else {
                    $redirect_page = ($_SESSION['role'] === 'sinhvien') ? 'View_DashboardSV.php' : 'DashboardSV.php';
                    echo "<script>alert('Đổi mật khẩu thành công!'); window.location='$redirect_page';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Mật khẩu hiện tại không đúng.'); window.history.back();</script>";
                exit();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
            color: #2c3e50;
        }

        .container {
            max-width: 400px;
            margin: 30px auto;
            background: white;
            padding: 20px 30px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #1976d2;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 8px 10px;
            background: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Đổi password cho tk: <?= htmlspecialchars($username) ?></h2>

        <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

        <form method="post" action="">
            <label for="current_password">Mật khẩu hiện tại:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">Mật khẩu mới:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Xác nhận mật khẩu mới:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Đổi mật khẩu</button>
        </form>
    </div>
</body>

</html>