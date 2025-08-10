<?php
require 'connect.php';

$masv = $_GET['masv'] ?? '';
$message = '';
$sv = null;
$result_diem = null;
$result_tkb = null;

if (!$masv) {
    $message = "Vui lòng nhập mã sinh viên!";
} else {
    $sql_sv = "SELECT sv.*, lh.TenLop, k.TenKhoa
        FROM SinhVien sv
        LEFT JOIN LopHoc lh ON sv.MaLop = lh.MaLop
        LEFT JOIN Khoa k ON lh.MaKhoa = k.MaKhoa
        WHERE sv.MaSV = '$masv'";

    $result_sv = $conn->query($sql_sv);

    if ($result_sv->num_rows == 0) {
        $message = "Không tìm thấy sinh viên!";
    } else {
        $sv = $result_sv->fetch_assoc();

        $sql_diem = "SELECT kq.*, mh.TenMon 
            FROM KetQuaHocTap kq
            JOIN MonHoc mh ON kq.MaMon = mh.MaMon
            WHERE kq.MaSV = '$masv'";

        $result_diem = $conn->query($sql_diem);

        $sql_tkb = "SELECT tkb.*, mh.TenMon, gv.TenGV, gv.HocVi, ph.TenPhong
            FROM ThoiKhoaBieu tkb
            JOIN MonHoc mh ON tkb.MaMon = mh.MaMon
            JOIN GiangVien gv ON tkb.MaGV = gv.MaGV
            JOIN PhongHoc ph ON tkb.MaPhong = ph.MaPhong
            WHERE tkb.MaLop = '{$sv['MaLop']}'
            ORDER BY tkb.NgayHoc, tkb.Thu, tkb.TietBatDau";

        $result_tkb = $conn->query($sql_tkb);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu thông tin sinh viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #3498db;
            padding: 20px 30px;
            color: white;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link i {
            margin-right: 5px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content {
            padding: 30px;
        }

        .section {
            margin-bottom: 40px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
            min-width: 140px;
            margin-right: 15px;
        }

        .info-value {
            color: #2c3e50;
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background: #3498db;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: top;
            color: #2c3e50;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #e8f4fd;
        }

        .empty-message {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 20px;
        }

        .error-message {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .no-data {
            text-align: center;
            color: #7f8c8d;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .no-data i {
            font-size: 48px;
            color: #bdc3c7;
        }

        .info-highlight {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
        }

        .grade-highlight {
            font-weight: bold;
            color: #2980b9;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                margin: 0 10px;
            }

            .header,
            .content {
                padding: 20px;
            }

            .section {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .info-label {
                min-width: auto;
                margin-right: 0;
                color: #3498db;
                font-size: 14px;
            }

            table {
                font-size: 12px;
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }

            th,
            td {
                padding: 8px 6px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 20px;
            }

            .section-title {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">
                <i class="fas fa-search"></i> Thông tin sinh viên
            </h1>
        </div>

        <div class="content">
            <a href="DashboardSV.php" class="back-link"><i class="fas fa-home"></i> Trang chủ</a>
            <?php if ($message): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php elseif ($sv): ?>

                <!-- Thông tin cá nhân -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-user"></i> Thông tin cá nhân
                    </h2>

                    <div class="info-highlight">
                        <div class="info-item">
                            <span class="info-label">Mã sinh viên:</span>
                            <span class="info-value"><strong><?= htmlspecialchars($sv['MaSV']) ?></strong></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Họ và tên:</span>
                            <span class="info-value"><strong><?= htmlspecialchars($sv['TenSV']) ?></strong></span>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div>
                            <div class="info-item">
                                <span class="info-label">Giới tính:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['GioiTinh']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày sinh:</span>
                                <span class="info-value"><?= date("d/m/Y", strtotime($sv['NgaySinh'])) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nơi sinh:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['NoiSinh']) ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['Email']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Số điện thoại:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['SDT']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Lớp:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['TenLop']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Khoa:</span>
                        <span class="info-value"><?= htmlspecialchars($sv['TenKhoa']) ?></span>
                    </div>
                </div>

                <!-- Bảng điểm -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-line"></i> Bảng điểm học tập
                    </h2>
                    <?php if ($result_diem && $result_diem->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-book"></i> Môn học</th>
                                    <th>Điểm trên lớp</th>
                                    <th>Điểm giữa kỳ</th>
                                    <th>Điểm cuối kỳ</th>
                                    <th><i class="fas fa-trophy"></i> Điểm tổng kết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_diem->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['TenMon']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemTrenLop']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemGiuaKy']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemCuoiKy']) ?></td>
                                        <td><span class="grade-highlight"><?= htmlspecialchars($row['DiemTongKet']) ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Chưa có dữ liệu bảng điểm</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thời khóa biểu -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i> Thời khóa biểu lớp
                    </h2>
                    <?php if ($result_tkb && $result_tkb->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th><i class="fas fa-calendar"></i> Ngày học</th>
                                    <th><i class="fas fa-clock"></i> Tiết học</th>
                                    <th><i class="fas fa-book"></i> Môn học</th>
                                    <th><i class="fas fa-chalkboard-teacher"></i> Giảng viên</th>
                                    <th><i class="fas fa-door-open"></i> Phòng học</th>
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
                        <div class="no-data">
                            <i class="fas fa-calendar-times"></i>
                            <span>Chưa có dữ liệu thời khóa biểu</span>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</body>

</html>