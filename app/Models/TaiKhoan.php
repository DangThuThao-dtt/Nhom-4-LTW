<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

class TaiKhoan
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }
public function danhSachBanToChuc(
    string $tuKhoa = '',
    int $cauLacBoId = 0,
    int $limit = 10,
    int $offset = 0
): array {
    $where = ["nd.vai_tro = 'ban_to_chuc'"];
    $params = [];

    if ($tuKhoa !== '') {
        $where[] = '(nd.ho_ten LIKE :ten OR nd.email LIKE :email)';
        $params['ten'] = '%' . $tuKhoa . '%';
        $params['email'] = '%' . $tuKhoa . '%';
    }

    if ($cauLacBoId > 0) {
        $where[] = 'clb.id = :clb_id';
        $params['clb_id'] = $cauLacBoId;
    }

    $sql = "
        SELECT
            nd.id,
            nd.ho_ten,
            nd.email,
            nd.vai_tro,
            clb.id AS cau_lac_bo_id,
            clb.ten_cau_lac_bo
        FROM nguoi_dung nd
        LEFT JOIN cau_lac_bo clb
            ON clb.nguoi_quan_ly_id = nd.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY nd.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue(
            ':' . $key,
            $value,
            is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function demTaiKhoanBanToChuc(
    string $tuKhoa = '',
    int $cauLacBoId = 0
): int {
    $where = ["nd.vai_tro = 'ban_to_chuc'"];
    $params = [];

    if ($tuKhoa !== '') {
        $where[] = '(nd.ho_ten LIKE :ten OR nd.email LIKE :email)';
        $params['ten'] = '%' . $tuKhoa . '%';
        $params['email'] = '%' . $tuKhoa . '%';
    }

    if ($cauLacBoId > 0) {
        $where[] = 'clb.id = :clb_id';
        $params['clb_id'] = $cauLacBoId;
    }

    $sql = "
        SELECT COUNT(DISTINCT nd.id)
        FROM nguoi_dung nd
        LEFT JOIN cau_lac_bo clb
            ON clb.nguoi_quan_ly_id = nd.id
        WHERE " . implode(' AND ', $where);

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

public function tongTaiKhoanBanToChuc(): int
{
    return (int) $this->pdo->query(
        "SELECT COUNT(*) FROM nguoi_dung
         WHERE vai_tro = 'ban_to_chuc'"
    )->fetchColumn();
}
    public function taoBanToChuc(
        string $hoTen,
        string $email,
        string $matKhau,
        int $cauLacBoId
    ): bool {
        $kiemTraEmail = $this->pdo->prepare(
            "SELECT id FROM nguoi_dung WHERE email = :email LIMIT 1"
        );
        $kiemTraEmail->execute(['email' => $email]);

        if ($kiemTraEmail->fetch()) {
            throw new RuntimeException('Email đã được sử dụng.');
        }

        $kiemTraClb = $this->pdo->prepare(
            "SELECT id, nguoi_quan_ly_id
             FROM cau_lac_bo
             WHERE id = :id"
        );
        $kiemTraClb->execute(['id' => $cauLacBoId]);
        $clb = $kiemTraClb->fetch();

        if (!$clb) {
            throw new RuntimeException('Câu lạc bộ không tồn tại.');
        }

        if ($clb['nguoi_quan_ly_id'] !== null) {
            throw new RuntimeException(
                'Câu lạc bộ này đã có tài khoản quản lý.'
            );
        }

        $hash = password_hash($matKhau, PASSWORD_DEFAULT);

        $this->pdo->beginTransaction();

        try {
            $sql = "
                INSERT INTO nguoi_dung
                    (ho_ten, email, mat_khau, vai_tro)
                VALUES
                    (:ho_ten, :email, :mat_khau, 'ban_to_chuc')
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'ho_ten' => $hoTen,
                'email' => $email,
                'mat_khau' => $hash,
            ]);

            $idTaiKhoan = (int) $this->pdo->lastInsertId();

            $ganClb = $this->pdo->prepare(
                "UPDATE cau_lac_bo
                 SET nguoi_quan_ly_id = :nguoi_quan_ly_id
                 WHERE id = :id AND nguoi_quan_ly_id IS NULL"
            );

            $ganClb->execute([
                'nguoi_quan_ly_id' => $idTaiKhoan,
                'id' => $cauLacBoId,
            ]);

            if ($ganClb->rowCount() !== 1) {
                throw new RuntimeException(
                    'Không thể gán tài khoản vào câu lạc bộ này.'
                );
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function capNhatThongTin(
        int $id,
        string $hoTen,
        string $email,
        int $cauLacBoId
    ): bool {
        if ($id <= 0 || $cauLacBoId <= 0) {
            throw new RuntimeException('Dữ liệu tài khoản không hợp lệ.');
        }

        $this->pdo->beginTransaction();

        try {
            // Khóa câu lạc bộ đích để tránh gán đồng thời cho hai tài khoản.
            $kiemTraClb = $this->pdo->prepare(
                "SELECT id, nguoi_quan_ly_id
                 FROM cau_lac_bo
                 WHERE id = :id
                 FOR UPDATE"
            );
            $kiemTraClb->execute(['id' => $cauLacBoId]);
            $clb = $kiemTraClb->fetch(PDO::FETCH_ASSOC);

            if (!$clb) {
                throw new RuntimeException('Câu lạc bộ không tồn tại.');
            }

            if (
                $clb['nguoi_quan_ly_id'] !== null
                && (int) $clb['nguoi_quan_ly_id'] !== $id
            ) {
                throw new RuntimeException('Câu lạc bộ đã có ban tổ chức phụ trách.');
            }

            $capNhatNguoiDung = $this->pdo->prepare(
                "UPDATE nguoi_dung
                 SET ho_ten = :ho_ten, email = :email
                 WHERE id = :id AND vai_tro = 'ban_to_chuc'"
            );
            $capNhatNguoiDung->execute([
                'id' => $id,
                'ho_ten' => $hoTen,
                'email' => $email,
            ]);

            if ($capNhatNguoiDung->rowCount() === 0) {
                $kiemTraTaiKhoan = $this->pdo->prepare(
                    "SELECT id FROM nguoi_dung
                     WHERE id = :id AND vai_tro = 'ban_to_chuc'"
                );
                $kiemTraTaiKhoan->execute(['id' => $id]);
                if (!$kiemTraTaiKhoan->fetch()) {
                    throw new RuntimeException('Không tìm thấy tài khoản ban tổ chức.');
                }
            }

            // Gỡ liên kết cũ của tài khoản này, không xóa câu lạc bộ.
            $goClbCu = $this->pdo->prepare(
                "UPDATE cau_lac_bo
                 SET nguoi_quan_ly_id = NULL
                 WHERE nguoi_quan_ly_id = :id"
            );
            $goClbCu->execute(['id' => $id]);

            // Gán tài khoản vào câu lạc bộ mới nếu câu lạc bộ vẫn chưa có người phụ trách.
            $ganClbMoi = $this->pdo->prepare(
                "UPDATE cau_lac_bo
                 SET nguoi_quan_ly_id = :tai_khoan_id
                 WHERE id = :clb_id AND nguoi_quan_ly_id IS NULL"
            );
            $ganClbMoi->execute([
                'tai_khoan_id' => $id,
                'clb_id' => $cauLacBoId,
            ]);

            if ($ganClbMoi->rowCount() !== 1) {
                throw new RuntimeException('Câu lạc bộ đã có ban tổ chức phụ trách.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function kiemTraMatKhauHienTai(int $id, string $matKhauCu): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT mat_khau
             FROM nguoi_dung
             WHERE id = :id AND vai_tro = 'ban_to_chuc'
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();

        return is_string($hash) && password_verify($matKhauCu, $hash);
    }

    public function datLaiMatKhau(int $id, string $matKhauMoi): bool
    {
        $hash = password_hash($matKhauMoi, PASSWORD_DEFAULT);

        $sql = "
            UPDATE nguoi_dung
            SET mat_khau = :mat_khau
            WHERE id = :id AND vai_tro = 'ban_to_chuc'
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'mat_khau' => $hash,
        ]);
    }
}