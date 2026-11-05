
<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

// ==========================================
// KHỞI ĐỘNG SESSION
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// NẠP FILE XÁC THỰC
// ==========================================

require_once __DIR__ . '/../app/Core/xac_thuc.php';

// Khôi phục đăng nhập từ cookie nếu người dùng chọn ghi nhớ
nguoiDungHienTai();

// ==========================================
// NẠP CONTROLLER
// ==========================================

// Controller dành cho Thành viên
require_once __DIR__ . '/../app/Controllers/su_kien_controller.php';
require_once __DIR__ . '/../app/Controllers/cau_lac_bo_controller.php';
require_once __DIR__ . '/../app/Controllers/dang_ky_controller.php';

// Controller dành cho Admin và Ban tổ chức
require_once __DIR__ . '/../app/Controllers/SuKienController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';

// ==========================================
// KHỞI TẠO CONTROLLER
// ==========================================

$controller = new SuKienThanhVienController($pdo);

$suKienController = new SuKienController($pdo);

$adminController = new AdminController();

// ==========================================
// LẤY THAM SỐ ĐIỀU HƯỚNG
// ==========================================

$trang = $_GET['trang'] ?? '';
$action = $_GET['action'] ?? '';

// ==========================================
// ĐĂNG XUẤT
// ==========================================

if ($trang === 'dang_xuat') {
    require_once __DIR__ . '/../app/Auth/dang_xuat.php';
    exit;
}

// ==========================================
// ĐIỀU HƯỚNG TRANG THÀNH VIÊN
// ==========================================

switch ($trang) {

    // Trang chủ Thành viên
    case 'trang-chu':
        $controller->trangChu();
        exit;

    // Danh sách sự kiện
    case 'su-kien':
        $controller->danhSach();
        exit;

    // Chi tiết sự kiện
    case 'chi-tiet':
        $controller->chiTiet();
        exit;

    // Danh sách câu lạc bộ
    case 'cau-lac-bo':
        $controllerCLB = new CauLacBoThanhVienController($pdo);
        $controllerCLB->danhSach();
        exit;

    // Tham gia câu lạc bộ
    case 'tham-gia-clb':
        $controllerCLB = new CauLacBoThanhVienController($pdo);
        $controllerCLB->thamGia();
        exit;

    // Rời câu lạc bộ
    case 'roi-clb':
        $controllerCLB = new CauLacBoThanhVienController($pdo);
        $controllerCLB->roiCLB();
        exit;

    // Danh sách đăng ký của tôi
    case 'dang-ky-cua-toi':
        $controllerDK = new DangKyController($pdo);
        $controllerDK->danhSachCuaNguoiDung();
        exit;
}

// ==========================================
// ĐIỀU HƯỚNG TRANG THEO VAI TRÒ
// ==========================================

// Trang chủ Admin
if ($trang === 'admin') {
    yeuCauVaiTro(['admin']);

    require __DIR__ . '/../app/Views/admin/dashboard.php';
    exit;
}

// Trang chủ Ban tổ chức
if ($trang === 'ban_to_chuc') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    require __DIR__ . '/../app/Views/ban_to_chuc/trang_chu.php';
    exit;
}

// Thông báo Ban tổ chức
if ($trang === 'thong_bao') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    require __DIR__ . '/../app/Views/ban_to_chuc/thong_bao.php';
    exit;
}

// Trang chủ Thành viên
if ($trang === 'thanh_vien') {
    yeuCauVaiTro(['thanh_vien']);

    require __DIR__ . '/../app/Views/thanh_vien/trang_chu.php';
    exit;
}

// ==========================================
// CHỨC NĂNG SỰ KIỆN CỦA BAN TỔ CHỨC
// ==========================================

// Trang thêm sự kiện
if ($trang === 'them_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    require __DIR__ . '/../app/Views/ban_to_chuc/them_su_kien.php';
    exit;
}

// Lưu sự kiện mới
if ($trang === 'luu_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $suKienController->them();
    exit;
}

// Sửa sự kiện
if ($trang === 'sua_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $suKienController->sua();
    exit;
}

// Chi tiết sự kiện
if ($trang === 'chi_tiet_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $suKienController->chiTiet();
    exit;
}

// Danh sách đăng ký sự kiện
if ($trang === 'danh_sach_dang_ky') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $suKienController->danhSachDangKy();
    exit;
}

// Điểm danh
if ($trang === 'diem_danh') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $controller->diemDanh();
    exit;
}

// Lưu sửa sự kiện
if ($trang === 'luu_sua_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $suKienController->capNhat();
    exit;
}

// Xóa sự kiện
if ($trang === 'xoa_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $suKienController->xoa();
    exit;
}

// Hủy sự kiện
if ($trang === 'huy_su_kien') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    $suKienController->huy();
    exit;
}

// ==========================================
// DANH SÁCH SỰ KIỆN CỦA CLB - BAN TỔ CHỨC
// ==========================================

if ($trang === 'danh_sach_su_kien_clb') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $suKienController->danhSach();
    exit;
}
// ==========================================
// THỐNG KÊ SỰ KIỆN - BAN TỔ CHỨC
// ==========================================

if ($trang === 'thong_ke') {
    yeuCauVaiTro(['ban_to_chuc']);

    $suKienController->thongKe();
    exit;
}
// ==========================================
// THÀNH VIÊN CÂU LẠC BỘ - BAN TỔ CHỨC
// ==========================================

if ($trang === 'thanh_vien_clb') {
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    require __DIR__ . '/../app/Views/ban_to_chuc/thanh_vien_clb.php';
    exit;
}

// ==========================================
// CHỨC NĂNG ADMIN
// ==========================================

if ($action !== '') {

    yeuCauVaiTro(['admin']);

    switch ($action) {

        // Dashboard
        case 'dashboard':
            $adminController->dashboard();
            exit;

        // ----------------------------------
        // QUẢN LÝ SỰ KIỆN
        // ----------------------------------

        case 'quan-ly-su-kien':
            $adminController->danhSachSuKien();
            exit;

        case 'them-su-kien':
            $adminController->themSuKien();
            exit;

        case 'cap-nhat-su-kien':
            $adminController->capNhatSuKien();
            exit;

        case 'doi-trang-thai-su-kien':
            $adminController->doiTrangThaiSuKien();
            exit;

        case 'xoa-su-kien':
            $adminController->xoaSuKien();
            exit;

        case 'chi-tiet-su-kien':
            $adminController->xemChiTietSuKien();
            exit;

        // ----------------------------------
        // QUẢN LÝ CÂU LẠC BỘ
        // ----------------------------------

        case 'quan-ly-cau-lac-bo':
            $adminController->danhSachCauLacBo();
            exit;

        case 'them-cau-lac-bo':
            $adminController->themCauLacBo();
            exit;

        case 'sua-cau-lac-bo':
            $adminController->suaCauLacBo();
            exit;

        case 'cap-nhat-cau-lac-bo':
            $adminController->capNhatCauLacBo();
            exit;

        case 'xoa-cau-lac-bo':
            $adminController->xoaCauLacBo();
            exit;

        // ----------------------------------
        // QUẢN LÝ TÀI KHOẢN BAN TỔ CHỨC
        // ----------------------------------

        case 'quan-ly-tai-khoan':
            $adminController->danhSachTaiKhoan();
            exit;

        case 'them-tai-khoan':
            $adminController->themTaiKhoan();
            exit;

        case 'cap-nhat-tai-khoan':
            $adminController->capNhatTaiKhoan();
            exit;

        case 'dat-lai-mat-khau-tai-khoan':
            $adminController->datLaiMatKhauTaiKhoan();
            exit;

        case 'tu-dong-tao-tai-khoan':
            $adminController->tuDongTaoTaiKhoan();
            exit;

        case 'xoa-tai-khoan':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Phuong thuc khong hop le.');
            }

            $adminController->xoaTaiKhoan();
            exit;

        // ----------------------------------
        // QUẢN LÝ TÀI KHOẢN THÀNH VIÊN
        // ----------------------------------

        case 'quan-ly-tai-khoan-thanh-vien':
            $adminController->danhSachTaiKhoanThanhVien();
            exit;

        // ----------------------------------
        // YÊU CẦU ĐẶT LẠI MẬT KHẨU
        // ----------------------------------

        case 'yeu-cau-dat-lai-mat-khau':
            $adminController->danhSachYeuCauDatLaiMatKhau();
            exit;

        // ----------------------------------
        // THỐNG KÊ
        // ----------------------------------

        case 'thong-ke':
            $adminController->hienThiThongKe();
            exit;

        // ----------------------------------
        // KHÔNG TÌM THẤY CHỨC NĂNG
        // ----------------------------------

        default:
            http_response_code(404);
            exit('Không tìm thấy chức năng quản trị.');
    }
}

// ==========================================
// MẶC ĐỊNH: TRANG CHỦ
// ==========================================

if ($trang === '') {
    header('Location: /Nhom-4-LTW/public/index.php?trang=trang-chu');
    exit;
}

// Mặc định hiển thị danh sách sự kiện
$controller->danhSach();
