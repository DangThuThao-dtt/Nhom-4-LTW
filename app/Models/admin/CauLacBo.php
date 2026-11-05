<?php

require_once __DIR__ . '/../../Core/ket_noi_csdl.php';

class CauLacBo
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }
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
            FROM cau_lac_bo AS clb
            LEFT JOIN nguoi_dung AS nd
                ON clb.nguoi_quan_ly_id = nd.id
            ORDER BY clb.id ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm một câu lạc bộ theo ID.
     * Trả về null nếu không tìm thấy.
     */
    public function timTheoId(int $id): ?array
    {
        $sql = "
            SELECT
                clb.id,
                clb.ten_cau_lac_bo,
                clb.mo_ta,
                clb.nguoi_quan_ly_id,
                nd.ho_ten AS ten_nguoi_quan_ly,
                clb.ngay_tao
            FROM cau_lac_bo AS clb
            LEFT JOIN nguoi_dung AS nd
                ON clb.nguoi_quan_ly_id = nd.id
            WHERE clb.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $cauLacBo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cauLacBo !== false ? $cauLacBo : null;
    }

    /**
     * Thêm câu lạc bộ mới.
     */
    public function them(
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        $sql = "
            INSERT INTO cau_lac_bo (
                ten_cau_lac_bo,
                mo_ta,
                nguoi_quan_ly_id
            )
            VALUES (
                :ten,
                :mo_ta,
                :nguoi_quan_ly_id
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'ten' => $ten,
            'mo_ta' => $moTa,
            'nguoi_quan_ly_id' => $nguoiQuanLyId,
        ]);
    }

    /**
     * Cập nhật thông tin câu lạc bộ.
     */
    public function capNhat(
        int $id,
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        $sql = "
            UPDATE cau_lac_bo
            SET
                ten_cau_lac_bo = :ten,
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

    /**
 * Xóa câu lạc bộ và tài khoản ban tổ chức phụ trách CLB.
 */
public function xoa(int $id): bool
{
    $this->pdo->beginTransaction();

    try {
        // 1. Lấy tài khoản ban tổ chức đang phụ trách CLB
        $sql = "
            SELECT nguoi_quan_ly_id
            FROM cau_lac_bo
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $nguoiQuanLyId = $stmt->fetchColumn();

        // Không tìm thấy CLB thì dừng
        if ($nguoiQuanLyId === false) {
            $this->pdo->rollBack();
            return false;
        }

        // 2. Xóa đăng ký thuộc các sự kiện của CLB
        $sql = "
            DELETE FROM dang_ky
            WHERE su_kien_id IN (
                SELECT id
                FROM (
                    SELECT id
                    FROM su_kien
                    WHERE cau_lac_bo_id = :id
                ) AS ds_su_kien
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // 3. Xóa thông báo thuộc các sự kiện của CLB
        $sql = "
            DELETE FROM thong_bao
            WHERE su_kien_id IN (
                SELECT id
                FROM (
                    SELECT id
                    FROM su_kien
                    WHERE cau_lac_bo_id = :id
                ) AS ds_su_kien
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // 4. Xóa các sự kiện thuộc CLB
        $sql = "
            DELETE FROM su_kien
            WHERE cau_lac_bo_id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // 5. Gỡ liên kết tài khoản quản lý khỏi CLB
        $sql = "
            UPDATE cau_lac_bo
            SET nguoi_quan_ly_id = NULL
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // 6. Xóa CLB
        $sql = "
            DELETE FROM cau_lac_bo
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // 7. Xóa tài khoản ban tổ chức phụ trách CLB
        if ($nguoiQuanLyId !== null) {
            $sql = "
                DELETE FROM nguoi_dung
                WHERE id = :nguoi_quan_ly_id
                  AND vai_tro = 'ban_to_chuc'
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nguoi_quan_ly_id' => $nguoiQuanLyId
            ]);
        }

        // Hoàn tất tất cả thao tác
        $this->pdo->commit();

        return true;

    } catch (Throwable $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        throw $e;
    }
}
}
