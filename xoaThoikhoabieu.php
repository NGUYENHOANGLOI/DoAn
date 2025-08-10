<?php
require 'connect.php';

if (isset($_GET['matkb'])) {
    $matkb = $_GET['matkb'];

    // Kiểm tra xem thời khóa biểu có tồn tại không
    $checkTKB = $conn->query("SELECT * FROM ThoiKhoaBieu WHERE MaTKB = '$matkb'");

    if ($checkTKB->num_rows == 0) {
        echo "<script>alert('Thời khóa biểu không tồn tại.'); window.location='quanlyThoikhoabieu.php';</script>";
    } else {
        // Xóa thời khóa biểu
        $sql = "DELETE FROM ThoiKhoaBieu WHERE MaTKB = '$matkb'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Xóa thời khóa biểu thành công!'); window.location='quanlyThoikhoabieu.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa thời khóa biểu: " . $conn->error . "'); window.location='quanlyThoikhoabieu.php';</script>";
        }
    }
} else {
    // Nếu không có mã thời khóa biểu được truyền vào
    echo "<script>alert('Không có mã thời khóa biểu được chọn.'); window.location='quanlyThoikhoabieu.php';</script>";
}

// Đóng kết nối
$conn->close();
