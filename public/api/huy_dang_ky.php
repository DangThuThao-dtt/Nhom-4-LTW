<?php
// public/api/huy_dang_ky.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../app/Controllers/DangKyController.php';

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

$controller = new DangKyController($pdo);
$result = $controller->huyDangKy($nguoiDungId, $dangKyId);
echo json_encode($result);
?>