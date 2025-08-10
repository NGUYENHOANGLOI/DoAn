<?php
session_start();
// Nếu đã đăng nhập thì chuyển hướng
if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: DashboardSV.php");
    exit();
  } elseif ($_SESSION['role'] === 'sinhvien') {
    header("Location: View_DashboardSV.php");
    exit();
  }
}
require 'connect.php';

$role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : '';
$username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
$password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

if (empty($role) || empty($username) || empty($password)) {
  $error_msg = "Vui lòng điền đầy đủ thông tin.";
  header("Location: index.php?error=" . urlencode($error_msg) . "&role=" . urlencode($role) . "&username=" . urlencode($username));
  exit();
}

if ($role == 'admin') {
  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'admin';
    header("Location: DashboardSV.php");
    exit();
  } else {
    $error_msg = "Tài khoản hoặc mật khẩu không đúng.";
    header("Location: index.php?error=" . urlencode($error_msg) . "&role=" . urlencode($role) . "&username=" . urlencode($username));
    exit();
  }
} elseif ($role == 'sinhvien') {
  $sql = "SELECT * FROM SinhVien WHERE MaSV='$username'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $storedPassword = $row['password'];

    // Nếu mật khẩu rỗng hoặc bằng chính MaSV => chưa đổi mật khẩu
    if (empty($storedPassword) || $storedPassword === $row['MaSV']) {
      if ($password === $row['MaSV']) {
        $_SESSION['MaSV'] = $username;
        $_SESSION['role'] = 'sinhvien';
        header("Location: View_DashboardSV.php");
        exit();
      } else {
        $error_msg = "Tài khoản hoặc mật khẩu không đúng.";
        header("Location: index.php?error=" . urlencode($error_msg) . "&role=" . urlencode($role) . "&username=" . urlencode($username));
        exit();
      }
    } else {
      // Đã đổi mật khẩu
      if ($password === $storedPassword) {
        $_SESSION['MaSV'] = $username;
        $_SESSION['role'] = 'sinhvien';
        header("Location: View_DashboardSV.php");
        exit();
      } else {
        $error_msg = "Tài khoản hoặc mật khẩu không đúng.";
        header("Location: index.php?error=" . urlencode($error_msg) . "&role=" . urlencode($role) . "&username=" . urlencode($username));
        exit();
      }
    }
  } else {
    $error_msg = "Mã sinh viên không tồn tại.";
    header("Location: index.php?error=" . urlencode($error_msg) . "&role=" . urlencode($role) . "&username=" . urlencode($username));
    exit();
  }
}

$conn->close();
