
CREATE DATABASE IF NOT EXISTS quan_ly_su_kien
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE quan_ly_su_kien;

-- 1. Tai khoan nguoi dung
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('thanh_vien', 'ban_to_chuc', 'admin')
        NOT NULL DEFAULT 'thanh_vien',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Cau lac bo
CREATE TABLE cau_lac_bo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_cau_lac_bo VARCHAR(150) NOT NULL,
    mo_ta TEXT,
    nguoi_quan_ly_id INT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_quan_ly_id)
        REFERENCES nguoi_dung(id)
        ON DELETE SET NULL
);

-- 3. Su kien
CREATE TABLE su_kien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cau_lac_bo_id INT NOT NULL,
    ten_su_kien VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    dia_diem VARCHAR(255),
    thoi_gian_bat_dau DATETIME NOT NULL,
    thoi_gian_ket_thuc DATETIME,
    so_luong_toi_da INT NOT NULL DEFAULT 100,
    trang_thai ENUM('nhap', 'cong_khai', 'da_huy', 'ket_thuc')
        NOT NULL DEFAULT 'nhap',
    han_dang_ky DATETIME DEFAULT NULL,
    nguoi_tao_id INT NOT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cau_lac_bo_id)
        REFERENCES cau_lac_bo(id),
    FOREIGN KEY (nguoi_tao_id)
        REFERENCES nguoi_dung(id)
);

-- 4. Dang ky tham gia su kien
CREATE TABLE dang_ky (
    id INT AUTO_INCREMENT PRIMARY KEY,
    su_kien_id INT NOT NULL,
    nguoi_dung_id INT NOT NULL,
    thoi_gian_dang_ky TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    trang_thai ENUM('da_dang_ky', 'da_huy', 'da_tham_gia')
        NOT NULL DEFAULT 'da_dang_ky',
    UNIQUE (su_kien_id, nguoi_dung_id),
    FOREIGN KEY (su_kien_id)
        REFERENCES su_kien(id),
    FOREIGN KEY (nguoi_dung_id)
        REFERENCES nguoi_dung(id)
);

-- 5. Thong bao
CREATE TABLE thong_bao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    su_kien_id INT DEFAULT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    noi_dung TEXT NOT NULL,
    loai VARCHAR(30) NOT NULL DEFAULT 'info',
    da_doc TINYINT(1) NOT NULL DEFAULT 0,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_thong_bao_nguoi_dung (nguoi_dung_id, da_doc, ngay_tao),
    INDEX idx_thong_bao_su_kien (su_kien_id),

    FOREIGN KEY (nguoi_dung_id)
        REFERENCES nguoi_dung(id)
        ON DELETE CASCADE,

    FOREIGN KEY (su_kien_id)
        REFERENCES su_kien(id)
        ON DELETE SET NULL
);
