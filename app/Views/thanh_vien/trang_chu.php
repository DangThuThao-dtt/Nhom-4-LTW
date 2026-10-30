
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;

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

    <link rel="stylesheet" href="/Nhom-4-LTW/public/assets/css/thanh_vien/style.css?v=6"">

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
            color: white;
            font: inherit;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .member-logout:hover {
            background: #064638;
        }
    </style>
</head>

<body class="member-page">

<header class="member-navbar">
    <a class="member-logo" href="index.php?trang=trang-chu">
        ✦ Sự kiện sinh viên
    </a>

    <nav class="member-nav-links">
        <a href="index.php?trang=trang-chu">Trang chủ</a>

        <a href="index.php?trang=su-kien">Sự kiện</a>

        <a href="index.php?trang=cau-lac-bo">Câu lạc bộ</a>

        <?php if ($nguoiDung): ?>

            <a href="index.php?trang=dang-ky-cua-toi">
                Đăng ký của tôi
            </a>

            <span class="member-greeting">
                Xin chào,
                <?= hienThiTrangChu($nguoiDung['ho_ten'] ?? 'bạn') ?>
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

            <a
                class="member-register"
                href="/Nhom-4-LTW/public/thanh_vien/dang_ky.php"
            >
                Đăng ký
            </a>

        <?php endif; ?>
    </nav>
</header>

<section class="member-hero">
    <div class="member-hero-content">
        <h1>Khám phá các sự kiện<br>của sinh viên</h1>

        <p>Kết nối · Học hỏi · Trải nghiệm</p>

        <form action="index.php" method="GET" class="member-search">
            <input type="hidden" name="trang" value="su-kien">

            <input
                type="text"
                name="tu_khoa"
                placeholder="Tìm kiếm sự kiện..."
                aria-label="Tìm kiếm sự kiện"
            >

            <button type="submit">Tìm kiếm</button>
        </form>
    </div>
</section>

<main class="member-container">

    <div class="member-section-heading">
        <div>
            <h2>Sự kiện nổi bật</h2>
            <p>Khám phá những hoạt động dành cho sinh viên</p>
        </div>

        <a class="member-view-all" href="index.php?trang=su-kien">
            Xem tất cả →
        </a>
    </div>

    <?php if (empty($danhSachSuKien)): ?>

        <div class="member-empty">
            Hiện chưa có sự kiện công khai.
        </div>

    <?php else: ?>

        <section class="member-event-grid">

            <?php foreach (array_slice($danhSachSuKien, 0, 6) as $suKien): ?>

                <article class="member-event-card">

                    <div class="member-event-placeholder">
    <img
        src="<?= !empty($suKien['hinh_anh'])
            ? '/Nhom-4-LTW/public/' . hienThiTrangChu($suKien['hinh_anh'])
            : '/Nhom-4-LTW/public/assets/image/default.jpg' ?>"
        alt="<?= hienThiTrangChu($suKien['ten_su_kien']) ?>"
        loading="lazy"
    >
</div>

                    <div class="member-event-content">

                        <div class="member-club-name">
                            <?= hienThiTrangChu($suKien['ten_cau_lac_bo']) ?>
                        </div>

                        <h3>
                            <?= hienThiTrangChu($suKien['ten_su_kien']) ?>
                        </h3>

                        <div class="member-event-info">
                            <p>
                                <span>▦</span>
                                <?= hienThiTrangChu($suKien['thoi_gian_bat_dau']) ?>
                            </p>

                            <p>
                                <span>⌖</span>
                                <?= hienThiTrangChu($suKien['dia_diem']) ?>
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

<footer class="member-footer">
    Sự kiện sinh viên · Kết nối cộng đồng, lan tỏa trải nghiệm
</footer>

</body>
</html>
