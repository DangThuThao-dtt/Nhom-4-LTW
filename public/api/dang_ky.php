<?php
// public/api/dang_ky.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['nguoi_dung']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thực hiện.']);
    exit;
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$suKienId = (int) ($_POST['su_kien_id'] ?? 0);

// 2. Kiểm tra dữ liệu đầu vào
if ($suKienId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện không hợp lệ.']);
    exit;
}

// 3. Lấy thông tin sự kiện
$stmt = $pdo->prepare("SELECT * FROM su_kien WHERE id = ?");
$stmt->execute([$suKienId]);
$suKien = $stmt->fetch();

if (!$suKien) {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện không tồn tại.']);
    exit;
}

// 4. Kiểm tra các quy tắc
$now = date('Y-m-d H:i:s');

if ($suKien['trang_thai'] !== 'cong_khai') {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện chưa mở hoặc đã đóng đăng ký.']);
    exit;
}

if ($now >= $suKien['thoi_gian_bat_dau']) {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện đã bắt đầu, không thể đăng ký.']);
    exit;
}

// Đếm số lượng
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS so_luong 
    FROM dang_ky 
    WHERE su_kien_id = ? AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
");
$stmt->execute([$suKienId]);
$ketQua = $stmt->fetch();
$soLuongHienTai = (int) $ketQua['so_luong'];

if ($soLuongHienTai >= $suKien['so_luong_toi_da']) {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện đã hết chỗ.']);
    exit;
}

// Kiểm tra trùng
$stmt = $pdo->prepare("
    SELECT id FROM dang_ky 
    WHERE nguoi_dung_id = ? AND su_kien_id = ? 
    AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
");
$stmt->execute([$nguoiDungId, $suKienId]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn đã đăng ký sự kiện này rồi.']);
    exit;
}

// 5. Thực hiện đăng ký
try {
    $pdo->beginTransaction();

    // Nếu đã hủy trước đó thì khôi phục
    $stmt = $pdo->prepare("
        SELECT id FROM dang_ky 
        WHERE nguoi_dung_id = ? AND su_kien_id = ? AND trang_thai = 'da_huy'
    ");
    $stmt->execute([$nguoiDungId, $suKienId]);
    $daHuy = $stmt->fetch();

    if ($daHuy) {
        $stmt = $pdo->prepare("
            UPDATE dang_ky 
            SET trang_thai = 'da_dang_ky', thoi_gian_dang_ky = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$daHuy['id']]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO dang_ky (nguoi_dung_id, su_kien_id, thoi_gian_dang_ky, trang_thai) 
            VALUES (?, ?, NOW(), 'da_dang_ky')
        ");
        $stmt->execute([$nguoiDungId, $suKienId]);
    }

    $pdo->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Đăng ký sự kiện thành công!',
        'so_luong_con_lai' => $suKien['so_luong_toi_da'] - $soLuongHienTai - 1
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>