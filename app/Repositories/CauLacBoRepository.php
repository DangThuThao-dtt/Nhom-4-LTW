
<?php

class CauLacBoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh sách câu lạc bộ
    public function layDanhSach(): array
    {
        $sql = "
            SELECT
                clb.id,
                clb.ten_cau_lac_bo,
                clb.mo_ta,
                clb.nguoi_quan_ly_id,
                nd.ho_ten AS ten_nguoi_quan_ly,
                clb.ngay_tao
            FROM cau_lac_bo clb
            LEFT JOIN nguoi_dung nd
                ON clb.nguoi_quan_ly_id = nd.id
            ORDER BY clb.id ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    // Lấy thông tin một câu lạc bộ theo ID
    public function layTheoId(int $id): array|false
    {
        $sql = "
            SELECT
                id,
                ten_cau_lac_bo,
                mo_ta,
                nguoi_quan_ly_id
            FROM cau_lac_bo
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Thêm câu lạc bộ
    public function them(
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        $sql = "
            INSERT INTO cau_lac_bo
                (ten_cau_lac_bo, mo_ta, nguoi_quan_ly_id)
            VALUES
                (:ten, :mo_ta, :nguoi_quan_ly_id)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'ten' => $ten,
            'mo_ta' => $moTa,
            'nguoi_quan_ly_id' => $nguoiQuanLyId,
        ]);
    }

    // Cập nhật câu lạc bộ
    public function capNhat(
        int $id,
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        $sql = "
            UPDATE cau_lac_bo
            SET ten_cau_lac_bo = :ten,
                mo_ta = :mo_ta,
                nguoi_quan_ly_id = :nguoi_quan_ly_id
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'ten' => $ten,
            'mo_ta' => $moTa,
            'nguoi_quan_ly_id' => $nguoiQuanLyId,
        ]);
    }

    // Xóa câu lạc bộ
    public function xoa(int $id): bool
    {
        $sql = "
            DELETE FROM cau_lac_bo
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
        // Lấy số liệu thống kê cho trang Dashboard
    public function layThongKeDashboard(): array
    {
        $tongCauLacBo = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM cau_lac_bo")
            ->fetchColumn();

        $tongTaiKhoan = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM nguoi_dung")
            ->fetchColumn();

        $tongSuKien = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM su_kien")
            ->fetchColumn();

        return [
            'tongCauLacBo' => $tongCauLacBo,
            'tongTaiKhoan' => $tongTaiKhoan,
            'tongSuKien' => $tongSuKien,
        ];
    }
}
