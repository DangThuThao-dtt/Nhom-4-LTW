
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

class QuanLyTaiKhoanController
{
    // Hien thi danh sach tai khoan
    public function danhSach(): void
    {
        $pdo = $GLOBALS['pdo'];

        $stmt = $pdo->query("
            SELECT id, ho_ten, email, vai_tro, ngay_tao
            FROM nguoi_dung
            ORDER BY id DESC
        ");

        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/tai_khoan.php';
    }
}
