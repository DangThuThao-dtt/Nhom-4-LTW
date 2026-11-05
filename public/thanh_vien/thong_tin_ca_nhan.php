
<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/Core/xac_thuc.php';

// Khôi phục đăng nhập nếu người dùng chọn ghi nhớ
nguoiDungHienTai();

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;

// Kiểm tra thành viên đã đăng nhập
if (!$nguoiDung || ($nguoiDung['vai_tro'] ?? '') !== 'thanh_vien') {
    header('Location: /Nhom-4-LTW/public/dang_nhap.php');
    exit;
}

$nguoiDungId = (int) $nguoiDung['id'];

$thongBao = '';
$loaiThongBao = 'error';

// Lấy thông tin mới nhất từ database
$stmt = $pdo->prepare(
    "SELECT id, ho_ten, email, so_dien_thoai, khoa, nganh
     FROM nguoi_dung
     WHERE id = :id
       AND vai_tro = 'thanh_vien'
     LIMIT 1"
);

$stmt->execute(['id' => $nguoiDungId]);

$thongTin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$thongTin) {
    session_unset();
    session_destroy();

    header('Location: /Nhom-4-LTW/public/dang_nhap.php');
    exit;
}

// Hàm hiển thị dữ liệu an toàn
function eThongTinCaNhan($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $hoTen = trim($_POST['ho_ten'] ?? '');
    $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
    $khoa = trim($_POST['khoa'] ?? '');
    $nganh = trim($_POST['nganh'] ?? '');

    if (
        $hoTen === ''
        || $soDienThoai === ''
        || $khoa === ''
        || $nganh === ''
    ) {
        $thongBao = 'Vui lòng nhập đầy đủ thông tin.';
    } else {

        $stmt = $pdo->prepare(
            "UPDATE nguoi_dung
             SET ho_ten = :ho_ten,
                 so_dien_thoai = :so_dien_thoai,
                 khoa = :khoa,
                 nganh = :nganh
             WHERE id = :id
               AND vai_tro = 'thanh_vien'"
        );

        $stmt->execute([
            'ho_ten' => $hoTen,
            'so_dien_thoai' => $soDienThoai,
            'khoa' => $khoa,
            'nganh' => $nganh,
            'id' => $nguoiDungId
        ]);

        // Cập nhật thông tin trong session
        $_SESSION['nguoi_dung']['ho_ten'] = $hoTen;
        $_SESSION['nguoi_dung']['so_dien_thoai'] = $soDienThoai;
        $_SESSION['nguoi_dung']['khoa'] = $khoa;
        $_SESSION['nguoi_dung']['nganh'] = $nganh;

        $thongBao = 'Cập nhật thông tin cá nhân thành công.';
        $loaiThongBao = 'success';

        // Cập nhật dữ liệu hiển thị
        $thongTin['ho_ten'] = $hoTen;
        $thongTin['so_dien_thoai'] = $soDienThoai;
        $thongTin['khoa'] = $khoa;
        $thongTin['nganh'] = $nganh;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thông tin cá nhân | Sự kiện sinh viên</title>

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/style.css?v=7"
    >

    <style>
        
* {
    box-sizing: border-box;
}

body.member-page {
    min-height: 100vh;
    margin: 0;
    display: flex;
    flex-direction: column;
}

/* Form thông tin cá nhân */
.profile-container {
    width: min(900px, calc(100% - 40px));
    margin: 18px auto 24px;
    padding: 32px 40px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
}

.profile-container h1 {
    margin: 0 0 8px;
    font-size: 28px;
    color: #1f2937;
}

.profile-description {
    margin: 0 0 26px;
    color: #6b7280;
    font-size: 15px;
}

.profile-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px 28px;
}

.profile-field {
    display: flex;
    flex-direction: column;
    gap: 9px;
    min-width: 0;
}

.profile-field label {
    font-size: 15px;
    font-weight: 600;
    color: #374151;
}

.profile-field input {
    width: 100%;
    height: 48px;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.profile-field input:focus {
    border-color: #647b6a;
    box-shadow: 0 0 0 3px rgba(82, 107, 91, 0.13);
}

.profile-field input[readonly] {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}

.profile-actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 14px;
    margin-top: 8px;
}

.profile-save,
.profile-back {
    min-height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}

/* Nút cập nhật màu xanh rêu trầm */
.profile-save {
    background: #6b8f71;
    color: #fff;
}

.profile-save:hover {
    background: #5b7d62;
}

.profile-back {
    background: #eef1ef;
    color: #374151;
}

.profile-back:hover {
    background: #e0e5e1;
}

.profile-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.profile-alert.success {
    background: #e4eee6;
    color: #385442;
}

.profile-alert.error {
    background: #fee2e2;
    color: #991b1b;
}

/* Menu tài khoản */
.member-account {
    position: relative;
}

.member-account-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
}

.member-account-menu {
    display: none;
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    z-index: 1000;
    width: 240px;
    padding: 12px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.15);
}

.member-account-menu.show {
    display: block;
}

.member-account-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 8px 10px 12px;
    border-bottom: 1px solid #e5e7eb;
}

.member-account-info strong {
    overflow-wrap: anywhere;
    color: #1f2937;
}

.member-account-info span {
    color: #6b7280;
    font-size: 13px;
}

.member-account-message {
    display: block;
    width: 100%;
    padding: 11px 10px;
    border: none;
    background: transparent;
    color: #374151;
    text-align: left;
    font-size: 14px;
    cursor: pointer;
}

.member-account-message:hover {
    background: #f3f4f6;
    border-radius: 7px;
}

.member-account-logout {
    margin-top: 4px;
}

.member-account-logout button {
    width: 100%;
    padding: 11px 10px;
    border: none;
    border-radius: 7px;
    background: #fee2e2;
    color: #b91c1c;
    text-align: left;
    font-size: 14px;
    cursor: pointer;
}

.member-account-logout button:hover {
    background: #fecaca;
}

.member-footer {
    margin-top: auto;
}

/* Màn hình nhỏ */
@media (max-width: 600px) {
    .profile-container {
        width: calc(100% - 24px);
        margin: 12px auto 20px;
        padding: 22px 18px;
    }

    .profile-container h1 {
        font-size: 23px;
    }

    .profile-description {
        margin-bottom: 20px;
    }

    .profile-form {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .profile-actions {
        flex-direction: column;
    }

    .profile-save,
    .profile-back {
        width: 100%;
    }
}

    </style>
</head>

<body class="member-page">

<header class="member-navbar">

    <a
        class="member-logo"
        href="/Nhom-4-LTW/public/index.php?trang=trang-chu"
    >
        ✦ Sự kiện sinh viên
    </a>

    <nav class="member-nav-links">

        <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
            Trang chủ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien">
            Sự kiện
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
            Câu lạc bộ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi">
            Đăng ký của tôi
        </a>

        <!-- Menu tài khoản -->
        <div class="member-account">

            <button
                type="button"
                class="member-account-btn"
                id="memberAccountBtn"
                aria-label="Tài khoản"
                aria-expanded="false"
                aria-controls="memberAccountMenu"
            >
                <span class="member-account-icon">♙</span>
            </button>

            <div
                class="member-account-menu"
                id="memberAccountMenu"
            >

                <div class="member-account-info">

                    <strong>
                        <?= eThongTinCaNhan(
                            $nguoiDung['ho_ten'] ?? 'Thành viên'
                        ) ?>
                    </strong>

                    <span>Tài khoản thành viên</span>

                </div>

                <button
                    type="button"
                    class="member-account-message"
                    id="memberProfileBtn"
                >
                    Thông tin cá nhân
                </button>

                <!-- Đăng xuất -->
                <form
                    method="POST"
                    action="/Nhom-4-LTW/app/Auth/dang_xuat.php"
                    class="member-account-logout"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            taoCsrfToken(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <button type="submit">
                        Đăng xuất
                    </button>

                </form>

            </div>

        </div>

    </nav>

</header>

<main class="profile-container">

    <h1>Thông tin cá nhân</h1>

    <p class="profile-description">
        Xem và cập nhật thông tin tài khoản của bạn.
    </p>

    <?php if ($thongBao !== ''): ?>

        <div class="profile-alert <?= eThongTinCaNhan($loaiThongBao) ?>">
            <?= eThongTinCaNhan($thongBao) ?>
        </div>

    <?php endif; ?>

    <form method="POST" class="profile-form">

        <div class="profile-field">

            <label for="ho_ten">Họ và tên</label>

            <input
                type="text"
                id="ho_ten"
                name="ho_ten"
                value="<?= eThongTinCaNhan($thongTin['ho_ten']) ?>"
                required
            >

        </div>

        <div class="profile-field">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                value="<?= eThongTinCaNhan($thongTin['email']) ?>"
                readonly
            >

        </div>

        <div class="profile-field">

            <label for="so_dien_thoai">Số điện thoại</label>

            <input
                type="tel"
                id="so_dien_thoai"
                name="so_dien_thoai"
                value="<?= eThongTinCaNhan($thongTin['so_dien_thoai']) ?>"
                required
            >

        </div>

        <div class="profile-field">

            <label for="khoa">Khoa</label>

            <input
                type="text"
                id="khoa"
                name="khoa"
                value="<?= eThongTinCaNhan($thongTin['khoa']) ?>"
                required
            >

        </div>

        <div class="profile-field">

            <label for="nganh">Ngành</label>

            <input
                type="text"
                id="nganh"
                name="nganh"
                value="<?= eThongTinCaNhan($thongTin['nganh']) ?>"
                required
            >

        </div>

        <div class="profile-actions">

            <button type="submit" class="profile-save">
                Cập nhật thông tin
            </button>

            <a
                href="/Nhom-4-LTW/public/index.php?trang=trang-chu"
                class="profile-back"
            >
                Quay lại
            </a>

        </div>

    </form>

</main>

<footer class="member-footer">
    Sự kiện sinh viên · Kết nối cộng đồng, lan tỏa trải nghiệm
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const accountBtn = document.getElementById('memberAccountBtn');
    const accountMenu = document.getElementById('memberAccountMenu');
    const profileBtn = document.getElementById('memberProfileBtn');

    if (!accountBtn || !accountMenu) {
        return;
    }

    // Bấm biểu tượng tài khoản để mở hoặc đóng menu
    accountBtn.addEventListener('click', function (event) {

        event.stopPropagation();

        const isOpen = accountMenu.classList.toggle('show');

        accountBtn.setAttribute(
            'aria-expanded',
            isOpen ? 'true' : 'false'
        );

    });

    // Bấm bên ngoài thì đóng menu
    document.addEventListener('click', function (event) {

        if (
            !accountBtn.contains(event.target) &&
            !accountMenu.contains(event.target)
        ) {

            accountMenu.classList.remove('show');

            accountBtn.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    });

    // Nút thông tin cá nhân
    if (profileBtn) {

        profileBtn.addEventListener('click', function () {

            window.location.href =
                '/Nhom-4-LTW/public/thanh_vien/thong_tin_ca_nhan.php';

        });

    }

});
</script>

</body>
</html>
