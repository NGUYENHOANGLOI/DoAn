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

// Lấy thông báo lỗi từ URL parameter (nếu có)
$error_message = '';
if (isset($_GET['error'])) {
  $error_message = urldecode($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #b3dcea;
      /* màu nền xanh nhạt */
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: #f9f9f9;
      padding: 40px;
      border-radius: 10px;
      width: 100%;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      max-width: 400px;
    }

    .login-container h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
      font-size: 24px;
    }

    .login-container button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: #ffffff;
      border-radius: 5px;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }

    .login-container button:hover {
      background: #2980b9;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
    }

    .login-container input[type="text"]::placeholder,
    .login-container input[type="password"]::placeholder {
      font-style: italic;
    }

    /* Style cho thông báo lỗi */
    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 20px;
      border: 1px solid #f5c6cb;
      text-align: center;
      font-size: 14px;
    }

    .fade-out {
      opacity: 0;
      transition: opacity 0.1s ease;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h2>Đăng nhập</h2>

    <?php if (!empty($error_message)): ?>
      <div class="error-message" id="error-message">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <select name="role" required>
        <option value="">Chọn vai trò</option>
        // Giữ lại thông tin đã nhập
        <option value="admin" <?php echo (isset($_GET['role']) && $_GET['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        <option value="sinhvien" <?php echo (isset($_GET['role']) && $_GET['role'] === 'sinhvien') ? 'selected' : ''; ?>>Sinh viên</option>
      </select><br><br>
      <input type="text" name="username" placeholder="Tên đăng nhập"
        value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>" required />
      <input type="password" name="password" placeholder="Nhập mật khẩu" required />
      <button type="submit" class="login-btn">Đăng nhập</button>
    </form>
  </div>
  </div>
  <script>
    // Khi quay lại từ lịch sử (bấm Back), trình duyệt có thể dùng cache
    // => ép tải lại để PHP kiểm tra session và redirect luôn
    window.addEventListener('pageshow', function(event) {
      if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        // ép reload nếu trang được phục hồi từ cache
        window.location.reload();
      }
    });

    // Tự động ẩn thông báo lỗi sau 5 giây
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
      setTimeout(function() {
        errorMessage.classList.add('fade-out');
        setTimeout(function() {
          errorMessage.style.display = 'none';
        }, 500);
      }, 1000);
    }
  </script>

</body>

</html>