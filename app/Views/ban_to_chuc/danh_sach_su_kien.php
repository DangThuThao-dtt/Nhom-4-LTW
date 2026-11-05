<?php
if (!isset($suKienList)) {
    $suKienList = [];
}

function eDanhSach($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function boDauTiengViet($text): string
{
    $text = mb_strtolower((string) $text, 'UTF-8');

    $coDau = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ','đ'
    ];

    $khongDau = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y','d'
    ];

    return str_replace($coDau, $khongDau, $text);
}

$nhanTrangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Đang mở đăng ký',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Đã kết thúc'
];

$tuKhoa = trim($_GET['tu_khoa'] ?? '');
$locTrangThai = $_GET['loc_trang_thai'] ?? '';
$tuNgay = $_GET['tu_ngay'] ?? '';
$denNgay = $_GET['den_ngay'] ?? '';

$trangThaiHopLe = ['', 'nhap', 'cong_khai', 'da_huy', 'ket_thuc'];

if (!in_array($locTrangThai, $trangThaiHopLe, true)) {
    $locTrangThai = '';
}

$loiKhoangNgay = '';

$kiemTraNgay = function ($ngay): bool {
    if ($ngay === '') {
        return true;
    }

    $date = DateTime::createFromFormat('!Y-m-d', $ngay);

    return $date !== false
        && $date->format('Y-m-d') === $ngay;
};

if (!$kiemTraNgay($tuNgay)) {
    $tuNgay = '';
}

if (!$kiemTraNgay($denNgay)) {
    $denNgay = '';
}

if (
    $tuNgay !== '' &&
    $denNgay !== '' &&
    $tuNgay > $denNgay
) {
    $loiKhoangNgay = 'Ngày bắt đầu lọc không được lớn hơn ngày kết thúc.';
}

$suKienList = array_values(array_filter(
    $suKienList,
    function ($suKien) use (
        $tuKhoa,
        $locTrangThai,
        $tuNgay,
        $denNgay,
        $loiKhoangNgay
    ) {
        $ten = (string) ($suKien['ten_su_kien'] ?? '');
        $trangThai = (string) ($suKien['trang_thai'] ?? '');
        $thoiGian = (string) ($suKien['thoi_gian_bat_dau'] ?? '');
        $ngayBatDau = substr($thoiGian, 0, 10);

        if (
            $tuKhoa !== '' &&
            strpos(
                boDauTiengViet($ten),
                boDauTiengViet($tuKhoa)
            ) === false
        ) {
            return false;
        }

        if (
            $locTrangThai !== '' &&
            $trangThai !== $locTrangThai
        ) {
            return false;
        }

        if ($loiKhoangNgay !== '') {
            return true;
        }

        if ($tuNgay !== '' && $ngayBatDau < $tuNgay) {
            return false;
        }

        if ($denNgay !== '' && $ngayBatDau > $denNgay) {
            return false;
        }

        return true;
    }
));

$nguoiDung = $nguoiDung ?? [];
$tenCauLacBo = $tenCauLacBo ?? 'Câu lạc bộ';
$soThongBaoChuaDoc = $soThongBaoChuaDoc ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý sự kiện</title>

    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css">

    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/btc_dssk.css">

    <style>
        /*
         * Điều chỉnh trang danh sách để nằm trong vùng nội dung
         * bên phải sidebar.
         */
        body {
            margin: 0;
            padding: 0;
        }
        .anh-su-kien {
    width: 100px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    display: block;
}

        .main-ban-to-chuc .container {
            width: 100%;
            max-width: none;
            min-width: 0;
            margin: 0;
            padding: 25px;
            box-sizing: border-box;
            overflow-x: auto;
        }

        .filter-form {
            margin-top: 20px;
            padding: 16px;
            background: #f4f6fb;
            border-radius: 8px;
        }

        .filter-form input,
        .filter-form select {
            padding: 9px;
            margin-top: 5px;
        }

        .filter-form > div {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .main-ban-to-chuc table {
            width: 100%;
            min-width: 1000px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .actions form {
            margin: 0;
        }

        .back-link {
            display: none;
        }
        /* Menu sidebar dùng chung với trang chủ */

.tk-menu {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.tk-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 46px;
    padding: 11px 13px;
    border-radius: 9px;
    color: #e3ebe5;
    font-size: 14px;
    text-decoration: none;
    transition: background .2s, color .2s;
}

.tk-menu a:hover,
.tk-menu a.active {
    background: rgba(255, 255, 255, .13);
    color: #fff;
}

.tk-menu a.active {
    box-shadow: inset 3px 0 var(--gold);
    font-weight: 650;
}

.tk-menu-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
}

.tk-menu-icon svg {
    display: block;
    width: 21px;
    height: 21px;
    stroke: currentColor;
}

        @media (max-width: 700px) {
            .main-ban-to-chuc .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="layout-ban-to-chuc">

    <!-- SIDEBAR BÊN TRÁI -->
    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">
            <div class="sidebar-logo-header">
                <h2><?= eDanhSach($tenCauLacBo) ?></h2>

                <a
                    href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
                    class="sidebar-logout-icon"
                    title="Đăng xuất"
                    aria-label="Đăng xuất"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22"
                         height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M13 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7"/>
                        <path d="M10 12h11"/>
                        <path d="m17 8 4 4-4 4"/>
                    </svg>
                </a>
            </div>

            <p>HỆ THỐNG QUẢN LÝ</p>
        </div>

        <nav class="tk-menu">

    <a href="/Nhom-4-LTW/public/index.php?trang=ban_to_chuc">
        <span class="tk-menu-icon">
            <!-- Icon tổng quan -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="m3 10 9-7 9 7"/>
                <path d="M5 9v12h14V9"/>
                <path d="M9 21v-7h6v7"/>
            </svg>
        </span>
        <span>Tổng quan</span>
    </a>

    <a class="active"
       href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb">
        <span class="tk-menu-icon">
            <!-- Icon sự kiện -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
        </span>
        <span>Sự kiện của CLB</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M9 11 12 14 22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </span>
        <span>Đăng ký tham gia</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=thong_ke">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M3 3v18h18"/>
                <path d="m7 14 4-4 4 4 6-7"/>
            </svg>
        </span>
        <span>Thống kê sự kiện</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=thanh_vien_clb">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="10" cy="7" r="4"/>
                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </span>
        <span>Thành viên CLB</span>
    </a>

</nav>
    </aside>

    <!-- VÙNG NỘI DUNG BÊN PHẢI -->
    <main class="main-ban-to-chuc">

        <!-- TOPBAR -->
        <header class="topbar-ban-to-chuc">

            <div class="page-heading"></div>

            <div class="topbar-actions">

                <a
                    class="notification-link"
                    href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
                    title="Thông báo"
                    aria-label="Thông báo"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22"
                         height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <?php if ((int) $soThongBaoChuaDoc > 0): ?>
                        <span class="notification-badge">
                            <?= (int) $soThongBaoChuaDoc ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="account-info">

                    <div class="account-avatar">
                        <?= eDanhSach(
                            mb_substr(
                                $nguoiDung['ho_ten'] ?? 'U',
                                0,
                                1,
                                'UTF-8'
                            )
                        ) ?>
                    </div>

                    <div>
                        <strong>
                            <?= eDanhSach($nguoiDung['ho_ten'] ?? '') ?>
                        </strong>

                        <small>Ban tổ chức</small>
                    </div>

                </div>
            </div>
        </header>

        <!-- NỘI DUNG DANH SÁCH SỰ KIỆN -->
        <div class="container">

            <h1>Quản lý sự kiện</h1>

            <p>Danh sách các sự kiện do Ban tổ chức quản lý.</p>

            <a
                class="btn"
                href="/Nhom-4-LTW/public/index.php?trang=them_su_kien"
            >
                + Thêm sự kiện
            </a>

            <!-- FORM LỌC -->
            <form
                method="GET"
                action="/Nhom-4-LTW/public/index.php"
                class="filter-form"
            >

                <input type="hidden" name="trang" value="danh_sach">

                <div>

                    <label>
                        Tên sự kiện<br>

                        <input
                            type="text"
                            name="tu_khoa"
                            value="<?= eDanhSach($tuKhoa) ?>"
                            placeholder="Nhập tên sự kiện..."
                        >
                    </label>

                    <label>
                        Trạng thái<br>

                        <select name="loc_trang_thai">
                            <option value="">Tất cả trạng thái</option>

                            <?php foreach ($nhanTrangThai as $ma => $nhan): ?>
                                <option
                                    value="<?= eDanhSach($ma) ?>"
                                    <?= $locTrangThai === $ma ? 'selected' : '' ?>
                                >
                                    <?= eDanhSach($nhan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Từ ngày bắt đầu<br>

                        <input
                            type="date"
                            name="tu_ngay"
                            value="<?= eDanhSach($tuNgay) ?>"
                        >
                    </label>

                    <label>
                        Đến ngày bắt đầu<br>

                        <input
                            type="date"
                            name="den_ngay"
                            value="<?= eDanhSach($denNgay) ?>"
                        >
                    </label>

                    <button type="submit" class="btn">
                        Tìm kiếm
                    </button>

                    <a
                        class="btn"
                        style="background:#64748b;"
                        href="/Nhom-4-LTW/public/index.php?trang=danh_sach"
                    >
                        Xóa bộ lọc
                    </a>

                </div>
            </form>

            <?php if ($loiKhoangNgay !== ''): ?>
                <p style="color:#c62828;" role="alert">
                    <?= eDanhSach($loiKhoangNgay) ?>
                </p>
            <?php endif; ?>

            <p style="color:#64748b;">
                Tìm thấy
                <strong><?= count($suKienList) ?></strong>
                sự kiện.
            </p>

            <!-- BẢNG SỰ KIỆN -->
            <table>

                <thead>
                <tr>
                    <th>STT</th>
                    <th>Ảnh sự kiện</th>
                    <th>Tên sự kiện</th>
                    <th>Địa điểm</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Số lượng tối đa</th>
                    <th>Đã đăng ký</th>
                    <th>Đã tham gia</th>
                    <th>Chỗ còn lại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>

                <tbody>

                <?php if (empty($suKienList)): ?>

                    <tr>
                        <td colspan="11" class="empty">
                            Chưa có sự kiện nào.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($suKienList as $index => $suKien): ?>

                        <?php
                        $trangThai = $suKien['trang_thai'] ?? '';

                        $daKetThuc = in_array(
                            $trangThai,
                            ['da_huy', 'ket_thuc'],
                            true
                        );
                        ?>

                        <tr>

                            <td><?= $index + 1 ?></td>
                            <td>
    <?php if (!empty($suKien['hinh_anh'])): ?>
        <img
            src="<?= eDanhSach($suKien['hinh_anh']) ?>"
            alt="Ảnh sự kiện"
            class="anh-su-kien"
        >
    <?php else: ?>
        <span style="color:#94a3b8;">Chưa có ảnh</span>
    <?php endif; ?>
</td>

                            <td>
                                <?= eDanhSach($suKien['ten_su_kien'] ?? '') ?>
                            </td>

                            <td>
                                <?= eDanhSach($suKien['dia_diem'] ?? '') ?>
                            </td>

                            <td>
                                <?= eDanhSach($suKien['thoi_gian_bat_dau'] ?? '') ?>
                            </td>

                            <td>
                                <?= (int) ($suKien['so_luong_toi_da'] ?? 0) ?>
                            </td>

                            <td>
                                <?= (int) ($suKien['so_nguoi_dang_ky'] ?? 0) ?>
                            </td>

                            <td>
                                <?= (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0) ?>
                            </td>

                            <td>
                                <?= (int) ($suKien['so_cho_con_lai'] ?? 0) ?>
                            </td>

                            <td>
                                <?= eDanhSach(
                                    $nhanTrangThai[$trangThai] ?? $trangThai
                                ) ?>
                            </td>

                            <td>
                                <div class="actions">

                                    <a
                                        class="btn view-btn"
                                        href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id=<?= (int) $suKien['id'] ?>"
                                    >
                                        Xem người đăng ký
                                    </a>

                                    <a
    class="btn"
    style="background:#7c3aed;"
    href="/Nhom-4-LTW/public/index.php?trang=chi_tiet_su_kien&id=<?= (int) $suKien['id'] ?>&nguon=ban_to_chuc"
>
    Chi tiết
</a>

                                    <?php if (!$daKetThuc): ?>

                                        <a
                                            class="btn"
                                            href="/Nhom-4-LTW/public/index.php?trang=sua_su_kien&id=<?= (int) $suKien['id'] ?>"
                                        >
                                            Sửa
                                        </a>

                                        <form
                                            action="/Nhom-4-LTW/public/index.php?trang=huy_su_kien"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn hủy sự kiện này?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= eDanhSach(taoCsrfToken()) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $suKien['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn"
                                                style="background:#d97706;"
                                            >
                                                Hủy
                                            </button>

                                        </form>

                                    <?php endif; ?>

                                    <form
                                        action="/Nhom-4-LTW/public/index.php?trang=xoa_su_kien"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= eDanhSach(taoCsrfToken()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $suKien['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn delete-btn"
                                        >
                                            Xóa
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </main>
</div>

</body>
</html>