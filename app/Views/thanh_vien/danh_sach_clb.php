
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;
$trangHienTai = 'cau-lac-bo';



function hienThiCLBTV($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách câu lạc bộ</title>

   
    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/style.css?v=7">

    <style>
        .member-greeting {
            color: #718078;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .member-logout-form {
            display: inline-flex;
            align-items: center;
            margin: 0;
        }

        .member-logout {
            padding: 8px 14px;
            border: 1px solid #075b49 !important;
            border-radius: 7px;
            background: #075b49 !important;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
        }

        .member-logout:hover {
            background: #064638 !important;
            border-color: #064638 !important;
            color: #ffffff !important;
        }

        .member-register-link {
            padding: 8px 13px;
            border-radius: 7px;
            background: #075b49;
            color: #ffffff !important;
        }

        .member-register-link:hover {
            background: #064638;
        }

        @media (max-width: 768px) {
            .member-navbar {
                flex-wrap: wrap;
            }

            .member-nav-links {
                flex-wrap: wrap;
                gap: 12px;
            }
        }
    </style>
</head>

<body class="member-page member-club-page">

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

        <a
            href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo"
            class="<?= $trangHienTai === 'cau-lac-bo'
                ? 'member-nav-active'
                : '' ?>"
        >
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
                            <?= hienThiCLBTV(
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

<div class="clb-layout">

    <aside class="clb-sidebar">
        <form action="/Nhom-4-LTW/public/index.php"
              method="GET"
              class="clb-filter-form">

            <input type="hidden" name="trang" value="cau-lac-bo">

            <div class="clb-search-box">
                <span class="clb-search-icon">⌕</span>

                <input
                    type="text"
                    name="tu_khoa"
                    value="<?= hienThiCLBTV($tuKhoa ?? '') ?>"
                    placeholder="Tìm kiếm câu lạc bộ..."
                    maxlength="100"
                >
            </div>

            <button type="submit" class="clb-search-btn">
                Tìm kiếm
            </button>

            <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo"
               class="clb-reset-btn">
                Xóa bộ lọc
            </a>
        </form>
    </aside>

    <main class="clb-main">

        <?php if (!empty($_SESSION['thong_bao_clb'])): ?>

            <div class="clb-notification">
                <?= hienThiCLBTV($_SESSION['thong_bao_clb']) ?>
            </div>

            <?php unset($_SESSION['thong_bao_clb']); ?>

        <?php endif; ?>

        <div class="clb-page-heading">
            <div>
                <h1>Danh sách câu lạc bộ</h1>
                <p>Khám phá các cộng đồng sinh viên trong trường</p>
            </div>

            <span class="clb-count">
                <?= count($danhSachCauLacBo ?? []) ?> câu lạc bộ
            </span>
        </div>

        <?php if (empty($danhSachCauLacBo)): ?>

            <div class="clb-empty">
                <h2>Không tìm thấy câu lạc bộ</h2>
                <p>Hãy thử tìm kiếm bằng từ khóa khác.</p>

                <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
                    Xem tất cả câu lạc bộ
                </a>
            </div>

        <?php else: ?>

            <div class="clb-list">

                <?php foreach ($danhSachCauLacBo as $i => $clb): ?>

                    <article class="clb-list-card">

                        <div class="clb-card-image">
                            <img
                                src="<?= !empty($clb['hinh_anh'])
                                    ? '/Nhom-4-LTW/public/' . hienThiCLBTV($clb['hinh_anh'])
                                    : '/Nhom-4-LTW/public/assets/image/default.jpg' ?>"
                                alt="<?= hienThiCLBTV($clb['ten_cau_lac_bo'] ?? '') ?>"
                                loading="lazy"
                            >
                        </div>

                        <div class="clb-card-info">
                            <h3>
                                <?= hienThiCLBTV($clb['ten_cau_lac_bo'] ?? '') ?>
                            </h3>

                            <p class="clb-card-description">
                                <?= hienThiCLBTV(
                                    !empty($clb['mo_ta'])
                                        ? $clb['mo_ta']
                                        : 'Câu lạc bộ chưa có mô tả.'
                                ) ?>
                            </p>

                            <p class="clb-card-category">
                                ✦ Cộng đồng sinh viên
                            </p>
                        </div>

                        <div class="clb-card-action">

                            <span class="clb-status">
                                Đang hoạt động
                            </span>

                            <a
                                href="/Nhom-4-LTW/public/index.php?trang=su-kien&amp;cau_lac_bo_id=<?= (int) ($clb['id'] ?? 0) ?>"
                                class="clb-detail-btn">
                                Xem sự kiện
                            </a>

                            <?php if (!$nguoiDung): ?>

                                <a
                                    href="/Nhom-4-LTW/public/dang_nhap.php"
                                    class="clb-join-btn">
                                    Đăng nhập để tham gia
                                </a>

                            <?php elseif (in_array(
                                (int) ($clb['id'] ?? 0),
                                $clbDaThamGia ?? [],
                                true
                            )): ?>

                                <button type="button"
                                        class="clb-joined-btn"
                                        disabled>
                                    ✓ Đã tham gia
                                </button>

                            <?php elseif (($nguoiDung['vai_tro'] ?? '') === 'thanh_vien'): ?>

                                <form
                                    method="POST"
                                    action="/Nhom-4-LTW/public/index.php?trang=tham-gia-clb"
                                    class="clb-join-form">

                                    <input
                                        type="hidden"
                                        name="cau_lac_bo_id"
                                        value="<?= (int) ($clb['id'] ?? 0) ?>"
                                    >

                                    <button type="submit" class="clb-join-btn">
                                        + Tham gia
                                    </button>
                                </form>

                            <?php else: ?>

                                <button type="button"
                                        class="clb-joined-btn"
                                        disabled>
                                    Chỉ dành cho thành viên
                                </button>

                            <?php endif; ?>

                        </div>
                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>
</div>

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
