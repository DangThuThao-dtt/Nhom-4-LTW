
<?php

session_start();

require_once __DIR__ . '/../app/Controllers/QuanLyAdminController.php';
require_once __DIR__ . '/../app/Controllers/QuanLyTaiKhoanController.php';

// Xac dinh trang can hien thi
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'dashboard':
        $controller = new QuanLyAdminController();
        $controller->dashboard();
        break;

    case 'cau-lac-bo':
        $controller = new QuanLyAdminController();
        $controller->danhSachCauLacBo();
        break;

    case 'tai-khoan':
    $controller = new QuanLyTaiKhoanController();
    $controller->danhSach();
    break;
    
    case 'them-cau-lac-bo':
        $controller = new QuanLyAdminController();
        $controller->themCauLacBo();
        break;

    case 'sua-cau-lac-bo':
        $controller = new QuanLyAdminController();
        $controller->suaCauLacBo();
        break;

    case 'xoa-cau-lac-bo':
    $controller = new QuanLyAdminController();
    $controller->xoaCauLacBo();
    break;

    case 'home':
    default:
        echo "Nhom 4 - He thong quan ly su kien sinh vien";
        break;
}
