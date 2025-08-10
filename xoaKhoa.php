<?php
require 'connect.php';

if (isset($_GET['makhoa'])) {
    $makhoa = $_GET['makhoa'];

    // Kiểm tra xem khoa có lớp học hoặc môn học liên quan không
    $checkLop = $conn->query("SELECT * FROM LopHoc WHERE MaKhoa = '$makhoa'");
    $checkMon = $conn->query("SELECT * FROM MonHoc WHERE MaKhoa = '$makhoa'");

    if ($checkLop->num_rows > 0 || $checkMon->num_rows > 0) {
        echo "<script>alert('Không thể xóa khoa vì còn lớp học hoặc môn học liên quan.'); window.location='quanlyKhoa.php';</script>";
    } else {
        $sql = "DELETE FROM Khoa WHERE MaKhoa = '$makhoa'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa khoa thành công!'); window.location='quanlyKhoa.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa khoa: " . $conn->error . "'); window.location='quanlyKhoa.php';</script>";
        }
    }
} else {
    header("Location: quanlyKhoa.php");
    exit;
}
