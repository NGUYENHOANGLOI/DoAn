<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['MaSV'])) {
    header("Location: logout.php");
    exit();
}

function xepLoai($diem)
{
    if ($diem >= 9.0 && $diem <= 10.0) return "Xuất sắc";
    if ($diem >= 8.0 && $diem < 9.0) return "Giỏi";
    if ($diem >= 7.0 && $diem < 8.0) return "Khá";
    if ($diem >= 5.0 && $diem < 7.0) return "Trung bình";
    if ($diem >= 4.0 && $diem < 5.0) return "Yếu";
    return "Kém";
}

$masv = $_SESSION['MaSV'];

$sql_sv = "SELECT sv.*, lh.TenLop, k.TenKhoa
        FROM SinhVien sv
        LEFT JOIN LopHoc lh ON sv.MaLop = lh.MaLop
        LEFT JOIN Khoa k ON lh.MaKhoa = k.MaKhoa
        WHERE sv.MaSV = '$masv'";
$result_sv = $conn->query($sql_sv);

if ($result_sv->num_rows == 0) {
    echo "Không tìm thấy sinh viên.";
    exit();
}

$sv = $result_sv->fetch_assoc();

$sql_kq = "SELECT mh.TenMon, mh.SoTinChi, kh.DiemTrenLop, kh.DiemGiuaKy, kh.DiemCuoiKy, kh.DiemTongKet
    FROM KetQuaHocTap kh
    JOIN MonHoc mh ON kh.MaMon = mh.MaMon
    WHERE kh.MaSV = '$masv'
    ORDER BY mh.TenMon";
$result_kq = $conn->query($sql_kq);

// Tính điểm trung bình tích lũy của sinh viên
$sql_avg = "SELECT ROUND(SUM(kq.DiemTongKet * mh.SoTinChi) / SUM(mh.SoTinChi), 2) AS DiemTB
    FROM KetQuaHocTap kq
    JOIN MonHoc mh ON kq.MaMon = mh.MaMon
    WHERE kq.MaSV = '$masv'";
$tb_result = $conn->query($sql_avg);

$diem_trung_binh = 0;
$xep_loai = '';
if ($tb_result && $tb_result->num_rows > 0) {
    $row = $tb_result->fetch_assoc();
    $diem_trung_binh = $row['DiemTB'] ?? 0;
    $xep_loai = xepLoai($diem_trung_binh);
}

$sql_tkb = "SELECT tkb.*, mh.TenMon, gv.TenGV, gv.HocVi, ph.TenPhong
            FROM ThoiKhoaBieu tkb
            JOIN MonHoc mh ON tkb.MaMon = mh.MaMon
            JOIN GiangVien gv ON tkb.MaGV = gv.MaGV
            JOIN PhongHoc ph ON tkb.MaPhong = ph.MaPhong
            WHERE tkb.MaLop = '{$sv['MaLop']}'
            ORDER BY tkb.NgayHoc, tkb.Thu, tkb.TietBatDau";

$result_tkb = $conn->query($sql_tkb);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin học tập - <?= htmlspecialchars($sv['TenSV']) ?></title>
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

        /* Phần chào mừng nổi bật */
        .welcome-section {
            background: #e8f4fd;
            border: 2px solid #dee2e6;
            margin-bottom: 25px;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .welcome-section h2 {
            color: #2980b9;
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .welcome-section p {
            color: #34495e;
            font-size: 16px;
        }

        /* Nhóm thông tin */
        .info-group {
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

        .info-grid {
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

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
        }

        .info-value {
            font-weight: bold;
            color: #2c3e50;
        }

        .grade-summary {
            text-align: center;
            padding: 20px;
        }

        .grade-number {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }

        .grade-text {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }

        .grade-level {
            display: inline-block;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            border-radius: 20px;
            font-weight: bold;
        }

        /* Bảng dữ liệu */
        .table-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .table-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .section-header {
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #f8f9fa;
            color: #2c3e50;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f8f9fa;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .grade-highlight {
            font-weight: bold;
            color: #3498db;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .empty-message i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
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
        <!-- Phần chào mừng nổi bật -->
        <div class="welcome-section">
            <h2>Chào mừng, <?= htmlspecialchars($sv['TenSV']) ?>!</h2>
            <p>Thông tin học tập của bạn</p>
        </div>

        <!-- Nhóm Thông tin cá nhân -->
        <div class="info-group">
            <div class="group-title">Thông tin cá nhân</div>
            <div class="info-grid">
                <div class="section">
                    <h2><i class="fas fa-user"></i> Chi tiết sinh viên</h2>
                    <div class="info-item">
                        <span class="info-label">Mã sinh viên:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['MaSV']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Lớp:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['TenLop']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Khoa:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['TenKhoa']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Giới tính:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['GioiTinh']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ngày sinh:</span>
                        <span class="info-value"><?= date("d/m/Y", strtotime($sv['NgaySinh'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['Email']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Số điện thoại:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['SDT']) ?></span>
                    </div>
                </div>

                <?php if ($result_kq && $result_kq->num_rows > 0): ?>
                    <div class="section">
                        <h2><i class="fas fa-chart-line"></i> Kết quả học tập</h2>
                        <div class="grade-summary">
                            <div class="grade-number"><?= number_format($diem_trung_binh, 2) ?></div>
                            <div class="grade-text">Điểm trung bình tích lũy</div>
                            <div class="grade-level"><?= $xep_loai ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nhóm Học tập -->
        <div class="info-group">
            <div class="group-title">Học tập</div>

            <!-- Bảng điểm -->
            <div class="table-section">
                <div class="section-header">
                    <i class="fas fa-clipboard-list"></i>
                    Bảng điểm chi tiết
                </div>
                <?php if ($result_kq && $result_kq->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th>Tín chỉ</th>
                                <th>Điểm trên lớp</th>
                                <th>Điểm giữa kỳ</th>
                                <th>Điểm cuối kỳ</th>
                                <th>Điểm tổng kết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result_kq->data_seek(0);
                            while ($row = $result_kq->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['TenMon']) ?></td>
                                    <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                                    <td><?= htmlspecialchars($row['DiemTrenLop'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['DiemGiuaKy'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['DiemCuoiKy'] ?? 'N/A') ?></td>
                                    <td><span class="grade-highlight"><?= htmlspecialchars($row['DiemTongKet']) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Chưa có kết quả học tập</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Thời khóa biểu -->
            <div class="table-section">
                <div class="section-header">
                    <i class="fas fa-calendar-alt"></i>
                    Thời khóa biểu
                </div>
                <?php if ($result_tkb && $result_tkb->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Thứ</th>
                                <th>Ngày học</th>
                                <th>Tiết</th>
                                <th>Môn học</th>
                                <th>Giảng viên</th>
                                <th>Phòng học</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tkb = $result_tkb->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $tkb['Thu'] == 8 ? 'Chủ nhật' : 'Thứ ' . $tkb['Thu'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($tkb['NgayHoc'])) ?></td>
                                    <td><?= $tkb['TietBatDau'] . ' - ' . $tkb['TietKetThuc'] ?></td>
                                    <td><?= htmlspecialchars($tkb['TenMon']) ?></td>
                                    <td><?= htmlspecialchars($tkb['HocVi'] . ' ' . $tkb['TenGV']) ?></td>
                                    <td><?= htmlspecialchars($tkb['TenPhong']) ?></td>
                                    <td><?= htmlspecialchars($tkb['GhiChu'] ?? '') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-calendar-times"></i>
                        <p>Chưa có thời khóa biểu</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>