<?php
// public/thanh_vien/dang_ky_cua_toi.php (FILE RUỘT)
session_start();
require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['nguoi_dung']['id'])) {
    exit('Ban can dang nhap de xem danh sach dang ky.');
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$tuKhoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';

// 2. Truy vấn dữ liệu
$sql = "
    SELECT dk.id AS dang_ky_id, dk.thoi_gian_dang_ky, dk.trang_thai AS trang_thai_dang_ky,
           sk.id AS su_kien_id, sk.ten_su_kien, sk.dia_diem, sk.thoi_gian_bat_dau, clb.ten_cau_lac_bo
    FROM dang_ky dk
    JOIN su_kien sk ON dk.su_kien_id = sk.id
    JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
    WHERE dk.nguoi_dung_id = :nguoi_dung_id
";

$params = ['nguoi_dung_id' => $nguoiDungId];

if ($tuKhoa !== '') {
    $sql .= " AND (sk.ten_su_kien LIKE :tu_khoa OR clb.ten_cau_lac_bo LIKE :tu_khoa_2)";
    $params['tu_khoa'] = '%' . $tuKhoa . '%';
    $params['tu_khoa_2'] = '%' . $tuKhoa . '%';
}

$sql .= " ORDER BY dk.thoi_gian_dang_ky DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$danhSachDangKy = $stmt->fetchAll();

// 3. GỌI FILE VỎ (Hiện tại là file vỏ tạm của bạn)
// Sau này người 2 làm xong, bạn chỉ cần đổi tên file ở dòng dưới là xong!
require_once __DIR__ . '/dang_ky_cua_toi_view.php';
?>