<?php
require 'connect.php';

if (isset($_GET['mamon'])) {
    $mamon = $_GET['mamon'];

    // Kiểm tra ràng buộc dữ liệu: Môn học có đang được sử dụng ở bảng ThoiKhoaBieu hoặc KetQuaHocTap không?
    $checkTKB = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaMon = '$mamon'");
    $checkKQ = $conn->query("SELECT * FROM KetQuaHocTap WHERE MaMon = '$mamon'");

    if ($checkTKB->num_rows > 0 || $checkKQ->num_rows > 0) {
        echo "<script>alert('Không thể xóa môn học vì đang được sử dụng trong thời khóa biểu hoặc kết quả học tập.'); 
              window.location='quanlyMonhoc.php';</script>";
    } else {
        $sql = "DELETE FROM MonHoc WHERE MaMon = '$mamon'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa môn học thành công!'); window.location='quanlyMonhoc.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa môn học: " . $conn->error . "'); window.location='quanlyMonhoc.php';</script>";
        }
    }
} else {
    header("Location: quanlyMonhoc.php");
    exit;
}
