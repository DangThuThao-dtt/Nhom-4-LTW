<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;
$trangHienTai = 'trang-chu';

function hienThiTrangChu($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sự kiện sinh viên - Trang chủ</title>

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/style.css?v=7"
    >
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

        <a
            href="/Nhom-4-LTW/public/index.php?trang=trang-chu"
            class="<?= $trangHienTai === 'trang-chu'
                ? 'member-nav-active'
                : '' ?>"
        >
            Trang chủ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien">
            Sự kiện
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
            Câu lạc bộ
        </a>

        <?php if ($nguoiDung): ?>

            <a href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi">
                Đăng ký của tôi
            </a>

            <!-- Khu vực tài khoản -->
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

                <!-- Menu tài khoản -->
                <div
                    class="member-account-menu"
                    id="memberAccountMenu"
                >

                    <div class="member-account-info">

                        <strong>
                            <?= hienThiTrangChu(
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

        <?php else: ?>

            <a href="/Nhom-4-LTW/public/dang_nhap.php">
                Đăng nhập
            </a>

            <a
                class="member-register-link"
                href="/Nhom-4-LTW/public/dang_ky.php"
            >
                Đăng ký
            </a>

        <?php endif; ?>

    </nav>

</header>

<!-- BANNER -->
<section class="member-hero">

    <div class="member-hero-content">

        <h1>
            Khám phá các sự kiện<br>
            của sinh viên
        </h1>

        <p>Kết nối · Học hỏi · Trải nghiệm</p>

        <!-- Tìm kiếm sự kiện -->
        <form
            action="index.php"
            method="GET"
            class="member-search"
        >

            <input
                type="hidden"
                name="trang"
                value="trang-chu"
            >

            <input
                type="text"
                name="tu_khoa"
                placeholder="Tìm kiếm sự kiện..."
                aria-label="Tìm kiếm sự kiện"
                value="<?= hienThiTrangChu(
                    $_GET['tu_khoa'] ?? ''
                ) ?>"
            >

            <button type="submit">
                Tìm kiếm
            </button>

        </form>

    </div>

</section>

<!-- DANH SÁCH SỰ KIỆN -->
<main class="member-container">

    <div class="member-section-heading">

        <div>

            <?php if (!empty($_GET['tu_khoa'])): ?>

                <h2>Kết quả tìm kiếm</h2>

                <p>
                    Từ khóa:
                    <?= hienThiTrangChu($_GET['tu_khoa']) ?>

                    — Tìm thấy
                    <?= count($danhSachSuKien) ?>
                    sự kiện
                </p>

            <?php else: ?>

                <h2>Sự kiện nổi bật</h2>

                <p>
                    Khám phá những hoạt động dành cho sinh viên
                </p>

            <?php endif; ?>

        </div>

        <?php if (empty($_GET['tu_khoa'])): ?>

            <a
                class="member-view-all"
                href="index.php?trang=su-kien"
            >
                Xem tất cả →
            </a>

        <?php endif; ?>

    </div>

    <?php if (empty($danhSachSuKien)): ?>

        <div class="member-empty">

            <?php if (!empty($_GET['tu_khoa'])): ?>

                Không tìm thấy sự kiện phù hợp với từ khóa
                "<?= hienThiTrangChu($_GET['tu_khoa']) ?>".

            <?php else: ?>

                Hiện chưa có sự kiện công khai.

            <?php endif; ?>

        </div>

    <?php else: ?>

        <section class="member-event-grid">

            <?php
            $danhSachHienThi = (
                ($_GET['tu_khoa'] ?? '') !== ''
            )
                ? $danhSachSuKien
                : array_slice($danhSachSuKien, 0, 6);
            ?>

            <?php foreach ($danhSachHienThi as $suKien): ?>

                <article class="member-event-card">

                    <div class="member-event-placeholder">

    <?php
    $duongDanAnh = trim((string) ($suKien['hinh_anh'] ?? ''));

    if ($duongDanAnh !== '') {
        // Nếu CSDL đã lưu đường dẫn đầy đủ thì giữ nguyên
        if (str_starts_with($duongDanAnh, '/Nhom-4-LTW/public/')) {
            $srcAnh = $duongDanAnh;
        } elseif (str_starts_with($duongDanAnh, 'Nhom-4-LTW/public/')) {
            $srcAnh = '/' . $duongDanAnh;
        } else {
            // Nếu CSDL chỉ lưu đường dẫn tương đối
            $srcAnh = '/Nhom-4-LTW/public/' . ltrim($duongDanAnh, '/');
        }
    } else {
        $srcAnh = '/Nhom-4-LTW/public/assets/image/default.jpg';
    }
    ?>

    <img
        src="<?= hienThiTrangChu($srcAnh) ?>"
        alt="<?= hienThiTrangChu($suKien['ten_su_kien'] ?? '') ?>"
        loading="lazy"
    >

</div>

                    <div class="member-event-content">

                        <div class="member-club-name">
                            <?= hienThiTrangChu(
                                $suKien['ten_cau_lac_bo'] ?? ''
                            ) ?>
                        </div>

                        <h3>
                            <?= hienThiTrangChu(
                                $suKien['ten_su_kien'] ?? ''
                            ) ?>
                        </h3>

                        <div class="member-event-info">

                            <p>
                                <span>▦</span>

                                <?= hienThiTrangChu(
                                    $suKien['thoi_gian_bat_dau'] ?? ''
                                ) ?>
                            </p>

                            <p>
                                <span>⌖</span>

                                <?= hienThiTrangChu(
                                    $suKien['dia_diem'] ?? ''
                                ) ?>
                            </p>

                        </div>

                        <span class="member-event-status">
                            Đang công khai
                        </span>

                        <a
                            class="member-detail-link"
                            href="index.php?trang=chi-tiet&id=<?= (int) $suKien['id'] ?>"
                        >
                            Xem chi tiết →
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

</main>

<!-- CHÂN TRANG -->
<footer class="member-footer">

    Sự kiện sinh viên · Kết nối cộng đồng, lan tỏa trải nghiệm

</footer>

<!-- XỬ LÝ MENU TÀI KHOẢN -->
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

    // Thông tin cá nhân
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