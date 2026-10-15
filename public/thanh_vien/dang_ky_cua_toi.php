
<?php
session_start();

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';


if (!isset($_SESSION['nguoi_dung']['id'])) {
    exit('Ban can dang nhap de xem danh sach dang ky.');
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];


$sql = "
    SELECT
        dk.id AS dang_ky_id,
        dk.thoi_gian_dang_ky,
        dk.trang_thai AS trang_thai_dang_ky,
        sk.ten_su_kien,
        sk.dia_diem,
        sk.thoi_gian_bat_dau,
        clb.ten_cau_lac_bo
    FROM dang_ky dk
    JOIN su_kien sk ON dk.su_kien_id = sk.id
    JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
    WHERE dk.nguoi_dung_id = :nguoi_dung_id
    ORDER BY dk.thoi_gian_dang_ky DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
$danhSachDangKy = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dang ky cua toi</title>
</head>
<body>
    <h1>Danh sach dang ky cua toi</h1>

    <?php if (empty($danhSachDangKy)): ?>
        <p>Ban chua dang ky su kien nao.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Ten su kien</th>
                    <th>Cau lac bo</th>
                    <th>Dia diem</th>
                    <th>Thoi gian bat dau</th>
                    <th>Ngay dang ky</th>
                    <th>Trang thai dang ky</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachDangKy as $dk): ?>
                    <tr>
                        <td><?= htmlspecialchars($dk['ten_su_kien']) ?></td>
                        <td><?= htmlspecialchars($dk['ten_cau_lac_bo']) ?></td>
                        <td><?= htmlspecialchars($dk['dia_diem'] ?? '') ?></td>
                        <td><?= htmlspecialchars($dk['thoi_gian_bat_dau']) ?></td>
                        <td><?= htmlspecialchars($dk['thoi_gian_dang_ky']) ?></td>
                        <td><?= htmlspecialchars($dk['trang_thai_dang_ky']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
