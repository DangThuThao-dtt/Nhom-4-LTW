
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;

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
          href="/Nhom-4-LTW/public/assets/css/thanh_vien/style.css">

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
        <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
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

            <span class="member-greeting">
                Xin chào,
                <?= hienThiDanhSachTV($nguoiDung['ho_ten'] ?? 'bạn') ?>
            </span>

<form
    method="POST"
    action="/Nhom-4-LTW/app/Auth/dang_xuat.php"
    class="member-logout-form"
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

    <button type="submit" class="member-logout">
        Đăng xuất
    </button>
</form>

        <?php else: ?>

            <a href="/Nhom-4-LTW/public/dang_nhap.php">
                Đăng nhập
            </a>

            <a class="member-register"
               href="/Nhom-4-LTW/public/thanh_vien/dang_ky.php">
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

            <select name="cau_lac_bo_id"
                    id="loc-clb"
                    class="member-filter-select">

                <option value="0">Tất cả câu lạc bộ</option>

                <?php foreach (($danhSachCauLacBo ?? []) as $clb): ?>
                    <option
                        value="<?= (int) $clb['id'] ?>"
                        <?= (int) ($cauLacBoId ?? 0) === (int) $clb['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= hienThiDanhSachTV($clb['ten_cau_lac_bo']) ?>
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

            <select id="loc-trang-thai"
                    class="member-filter-select"
                    disabled>
                <option>Tất cả trạng thái</option>
            </select>

            <button type="submit" class="member-filter-submit">
                Tìm kiếm
            </button>

            <a class="member-reset-filter"
               href="/Nhom-4-LTW/public/index.php?trang=su-kien">
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

                <a href="/Nhom-4-LTW/public/index.php?trang=su-kien"
                   class="member-filter-submit member-back-link">
                    Xem tất cả sự kiện
                </a>
            </div>

        <?php else: ?>

            <div class="member-results-list">

                <?php foreach ($danhSachSuKien as $i => $suKien): ?>

                    <article class="member-result-card">

    <img
        class="member-result-image"
        src="<?= !empty($suKien['hinh_anh'])
            ? '/Nhom-4-LTW/public/' . htmlspecialchars($suKien['hinh_anh'])
            : '/Nhom-4-LTW/public/assets/image/su_kien/default.jpg' ?>"
        alt="<?= htmlspecialchars($suKien['ten_su_kien']) ?>"
        loading="lazy"
    >

    <div class="member-result-info">

        <h2>
            <?= hienThiDanhSachTV($suKien['ten_su_kien']) ?>
        </h2>

                            <p class="member-result-club">
                                <span>♧</span>
                                <?= hienThiDanhSachTV(
                                    $suKien['ten_cau_lac_bo']
                                ) ?>
                            </p>

                            <p class="member-result-meta">
                                <span>▦</span>
                                <?= hienThiDanhSachTV(
                                    $suKien['thoi_gian_bat_dau']
                                ) ?>
                            </p>

                            <p class="member-result-meta">
                                <span>⌖</span>
                                <?= hienThiDanhSachTV($suKien['dia_diem']) ?>
                            </p>

                        </div>

                        <div class="member-result-action">

                            <span class="member-status-badge">
                                Đang công khai
                            </span>

                            <a
                                class="member-result-button"
                                href="/Nhom-4-LTW/public/index.php?trang=chi-tiet&id=<?= (int) $suKien['id'] ?>"
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

</body>
</html>
