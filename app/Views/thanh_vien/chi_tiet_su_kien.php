<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;
function hienThiChiTiet($giaTri): string
{
    return htmlspecialchars(
        (string) ($giaTri ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$tenSuKien = $suKien['ten_su_kien'] ?? 'Chi tiết sự kiện';
$trangThai = $suKien['trang_thai'] ?? '';
$laCongKhai = $trangThai === 'cong_khai';
$thoiGian = !empty($suKien['thoi_gian_bat_dau'])
    ? strtotime($suKien['thoi_gian_bat_dau'])
    : false;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= hienThiChiTiet($tenSuKien) ?></title>

    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/thanh_vien/style.css?v=4">
</head>

<body class="member-page member-detail-page">
<header class="member-navbar">
    <a class="member-logo"
       href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
        ✦ Sự kiện sinh viên
    </a>

    <nav class="member-nav-links">
        <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
            Trang chủ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien"
           class="member-nav-active">
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
                <?= hienThiChiTiet($nguoiDung['ho_ten'] ?? 'Thành viên') ?>
            </span>

            <form method="POST"
                  action="/Nhom-4-LTW/app/Auth/dang_xuat.php"
                  class="member-logout-form">
                <button type="submit" class="member-logout">
                    Đăng xuất
                </button>
            </form>
        <?php else: ?>
            <a href="/Nhom-4-LTW/public/dang_nhap.php">
                Đăng nhập
            </a>

            <a href="/Nhom-4-LTW/public/thanh_vien/dang_ky.php"
               class="member-register-link">
                Đăng ký
            </a>
        <?php endif; ?>
    </nav>
</header>


<main class="member-detail-container">

    <a class="member-detail-back"
       href="index.php?trang=su-kien">
        <span>←</span> Quay lại danh sách
    </a>

    <div class="member-detail-layout">

        <!-- CỘT NỘI DUNG CHÍNH -->
        <section class="member-detail-main">

            <div class="member-detail-event">

                <div class="member-detail-top">

                    <img
    src="<?= !empty($suKien['hinh_anh'])
        ? '/Nhom-4-LTW/public/' . htmlspecialchars($suKien['hinh_anh'])
        : '/Nhom-4-LTW/public/assets/image/su_kien/default.jpg' ?>"
    alt="<?= htmlspecialchars($suKien['ten_su_kien']) ?>"
    loading="lazy"
>

                    <div class="member-detail-heading">

                        <div class="member-detail-title-row">
                            <h1><?= hienThiChiTiet($tenSuKien) ?></h1>

                            <span class="member-status-badge">
                                <?= $laCongKhai
                                    ? 'Công khai'
                                    : hienThiChiTiet($trangThai) ?>
                            </span>
                        </div>

                        <div class="member-detail-meta-grid">

                            <p>
                                <span>♧</span>
                                <?= hienThiChiTiet(
                                    $suKien['ten_cau_lac_bo'] ?? ''
                                ) ?>
                            </p>

                            <p>
                                <span>▦</span>
                                <?php if ($thoiGian): ?>
                                    <?= date('d/m/Y - H:i', $thoiGian) ?>
                                <?php else: ?>
                                    Chưa cập nhật thời gian
                                <?php endif; ?>
                            </p>

                            <p>
                                <span>⌖</span>
                                <?= hienThiChiTiet(
                                    $suKien['dia_diem'] ?? 'Chưa cập nhật'
                                ) ?>
                            </p>

                            <p>
                                <span>♙</span>
                                Số lượng tối đa:
                                <?= hienThiChiTiet(
                                    $suKien['so_luong_toi_da'] ?? 'Chưa cập nhật'
                                ) ?>
                            </p>

                        </div>
                    </div>

                </div>

                <div class="member-detail-description">
                    <h2>Mô tả sự kiện</h2>

                    <p>
                        <?php if (!empty($suKien['mo_ta'])): ?>
                            <?= nl2br(hienThiChiTiet($suKien['mo_ta'])) ?>
                        <?php else: ?>
                            Sự kiện chưa có nội dung mô tả.
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($laCongKhai): ?>
                    <button
                        type="button"
                        class="member-register-event"
                        id="btnDangKy"
                        data-su-kien-id="<?= (int) ($suKien['id'] ?? 0) ?>">
                        Đăng ký tham gia
                    </button>
                <?php else: ?>
                    <button type="button" class="member-register-event" disabled>
                        Sự kiện chưa mở đăng ký
                    </button>
                <?php endif; ?>

            <p id="thongBaoDangKy" role="status" aria-live="polite"></p>


            </div>

        </section>

        <!-- CỘT THÔNG TIN BÊN PHẢI -->
        <aside class="member-detail-sidebar">

            <section class="member-contact-card">
                <h2>Thông tin liên hệ</h2>

                <div class="member-contact-person">
                    <div class="member-contact-avatar">♙</div>

                    <div>
                        <strong>Ban tổ chức</strong>
                        <p>Đơn vị tổ chức sự kiện</p>
                    </div>
                </div>

                <p class="member-contact-note">
                    Vui lòng liên hệ câu lạc bộ tổ chức để biết thêm chi tiết.
                </p>
            </section>

            <section class="member-share-card">
                <h2>Chia sẻ</h2>

                <div class="member-share-links">
                    <a href="https://www.facebook.com/"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Facebook">f</a>

                    <a href="https://zalo.me/"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Zalo">Z</a>

                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText(window.location.href).then(() => alert('Đã sao chép liên kết sự kiện!')).catch(() => alert('Không thể sao chép tự động. Hãy sao chép đường dẫn trên trình duyệt.'))"
                        aria-label="Sao chép liên kết">🔗</button>
                </div>
            </section>

        </aside>

    </div>

</main>

<script>
const nutDangKy = document.getElementById('btnDangKy');
const thongBao = document.getElementById('thongBaoDangKy');

if (nutDangKy) {
    nutDangKy.addEventListener('click', async function () {
        const suKienId = this.dataset.suKienId;

        if (!suKienId || suKienId === '0') {
            thongBao.textContent = 'Không xác định được sự kiện.';
            return;
        }

        this.disabled = true;
        this.textContent = 'Đang xử lý...';
        thongBao.textContent = '';

        try {
            const duLieu = new URLSearchParams();
            duLieu.append('action', 'dang_ky');
            duLieu.append('su_kien_id', suKienId);

            const response = await fetch('api/dang_ky_tham_gia.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: duLieu.toString(),
                credentials: 'same-origin'
            });

            const ketQua = await response.json();

            thongBao.textContent = ketQua.message ||
                'Không nhận được thông báo từ máy chủ.';

            if (ketQua.status === 'success') {
    thongBao.style.color = 'green';
    this.textContent = 'Đã đăng ký';

    thongBao.textContent =
        ketQua.message || 'Đăng ký thành công! Đang mở danh sách của bạn...';

    setTimeout(() => {
        alert('Đăng ký tham gia sự kiện thành công!');
window.location.reload();
    }, 1000);
} else {
                thongBao.style.color = 'red';
                this.disabled = false;
                this.textContent = 'Đăng ký tham gia';

                if (
                    ketQua.message &&
                    ketQua.message.includes('đăng nhập')
                ) {
                    thongBao.textContent += ' Vui lòng đăng nhập trước.';
                }
            }
        } catch (error) {
            thongBao.style.color = 'red';
            thongBao.textContent =
                'Không thể kết nối máy chủ. Bạn hãy thử lại.';
            this.disabled = false;
            this.textContent = 'Đăng ký tham gia';
        }
    });
}
</script>

<footer class="member-footer">
    Sự kiện sinh viên · Kết nối cộng đồng, lan tỏa trải nghiệm
</footer>

</body>
</html>
