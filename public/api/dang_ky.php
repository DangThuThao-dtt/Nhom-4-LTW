<?php
// public/api/dang_ky.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../app/Controllers/DangKyController.php';

if (!isset($_SESSION['nguoi_dung']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thực hiện.']);
    exit;
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$suKienId = (int) ($_POST['su_kien_id'] ?? 0);

if ($suKienId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sự kiện không hợp lệ.']);
    exit;
}

$controller = new DangKyController($pdo);
$result = $controller->dangKy($nguoiDungId, $suKienId);
echo json_encode($result);
?>