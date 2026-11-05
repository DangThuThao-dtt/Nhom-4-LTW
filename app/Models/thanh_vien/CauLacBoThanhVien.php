
<?php

class CauLacBoThanhVien
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lay danh sach cau lac bo, co ho tro tim kiem
    public function layDanhSach(string $tuKhoa = ''): array
    {
        $sql = "
            SELECT
                clb.id,
                clb.ten_cau_lac_bo,
                clb.hinh_anh,
                clb.mo_ta,
                clb.nguoi_quan_ly_id,
                nd.ho_ten AS ten_nguoi_quan_ly,
                clb.ngay_tao
            FROM cau_lac_bo clb
            LEFT JOIN nguoi_dung nd
                ON clb.nguoi_quan_ly_id = nd.id
        ";

        $params = [];

        if ($tuKhoa !== '') {
            $sql .= " WHERE clb.ten_cau_lac_bo LIKE :tu_khoa";
            $params['tu_khoa'] = '%' . $tuKhoa . '%';
        }

        $sql .= " ORDER BY clb.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Them cau lac bo
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

    // Cap nhat cau lac bo
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

    // Xoa cau lac bo
    public function xoa(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM cau_lac_bo WHERE id = :id"
        );

        return $stmt->execute(['id' => $id]);
    }

    // Kiem tra nguoi dung da tham gia cau lac bo hay chua
    public function daThamGia(
        int $cauLacBoId,
        int $nguoiDungId
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM thanh_vien_cau_lac_bo
            WHERE cau_lac_bo_id = :cau_lac_bo_id
              AND nguoi_dung_id = :nguoi_dung_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'cau_lac_bo_id' => $cauLacBoId,
            'nguoi_dung_id' => $nguoiDungId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Tham gia cau lac bo
    public function thamGia(
        int $cauLacBoId,
        int $nguoiDungId
    ): bool {
        $sql = "
            INSERT INTO thanh_vien_cau_lac_bo
                (cau_lac_bo_id, nguoi_dung_id)
            VALUES
                (:cau_lac_bo_id, :nguoi_dung_id)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'cau_lac_bo_id' => $cauLacBoId,
            'nguoi_dung_id' => $nguoiDungId,
        ]);
    }

    // Lay danh sach ID cau lac bo ma nguoi dung da tham gia
    public function layCLBDaThamGia(int $nguoiDungId): array
    {
        $sql = "
            SELECT cau_lac_bo_id
            FROM thanh_vien_cau_lac_bo
            WHERE nguoi_dung_id = :nguoi_dung_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
        ]);

        return array_map(
            'intval',
            $stmt->fetchAll(PDO::FETCH_COLUMN)
        );
    }

    // Lay danh sach cau lac bo ma nguoi dung da tham gia
    public function layChiTietCLBDaThamGia(
        int $nguoiDungId
    ): array {
        $sql = "
            SELECT
                clb.id,
                clb.ten_cau_lac_bo,
                tv.ngay_tham_gia
            FROM thanh_vien_cau_lac_bo tv
            JOIN cau_lac_bo clb
                ON tv.cau_lac_bo_id = clb.id
            WHERE tv.nguoi_dung_id = :nguoi_dung_id
            ORDER BY tv.ngay_tham_gia DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nguoi dung roi khoi cau lac bo
    public function roiCLB(
        int $cauLacBoId,
        int $nguoiDungId
    ): bool {
        $sql = "
            DELETE FROM thanh_vien_cau_lac_bo
            WHERE cau_lac_bo_id = :cau_lac_bo_id
              AND nguoi_dung_id = :nguoi_dung_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'cau_lac_bo_id' => $cauLacBoId,
            'nguoi_dung_id' => $nguoiDungId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
