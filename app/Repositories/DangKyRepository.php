<?php
// app/Repositories/DangKyRepository.php

class DangKyRepository {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getDanhSachDangKy($nguoiDungId, $tuKhoa = '') {
        $sql = "
            SELECT dk.id AS dang_ky_id, dk.thoi_gian_dang_ky, dk.trang_thai AS trang_thai_dang_ky,
                   sk.id AS su_kien_id, sk.ten_su_kien, sk.dia_diem, 
                   sk.thoi_gian_bat_dau, sk.thoi_gian_ket_thuc, 
                   clb.ten_cau_lac_bo
            FROM dang_ky dk
            JOIN su_kien sk ON dk.su_kien_id = sk.id
            JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
            WHERE dk.nguoi_dung_id = :nguoi_dung_id
        ";

        $params = ['nguoi_dung_id' => $nguoiDungId];

        if ($tuKhoa !== '') {
            $sql .= " AND (sk.ten_su_kien LIKE :tu_khoa OR clb.ten_cau_lac_bo LIKE :tu_khoa_2)";
            $params['tu_khoa'] = '%' . $tuKhoa . '%';
            $params['tu_khoa_2'] = '%' . $tuKhoa . '%';
        }

        $sql .= " ORDER BY dk.thoi_gian_dang_ky DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuKienById($suKienId) {
        $stmt = $this->pdo->prepare("SELECT * FROM su_kien WHERE id = ?");
        $stmt->execute([$suKienId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countDangKyBySuKien($suKienId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) AS so_luong 
            FROM dang_ky 
            WHERE su_kien_id = ? AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
        ");
        $stmt->execute([$suKienId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['so_luong'];
    }

    public function checkDaDangKy($nguoiDungId, $suKienId) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM dang_ky 
            WHERE nguoi_dung_id = ? AND su_kien_id = ? 
            AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
        ");
        $stmt->execute([$nguoiDungId, $suKienId]);
        return $stmt->fetch();
    }

    public function getDangKyById($dangKyId, $nguoiDungId) {
        $sql = "SELECT dk.*, sk.thoi_gian_bat_dau 
                FROM dang_ky dk 
                JOIN su_kien sk ON dk.su_kien_id = sk.id 
                WHERE dk.id = ? AND dk.nguoi_dung_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dangKyId, $nguoiDungId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertDangKy($nguoiDungId, $suKienId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO dang_ky (nguoi_dung_id, su_kien_id, thoi_gian_dang_ky, trang_thai) 
            VALUES (?, ?, NOW(), 'da_dang_ky')
        ");
        return $stmt->execute([$nguoiDungId, $suKienId]);
    }

    public function restoreDangKy($dangKyId) {
        $stmt = $this->pdo->prepare("
            UPDATE dang_ky 
            SET trang_thai = 'da_dang_ky', thoi_gian_dang_ky = NOW() 
            WHERE id = ? AND trang_thai = 'da_huy'
        ");
        return $stmt->execute([$dangKyId]);
    }

    public function huyDangKy($dangKyId) {
        $stmt = $this->pdo->prepare("UPDATE dang_ky SET trang_thai = 'da_huy' WHERE id = ?");
        return $stmt->execute([$dangKyId]);
    }
}
?>