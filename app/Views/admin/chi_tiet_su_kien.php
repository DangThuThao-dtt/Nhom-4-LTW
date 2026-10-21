
<?php
$e = static fn($value) => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$sk = $chiTietSuKien;
$sucChua = max(0, (int) ($sk['so_luong_toi_da'] ?? 0));
$daDangKy = max(0, (int) ($sk['so_luot_dang_ky'] ?? 0));
$phanTram = $sucChua > 0
    ? min(100, round($daDangKy / $sucChua * 100))
    : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết sự kiện</title>
</head>
<body>
    <h1><?= $e($sk['ten_su_kien']) ?></h1>
    <p>Câu lạc bộ: <?= $e($sk['ten_cau_lac_bo']) ?></p>
    <p>Địa điểm: <?= $e($sk['dia_diem']) ?></p>
    <p>Bắt đầu: <?= $e($sk['thoi_gian_bat_dau']) ?></p>
    <p>Kết thúc: <?= $e($sk['thoi_gian_ket_thuc']) ?></p>
    <p>Trạng thái: <?= $e($sk['trang_thai']) ?></p>
    <p>Mô tả: <?= nl2br($e($sk['mo_ta'])) ?></p>

    <h2>Tình trạng đăng ký</h2>
    <p><?= $daDangKy ?> / <?= $sucChua ?> lượt đăng ký</p>
    <p>Đã sử dụng <?= $phanTram ?>% sức chứa</p>

    <?php if ($sucChua > 0 && $daDangKy >= $sucChua): ?>
        <p>Sự kiện đã đủ chỗ.</p>
    <?php endif; ?>

    <p>
        <a href="index.php?action=quan-ly-su-kien">
            Quay lại danh sách
        </a>
    </p>
</body>
</html>
