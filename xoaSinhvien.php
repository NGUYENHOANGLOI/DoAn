<?php
require 'connect.php';

if (isset($_GET['masv'])) {
    $masv = $_GET['masv'];

    // Kiểm tra xem sinh viên có kết quả học tập không
    $checkKQ = $conn->query("SELECT * FROM KetQuaHocTap WHERE MaSV = '$masv'");

    if ($checkKQ->num_rows > 0) {
        echo "<script>alert('Không thể xóa sinh viên vì đã có kết quả học tập.'); window.location='quanlySinhvien.php';</script>";
    } else {
        $sql = "DELETE FROM SinhVien WHERE MaSV = '$masv'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa sinh viên thành công!'); window.location='quanlySinhvien.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa sinh viên: " . $conn->error . "'); window.location='quanlySinhvien.php';</script>";
        }
    }
} else {
    header("Location: quanlySinhvien.php");
    exit;
}
