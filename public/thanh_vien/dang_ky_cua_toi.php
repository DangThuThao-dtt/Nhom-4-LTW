<?php
// public/thanh_vien/dang_ky_cua_toi.php
session_start();
require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../app/Controllers/DangKyController.php';

if (!isset($_SESSION['nguoi_dung']['id'])) {
    exit('Ban can dang nhap de xem danh sach dang ky.');
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$tuKhoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';

$controller = new DangKyController($pdo);
$data = $controller->index($nguoiDungId, $tuKhoa);

extract($data);
require_once __DIR__ . '/dang_ky_cua_toi_view.php';
?>