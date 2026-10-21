
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

class QuanLySuKien
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    
    // Lay danh sach su kien va so luot dang ky
    
public function layDanhSach(array $boLoc = []): array
{
    $tuKhoa = trim($boLoc['tu_khoa'] ?? '');
    $cauLacBoId = (int) ($boLoc['cau_lac_bo_id'] ?? 0);
    $trangThai = $boLoc['trang_thai'] ?? '';
    $tuNgay = $boLoc['tu_ngay'] ?? '';
    $denNgay = $boLoc['den_ngay'] ?? '';

    $sapXep = $boLoc['sap_xep'] ?? 'thoi_gian';
    $huong = strtoupper($boLoc['huong'] ?? 'ASC');

    $cotSapXep = [
        'thoi_gian' => 'sk.thoi_gian_bat_dau',
        'dang_ky' => 'so_luot_dang_ky',
    ];

    if (!isset($cotSapXep[$sapXep])) {
        $sapXep = 'thoi_gian';
    }

    if (!in_array($huong, ['ASC', 'DESC'], true)) {
        $huong = 'ASC';
    }

    $where = ['1 = 1'];
    $params = [];

    if ($tuKhoa !== '') {
        $where[] = 'sk.ten_su_kien LIKE :tu_khoa';
        $params['tu_khoa'] = '%' . $tuKhoa . '%';
    }

    if ($cauLacBoId > 0) {
        $where[] = 'sk.cau_lac_bo_id = :clb_id';
        $params['clb_id'] = $cauLacBoId;
    }

    if (in_array($trangThai, [
        'nhap', 'cong_khai', 'da_huy', 'ket_thuc'
    ], true)) {
        $where[] = 'sk.trang_thai = :trang_thai';
        $params['trang_thai'] = $trangThai;
    }

    if ($tuNgay !== '') {
        $where[] = 'sk.thoi_gian_bat_dau >= :tu_ngay';
        $params['tu_ngay'] = $tuNgay . ' 00:00:00';
    }

    if ($denNgay !== '') {
        $where[] = 'sk.thoi_gian_bat_dau <= :den_ngay';
        $params['den_ngay'] = $denNgay . ' 23:59:59';
    }

    $dieuKien = implode(' AND ', $where);

    $sql = "
        SELECT
            sk.id,
            sk.cau_lac_bo_id,
            sk.ten_su_kien,
            sk.mo_ta,
            sk.dia_diem,
            sk.thoi_gian_bat_dau,
            sk.thoi_gian_ket_thuc,
            sk.so_luong_toi_da,
            sk.trang_thai,
            clb.ten_cau_lac_bo,
            COUNT(dk.id) AS so_luot_dang_ky
        FROM su_kien sk
        INNER JOIN cau_lac_bo clb
            ON clb.id = sk.cau_lac_bo_id
        LEFT JOIN dang_ky dk
            ON dk.su_kien_id = sk.id
            AND dk.trang_thai = 'da_dang_ky'
        WHERE $dieuKien
        GROUP BY
            sk.id, sk.cau_lac_bo_id, sk.ten_su_kien,
            sk.mo_ta, sk.dia_diem, sk.thoi_gian_bat_dau,
            sk.thoi_gian_ket_thuc, sk.so_luong_toi_da,
            sk.trang_thai, clb.ten_cau_lac_bo
        ORDER BY {$cotSapXep[$sapXep]} $huong, sk.id DESC
        LIMIT :gioi_han OFFSET :vi_tri
    ";

    $soDongMoiTrang = max(
        1,
        min(100, (int) ($boLoc['so_dong'] ?? 10))
    );
    $trang = max(1, (int) ($boLoc['trang'] ?? 1));
    $offset = ($trang - 1) * $soDongMoiTrang;

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }

    $stmt->bindValue(':gioi_han', $soDongMoiTrang, PDO::PARAM_INT);
    $stmt->bindValue(':vi_tri', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function demDanhSach(array $boLoc = []): int
{
    $where = ['1 = 1'];
    $params = [];

    $tuKhoa = trim($boLoc['tu_khoa'] ?? '');
    $cauLacBoId = (int) ($boLoc['cau_lac_bo_id'] ?? 0);
    $trangThai = $boLoc['trang_thai'] ?? '';
    $tuNgay = $boLoc['tu_ngay'] ?? '';
    $denNgay = $boLoc['den_ngay'] ?? '';

    if ($tuKhoa !== '') {
        $where[] = 'ten_su_kien LIKE :tu_khoa';
        $params['tu_khoa'] = '%' . $tuKhoa . '%';
    }

    if ($cauLacBoId > 0) {
        $where[] = 'cau_lac_bo_id = :clb_id';
        $params['clb_id'] = $cauLacBoId;
    }

    if (in_array($trangThai, [
        'nhap', 'cong_khai', 'da_huy', 'ket_thuc'
    ], true)) {
        $where[] = 'trang_thai = :trang_thai';
        $params['trang_thai'] = $trangThai;
    }

    if ($tuNgay !== '') {
        $where[] = 'thoi_gian_bat_dau >= :tu_ngay';
        $params['tu_ngay'] = $tuNgay . ' 00:00:00';
    }

    if ($denNgay !== '') {
        $where[] = 'thoi_gian_bat_dau <= :den_ngay';
        $params['den_ngay'] = $denNgay . ' 23:59:59';
    }

    $sql = 'SELECT COUNT(*) FROM su_kien WHERE '
        . implode(' AND ', $where);

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

public function layChiTiet(int $id): array|false
{
    $sql = "
        SELECT
            sk.*,
            clb.ten_cau_lac_bo,
            COUNT(dk.id) AS so_luot_dang_ky
        FROM su_kien sk
        INNER JOIN cau_lac_bo clb
            ON clb.id = sk.cau_lac_bo_id
        LEFT JOIN dang_ky dk
            ON dk.su_kien_id = sk.id
            AND dk.trang_thai = 'da_dang_ky'
        WHERE sk.id = :id
        GROUP BY
            sk.id, clb.ten_cau_lac_bo
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // Tim su kien theo ID
    public function timTheoId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM su_kien WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Them su kien
    public function them(array $data): bool
    {
        $sql = "
            INSERT INTO su_kien (
                cau_lac_bo_id,
                ten_su_kien,
                mo_ta,
                dia_diem,
                thoi_gian_bat_dau,
                thoi_gian_ket_thuc,
                so_luong_toi_da,
                trang_thai,
                nguoi_tao_id
            ) VALUES (
                :cau_lac_bo_id,
                :ten_su_kien,
                :mo_ta,
                :dia_diem,
                :bat_dau,
                :ket_thuc,
                :so_luong,
                :trang_thai,
                :nguoi_tao_id
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'cau_lac_bo_id' => $data['cau_lac_bo_id'],
            'ten_su_kien' => $data['ten_su_kien'],
            'mo_ta' => $data['mo_ta'] ?: null,
            'dia_diem' => $data['dia_diem'] ?: null,
            'bat_dau' => $data['thoi_gian_bat_dau'],
            'ket_thuc' => $data['thoi_gian_ket_thuc'] ?: null,
            'so_luong' => $data['so_luong_toi_da'],
            'trang_thai' => $data['trang_thai'],
            'nguoi_tao_id' => $data['nguoi_tao_id'],
        ]);
    }

    // Cap nhat su kien
    
    // Cap nhat su kien
    public function capNhat(int $id, array $data): bool
    {
        $sql = "
            UPDATE su_kien SET
                cau_lac_bo_id = :cau_lac_bo_id,
                ten_su_kien = :ten_su_kien,
                mo_ta = :mo_ta,
                dia_diem = :dia_diem,
                thoi_gian_bat_dau = :bat_dau,
                thoi_gian_ket_thuc = :ket_thuc,
                so_luong_toi_da = :so_luong,
                trang_thai = :trang_thai
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'cau_lac_bo_id' => $data['cau_lac_bo_id'],
            'ten_su_kien' => $data['ten_su_kien'],
            'mo_ta' => $data['mo_ta'] ?: null,
            'dia_diem' => $data['dia_diem'] ?: null,
            'bat_dau' => $data['thoi_gian_bat_dau'],
            'ket_thuc' => $data['thoi_gian_ket_thuc'] ?: null,
            'so_luong' => $data['so_luong_toi_da'],
            'trang_thai' => $data['trang_thai'],
        ]);
    }


    // Cap nhat trang thai: nhap, cong_khai, da_huy, ket_thuc
    public function capNhatTrangThai(int $id, string $trangThai): bool
    {
        $cacTrangThai = ['nhap', 'cong_khai', 'da_huy', 'ket_thuc'];

        if (!in_array($trangThai, $cacTrangThai, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE su_kien SET trang_thai = :trang_thai
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'trang_thai' => $trangThai,
        ]);
    }

    // Xoa su kien neu chua co dang ky lien quan
    public function xoa(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM su_kien WHERE id = :id"
        );

        return $stmt->execute(['id' => $id])
            && $stmt->rowCount() > 0;
    }
    public function coDangKy(int $id): bool
{
    $stmt = $this->pdo->prepare(
        "SELECT COUNT(*) FROM dang_ky WHERE su_kien_id = :id"
    );

    $stmt->execute(['id' => $id]);

    return (int) $stmt->fetchColumn() > 0;
}
}
