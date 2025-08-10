<?php
require 'connect.php';

if (isset($_GET['masv'])) {
  $masv = $_GET['masv'];

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $tensv = $_POST['tensv'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];
    $noisinh = $_POST['noisinh'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $malop = $_POST['malop'];

    // Câu lệnh cập nhật
    $sql = "UPDATE SinhVien SET TenSV = '$tensv', GioiTinh = '$gioitinh', NgaySinh = '$ngaysinh',
          NoiSinh = '$noisinh', Email = '$email', SDT = '$sdt', MaLop = '$malop' WHERE MaSV = '$masv'";

    if ($conn->query($sql) === TRUE) {
      header("Location: quanlySinhvien.php");
      exit;
    } else {
      $message = "Lỗi: " . $conn->error;
    }
  }

  // Lấy dữ liệu sinh viên cần sửa
  $result = $conn->query("SELECT * FROM SinhVien WHERE MaSV = '$masv'");
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
  } else {
    header("Location: quanlySinhvien.php");
    exit;
  }

  // Lấy danh sách lớp để dropdown chọn
  $lopResult = $conn->query("SELECT MaLop, TenLop FROM LopHoc");
} else {
  header("Location: quanlySinhvien.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sửa Sinh viên</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: #f0f2f5;
      font-family: Arial, sans-serif;
      color: #2c3e50;
      padding: 20px;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background: white;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    .header h2 {
      text-align: center;
      color: #2c3e50;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .form-section {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    input[type="text"],
    input[type="date"],
    input[type="email"],
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      outline: none;
    }

    input[type="text"]:disabled {
      background-color: #e9ecef;
      opacity: 1;
      cursor: not-allowed;
    }

    .help-text {
      color: #6c757d;
      font-size: 12px;
      font-style: italic;
      margin-bottom: 10px;
    }

    .button-group {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 20px;
    }

    button {
      padding: 8px 10px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-primary {
      background: #007bff;
      color: white;
    }

    .btn-primary:hover {
      background: #0056b3;
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
    }

    .btn-secondary:hover {
      background: #545b62;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h2>Sửa thông tin Sinh viên</h2>
    </div>

    <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

    <div class="form-section">
      <form method="post" action="">
        <input type="hidden" name="action" value="suasinhvien">
        <input type="hidden" name="masv" value="<?= htmlspecialchars($row['MaSV']) ?>">

        <div class="form-group">
          Mã SV:
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['MaSV']) ?>" disabled>
          <div class="help-text">Mã sinh viên không thể thay đổi</div>
        </div>

        <div class="form-group">
          Tên SV:
          <input type="text" class="form-control" name="tensv" value="<?= htmlspecialchars($row['TenSV']) ?>" required>

          Giới tính:
          <select name="gioitinh" class="form-control" required>
            <option value="">Chọn giới tính</option>
            <option value="Nam" <?= $row['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= $row['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
          </select>

          Ngày sinh:
          <input type="date" class="form-control" name="ngaysinh" value="<?= htmlspecialchars($row['NgaySinh']) ?>" required>

          Nơi sinh:
          <input type="text" class="form-control" name="noisinh" value="<?= htmlspecialchars($row['NoiSinh']) ?>" required>

          Email:
          <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($row['Email']) ?>" required>

          SĐT:
          <input type="text" class="form-control" name="sdt" value="<?= htmlspecialchars($row['SDT']) ?>" required>

          Lớp:
          <select name="malop" class="form-control" required>
            <option value="">Chọn lớp</option>
            <?php if ($lopResult->num_rows > 0): ?>
              <?php while ($lop = $lopResult->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($lop['MaLop']) ?>" <?= $lop['MaLop'] == $row['MaLop'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($lop['TenLop']) ?>
                </option>
              <?php endwhile; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="button-group">
          <button type="submit" class="btn-primary">Lưu thay đổi</button>
          <a href="quanlySinhvien.php"><button type="button" class="btn-secondary">Hủy</button></a>
        </div>
      </form>
    </div>
  </div>
</body>

</html>