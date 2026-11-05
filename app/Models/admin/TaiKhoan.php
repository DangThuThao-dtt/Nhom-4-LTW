<?php


require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
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
        ORDER BY nd.id ASC
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
        throw new \InvalidArgumentException(
            'ID tài khoản hoặc CLB không hợp lệ.'
        );
    }

    $pdo = $this->pdo;

    try {
        $pdo->beginTransaction();

        // 1. Kiểm tra tài khoản Ban tổ chức.
        $stmtTaiKhoan = $pdo->prepare(
            "SELECT id
             FROM nguoi_dung
             WHERE id = :id
               AND vai_tro = 'ban_to_chuc'
             FOR UPDATE"
        );

        $stmtTaiKhoan->execute(['id' => $id]);

        if (!$stmtTaiKhoan->fetch(PDO::FETCH_ASSOC)) {
            throw new \RuntimeException(
                'Không tìm thấy tài khoản Ban tổ chức.'
            );
        }

        // 2. Kiểm tra CLB được chọn.
        $stmtClb = $pdo->prepare(
            "SELECT id, nguoi_quan_ly_id
             FROM cau_lac_bo
             WHERE id = :clb_id
             FOR UPDATE"
        );

        $stmtClb->execute(['clb_id' => $cauLacBoId]);
        $clb = $stmtClb->fetch(PDO::FETCH_ASSOC);

        if (!$clb) {
            throw new \RuntimeException(
                'Không tìm thấy câu lạc bộ.'
            );
        }

        $nguoiQuanLyHienTai = $clb['nguoi_quan_ly_id'];

        // Không cho chọn CLB đang thuộc tài khoản khác.
        if (
            $nguoiQuanLyHienTai !== null &&
            (int) $nguoiQuanLyHienTai !== $id
        ) {
            throw new \RuntimeException(
                'Câu lạc bộ đã được Ban tổ chức khác phụ trách.'
            );
        }

        // 3. Kiểm tra email có bị tài khoản khác sử dụng không.
        $stmtEmail = $pdo->prepare(
            "SELECT id
             FROM nguoi_dung
             WHERE email = :email
               AND id <> :id
             LIMIT 1"
        );

        $stmtEmail->execute([
            'email' => $email,
            'id' => $id,
        ]);

        if ($stmtEmail->fetch(PDO::FETCH_ASSOC)) {
            throw new \RuntimeException(
                'Email đã được tài khoản khác sử dụng.'
            );
        }

        // 4. Cập nhật họ tên và email.
        $stmtCapNhatTaiKhoan = $pdo->prepare(
            "UPDATE nguoi_dung
             SET ho_ten = :ho_ten,
                 email = :email
             WHERE id = :id
               AND vai_tro = 'ban_to_chuc'"
        );

        $stmtCapNhatTaiKhoan->execute([
            'ho_ten' => $hoTen,
            'email' => $email,
            'id' => $id,
        ]);

        // 5. Nếu CLB đã thuộc chính tài khoản này thì giữ nguyên.
        // Nếu CLB đang trống thì chuyển tài khoản sang CLB đó.
        if (
            $nguoiQuanLyHienTai === null ||
            (int) $nguoiQuanLyHienTai !== $id
        ) {
            // Gỡ liên kết CLB cũ.
            $stmtBoClbCu = $pdo->prepare(
                "UPDATE cau_lac_bo
                 SET nguoi_quan_ly_id = NULL
                 WHERE nguoi_quan_ly_id = :id"
            );

            $stmtBoClbCu->execute(['id' => $id]);

            // Gán CLB mới.
            $stmtGanClb = $pdo->prepare(
                "UPDATE cau_lac_bo
                 SET nguoi_quan_ly_id = :id
                 WHERE id = :clb_id
                   AND nguoi_quan_ly_id IS NULL"
            );

            $stmtGanClb->execute([
                'id' => $id,
                'clb_id' => $cauLacBoId,
            ]);

            // Kiểm tra kết quả gán CLB.
            $stmtKiemTra = $pdo->prepare(
                "SELECT nguoi_quan_ly_id
                 FROM cau_lac_bo
                 WHERE id = :clb_id"
            );

            $stmtKiemTra->execute([
                'clb_id' => $cauLacBoId,
            ]);

            $nguoiQuanLySauCapNhat = $stmtKiemTra->fetchColumn();

            if (
                $nguoiQuanLySauCapNhat === false ||
                (int) $nguoiQuanLySauCapNhat !== $id
            ) {
                throw new \RuntimeException(
                    'Không thể gán câu lạc bộ cho tài khoản.'
                );
            }
        }

        $pdo->commit();

        return true;
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
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
    
public function danhSachClbChuaCoBanToChuc(): array
{
    $sql = "
        SELECT id, ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE nguoi_quan_ly_id IS NULL
        ORDER BY ten_cau_lac_bo ASC
    ";

    $stmt = $this->pdo->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function tuDongTaoTaiKhoanBanToChuc(array $clbIds): array
{
    $clbIds = array_values(array_unique(array_filter(
        array_map('intval', $clbIds),
        fn($id) => $id > 0
    )));

    if (empty($clbIds)) {
        throw new InvalidArgumentException('Chưa chọn câu lạc bộ.');
    }

    $this->pdo->beginTransaction();

    try {
        $placeholders = implode(',', array_fill(0, count($clbIds), '?'));

        $sql = "
            SELECT id, ten_cau_lac_bo
            FROM cau_lac_bo
            WHERE id IN ($placeholders)
              AND nguoi_quan_ly_id IS NULL
            ORDER BY id ASC
            FOR UPDATE
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($clbIds);

        $danhSachClb = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nếu có câu lạc bộ không còn ở trạng thái chưa được phân công,
        // hủy toàn bộ thao tác.
        if (count($danhSachClb) !== count($clbIds)) {
            throw new RuntimeException(
                'Một hoặc nhiều câu lạc bộ đã có người quản lý. Vui lòng tải lại trang.'
            );
        }

        $ketQua = [];

        foreach ($danhSachClb as $clb) {
            $clbId = (int) $clb['id'];

            do {
                $email = 'clb' . $clbId . '_'
                    . bin2hex(random_bytes(5))
                    . '@nhom4.local';

                $kiemTra = $this->pdo->prepare(
                    "SELECT id FROM nguoi_dung WHERE email = ? LIMIT 1"
                );
                $kiemTra->execute([$email]);
            } while ($kiemTra->fetchColumn());

            $matKhau = bin2hex(random_bytes(8));
            $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);

            $hoTen = 'Ban tổ chức - ' . $clb['ten_cau_lac_bo'];

            $taoTaiKhoan = $this->pdo->prepare("
                INSERT INTO nguoi_dung
                    (ho_ten, email, mat_khau, vai_tro)
                VALUES (?, ?, ?, 'ban_to_chuc')
            ");

            $taoTaiKhoan->execute([
                $hoTen,
                $email,
                $matKhauHash
            ]);

            $taiKhoanId = (int) $this->pdo->lastInsertId();

            $ganClb = $this->pdo->prepare("
                UPDATE cau_lac_bo
                SET nguoi_quan_ly_id = ?
                WHERE id = ?
                  AND nguoi_quan_ly_id IS NULL
            ");

            $ganClb->execute([$taiKhoanId, $clbId]);

            if ($ganClb->rowCount() !== 1) {
                throw new RuntimeException('Không thể gán tài khoản cho câu lạc bộ.');
            }

            $ketQua[] = [
                'cau_lac_bo' => $clb['ten_cau_lac_bo'],
                'ho_ten' => $hoTen,
                'email' => $email,
                'mat_khau' => $matKhau
            ];
        }

        $this->pdo->commit();

        return $ketQua;

    } catch (Throwable $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        throw $e;
    }
}
public function xoa(int $id): bool
{
    if ($id <= 0) {
        throw new InvalidArgumentException('ID tài khoản không hợp lệ.');
    }

    $this->pdo->beginTransaction();

    try {
        // Chỉ xóa tài khoản Ban tổ chức.
        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM nguoi_dung
             WHERE id = :id
               AND vai_tro = 'ban_to_chuc'
             FOR UPDATE"
        );

        $stmt->execute(['id' => $id]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException(
                'Không tìm thấy tài khoản Ban tổ chức.'
            );
        }

        // Gỡ tài khoản khỏi câu lạc bộ trước.
        $stmtGoiClb = $this->pdo->prepare(
            "UPDATE cau_lac_bo
             SET nguoi_quan_ly_id = NULL
             WHERE nguoi_quan_ly_id = :id"
        );

        $stmtGoiClb->execute(['id' => $id]);

        // Xóa tài khoản.
        $stmtXoa = $this->pdo->prepare(
            "DELETE FROM nguoi_dung
             WHERE id = :id
               AND vai_tro = 'ban_to_chuc'"
        );

        $stmtXoa->execute(['id' => $id]);

        if ($stmtXoa->rowCount() !== 1) {
            throw new RuntimeException('Không thể xóa tài khoản.');
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
}