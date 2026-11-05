
<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}

require_once __DIR__ . '/ket_noi_csdl.php';

/**
 * Lấy thông tin người dùng đang đăng nhập từ session.
 */
function nguoiDungHienTai(): ?array
{
    return $_SESSION['nguoi_dung'] ?? null;
}

/**
 * Yêu cầu người dùng đăng nhập.
 */
function yeuCauDangNhap(): void
{
    if (nguoiDungHienTai() === null) {
        header('Location: /Nhom-4-LTW/public/dang_nhap.php');
        exit;
    }
}

/**
 * Kiểm tra vai trò người dùng.
 */
function yeuCauVaiTro(array $vaiTroChoPhep): void
{
    yeuCauDangNhap();

    $nguoiDung = nguoiDungHienTai();

    if (
        $nguoiDung === null ||
        !in_array($nguoiDung['vai_tro'] ?? '', $vaiTroChoPhep, true)
    ) {
        http_response_code(403);
        exit('Ban khong co quyen truy cap chuc nang nay.');
    }
}

/**
 * Tạo hoặc lấy CSRF token của phiên đăng nhập hiện tại.
 */
function taoCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Kiểm tra CSRF token gửi từ biểu mẫu POST.
 */
function kiemTraCsrfToken(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong duoc phep.');
    }

    $tokenGuiLen = $_POST['csrf_token'] ?? '';
    $tokenTrongSession = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($tokenGuiLen) ||
        !is_string($tokenTrongSession) ||
        $tokenTrongSession === '' ||
        !hash_equals($tokenTrongSession, $tokenGuiLen)
    ) {
        http_response_code(403);
        exit('Yeu cau khong hop le. Vui long tai lai trang va thu lai.');
    }
}
