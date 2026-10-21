<?php

session_start();

require_once __DIR__ . '/../app/Controllers/AdminController.php';

$action = $_GET['action'] ?? 'quan-ly-su-kien';

$admin = new AdminController();

switch ($action) {
    // Quan ly su kien
    case 'quan-ly-su-kien':
        $admin->danhSachSuKien();
        break;

    case 'them-su-kien':
        $admin->themSuKien();
        break;

    case 'cap-nhat-su-kien':
        $admin->capNhatSuKien();
        break;

    case 'doi-trang-thai-su-kien':
        $admin->doiTrangThaiSuKien();
        break;

    case 'xoa-su-kien':
        $admin->xoaSuKien();
        break;

    case 'chi-tiet-su-kien':
        $admin->xemChiTietSuKien();
        break;

    case 'thong-ke':
        $admin->hienThiThongKe();
        break;

    case 'quan-ly-tai-khoan':
        $admin->danhSachTaiKhoan();
        break;

    case 'them-tai-khoan':
        $admin->themTaiKhoan();
        break;

    case 'cap-nhat-tai-khoan':
        $admin->capNhatTaiKhoan();
        break;

    case 'dat-lai-mat-khau-tai-khoan':
        $admin->datLaiMatKhauTaiKhoan();
        break;

    default:
        http_response_code(404);
        echo 'Khong tim thay chuc nang.';
        break;
}
