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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin học tập Sinh Viên</title>
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
            align-items: center;
            text-align: center;
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
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            margin: 20px 0;
        }

        table {
            margin: 20px auto;
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;

        }

        table caption {
            font-size: 18px;
            padding: 15px;
            background: #007bff;
            color: white;
            margin: 0;
        }

        th {
            background: #f8f9fa;
            color: #333;
            padding: 10px;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            text-align: center;
        }

        .student-info,
        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            box-sizing: border-box;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .student-info p,
        .info div {
            margin: 8px 0;
            font-size: 17px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Hệ Thống Thông Tin Sinh Viên</h1>
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
        <div class="info-row">
            <div class="student-info">
                <h2>Thông tin Sinh viên</h2>
                <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($sv['MaSV']) ?></p>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($sv['TenSV']) ?></p>
                <p><strong>Lớp:</strong> <?= htmlspecialchars($sv['TenLop']) ?></p>
                <p><strong>Giới tính:</strong> <?= htmlspecialchars($sv['GioiTinh']) ?></p>
                <p><strong>Ngày sinh:</strong> <?= date("d-m-Y", strtotime($sv['NgaySinh'])) ?></p>
                <p><strong>Nơi sinh:</strong> <?= htmlspecialchars($sv['NoiSinh']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($sv['Email']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($sv['SDT']) ?></p>
                <p><strong>Khoa:</strong> <?= htmlspecialchars($sv['TenKhoa']) ?></p>
            </div>

            <?php if ($result_kq && $result_kq->num_rows > 0): ?>
                <div class="info">
                    <h2>Kết quả học tập tổng quan</h2>
                    <div><strong>Điểm trung bình tích lũy:</strong> <?= number_format($diem_trung_binh, 2) ?></div>
                    <div><strong>Xếp loại:</strong> <?= $xep_loai ?></div>
                </div>
        </div>
    <?php endif; ?>

    <table border="1" cellpadding="6" cellspacing="0">
        <caption><strong>Kết quả học tập chi tiết</strong></caption>
        <thead>
            <tr>
                <th>Môn học</th>
                <th>Số tín chỉ</th>
                <th>Điểm trên lớp</th>
                <th>Điểm giữa kỳ</th>
                <th>Điểm cuối kỳ</th>
                <th>Điểm tổng kết</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_kq && $result_kq->num_rows > 0): ?>
                <?php while ($row = $result_kq->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['TenMon']) ?></td>
                        <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                        <td><?= htmlspecialchars($row['DiemTrenLop']) ?></td>
                        <td><?= htmlspecialchars($row['DiemGiuaKy']) ?></td>
                        <td><?= htmlspecialchars($row['DiemCuoiKy']) ?></td>
                        <td><?= htmlspecialchars($row['DiemTongKet']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Chưa có kết quả học tập.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table border="1" cellpadding="7" cellspacing="0">
        <caption><strong>Thời khóa biểu</strong></caption>
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
            <?php if ($result_tkb && $result_tkb->num_rows > 0): ?>
                <?php while ($tkb = $result_tkb->fetch_assoc()): ?>
                    <tr>
                        <td><?= $tkb['Thu'] == 8 ? 'Chủ nhật' : 'Thứ ' . $tkb['Thu'] ?></td>
                        <td><?= date("d-m-Y", strtotime($tkb['NgayHoc'])) ?></td>
                        <td><?= $tkb['TietBatDau'] . ' - ' . $tkb['TietKetThuc'] ?></td>
                        <td><?= htmlspecialchars($tkb['TenMon']) ?></td>
                        <td><?= htmlspecialchars($tkb['HocVi'] . ' ' . $tkb['TenGV']) ?></td>
                        <td><?= htmlspecialchars($tkb['TenPhong']) ?></td>
                        <td><?= htmlspecialchars($tkb['GhiChu'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">Không có thời khóa biểu nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>

</html>