<?php

require_once __DIR__ . '/../../Core/ket_noi_csdl.php';

class TaiKhoanThanhVien
{
    private PDO $conn;

    public function __construct()
    {
        global $pdo;
        $this->conn = $pdo;
    }

    public function danhSachTaiKhoanThanhVien(): array
    {
        $sql = "
            SELECT
                nd.id,
                nd.ho_ten,
                nd.email,

                COALESCE(
                    clb.ten_cau_lac_bo,
                    'Chưa gán CLB'
                ) AS ten_cau_lac_bo,

                CASE dk.trang_thai
                    WHEN 'da_dang_ky' THEN 'Đang tham gia'
                    WHEN 'da_huy' THEN 'Đã hủy'
                    WHEN 'da_tham_gia' THEN 'Đã tham gia sự kiện'
                    ELSE 'Chưa đăng ký sự kiện'
                END AS trang_thai

            FROM nguoi_dung nd

            LEFT JOIN (
                SELECT
                    tvclb.nguoi_dung_id,
                    GROUP_CONCAT(
                        clb.ten_cau_lac_bo
                        SEPARATOR ', '
                    ) AS ten_cau_lac_bo
                FROM thanh_vien_cau_lac_bo tvclb
                INNER JOIN cau_lac_bo clb
                    ON clb.id = tvclb.cau_lac_bo_id
                GROUP BY tvclb.nguoi_dung_id
            ) clb
                ON clb.nguoi_dung_id = nd.id

            LEFT JOIN dang_ky dk
                ON dk.nguoi_dung_id = nd.id
                AND dk.id = (
                    SELECT dk2.id
                    FROM dang_ky dk2
                    WHERE dk2.nguoi_dung_id = nd.id
                    ORDER BY
                        dk2.thoi_gian_dang_ky DESC,
                        dk2.id DESC
                    LIMIT 1
                )

            WHERE nd.vai_tro = 'thanh_vien'

            ORDER BY nd.id DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}