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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fafafa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .back-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .back-link:hover {
            color: #333;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .content {
            padding: 30px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
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
        }

        .info-label {
            font-weight: 500;
            color: #555;
            min-width: 120px;
            margin-right: 15px;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        th {
            background: #f8f9fa;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .empty-message {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .error-message {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 0 10px;
            }

            .header,
            .content {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 14px;
            }

            th,
            td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a class="back-link" href="DashboardSV.php"><i class="fas fa-home"></i> Trang chủ</a>
            <h1 class="page-title">Thông tin sinh viên</h1>
        </div>

        <div class="content">
            <?php if ($message): ?>
                <div class="error-message"><?= htmlspecialchars($message) ?></div>
            <?php elseif ($sv): ?>

                <!-- Thông tin cá nhân -->
                <div class="section">
                    <h2 class="section-title">Thông tin cá nhân</h2>
                    <div class="info-grid">
                        <div>
                            <div class="info-item">
                                <span class="info-label">Mã sinh viên:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['MaSV']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Họ và tên:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['TenSV']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Giới tính:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['GioiTinh']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày sinh:</span>
                                <span class="info-value"><?= date("d/m/Y", strtotime($sv['NgaySinh'])) ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="info-item">
                                <span class="info-label">Nơi sinh:</span>
                                <span class="info-value"><?= htmlspecialchars($sv['NoiSinh']) ?></span>
                            </div>
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
                    <h2 class="section-title">Bảng điểm học tập</h2>
                    <?php if ($result_diem && $result_diem->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Môn học</th>
                                    <th>Điểm trên lớp</th>
                                    <th>Điểm giữa kỳ</th>
                                    <th>Điểm cuối kỳ</th>
                                    <th>Điểm tổng kết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_diem->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['TenMon']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemTrenLop']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemGiuaKy']) ?></td>
                                        <td><?= htmlspecialchars($row['DiemCuoiKy']) ?></td>
                                        <td><strong><?= htmlspecialchars($row['DiemTongKet']) ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            Chưa có dữ liệu bảng điểm
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thời khóa biểu -->
                <div class="section">
                    <h2 class="section-title">Thời khóa biểu lớp</h2>
                    <?php if ($result_tkb && $result_tkb->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th>Ngày học</th>
                                    <th>Tiết học</th>
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
                        <div class="no-data">
                            Chưa có dữ liệu thời khóa biểu
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</body>

</html>