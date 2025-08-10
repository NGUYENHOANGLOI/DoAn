<?php
require 'connect.php';

if (isset($_GET['maphong'])) {
    $maphong = $_GET['maphong'];

    // Kiểm tra xem phòng học có liên quan trong thời khóa biểu không
    $checkTKB = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaPhong = '$maphong'");

    if ($checkTKB->num_rows > 0) {
        echo "<script>alert('Không thể xóa phòng học vì đang được sử dụng trong thời khóa biểu.'); window.location='quanlyPhonghoc.php';</script>";
    } else {
        $sql = "DELETE FROM PhongHoc WHERE MaPhong = '$maphong'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa phòng học thành công!'); window.location='quanlyPhonghoc.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa phòng học: " . $conn->error . "'); window.location='quanlyPhonghoc.php';</script>";
        }
    }
} else {
    header("Location: quanlyPhonghoc.php");
    exit;
}
