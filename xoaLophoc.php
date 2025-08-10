<?php
require 'connect.php';

if (isset($_GET['malop'])) {
    $malop = $_GET['malop'];

    // Kiểm tra xem lớp học có sinh viên không
    $checkSV = $conn->query("SELECT * FROM SinhVien WHERE MaLop = '$malop'");
    // Kiểm tra xem lớp học có trong thời khóa biểu không
    $checkTKB = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaLop = '$malop'");

    if ($checkSV->num_rows > 0) {
        echo "<script>alert('Không thể xóa lớp học vì còn sinh viên trong lớp.'); window.location='quanlylophoc.php';</script>";
    } else {
        $sql = "DELETE FROM LopHoc WHERE MaLop = '$malop'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa lớp học thành công!'); window.location='quanlyLophoc.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa lớp học: " . $conn->error . "'); window.location='quanlyLophoc.php';</script>";
        }
    }
} else {
    header("Location: quanlylophoc.php");
    exit;
}
