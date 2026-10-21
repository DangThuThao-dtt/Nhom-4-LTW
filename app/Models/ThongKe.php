<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

class ThongKe
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function tongSoSuKien(): int
    {
        return (int) $this->pdo
            ->query("SELECT COUNT(*) FROM su_kien")
            ->fetchColumn();
    }

    public function demTheoTrangThai(): array
    {
        $sql = "
            SELECT trang_thai, COUNT(*) AS so_luong
            FROM su_kien
            GROUP BY trang_thai
            ORDER BY trang_thai
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function tongLuotDangKy(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM dang_ky
            WHERE trang_thai = 'da_dang_ky'
        ";

        return (int) $this->pdo
            ->query($sql)
            ->fetchColumn();
    }

    public function dangKyTheoSuKien(): array
    {
        $sql = "
            SELECT
                sk.id,
                sk.ten_su_kien,
                sk.so_luong_toi_da,
                sk.trang_thai,
                COUNT(dk.id) AS so_luot_dang_ky
            FROM su_kien sk
            LEFT JOIN dang_ky dk
                ON dk.su_kien_id = sk.id
                AND dk.trang_thai = 'da_dang_ky'
            GROUP BY
                sk.id,
                sk.ten_su_kien,
                sk.so_luong_toi_da,
                sk.trang_thai
            ORDER BY sk.id DESC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }
}