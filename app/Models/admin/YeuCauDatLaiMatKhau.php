<?php

require_once __DIR__ . '/../../Core/ket_noi_csdl.php';

class YeuCauDatLaiMatKhau
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Đếm số yêu cầu cấp lại mật khẩu đang chờ xử lý.
    public function demYeuCauChoXuLy(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM yeu_cau_dat_lai_mat_khau
            WHERE trang_thai = 'cho_xu_ly'
        ";

        return (int) $this->pdo
            ->query($sql)
            ->fetchColumn();
    }

    // Lấy danh sách yêu cầu đang chờ xử lý.
    public function danhSachYeuCauChoXuLy(): array
    {
        $sql = "
            SELECT
                yc.id,
                yc.nguoi_dung_id,
                yc.noi_dung,
                yc.trang_thai,
                yc.ngay_gui,
                nd.ho_ten,
                nd.email
            FROM yeu_cau_dat_lai_mat_khau yc
            INNER JOIN nguoi_dung nd
                ON nd.id = yc.nguoi_dung_id
            WHERE yc.trang_thai = 'cho_xu_ly'
            ORDER BY yc.ngay_gui DESC
        ";

        return $this->pdo
            ->query($sql)
            ->fetchAll();
    }
    public function danhDauDaXuLyTheoNguoiDung(
    int $nguoiDungId,
    int $adminId
): bool {
    $sql = "
        UPDATE yeu_cau_dat_lai_mat_khau
        SET
            trang_thai = 'da_xu_ly',
            admin_id = :admin_id,
            ghi_chu_admin = 'Đã cấp lại tài khoản thành công',
            ngay_xu_ly = NOW()
        WHERE nguoi_dung_id = :nguoi_dung_id
          AND trang_thai = 'cho_xu_ly'
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        'admin_id' => $adminId,
        'nguoi_dung_id' => $nguoiDungId,
    ]);
}
}