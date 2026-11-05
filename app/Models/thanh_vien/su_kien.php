
<?php

class SuKien
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh sách sự kiện, có thể tìm kiếm và lọc
    public function layDanhSach(
        string $tuKhoa = '',
        int $cauLacBoId = 0,
        string $tuNgay = '',
        string $denNgay = ''
    ): array {
        $sql = "
            SELECT
                sk.id,
                sk.ten_su_kien,
                sk.mo_ta,
                sk.hinh_anh,
                sk.dia_diem,
                sk.thoi_gian_bat_dau,
                sk.thoi_gian_ket_thuc,
                sk.so_luong_toi_da,
                sk.trang_thai,
                clb.ten_cau_lac_bo
            FROM su_kien AS sk
            INNER JOIN cau_lac_bo AS clb
                ON sk.cau_lac_bo_id = clb.id
            WHERE sk.trang_thai = 'cong_khai'
        ";

        $params = [];

        // Tìm kiếm theo tên sự kiện
        $tuKhoa = trim($tuKhoa);

        if ($tuKhoa !== '') {
            $sql .= " AND sk.ten_su_kien LIKE :tu_khoa";
            $params['tu_khoa'] = '%' . $tuKhoa . '%';
        }

        // Lọc theo câu lạc bộ
        if ($cauLacBoId > 0) {
            $sql .= " AND sk.cau_lac_bo_id = :clb_id";
            $params['clb_id'] = $cauLacBoId;
        }

        // Lọc sự kiện có thời gian bắt đầu từ ngày đã chọn
        if ($tuNgay !== '') {
            $sql .= " AND sk.thoi_gian_bat_dau >= :tu_ngay";
            $params['tu_ngay'] = $tuNgay . ' 00:00:00';
        }

        // Lọc sự kiện có thời gian bắt đầu đến hết ngày đã chọn
        if ($denNgay !== '') {
            $sql .= " AND sk.thoi_gian_bat_dau <= :den_ngay";
            $params['den_ngay'] = $denNgay . ' 23:59:59';
        }

        $sql .= " ORDER BY sk.thoi_gian_bat_dau ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Lấy danh sách câu lạc bộ
    public function layDanhSachCauLacBo(): array
    {
        $sql = "
            SELECT id, ten_cau_lac_bo
            FROM cau_lac_bo
            ORDER BY ten_cau_lac_bo ASC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    // Lấy chi tiết sự kiện và số người đã đăng ký
    public function layChiTiet(int $id): ?array
    {
        $sql = "
            SELECT
                sk.id,
                sk.ten_su_kien,
                sk.mo_ta,
                sk.hinh_anh,
                sk.dia_diem,
                sk.thoi_gian_bat_dau,
                sk.thoi_gian_ket_thuc,
                sk.so_luong_toi_da,
                sk.trang_thai,
                clb.ten_cau_lac_bo,

                (
                    SELECT COUNT(*)
                    FROM dang_ky AS dk
                    WHERE dk.su_kien_id = sk.id
                      AND dk.trang_thai <> 'da_huy'
                ) AS so_nguoi_da_dang_ky

            FROM su_kien AS sk
            INNER JOIN cau_lac_bo AS clb
                ON sk.cau_lac_bo_id = clb.id
            WHERE sk.id = :id
              AND sk.trang_thai = 'cong_khai'
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $suKien = $stmt->fetch();

        return $suKien ?: null;
    }
}
