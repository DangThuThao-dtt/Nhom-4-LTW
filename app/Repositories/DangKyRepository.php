
<?php

class DangKyRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh sách đăng ký của một thành viên
    public function layDanhSachCuaNguoiDung(
        int $nguoiDungId,
        string $tuKhoa = ''
    ): array {
        $sql = "
            SELECT
                dk.id AS dang_ky_id,
                dk.thoi_gian_dang_ky,
                dk.trang_thai AS trang_thai_dang_ky,
                sk.id AS su_kien_id,
                sk.ten_su_kien,
                sk.dia_diem,
                sk.thoi_gian_bat_dau,
                sk.thoi_gian_ket_thuc,
                clb.ten_cau_lac_bo
            FROM dang_ky dk
            JOIN su_kien sk ON dk.su_kien_id = sk.id
            JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
            WHERE dk.nguoi_dung_id = :nguoi_dung_id
              AND (
                  sk.ten_su_kien LIKE :tu_khoa_su_kien
                  OR clb.ten_cau_lac_bo LIKE :tu_khoa_clb
              )
            ORDER BY dk.thoi_gian_dang_ky DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $tuKhoaTimKiem = '%' . trim($tuKhoa) . '%';

        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'tu_khoa_su_kien' => $tuKhoaTimKiem,
            'tu_khoa_clb' => $tuKhoaTimKiem
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy một đăng ký thuộc về người dùng
    public function layMotDangKy(
        int $dangKyId,
        int $nguoiDungId
    ): ?array {
        $sql = "
            SELECT id, nguoi_dung_id, su_kien_id,
                   thoi_gian_dang_ky, trang_thai
            FROM dang_ky
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Cập nhật trạng thái đăng ký
    public function capNhatTrangThai(
        int $dangKyId,
        int $nguoiDungId,
        string $trangThai
    ): bool {
        if (!in_array($trangThai, ['da_dang_ky', 'da_huy'], true)) {
            return false;
        }

        $sql = "
    UPDATE dang_ky
    SET trang_thai = :trang_thai,
        thoi_gian_dang_ky = CASE
            WHEN :trang_thai_check = 'da_dang_ky' THEN NOW()
            ELSE thoi_gian_dang_ky
        END
    WHERE id = :dang_ky_id
      AND nguoi_dung_id = :nguoi_dung_id
";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'trang_thai' => $trangThai,
            'trang_thai_check' => $trangThai,
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        return $stmt->rowCount() > 0;
    }

    // Xóa đăng ký đã hủy
    public function xoaDangKyDaHuy(
        int $dangKyId,
        int $nguoiDungId
    ): bool {
        $sql = "
            DELETE FROM dang_ky
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
              AND trang_thai = 'da_huy'
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        return $stmt->rowCount() > 0;
    }
        // Lấy thông tin sự kiện theo ID
    public function layThongTinSuKien(int $suKienId): ?array
    {
        $sql = "SELECT * FROM su_kien WHERE id = :su_kien_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['su_kien_id' => $suKienId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Đếm số người đã đăng ký hoặc đã tham gia sự kiện
    public function demSoLuongDangKy(int $suKienId): int
    {
        $sql = "
            SELECT COUNT(*) AS so_luong
            FROM dang_ky
            WHERE su_kien_id = :su_kien_id
              AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['su_kien_id' => $suKienId]);

        return (int) $stmt->fetchColumn();
    }

    // Kiểm tra người dùng đã đăng ký sự kiện chưa
    public function daDangKySuKien(
        int $nguoiDungId,
        int $suKienId
    ): bool {
        $sql = "
            SELECT id
            FROM dang_ky
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id = :su_kien_id
              AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm đăng ký đã hủy để người dùng có thể đăng ký lại
    public function layDangKyDaHuy(
        int $nguoiDungId,
        int $suKienId
    ): ?array {
        $sql = "
            SELECT id
            FROM dang_ky
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id = :su_kien_id
              AND trang_thai = 'da_huy'
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Thực hiện đăng ký mới
    public function themDangKy(
        int $nguoiDungId,
        int $suKienId
    ): bool {
        $sql = "
            INSERT INTO dang_ky
                (nguoi_dung_id, su_kien_id, thoi_gian_dang_ky, trang_thai)
            VALUES
                (:nguoi_dung_id, :su_kien_id, NOW(), 'da_dang_ky')
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId
        ]);
    }
}
