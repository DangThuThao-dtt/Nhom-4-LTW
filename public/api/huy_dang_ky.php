<?php
// public/api/huy_dang_ky.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['nguoi_dung']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thực hiện.']);
    exit;
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$dangKyId = (int) ($_POST['dang_ky_id'] ?? 0);

if ($dangKyId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Đăng ký không hợp lệ.']);
    exit;
}

// 2. Lấy thông tin đăng ký + kiểm tra quyền sở hữu
$sql = "SELECT dk.*, sk.thoi_gian_bat_dau 
        FROM dang_ky dk 
        JOIN su_kien sk ON dk.su_kien_id = sk.id 
        WHERE dk.id = ? AND dk.nguoi_dung_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dangKyId, $nguoiDungId]);
$dangKy = $stmt->fetch();

if (!$dangKy) {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đăng ký hoặc bạn không có quyền hủy.']);
    exit;
}

// 3. Kiểm tra trạng thái
if ($dangKy['trang_thai'] !== 'da_dang_ky') {
    echo json_encode(['status' => 'error', 'message' => 'Đăng ký này không thể hủy (đã hủy hoặc đã tham gia).']);
    exit;
}

// 4. Kiểm tra hạn hủy (phải hủy trước 24h)
$now = time();
$thoiGianBatDau = strtotime($dangKy['thoi_gian_bat_dau']);
$soGiayConLai = $thoiGianBatDau - $now;

if ($soGiayConLai < 86400) { // 24h = 86400 giây
    echo json_encode(['status' => 'error', 'message' => 'Đã quá hạn hủy đăng ký (phải hủy trước 24 giờ so với thời gian bắt đầu).']);
    exit;
}

// 5. Thực hiện hủy
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE dang_ky SET trang_thai = 'da_huy' WHERE id = ?");
    $stmt->execute([$dangKyId]);

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Hủy đăng ký thành công!']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>