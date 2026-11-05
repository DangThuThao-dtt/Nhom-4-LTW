
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;
$trangHienTai = 'dang-ky-cua-toi';
function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function dinhDangNgayGio($value): string
{
    if (empty($value)) {
        return 'Chưa cập nhật';
    }

    $timestamp = strtotime($value);

    return $timestamp !== false
        ? date('d/m/Y H:i', $timestamp)
        : 'Chưa cập nhật';
}

$danhSachDangKy = $danhSachDangKy ?? [];
$tuKhoa = $tuKhoa ?? '';
$thongBaoNhacNho = $thongBaoNhacNho ?? '';
$loaiThongBao = $loaiThongBao ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sự kiện đã đăng ký</title>
    <link rel="stylesheet" href="/Nhom-4-LTW/public/assets/css/style.css?v=7">
</head>

<body class="member-page dang-ky-cua-toi-page">

<header class="member-navbar">
    <a class="member-logo"
       href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
        ✦ Sự kiện sinh viên
    </a>

    <nav class="member-nav-links">
        <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu"
           class="<?= ($trangHienTai ?? '') === 'trang-chu' ? 'member-nav-active' : '' ?>">
            Trang chủ
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien"
           class="<?= ($trangHienTai ?? '') === 'su-kien' ? 'member-nav-active' : '' ?>">
            Sự kiện
        </a>

        <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo"
           class="<?= ($trangHienTai ?? '') === 'cau-lac-bo' ? 'member-nav-active' : '' ?>">
            Câu lạc bộ
        </a>

        <?php if ($nguoiDung): ?>
            <a href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi"
               class="<?= ($trangHienTai ?? '') === 'dang-ky-cua-toi' ? 'member-nav-active' : '' ?>">
                Đăng ký của tôi
            </a>

            <!-- Menu tài khoản -->
            <div class="member-account">
                <button type="button"
        class="member-account-btn"
        id="memberAccountBtn"
        aria-label="Tài khoản"
        aria-expanded="false"
        aria-controls="memberAccountMenu">
    <span class="member-account-icon">♙</span>
</button>

                <div class="member-account-menu"
                     id="memberAccountMenu">

                    <div class="member-account-info">
                        <strong>
                            <?= htmlspecialchars(
                                $nguoiDung['ho_ten'] ?? 'Thành viên',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                        <span>Tài khoản thành viên</span>
                    </div>

                    <button type="button"
                            class="member-account-message"
                            id="memberProfileBtn">
                        Thông tin cá nhân
                    </button>

                    <form method="POST"
                          action="/Nhom-4-LTW/app/Auth/dang_xuat.php"
                          class="member-account-logout">
                        <button type="submit">Đăng xuất</button>
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

<main class="container">
    <div class="page-heading">
        <h1>Sự kiện đã đăng ký</h1>
        <p class="subtitle">
            Theo dõi các sự kiện bạn đã đăng ký và quản lý đăng ký của mình.
        </p>
    </div>

<?php if (!empty($thongBaoNhacNho)): ?>
    <div class="thong-bao-nhac-nho <?= e($loaiThongBao) ?>">
        <strong>Thông báo nhắc nhở</strong>
        <p><?= e($thongBaoNhacNho) ?></p>
    </div>
<?php endif; ?>

    <div id="thongBao" class="message" role="status" aria-live="polite"></div>

<!-- Danh sách câu lạc bộ đã tham gia -->
<section class="clb-section">

    <div class="table-heading">
        <h2>Câu lạc bộ đã tham gia</h2>
        <p>Danh sách những câu lạc bộ bạn đã trở thành thành viên.</p>
    </div>

    <?php if (!empty($danhSachCLBDaThamGia)): ?>

        <div class="clb-list">

            <?php foreach ($danhSachCLBDaThamGia as $clb): ?>

                <div class="clb-item">

                    <h3>
                        <?= e($clb['ten_cau_lac_bo'] ?? '') ?>
                    </h3>

                    <p>
                        Ngày tham gia:
                        <?= e(dinhDangNgayGio($clb['ngay_tham_gia'] ?? null)) ?>
                    </p>

                    <a href="/Nhom-4-LTW/public/index.php?trang=su-kien&cau_lac_bo_id=<?= (int) $clb['id'] ?>">
                        Xem sự kiện
                    </a>
<?php if (($nguoiDung['vai_tro'] ?? '') === 'thanh_vien'): ?>
    <form method="POST"
          action="/Nhom-4-LTW/public/index.php?trang=roi-clb"
          onsubmit="return confirm('Bạn có chắc chắn muốn rời câu lạc bộ này không?');"
          class="clb-leave-form">

        <input type="hidden"
               name="cau_lac_bo_id"
               value="<?= (int) $clb['id'] ?>">

        <button type="submit" class="clb-leave-btn">
            Rời câu lạc bộ
        </button>
    </form>
<?php endif; ?>
                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="clb-empty">
            Bạn chưa tham gia câu lạc bộ nào.
            <br>
            Hãy khám phá các câu lạc bộ và tham gia ngay nhé!
            <br><br>

            <a class="link-kham-pha-clb" href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
                Khám phá câu lạc bộ →
            </a>
        </div>

    <?php endif; ?>

</section>
    <section class="table-card">
        <div class="table-heading">
            <h2>Danh sách đăng ký của tôi</h2>
            <p>Thông tin và trạng thái đăng ký sự kiện của bạn.</p>

            <form method="GET"
                  action="/Nhom-4-LTW/public/index.php"
                  class="search-form">

                <input type="hidden"
                       name="trang"
                       value="dang-ky-cua-toi">

                <input
                    type="text"
                    name="tu_khoa"
                    value="<?= e($tuKhoa) ?>"
                    maxlength="100"
                    placeholder="Nhập tên sự kiện cần tìm..."
                    aria-label="Tìm kiếm theo tên sự kiện">

                <button type="submit" class="btn btn-primary">
                    Tìm kiếm
                </button>

                <?php if (!empty($tuKhoa)): ?>
                    <a class="btn btn-secondary"
                       href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi">
                        Xóa tìm kiếm
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($danhSachDangKy)): ?>
            <div class="empty">
                <div class="empty-icon">📭</div>
                <h3>Bạn chưa đăng ký sự kiện nào</h3>
                <p>Khám phá các sự kiện thú vị và đăng ký tham gia ngay nhé!</p>

                <a class="btn btn-primary"
                   href="/Nhom-4-LTW/public/index.php?trang=su-kien">
                    Khám phá sự kiện
                </a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tên sự kiện</th>
                            <th>Câu lạc bộ</th>
                            <th>Địa điểm</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($danhSachDangKy as $dk): ?>
                        <?php
                        $trangThai = $dk['trang_thai_dang_ky'] ?? '';

                        $thongTinTrangThai = [
                            'da_dang_ky' => [
                                'class' => 'da-dang-ky',
                                'text' => 'Đã đăng ký'
                            ],
                            'da_huy' => [
                                'class' => 'da-huy',
                                'text' => 'Đã hủy'
                            ],
                            'da_tham_gia' => [
                                'class' => 'da-tham-gia',
                                'text' => 'Đã tham gia'
                            ]
                        ];

                        $trangThaiHienThi = $thongTinTrangThai[$trangThai]
                            ?? ['class' => '', 'text' => $trangThai];

                        $idDangKy = (int) ($dk['dang_ky_id'] ?? 0);
                        ?>

                        <tr id="row-<?= $idDangKy ?>">
                            <td class="event-name">
                                <?= e($dk['ten_su_kien'] ?? '') ?>
                            </td>

                            <td class="club-name">
                                <?= e($dk['ten_cau_lac_bo'] ?? '') ?>
                            </td>

                            <td><?= e($dk['dia_diem'] ?? '') ?></td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_bat_dau'] ?? null)) ?>
                            </td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_dang_ky'] ?? null)) ?>
                            </td>

                            <td class="td-trang-thai">
                                <span class="status <?= e($trangThaiHienThi['class']) ?>">
                                    <?= e($trangThaiHienThi['text']) ?>
                                </span>
                            </td>

                            <td class="td-hanh-dong">
                                <?php if ($trangThai === 'da_dang_ky'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-cancel nut-huy"
                                        data-id="<?= $idDangKy ?>"
                                        data-ten="<?= e($dk['ten_su_kien'] ?? '') ?>">
                                        Hủy đăng ký
                                    </button>
                                <?php elseif ($trangThai === 'da_huy'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-delete nut-xoa"
                                        data-id="<?= $idDangKy ?>"
                                        data-ten="<?= e($dk['ten_su_kien'] ?? '') ?>">
                                        Xóa đăng ký
                                    </button>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <p class="back-to-events">
        <a class="link-quay-lai-su-kien" href="/Nhom-4-LTW/public/index.php?trang=su-kien">
            ← Quay lại danh sách sự kiện
        </a>
    </p>
</main>

<footer class="footer">
    Hệ thống quản lý sự kiện và câu lạc bộ sinh viên
</footer>

<!-- Hộp thoại xác nhận hủy đăng ký -->
<div id="modalXacNhan" class="modal-overlay">
    <div class="modal-box">
        <h3>Xác nhận hủy đăng ký</h3>

        <p id="modalText">
            Bạn có chắc chắn muốn hủy đăng ký sự kiện này không?
        </p>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="btnDong">
                Giữ đăng ký
            </button>

            <button type="button" class="btn btn-cancel" id="btnXacNhanHuy">
                Xác nhận hủy
            </button>
        </div>
    </div>
</div>

<script>
    let dangKyIdCanHuy = null;
    let nutDangHuy = null;

    const modal = document.getElementById('modalXacNhan');
    const modalText = document.getElementById('modalText');
    const btnDong = document.getElementById('btnDong');
    const btnXacNhanHuy = document.getElementById('btnXacNhanHuy');

    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');
        el.textContent = noiDung;
        el.className = 'message ' + loai;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function moModal(id, tenSuKien, button) {
        dangKyIdCanHuy = id;
        nutDangHuy = button;

        modalText.textContent =
            'Bạn có chắc chắn muốn hủy đăng ký sự kiện "' +
            tenSuKien + '" không?';

        modal.classList.add('show');
    }

    function dongModal() {
        modal.classList.remove('show');
        dangKyIdCanHuy = null;
        nutDangHuy = null;
    }

    btnDong.addEventListener('click', dongModal);

    document.querySelectorAll('.nut-huy').forEach(button => {
        button.addEventListener('click', function () {
            moModal(this.dataset.id, this.dataset.ten, this);
        });
    });

    // Hủy đăng ký
    btnXacNhanHuy.addEventListener('click', async function () {
        if (!dangKyIdCanHuy || !nutDangHuy) {
            return;
        }

        const id = dangKyIdCanHuy;
        const button = nutDangHuy;
        const row = document.getElementById('row-' + id);

        button.disabled = true;
        button.textContent = 'Đang xử lý...';
        btnXacNhanHuy.disabled = true;

        try {
            const body = new URLSearchParams();
            body.append('action', 'huy_dang_ky');
            body.append('dang_ky_id', id);

            const response = await fetch(
                '/Nhom-4-LTW/public/api/dang_ky_tham_gia.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString(),
                    credentials: 'same-origin'
                }
            );

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Không thể hủy đăng ký.');
            }

            hienThiThongBao(
                data.message || 'Hủy đăng ký thành công.',
                'success'
            );

            row.querySelector('.td-trang-thai').innerHTML =
                '<span class="status da-huy">Đã hủy</span>';

            row.querySelector('.td-hanh-dong').innerHTML =
                '<button type="button" class="btn btn-delete nut-xoa" ' +
                'data-id="' + id + '" data-ten="' +
                button.dataset.ten.replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;') +
                '">Xóa đăng ký</button>';

            ganSuKienXoa(row.querySelector('.nut-xoa'));

        } catch (error) {
            hienThiThongBao(
                error.message || 'Không thể kết nối máy chủ.',
                'error'
            );

            button.disabled = false;
            button.textContent = 'Hủy đăng ký';
        } finally {
            dongModal();
            btnXacNhanHuy.disabled = false;
        }
    });

    // Xóa đăng ký đã hủy
    async function xuLyXoa(button) {
        const id = button.dataset.id;
        const tenSuKien = button.dataset.ten;
        const row = document.getElementById('row-' + id);

        if (!confirm(
            'Bạn có chắc chắn muốn xóa đăng ký đã hủy của sự kiện "' +
            tenSuKien + '" không? Thao tác này không thể hoàn tác.'
        )) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Đang xóa...';

        try {
            const body = new URLSearchParams();
            body.append('action', 'xoa_dang_ky');
            body.append('dang_ky_id', id);

            const response = await fetch(
                '/Nhom-4-LTW/public/api/dang_ky_tham_gia.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString(),
                    credentials: 'same-origin'
                }
            );

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Không thể xóa đăng ký.');
            }

            row.remove();

            const tongSo = document.querySelector('.summary-number');

if (tongSo) {
    tongSo.textContent = Math.max(
        0,
        Number(tongSo.textContent) - 1
    );
}

            hienThiThongBao(data.message || 'Đã xóa đăng ký.', 'success');

            const tbody = document.querySelector('tbody');

            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                window.location.reload();
            }

        } catch (error) {
            hienThiThongBao(
                error.message || 'Không thể kết nối máy chủ.',
                'error'
            );

            button.disabled = false;
            button.textContent = 'Xóa đăng ký';
        }
    }

    function ganSuKienXoa(button) {
        if (!button) {
            return;
        }

        button.addEventListener('click', function () {
            xuLyXoa(this);
        });
    }

    document.querySelectorAll('.nut-xoa').forEach(ganSuKienXoa);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            dongModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            dongModal();
        }
    });
</script>
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
