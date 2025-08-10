<?php
session_start();
require 'connect.php';

if (isset($_SESSION['MaSV'])) {
  header("Location: logout.php");
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Sinh Viên</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      padding-top: 80px;
    }

    .header {
      position: fixed;
      top: 0;
      left: 5px;
      right: 5px;
      background: white;
      padding: 20px 10px;
      text-align: center;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1;
    }

    .header h1 {
      color: #2c3e50;
      font-size: 24px;
      font-weight: bold;
      text-align: left;
    }

    .user-controls {
      display: flex;
      gap: 5px;
    }

    .user-controls .btn {
      padding: 8px 10px;
      border: none;
      border-radius: 5px;
      font-size: 13px;
      font-weight: 400;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .password {
      background: #3498db;
      color: #ffffff;
    }

    .password:hover {
      background: #2980b9;
    }

    .logout {
      background: #e74c3c;
      color: #ffffff;
    }

    .logout:hover {
      background: #c0392b;
    }

    .container {
      max-width: 1500px;
      margin: 20px auto;
      padding: 0 20px;
    }

    /* Tra cứu nổi bật */
    .search-section {
      background: #e8f4fd;
      border: 2px solid #dee2e6;
      margin-bottom: 25px;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .search-section h2 {
      color: #2980b9;
      font-size: 22px;
      margin-bottom: 15px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .search-section form {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .search-section input[type="text"] {
      flex: 1;
      padding: 12px 16px;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      font-size: 14px;
      color: #2c3e50;
      outline: none;
    }

    .search-section button {
      background: #3498db;
      color: #ffffff;
      border: none;
      padding: 12px 20px;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }

    .search-section button:hover {
      background: #2980b9;
    }

    /* Nhóm chức năng */
    .function-group {
      margin-bottom: 30px;
    }

    .group-title {
      color: #34495e;
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 15px;
      padding-left: 10px;
      border-left: 4px solid #3498db;
      background: white;
      padding: 10px 15px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .section {
      background: white;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 25px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }

    .section:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .section h2 {
      color: #2c3e50;
      font-size: 18px;
      margin-bottom: 15px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .section button {
      background: #007bff;
      color: #ffffff;
      border: none;
      padding: 12px 18px;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .section button:hover {
      background: #0056b3;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>Hệ Thống Quản Lý Sinh Viên</h1>
    <div class="user-controls">
      <a href="doipassword.php" class="btn password" title="Đổi mật khẩu">
        <i class="fas fa-key"></i> Đổi mật khẩu
      </a>
      <a href="logout.php" class="btn logout" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?')" title="Đăng xuất">
        <i class="fas fa-sign-out-alt"></i> Đăng xuất
      </a>
    </div>
  </div>
  <div class="container">

    <!-- Tra cứu nổi bật -->
    <div class="search-section">
      <h2><i class="fas fa-search"></i> Tra cứu sinh viên</h2>
      <form action="tracuu.php" method="GET">
        <input type="text" name="masv" placeholder="Nhập mã sinh viên để tra cứu..." required />
        <button type="submit"><i class="fas fa-search"></i> Tra cứu</button>
      </form>
    </div>

    <!-- Nhóm Quản lý Học vụ -->
    <div class="function-group">
      <div class="group-title">Quản lý Học vụ</div>
      <div class="dashboard">
        <div class="section">
          <h2><i class="fas fa-calendar-alt"></i> Quản lý TKB</h2>
          <form action="quanlyThoikhoabieu.php" method="post">
            <button type="submit">Quản lý TKB</button>
          </form>
        </div>

        <div class="section">
          <h2><i class="fas fa-chart-line"></i> Quản lý điểm</h2>
          <form action="quanlyKetquahoctap.php" method="post">
            <button type="submit">Kết quả học tập</button>
          </form>
        </div>

        <div class="section">
          <h2><i class="fas fa-book"></i> Quản lý môn học</h2>
          <form action="quanlyMonhoc.php" method="post">
            <button type="submit">Quản lý môn học</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Nhóm Quản lý Tổ chức -->
    <div class="function-group">
      <div class="group-title">Quản lý Tổ chức</div>
      <div class="dashboard">
        <div class="section">
          <h2><i class="fas fa-university"></i> Quản lý khoa</h2>
          <form action="quanlyKhoa.php" method="post">
            <button type="submit">Quản lý Khoa</button>
          </form>
        </div>

        <div class="section">
          <h2><i class="fas fa-users"></i> Quản lý lớp học</h2>
          <form action="quanlyLophoc.php" method="post">
            <button type="submit">Quản lý lớp học</button>
          </form>
        </div>

        <div class="section">
          <h2><i class="fas fa-door-open"></i> Quản lý phòng học</h2>
          <form action="quanlyPhonghoc.php" method="post">
            <button type="submit">Quản lý phòng học</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Nhóm Quản lý Nhân sự -->
    <div class="function-group">
      <div class="group-title">Quản lý Nhân sự</div>
      <div class="dashboard">
        <div class="section">
          <h2><i class="fas fa-user-graduate"></i> Quản lý sinh viên</h2>
          <form action="quanlySinhvien.php" method="post">
            <button type="submit">Quản lý sinh viên</button>
          </form>
        </div>

        <div class="section">
          <h2><i class="fas fa-chalkboard-teacher"></i> Quản lý giảng viên</h2>
          <form action="quanlyGiangvien.php" method="post">
            <button type="submit">Quản lý giảng viên</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>

</html>