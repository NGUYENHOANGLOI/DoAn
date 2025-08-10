<?php
$host = "127.0.0.1";      // hoặc "127.0.0.1"
$user = "root";           // tài khoản mặc định của XAMPP
$password = "";           // thường để trống nếu dùng XAMPP
$dbname = "quanlysinhvien";   // tên CSDL bạn đã tạo";

// Kết nối MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Thiết lập charset để hỗ trợ tiếng Việt
$conn->set_charset("utf8mb4");
