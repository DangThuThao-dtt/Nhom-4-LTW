<?php
require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../Core/xac_thuc.php';

class ThongBao
{
    public static function taoNeuChuaCo(
        PDO $pdo,
        int $nguoiDungId,
        ?int $suKienId,
        string $loai,
        string $tieuDe,
        string $noiDung
    ): void {
        $sql = "
            SELECT id
            FROM thong_bao
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id <=> :su_kien_id
              AND loai = :loai
              AND tieu_de = :tieu_de
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'loai' => $loai,
            'tieu_de' => $tieuDe
        ]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return;
        }

        $sql = "
            INSERT INTO thong_bao (
                nguoi_dung_id,
                su_kien_id,
                tieu_de,
                noi_dung,
                loai,
                da_doc,
                ngay_tao
            ) VALUES (
                :nguoi_dung_id,
                :su_kien_id,
                :tieu_de,
                :noi_dung,
                :loai,
                0,
                NOW()
            )
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
            'loai' => $loai
        ]);
    }
}
yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();
$tenCauLacBo = 'Câu lạc bộ';

if ($nguoiDung) {
    $stmtTenClb = $pdo->prepare("
        SELECT ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE nguoi_quan_ly_id = :nguoi_dung_id
        LIMIT 1
    ");

    $stmtTenClb->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);

    $tenCauLacBo = $stmtTenClb->fetchColumn() ?: 'Câu lạc bộ';
}
$laAdmin = (($nguoiDung['vai_tro'] ?? '') === 'admin');




if ($laAdmin) {
    // Admin xem thống kê toàn hệ thống.
    $sqlThongKe = "
        SELECT
            COUNT(DISTINCT s.id) AS tong_su_kien,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'cong_khai'
                 AND (s.thoi_gian_ket_thuc IS NULL
                      OR s.thoi_gian_ket_thuc > NOW())
                THEN s.id
            END) AS su_kien_cong_khai,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'ket_thuc'
                  OR (s.thoi_gian_ket_thuc IS NOT NULL
                      AND s.thoi_gian_ket_thuc <= NOW())
                THEN s.id
            END) AS su_kien_da_ket_thuc,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'da_huy'
                THEN s.id
            END) AS su_kien_da_huy,
            COUNT(DISTINCT CASE
                WHEN dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
                THEN dk.id
            END) AS tong_luot_dang_ky,
            COUNT(DISTINCT CASE
                WHEN dk.trang_thai = 'da_tham_gia'
                THEN dk.id
            END) AS tong_luot_tham_gia
        FROM su_kien s
        LEFT JOIN dang_ky dk ON dk.su_kien_id = s.id
    ";

    $stmt = $pdo->query($sqlThongKe);
} else {
    // Ban tổ chức chỉ xem thống kê sự kiện của CLB mình quản lý.
    $sqlThongKe = "
        SELECT
            COUNT(DISTINCT s.id) AS tong_su_kien,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'cong_khai'
                 AND (s.thoi_gian_ket_thuc IS NULL
                      OR s.thoi_gian_ket_thuc > NOW())
                THEN s.id
            END) AS su_kien_cong_khai,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'ket_thuc'
                  OR (s.thoi_gian_ket_thuc IS NOT NULL
                      AND s.thoi_gian_ket_thuc <= NOW())
                THEN s.id
            END) AS su_kien_da_ket_thuc,
            COUNT(DISTINCT CASE
                WHEN s.trang_thai = 'da_huy'
                THEN s.id
            END) AS su_kien_da_huy,
            COUNT(DISTINCT CASE
                WHEN dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
                THEN dk.id
            END) AS tong_luot_dang_ky,
            COUNT(DISTINCT CASE
                WHEN dk.trang_thai = 'da_tham_gia'
                THEN dk.id
            END) AS tong_luot_tham_gia
        FROM cau_lac_bo clb
        LEFT JOIN su_kien s ON s.cau_lac_bo_id = clb.id
        LEFT JOIN dang_ky dk ON dk.su_kien_id = s.id
        WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
    ";

    $stmt = $pdo->prepare($sqlThongKe);
    $stmt->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);
}

$thongKe = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$giaTriMacDinh = [
    'tong_su_kien' => 0,
    'su_kien_cong_khai' => 0,
    'su_kien_da_ket_thuc' => 0,
    'su_kien_da_huy' => 0,
    'tong_luot_dang_ky' => 0,
    'tong_luot_tham_gia' => 0
];


$thongKe = array_merge($giaTriMacDinh, $thongKe);

// Tinh ty le tham du.
$tyLeThamDu = (int) $thongKe['tong_luot_dang_ky'] > 0
    ? round(
        (int) $thongKe['tong_luot_tham_gia']
        / (int) $thongKe['tong_luot_dang_ky'] * 100
    )
    : 0;

// Lay 5 su kien cong khai sap dien ra.
$sqlSapDienRa = "
    SELECT
        s.id,
        s.ten_su_kien,
        s.dia_diem,
        s.thoi_gian_bat_dau,
        s.so_luong_toi_da,
        COUNT(
            CASE
                WHEN dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
                THEN 1
            END
        ) AS so_nguoi_dang_ky
    FROM su_kien s
    LEFT JOIN dang_ky dk ON dk.su_kien_id = s.id
";

if ($laAdmin) {
    $sqlSapDienRa .= "
        WHERE s.trang_thai = 'cong_khai'
          AND s.thoi_gian_bat_dau >= NOW()
    ";
    $stmtSapDienRa = $pdo->query(
        $sqlSapDienRa . "
        GROUP BY s.id
        ORDER BY s.thoi_gian_bat_dau ASC
        LIMIT 5
        "
    );
} else {
    $sqlSapDienRa .= "
        INNER JOIN cau_lac_bo clb ON clb.id = s.cau_lac_bo_id
        WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
          AND s.trang_thai = 'cong_khai'
          AND s.thoi_gian_bat_dau >= NOW()
    ";
    $stmtSapDienRa = $pdo->prepare(
        $sqlSapDienRa . "
        GROUP BY s.id
        ORDER BY s.thoi_gian_bat_dau ASC
        LIMIT 5
        "
    );
    $stmtSapDienRa->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);
}

$suKienSapDienRa = $stmtSapDienRa->fetchAll(PDO::FETCH_ASSOC);
// Lay thong ke dang ky theo 6 thang gan nhat.
$sqlBieuDo = "
    SELECT
        DATE_FORMAT(dk.thoi_gian_dang_ky, '%Y-%m') AS thang,
        COUNT(dk.id) AS so_luot
    FROM dang_ky dk
    INNER JOIN su_kien s ON s.id = dk.su_kien_id
";

if (!$laAdmin) {
    $sqlBieuDo .= "
        INNER JOIN cau_lac_bo clb ON clb.id = s.cau_lac_bo_id
        WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
          AND dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
    ";
} else {
    $sqlBieuDo .= "
        WHERE dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')
    ";
}

$sqlBieuDo .= "
    AND dk.thoi_gian_dang_ky >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY DATE_FORMAT(dk.thoi_gian_dang_ky, '%Y-%m')
    ORDER BY thang ASC
";

$stmtBieuDo = $laAdmin
    ? $pdo->query($sqlBieuDo)
    : $pdo->prepare($sqlBieuDo);

if (!$laAdmin) {
    $stmtBieuDo->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);
}

$duLieuBieuDo = $stmtBieuDo->fetchAll(PDO::FETCH_ASSOC);


// Lay 5 luot dang ky gan nhat.
$sqlDangKyGanDay = "
    SELECT
        dk.id,
        dk.thoi_gian_dang_ky,
        dk.trang_thai,
        nd.ho_ten,
        s.ten_su_kien
    FROM dang_ky dk
    INNER JOIN nguoi_dung nd ON nd.id = dk.nguoi_dung_id
    INNER JOIN su_kien s ON s.id = dk.su_kien_id
";

if (!$laAdmin) {
    $sqlDangKyGanDay .= "
        INNER JOIN cau_lac_bo clb ON clb.id = s.cau_lac_bo_id
        WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
    ";
}

$sqlDangKyGanDay .= "
    ORDER BY dk.thoi_gian_dang_ky DESC
    LIMIT 5
";

$stmtDangKyGanDay = $laAdmin
    ? $pdo->query($sqlDangKyGanDay)
    : $pdo->prepare($sqlDangKyGanDay);

if (!$laAdmin) {
    $stmtDangKyGanDay->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);
}

$dangKyGanDay = $stmtDangKyGanDay->fetchAll(PDO::FETCH_ASSOC);


// Tao thong bao cho cac su kien sap dien ra hoac gan het cho.
foreach ($suKienSapDienRa as $suKien) {
    $suKienId = (int) $suKien['id'];
    $tenSuKien = $suKien['ten_su_kien'];
    $toiDa = (int) $suKien['so_luong_toi_da'];
    $daDangKy = (int) $suKien['so_nguoi_dang_ky'];
    $conCho = max(0, $toiDa - $daDangKy);

    $ngayBatDau = strtotime($suKien['thoi_gian_bat_dau']);
    $soGiayConLai = $ngayBatDau !== false
        ? $ngayBatDau - time()
        : null;

    // Thong bao khi su kien sap dien ra trong 3 ngay.
    if (
        $soGiayConLai !== null
        && $soGiayConLai >= 0
        && $soGiayConLai <= 3 * 86400
    ) {
        $tieuDe = 'Sự kiện sắp diễn ra';
        $noiDung = 'Sự kiện "' . $tenSuKien
            . '" sẽ bắt đầu trong vòng 3 ngày.';

        $stmtKiemTra = $pdo->prepare("
            SELECT id
            FROM thong_bao
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id = :su_kien_id
              AND loai = :loai
            LIMIT 1
        ");

        $stmtKiemTra->execute([
            'nguoi_dung_id' => (int) $nguoiDung['id'],
            'su_kien_id' => $suKienId,
            'loai' => 'sap_dien_ra'
        ]);

        if (!$stmtKiemTra->fetch()) {
            $stmtTao = $pdo->prepare("
                INSERT INTO thong_bao (
                    nguoi_dung_id,
                    su_kien_id,
                    tieu_de,
                    noi_dung,
                    loai,
                    da_doc,
                    ngay_tao
                ) VALUES (
                    :nguoi_dung_id,
                    :su_kien_id,
                    :tieu_de,
                    :noi_dung,
                    :loai,
                    0,
                    NOW()
                )
            ");

            $stmtTao->execute([
                'nguoi_dung_id' => (int) $nguoiDung['id'],
                'su_kien_id' => $suKienId,
                'tieu_de' => $tieuDe,
                'noi_dung' => $noiDung,
                'loai' => 'sap_dien_ra'
            ]);
        }
    }

    // Thong bao khi su kien con it cho.
    if (
        $toiDa > 0
        && $daDangKy < $toiDa
        && $daDangKy / $toiDa >= 0.8
    ) {
        $tieuDe = 'Sự kiện sắp hết chỗ';
        $noiDung = 'Sự kiện "' . $tenSuKien
            . '" đã có ' . $daDangKy . '/' . $toiDa
            . ' người đăng ký, chỉ còn ' . $conCho . ' chỗ.';

        $stmtKiemTra = $pdo->prepare("
            SELECT id
            FROM thong_bao
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id = :su_kien_id
              AND loai = :loai
            LIMIT 1
        ");

        $stmtKiemTra->execute([
            'nguoi_dung_id' => (int) $nguoiDung['id'],
            'su_kien_id' => $suKienId,
            'loai' => 'gan_het_cho'
        ]);

        if (!$stmtKiemTra->fetch()) {
            $stmtTao = $pdo->prepare("
                INSERT INTO thong_bao (
                    nguoi_dung_id,
                    su_kien_id,
                    tieu_de,
                    noi_dung,
                    loai,
                    da_doc,
                    ngay_tao
                ) VALUES (
                    :nguoi_dung_id,
                    :su_kien_id,
                    :tieu_de,
                    :noi_dung,
                    :loai,
                    0,
                    NOW()
                )
            ");

            $stmtTao->execute([
                'nguoi_dung_id' => (int) $nguoiDung['id'],
                'su_kien_id' => $suKienId,
                'tieu_de' => $tieuDe,
                'noi_dung' => $noiDung,
                'loai' => 'gan_het_cho'
            ]);
        }
    }
}

$stmtDemThongBao = $pdo->prepare("
    SELECT COUNT(*)
    FROM thong_bao
    WHERE nguoi_dung_id = :nguoi_dung_id
      AND da_doc = 0
");

$stmtDemThongBao->execute([
    'nguoi_dung_id' => (int) $nguoiDung['id']
]);

$soThongBaoChuaDoc = (int) $stmtDemThongBao->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ban tổ chức</title>

</head>
<style>
   

.dashboard-content {
    min-width: 0;
    padding: 28px;
    background: #eef8f2;
    color: #26352e;
}

.eh-welcome {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.eh-eyebrow {
    color: #2f9e75;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.eh-welcome h1 {
    margin: 8px 0;
    color: #26352e;
    font-size: 27px;
    font-weight: 750;
}

.eh-welcome p {
    margin: 0;
    color: #858f89;
    font-size: 13px;
}

.eh-primary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 18px;
    border-radius: 9px;
    background: #2f9e75;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
}

.eh-primary-button:hover {
    background: #25835f;
    color: #fff;
}

/* Thong ke */

.eh-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 22px;
}

.eh-stat-card,
.eh-panel {
    min-width: 0;
    padding: 22px;
    border: 1px solid #e5eee8;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(38, 53, 46, 0.035);
}

.eh-stat-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #858f89;
    font-size: 12px;
    font-weight: 600;
}

.eh-stat-card > strong {
    display: block;
    margin: 16px 0 8px;
    color: #26352e;
    font-size: 30px;
    line-height: 1;
}

.eh-stat-card small {
    color: #999f9b;
    font-size: 11px;
}

.eh-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 17px;
}

.eh-stat-icon.purple {
    color: #2f9e75;
    background: #e5f5ed;
}

.eh-stat-icon.blue {
    color: #438c79;
    background: #e8f4ef;
}

.eh-stat-icon.orange {
    color: #d99a42;
    background: #fff4e4;
}

.eh-stat-icon.green {
    color: #25835f;
    background: #e5f5ed;
}

/* Panel */

.eh-chart-grid,
.eh-bottom-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
    gap: 20px;
    margin-bottom: 22px;
}

.eh-panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 22px;
}

.eh-panel-heading h3 {
    margin: 0 0 7px;
    color: #26352e;
    font-size: 15px;
    font-weight: 750;
}

.eh-panel-heading p {
    margin: 0;
    color: #999f9b;
    font-size: 11px;
}

.eh-panel-badge {
    padding: 7px 10px;
    border-radius: 7px;
    background: #eaf7f1;
    color: #2f9e75;
    font-size: 10px;
    white-space: nowrap;
}

.eh-empty {
    padding: 28px 12px;
    color: #999f9b;
    font-size: 12px;
    text-align: center;
}

/* Bieu do cot */

.eh-bar-chart {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: 15px;
    height: 230px;
    padding: 12px 5px 0;
    border-bottom: 1px solid #e5eee8;
}

.eh-bar-column {
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    height: 100%;
    min-width: 0;
}

.eh-bar-value {
    margin-bottom: 8px;
    color: #858f89;
    font-size: 11px;
}

.eh-bar-track {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    width: 100%;
    height: 78%;
}

.eh-bar {
    width: min(38px, 75%);
    min-height: 4px;
    border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, #70c6a2, #2f9e75);
}

.eh-bar-label {
    margin: 10px 0 12px;
    color: #858f89;
    font-size: 10px;
    white-space: nowrap;
}

/* Bieu do tron */

.eh-donut-area {
    display: flex;
    justify-content: center;
    padding: 5px 0 20px;
}

.eh-donut {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 165px;
    height: 165px;
    border-radius: 50%;
}

.eh-donut-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 112px;
    height: 112px;
    border-radius: 50%;
    background: #fff;
}

.eh-donut-center strong {
    color: #26352e;
    font-size: 27px;
}

.eh-donut-center span {
    margin-top: 3px;
    color: #999f9b;
    font-size: 11px;
}

.eh-legend {
    display: grid;
    gap: 12px;
}

.eh-legend > div {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #858f89;
    font-size: 11px;
}

.eh-legend strong {
    margin-left: auto;
    color: #26352e;
}

.eh-legend-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
}

.eh-legend-dot.purple {
    background: #2f9e75;
}

.eh-legend-dot.teal {
    background: #48b99a;
}

.eh-legend-dot.orange {
    background: #f3a641;
}

.eh-legend-dot.gray {
    background: #a5adba;
}

/* Su kien */

.eh-event-list,
.eh-registration-list {
    display: grid;
}

.eh-event-row {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 15px 0;
    border-bottom: 1px solid #edf2ee;
}

.eh-event-row:first-child {
    padding-top: 0;
}

.eh-event-row:last-child {
    padding-bottom: 0;
    border-bottom: 0;
}

.eh-event-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 43px;
    height: 48px;
    border-radius: 9px;
    background: #e5f5ed;
    color: #2f9e75;
    font-size: 17px;
    font-weight: 750;
}

.eh-event-date small {
    margin-top: 2px;
    font-size: 10px;
    font-weight: 500;
}

.eh-event-info {
    flex: 1;
    min-width: 0;
}

.eh-event-info h4 {
    overflow: hidden;
    margin: 0 0 5px;
    color: #26352e;
    font-size: 12px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.eh-event-info p {
    overflow: hidden;
    margin: 0 0 7px;
    color: #999f9b;
    font-size: 10px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.eh-event-capacity {
    display: flex;
    align-items: center;
    gap: 9px;
}

.eh-capacity-track {
    flex: 1;
    height: 5px;
    overflow: hidden;
    border-radius: 10px;
    background: #e8eee9;
}

.eh-capacity-fill {
    height: 100%;
    border-radius: inherit;
    background: #2f9e75;
}

.eh-event-capacity span {
    color: #858f89;
    font-size: 10px;
    white-space: nowrap;
}

.eh-event-link {
    color: #2f9e75;
    font-size: 20px;
    text-decoration: none;
}

.eh-text-link {
    color: #2f9e75;
    font-size: 11px;
    font-weight: 650;
    text-decoration: none;
    white-space: nowrap;
}

/* Dang ky gan day */

.eh-registration-row {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 14px 0;
    border-bottom: 1px solid #edf2ee;
}

.eh-registration-row:first-child {
    padding-top: 0;
}

.eh-registration-row:last-child {
    padding-bottom: 0;
    border-bottom: 0;
}

.eh-registration-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e5f5ed;
    color: #2f9e75;
    font-size: 13px;
    font-weight: 750;
}

.eh-registration-info {
    flex: 1;
    min-width: 0;
}

.eh-registration-info h4 {
    overflow: hidden;
    margin: 0 0 4px;
    color: #26352e;
    font-size: 12px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.eh-registration-info p {
    overflow: hidden;
    margin: 0 0 4px;
    color: #858f89;
    font-size: 10px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.eh-registration-info small {
    color: #a0a7a2;
    font-size: 10px;
}

.eh-status {
    flex-shrink: 0;
    padding: 6px 8px;
    border-radius: 6px;
    font-size: 10px;
    white-space: nowrap;
}

.eh-status.success {
    background: #e5f5ed;
    color: #25835f;
}

.eh-status.pending {
    background: #fff4e3;
    color: #c28a2c;
}

.eh-status.cancel {
    background: #fff0f0;
    color: #d65d5d;
}

/* Responsive */

@media (max-width: 1200px) {
    .eh-stat-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .eh-chart-grid,
    .eh-bottom-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 650px) {
    .dashboard-content {
        padding: 16px;
    }

    .eh-welcome {
        align-items: flex-start;
        flex-direction: column;
    }

    .eh-welcome h1 {
        font-size: 22px;
    }

    .eh-stat-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .eh-stat-card,
    .eh-panel {
        padding: 16px;
    }

    .eh-registration-row {
        flex-wrap: wrap;
    }

    .eh-status {
        margin-left: 47px;
    }
}
    /* Sua menu trang chu Ban to chuc */
.tk-menu {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .tk-menu a {
            display: flex;
            align-items: center;
            gap: 12px;

            min-height: 46px;
            padding: 11px 13px;
            border-radius: 9px;

            color: #e3ebe5;
            transition: background .2s, color .2s;
        }

        .tk-menu a:hover,
        .tk-menu a.active {
            background: rgba(255, 255, 255, .13);
            color: #fff;
        }

        .tk-menu a.active {
    box-shadow: inset 3px 0 var(--gold);
    font-weight: 650;
}

        .tk-menu-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .tk-menu-icon svg {
            width: 21px;
            height: 21px;
            stroke: currentColor;
        }
</style>
<body>
<div class="layout-ban-to-chuc">

    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">
    <div class="sidebar-logo-header">
        <h2>
            <?= htmlspecialchars(
                $tenCauLacBo ?? 'Câu lạc bộ',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h2>

        <a href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
   class="sidebar-logout-icon"
   title="Đăng xuất"
   aria-label="Đăng xuất">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="22" height="22"
         viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="1.8"
         stroke-linecap="round"
         stroke-linejoin="round">

        <!-- Cánh cửa -->
        <path d="M13 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7"/>

        <!-- Mũi tên đi ra -->
        <path d="M10 12h11"/>
        <path d="m17 8 4 4-4 4"/>

    </svg>
</a>
    </div>

    <p>HỆ THỐNG QUẢN LÝ</p>
</div>

        <nav class="tk-menu">

            <a class="active"
   href="/Nhom-4-LTW/public/index.php?trang=ban_to_chuc">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m3 10 9-7 9 7"/>
                        <path d="M5 9v12h14V9"/>
                        <path d="M9 21v-7h6v7"/>
                    </svg>
                </span>

                <span>Tổng quan</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </span>

                <span>Sự kiện của CLB</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </span>

                <span>Đăng ký tham gia</span>

            </a>

            <a
                
                href="/Nhom-4-LTW/public/index.php?trang=thong_ke"
            >

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M3 3v18h18"/>
                        <path d="m7 14 4-4 4 4 6-7"/>
                    </svg>
                </span>

                <span>Thống kê sự kiện</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=thanh_vien_clb">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>

                <span>Thành viên CLB</span>

            </a>

        </nav>


    </aside>


    <main class="main-ban-to-chuc">

    <header class="topbar-ban-to-chuc">
        <div class="page-heading">
            <!-- Đã bỏ tiêu đề Tổng quan và lời chào -->
        </div>

        <div class="topbar-actions">
            <a class="notification-link"
       href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
       title="Thông báo"
       aria-label="Thông báo">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="22" height="22"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="1.8"
             stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
            <path d="M10 21h4"/>
        </svg>

        <?php if (($soThongBaoChuaDoc ?? 0) > 0): ?>
            <span class="notification-badge">
                <?= (int) $soThongBaoChuaDoc ?>
            </span>
        <?php endif; ?>
    </a>

            <div class="account-info">
                <div class="account-avatar">
                    <?= htmlspecialchars(
                        mb_substr(
                            $nguoiDung['ho_ten'] ?? 'U',
                            0,
                            1,
                            'UTF-8'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

                <div>
                    <strong>
                        <?= htmlspecialchars(
                            $nguoiDung['ho_ten'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                    <small>Ban tổ chức</small>
                </div>
            </div>
        </div>
    </header>


    <div class="dashboard-content">

    <?php
    // Du lieu bieu do cot: luot dang ky theo thang
    $nhanThang = [];
    $giaTriThang = [];

    foreach ($duLieuBieuDo as $dong) {
        $nhanThang[] = date(
            'm/Y',
            strtotime($dong['thang'] . '-01')
        );

        $giaTriThang[] = (int) $dong['so_luot'];
    }

    $maxDangKy = !empty($giaTriThang)
        ? max($giaTriThang)
        : 1;

    // Du lieu bieu do tron
    $tongSuKien = (int) $thongKe['tong_su_kien'];

    $soCongKhai = (int) $thongKe['su_kien_cong_khai'];
    $soKetThuc = (int) $thongKe['su_kien_da_ket_thuc'];
    $soHuy = (int) $thongKe['su_kien_da_huy'];

    $soKhac = max(
        0,
        $tongSuKien - $soCongKhai - $soKetThuc - $soHuy
    );

    $phanCongKhai = $tongSuKien > 0
        ? $soCongKhai / $tongSuKien * 100
        : 0;

    $phanKetThuc = $tongSuKien > 0
        ? $soKetThuc / $tongSuKien * 100
        : 0;

    $phanHuy = $tongSuKien > 0
        ? $soHuy / $tongSuKien * 100
        : 0;

    $phanKhac = $tongSuKien > 0
        ? $soKhac / $tongSuKien * 100
        : 0;

    $moc1 = $phanCongKhai;
    $moc2 = $moc1 + $phanKetThuc;
    $moc3 = $moc2 + $phanHuy;
    $moc4 = $moc3 + $phanKhac;
    ?>

    <!-- Tieu de dashboard -->
    <section class="eh-welcome">
        <div>
            <span class="eh-eyebrow">DASHBOARD</span>

            <h1>
                Xin chào,
                <?= htmlspecialchars(
                    $nguoiDung['ho_ten'] ?? 'Ban tổ chức',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>!
            </h1>

            <p>
                Theo dõi tình hình sự kiện và hoạt động đăng ký
                của câu lạc bộ.
            </p>
        </div>

        <a
            class="eh-primary-button"
            href="/Nhom-4-LTW/public/index.php?trang=them_su_kien"
        >
            + Tạo sự kiện
        </a>
    </section>


    <!-- 4 the thong ke -->
    <section class="eh-stat-grid">

    <!-- Tổng số sự kiện -->
    <article class="eh-stat-card">
        <div class="eh-stat-heading">
            <span>Tổng số sự kiện</span>

            <span class="eh-stat-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22" height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </span>
        </div>

        <strong><?= (int) $thongKe['tong_su_kien'] ?></strong>
        <small>Tất cả sự kiện được quản lý</small>
    </article>

    <!-- Sự kiện công khai -->
    <article class="eh-stat-card">
        <div class="eh-stat-heading">
            <span>Sự kiện công khai</span>

            <span class="eh-stat-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22" height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M2 12h20"/>
                    <path d="M12 2a15 15 0 0 1 0 20"/>
                    <path d="M12 2a15 15 0 0 0 0 20"/>
                </svg>
            </span>
        </div>

        <strong><?= (int) $thongKe['su_kien_cong_khai'] ?></strong>
        <small>Sự kiện đang mở đăng ký</small>
    </article>

    <!-- Lượt đăng ký -->
    <article class="eh-stat-card">
        <div class="eh-stat-heading">
            <span>Lượt đăng ký</span>

            <span class="eh-stat-icon orange">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22" height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="10" cy="7" r="4"/>
                    <path d="M20 8v6M17 11h6"/>
                </svg>
            </span>
        </div>

        <strong><?= (int) $thongKe['tong_luot_dang_ky'] ?></strong>
        <small>Đăng ký hợp lệ</small>
    </article>

    <!-- Lượt tham gia -->
    <article class="eh-stat-card">
        <div class="eh-stat-heading">
            <span>Lượt tham gia</span>

            <span class="eh-stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22" height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </span>
        </div>

        <strong><?= (int) $thongKe['tong_luot_tham_gia'] ?></strong>
        <small>Đã được điểm danh</small>
    </article>

</section>


    <!-- Bieu do -->
    <section class="eh-chart-grid">

        <!-- Bieu do cot -->
        <div class="eh-panel">
            <div class="eh-panel-heading">
                <div>
                    <h3>Lượt đăng ký</h3>
                    <p>Thống kê theo tháng</p>
                </div>

                <span class="eh-panel-badge">6 tháng gần nhất</span>
            </div>

            <?php if (empty($giaTriThang)): ?>

                <div class="eh-empty">
                    Chưa có dữ liệu đăng ký trong khoảng thời gian này.
                </div>

            <?php else: ?>

                <div class="eh-bar-chart">

                    <?php foreach ($giaTriThang as $i => $soLuong): ?>

                        <?php
                        $chieuCao = $maxDangKy > 0
                            ? max(5, ($soLuong / $maxDangKy) * 100)
                            : 5;
                        ?>

                        <div class="eh-bar-column">

                            <span class="eh-bar-value">
                                <?= $soLuong ?>
                            </span>

                            <div class="eh-bar-track">
                                <div
                                    class="eh-bar"
                                    style="height:<?= $chieuCao ?>%;"
                                ></div>
                            </div>

                            <span class="eh-bar-label">
                                <?= htmlspecialchars(
                                    $nhanThang[$i],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>
        </div>


        <!-- Bieu do tron -->
        <div class="eh-panel">
            <div class="eh-panel-heading">
                <div>
                    <h3>Trạng thái sự kiện</h3>
                    <p>Phân bố sự kiện hiện có</p>
                </div>
            </div>

            <?php if ($tongSuKien > 0): ?>

                <div class="eh-donut-area">

                    <div
                        class="eh-donut"
                        style="background:conic-gradient(
                            #7c5ce7 0% <?= $moc1 ?>%,
                            #20b8a6 <?= $moc1 ?>% <?= $moc2 ?>%,
                            #f3a641 <?= $moc2 ?>% <?= $moc3 ?>%,
                            #a5adba <?= $moc3 ?>% <?= $moc4 ?>%
                        );"
                    >
                        <div class="eh-donut-center">
                            <strong><?= $tongSuKien ?></strong>
                            <span>Sự kiện</span>
                        </div>
                    </div>

                </div>

            <?php else: ?>

                <div class="eh-empty">
                    Chưa có sự kiện để thống kê.
                </div>

            <?php endif; ?>

            <div class="eh-legend">

                <div>
                    <span class="eh-legend-dot purple"></span>
                    <span>Đang công khai</span>
                    <strong><?= $soCongKhai ?></strong>
                </div>

                <div>
                    <span class="eh-legend-dot teal"></span>
                    <span>Đã kết thúc</span>
                    <strong><?= $soKetThuc ?></strong>
                </div>

                <div>
                    <span class="eh-legend-dot orange"></span>
                    <span>Đã hủy</span>
                    <strong><?= $soHuy ?></strong>
                </div>

                <div>
                    <span class="eh-legend-dot gray"></span>
                    <span>Trạng thái khác</span>
                    <strong><?= $soKhac ?></strong>
                </div>

            </div>
        </div>

    </section>


    <!-- Danh sach su kien va dang ky -->
    <section class="eh-bottom-grid">

        <!-- Su kien sap dien ra -->
        <div class="eh-panel">

            <div class="eh-panel-heading">
                <div>
                    <h3>Sự kiện sắp diễn ra</h3>
                    <p>Các sự kiện gần nhất</p>
                </div>

                <a
                    class="eh-text-link"
                    href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb"
                >
                    Xem tất cả →
                </a>
            </div>

            <?php if (empty($suKienSapDienRa)): ?>

                <div class="eh-empty">
                    Hiện chưa có sự kiện sắp diễn ra.
                </div>

            <?php else: ?>

                <div class="eh-event-list">

                    <?php foreach ($suKienSapDienRa as $suKien): ?>

                        <?php
                        $toiDa = (int) $suKien['so_luong_toi_da'];
                        $daDangKy = (int) $suKien['so_nguoi_dang_ky'];

                        $conCho = max(0, $toiDa - $daDangKy);

                        $tyLeLapDay = $toiDa > 0
                            ? min(100, round($daDangKy / $toiDa * 100))
                            : 0;
                        ?>

                        <article class="eh-event-row">

                            <div class="eh-event-date">
                                <?= date(
                                    'd',
                                    strtotime($suKien['thoi_gian_bat_dau'])
                                ) ?>

                                <small>
                                    <?= date(
                                        'm',
                                        strtotime($suKien['thoi_gian_bat_dau'])
                                    ) ?>
                                </small>
                            </div>

                            <div class="eh-event-info">

                                <h4>
                                    <?= htmlspecialchars(
                                        $suKien['ten_su_kien'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h4>

                                <p>
                                    <?= htmlspecialchars(
                                        $suKien['dia_diem'] ?? 'Chưa cập nhật',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <div class="eh-event-capacity">
                                    <div class="eh-capacity-track">
                                        <div
                                            class="eh-capacity-fill"
                                            style="width:<?= $tyLeLapDay ?>%;"
                                        ></div>
                                    </div>

                                    <span>
                                        <?= $daDangKy ?>/<?= $toiDa ?>
                                    </span>
                                </div>

                            </div>

                            <a
                                class="eh-event-link"
                                href="/Nhom-4-LTW/public/index.php?trang=chi_tiet_su_kien&id=<?= (int) $suKien['id'] ?>"
                            >
                                →
                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>


        <!-- Dang ky gan day -->
        <div class="eh-panel">

            <div class="eh-panel-heading">
                <div>
                    <h3>Đăng ký gần đây</h3>
                    <p>5 lượt đăng ký mới nhất</p>
                </div>

                <a
                    class="eh-text-link"
                    href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky"
                >
                    Xem tất cả →
                </a>
            </div>

            <?php if (empty($dangKyGanDay)): ?>

                <div class="eh-empty">
                    Chưa có lượt đăng ký nào.
                </div>

            <?php else: ?>

                <div class="eh-registration-list">

                    <?php foreach ($dangKyGanDay as $dangKy): ?>

                        <?php
                        $trangThai = $dangKy['trang_thai'];

                        if ($trangThai === 'da_tham_gia') {
                            $tenTrangThai = 'Đã tham gia';
                            $classTrangThai = 'success';
                        } elseif ($trangThai === 'da_huy') {
                            $tenTrangThai = 'Đã hủy';
                            $classTrangThai = 'cancel';
                        } else {
                            $tenTrangThai = 'Đã đăng ký';
                            $classTrangThai = 'pending';
                        }
                        ?>

                        <article class="eh-registration-row">

                            <div class="eh-registration-avatar">
                                <?= htmlspecialchars(
                                    mb_substr(
                                        $dangKy['ho_ten'] ?? '?',
                                        0,
                                        1,
                                        'UTF-8'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="eh-registration-info">

                                <h4>
                                    <?= htmlspecialchars(
                                        $dangKy['ho_ten'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h4>

                                <p>
                                    <?= htmlspecialchars(
                                        $dangKy['ten_su_kien'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <small>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($dangKy['thoi_gian_dang_ky'])
                                    ) ?>
                                </small>

                            </div>

                            <span class="eh-status <?= $classTrangThai ?>">
                                <?= $tenTrangThai ?>
                            </span>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>
    </main>
</div>
</body>
</html>
