CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL
);

-- Thêm tài khoản mẫu:
INSERT INTO users (username, password) VALUES ('admin', '123456');


-- Bảng Khoa
CREATE TABLE Khoa (
    MaKhoa CHAR(5) PRIMARY KEY NOT NULL,
    TenKhoa VARCHAR(100) NOT NULL
);

-- Bảng lớp học
CREATE TABLE LopHoc (
    MaLop CHAR(5) PRIMARY KEY NOT NULL,
    TenLop VARCHAR(50) NOT NULL,
    SiSo INT NOT NULL DEFAULT 0,
    MaKhoa CHAR(5),
    FOREIGN KEY (MaKhoa) REFERENCES Khoa(MaKhoa)
);

-- Bảng sinh viên
CREATE TABLE SinhVien (
    MaSV CHAR(6) PRIMARY KEY NOT NULL,
    TenSV VARCHAR(100) NOT NULL,
    GioiTinh ENUM('Nam','Nữ') NOT NULL,
    NgaySinh DATE NOT NULL,
    NoiSinh VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) NOT NULL,
    MaLop CHAR(5),
    password VARCHAR(100) NOT NULL,
    FOREIGN KEY (MaLop) REFERENCES LopHoc(MaLop)
);

-- Bảng môn học
CREATE TABLE MonHoc (
    MaMon CHAR(4) PRIMARY KEY NOT NULL,
    TenMon VARCHAR(50) NOT NULL,
    Sotiet INT NOT NULL, 
    SoTinChi INT NOT NULL,
    MaKhoa CHAR(5),
    FOREIGN KEY (MaKhoa) REFERENCES Khoa(MaKhoa)
);

CREATE TABLE KetQuaHocTap (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaSV CHAR(6) NOT NULL,
    MaMon CHAR(4) NOT NULL,
    DiemTrenLop DECIMAL(4,2) NOT NULL,
    DiemGiuaKy DECIMAL(4,2) NOT NULL,
    DiemCuoiKy DECIMAL(4,2) NOT NULL,
    DiemTongKet DECIMAL(4,2) GENERATED ALWAYS AS (
        ROUND(DiemTrenLop * 0.1 + DiemGiuaKy * 0.3 + DiemCuoiKy * 0.6, 2)
    ) STORED,
    FOREIGN KEY (MaSV) REFERENCES SinhVien(MaSV),
    FOREIGN KEY (MaMon) REFERENCES MonHoc(MaMon),
    UNIQUE (MaSV, MaMon)
);

CREATE TABLE GiangVien (
    MaGV CHAR(5) PRIMARY KEY NOT NULL,
    TenGV VARCHAR(100) NOT NULL,
    GioiTinh ENUM('Nam','Nữ') NOT NULL,
    HocVi ENUM('Cử nhân', 'Thạc sĩ', 'Tiến sĩ', 'Phó giáo sư', 'Giáo sư') NOT NULL,
    DienThoai VARCHAR(15),
    Email VARCHAR(100),
    MaKhoa CHAR(5),
    FOREIGN KEY (MaKhoa) REFERENCES Khoa(MaKhoa),
    UNIQUE (Email)
);

CREATE TABLE PhongHoc (
    MaPhong CHAR(5) PRIMARY KEY NOT NULL,
    TenPhong VARCHAR(50) NOT NULL,
    DayNha VARCHAR(20) NOT NULL,
    LoaiPhong ENUM('Lý thuyết', 'Thực hành') NOT NULL
);


CREATE TABLE ThoiKhoaBieu (
    MaTKB INT AUTO_INCREMENT PRIMARY KEY,
    MaLop CHAR(5) NOT NULL,
    MaMon CHAR(4) NOT NULL,
    MaGV CHAR(5) NOT NULL,
    MaPhong CHAR(5) NOT NULL,
    NgayHoc DATE NOT NULL, 	
    Thu TINYINT NOT NULL CHECK (Thu BETWEEN 2 AND 8),
    TietBatDau TINYINT NOT NULL CHECK (TietBatDau BETWEEN 1 AND 12),
    TietKetThuc TINYINT NOT NULL CHECK (TietKetThuc BETWEEN 1 AND 12 AND TietKetThuc >= TietBatDau),
    GhiChu TEXT,
    FOREIGN KEY (MaLop) REFERENCES LopHoc(MaLop),
    FOREIGN KEY (MaMon) REFERENCES MonHoc(MaMon),
    FOREIGN KEY (MaGV) REFERENCES GiangVien(MaGV),
    FOREIGN KEY (MaPhong) REFERENCES PhongHoc(MaPhong)
);
