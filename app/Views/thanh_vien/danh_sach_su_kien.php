
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;
$trangHienTai = 'su-kien';

function hienThiDanhSachTV($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$soSuKien = count($danhSachSuKien ?? []);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sự kiện sinh viên</title>

   
    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/style.css">

    <style>
        .member-greeting {
            color: #075c49;
            font-size: 14px;
            white-space: nowrap;
        }

        .member-logout-form {
            display: inline-flex;
            margin: 0;
        }

        .member-logout {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #075c49;
            color: #fff;
            font: inherit;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .member-logout:hover {
            background: #064638;
        }
    </style>
</head>

<body class="member-page member-list-page">

<header class="member-navbar">
    <a class="member-logo"
       href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
        ✦ Sự kiện sinh viên
    </a>

    <nav class="member-nav-links">
        <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu"
           class="<?= $trangHienTai === 'trang-chu' ? 'member-nav-active' : '' ?>">
            Trang chủ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien"
           class="<?= $trangHienTai === 'su-kien' ? 'member-nav-active' : '' ?>">
            Sự kiện
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo"
           class="<?= $trangHienTai === 'cau-lac-bo' ? 'member-nav-active' : '' ?>">
            Câu lạc bộ
        </a>

        <?php if ($nguoiDung): ?>

            <a href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi"
               class="<?= $trangHienTai === 'dang-ky-cua-toi' ? 'member-nav-active' : '' ?>">
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

                <div class="member-account-menu"
                     id="memberAccountMenu">

                    <div class="member-account-info">
                        <strong>
                            <?= hienThiDanhSachTV($nguoiDung['ho_ten'] ?? 'Thành viên') ?>
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

            <a class="member-register-link"
               href="/Nhom-4-LTW/public/dang_ky.php">
                Đăng ký
            </a>

        <?php endif; ?>
    </nav>
</header>

<main class="member-list-layout">

    <!-- BỘ LỌC BÊN TRÁI -->
    <aside class="member-sidebar">

        <form action="/Nhom-4-LTW/public/index.php"
              method="GET"
              class="member-filter-form">

            <input type="hidden" name="trang" value="su-kien">

            <label class="member-search-box">
                <span>⌕</span>

                <input
                    type="text"
                    name="tu_khoa"
                    value="<?= hienThiDanhSachTV($tuKhoa ?? '') ?>"
                    placeholder="Tìm kiếm sự kiện..."
                    maxlength="100"
                >
            </label>

            <label class="member-filter-label" for="loc-clb">
                Câu lạc bộ
            </label>

            <select
                name="cau_lac_bo_id"
                id="loc-clb"
                class="member-filter-select"
            >
                <option value="0">Tất cả câu lạc bộ</option>

                <?php foreach (($danhSachCauLacBo ?? []) as $clb): ?>
                    <option
                        value="<?= (int) ($clb['id'] ?? 0) ?>"
                        <?= (int) ($cauLacBoId ?? 0) === (int) ($clb['id'] ?? 0)
                            ? 'selected'
                            : '' ?>
                    >
                        <?= hienThiDanhSachTV($clb['ten_cau_lac_bo'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="member-filter-label" for="tu-ngay">
                Từ ngày
            </label>

            <input
                type="date"
                name="tu_ngay"
                id="tu-ngay"
                class="member-filter-select"
                value="<?= hienThiDanhSachTV($tuNgay ?? '') ?>"
            >

            <label class="member-filter-label" for="den-ngay">
                Đến ngày
            </label>

            <input
                type="date"
                name="den_ngay"
                id="den-ngay"
                class="member-filter-select"
                value="<?= hienThiDanhSachTV($denNgay ?? '') ?>"
            >

            <label class="member-filter-label" for="loc-trang-thai">
                Trạng thái
            </label>

            <select
                id="loc-trang-thai"
                class="member-filter-select"
                disabled
            >
                <option>Tất cả trạng thái</option>
            </select>

            <button type="submit" class="member-filter-submit">
                Tìm kiếm
            </button>

            <a
                class="member-reset-filter"
                href="/Nhom-4-LTW/public/index.php?trang=su-kien"
            >
                Xóa bộ lọc
            </a>
        </form>

    </aside>

    <!-- DANH SÁCH BÊN PHẢI -->
    <section class="member-results">

        <div class="member-results-heading">
            <div>
                <h1>Danh sách sự kiện</h1>

                <p>
                    Khám phá hoạt động và trải nghiệm dành cho sinh viên
                </p>
            </div>

            <span class="member-results-count">
                <?= $soSuKien ?> sự kiện
            </span>
        </div>

        <?php if ($soSuKien === 0): ?>

            <div class="member-empty">
                <div class="member-empty-icon">⌕</div>

                <h2>Không tìm thấy sự kiện</h2>

                <p>Hãy thử từ khóa khác hoặc thay đổi bộ lọc.</p>

                <a
                    href="/Nhom-4-LTW/public/index.php?trang=su-kien"
                    class="member-filter-submit member-back-link"
                >
                    Xem tất cả sự kiện
                </a>
            </div>

        <?php else: ?>

            <div class="member-results-list">

                <?php foreach ($danhSachSuKien as $i => $suKien): ?>

                    <article class="member-result-card">

                        <?php
$duongDanAnh = trim((string) ($suKien['hinh_anh'] ?? ''));

if ($duongDanAnh !== '') {
    if (str_starts_with($duongDanAnh, '/Nhom-4-LTW/public/')) {
        $srcAnh = $duongDanAnh;
    } elseif (str_starts_with($duongDanAnh, 'Nhom-4-LTW/public/')) {
        $srcAnh = '/' . $duongDanAnh;
    } else {
        $srcAnh = '/Nhom-4-LTW/public/' . ltrim($duongDanAnh, '/');
    }
} else {
    $srcAnh = '/Nhom-4-LTW/public/assets/image/su_kien/default.jpg';
}
?>

<img
    class="member-result-image"
    src="<?= hienThiDanhSachTV($srcAnh) ?>"
    alt="<?= hienThiDanhSachTV($suKien['ten_su_kien'] ?? '') ?>"
    loading="lazy"
>

                        <div class="member-result-info">

                            <h2>
                                <?= hienThiDanhSachTV($suKien['ten_su_kien'] ?? '') ?>
                            </h2>

                            <p class="member-result-club">
                                <span>♧</span>

                                <?= hienThiDanhSachTV(
                                    $suKien['ten_cau_lac_bo'] ?? ''
                                ) ?>
                            </p>

                            <p class="member-result-meta">
                                <span>▦</span>

                                <?= hienThiDanhSachTV(
                                    $suKien['thoi_gian_bat_dau'] ?? ''
                                ) ?>
                            </p>

                            <p class="member-result-meta">
                                <span>⌖</span>

                                <?= hienThiDanhSachTV(
                                    $suKien['dia_diem'] ?? ''
                                ) ?>
                            </p>

                        </div>

                        <div class="member-result-action">

                            <span class="member-status-badge">
                                Đang công khai
                            </span>

                            <a
                                class="member-result-button"
                                href="/Nhom-4-LTW/public/index.php?trang=chi-tiet&id=<?= (int) ($suKien['id'] ?? 0) ?>"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<footer class="member-footer">
    Sự kiện sinh viên · Kết nối cộng đồng, lan tỏa trải nghiệm
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const accountBtn = document.getElementById('memberAccountBtn');
    const accountMenu = document.getElementById('memberAccountMenu');
    const profileBtn = document.getElementById('memberProfileBtn');

    if (!accountBtn || !accountMenu) return;

    accountBtn.addEventListener('click', function () {
        const isOpen = accountMenu.classList.toggle('show');
        accountBtn.setAttribute('aria-expanded', isOpen);
    });

    document.addEventListener('click', function (event) {
        if (
            !accountBtn.contains(event.target) &&
            !accountMenu.contains(event.target)
        ) {
            accountMenu.classList.remove('show');
            accountBtn.setAttribute('aria-expanded', 'false');
        }
    });

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
