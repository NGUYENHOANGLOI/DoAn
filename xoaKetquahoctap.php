<?php
require 'connect.php';

if (isset($_GET['masv']) && isset($_GET['mamon'])) {
    $masv = $_GET['masv'];
    $mamon = $_GET['mamon'];

    $sql = "DELETE FROM KetQuaHocTap WHERE MaSV = '$masv' AND MaMon = '$mamon'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Đã xóa kết quả học tập thành công!'); window.location='quanlyKetQuaHocTap.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa: " . $conn->error . "'); window.location='quanlyKetquahoctap.php';</script>";
    }
} else {
    header("Location: quanlyKetquahoctap.php");
    exit;
}
