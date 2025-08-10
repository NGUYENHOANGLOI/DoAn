<?php
require 'connect.php';

if (isset($_GET['magv'])) {
    $magv = $_GET['magv'];

    // Kiểm tra giảng viên có lịch dạy không
    $checkTKB = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaGV = '$magv'");

    if ($checkTKB->num_rows > 0) {
        echo "<script>alert('Không thể xóa giảng viên vì đang có trong thời khóa biểu.'); window.location='quanlyGiangvien.php';</script>";
    } else {
        $sql = "DELETE FROM GiangVien WHERE MaGV = '$magv'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa giảng viên thành công!'); window.location='quanlyGiangvien.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa giảng viên: " . $conn->error . "'); window.location='quanlyGiangvien.php';</script>";
        }
    }
} else {
    header("Location: quanlyGiangVien.php");
    exit;
}
