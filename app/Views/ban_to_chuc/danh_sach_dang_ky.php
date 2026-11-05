<?php
require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../Core/xac_thuc.php';

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

global $pdo;

$tenCauLacBo = 'Câu lạc bộ';
$soThongBaoChuaDoc = $soThongBaoChuaDoc ?? 0;

if ($nguoiDung) {
    $stmtTenClb = $pdo->prepare("
        SELECT ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE nguoi_quan_ly_id = :nguoi_dung_id
        LIMIT 1
    ");

    $stmtTenClb->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);

    $tenCauLacBo = $stmtTenClb->fetchColumn()
        ?: 'Câu lạc bộ';
}

function eDangKy($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$coSuKienDangChon = isset($suKien) && is_array($suKien);
$daDenGioBatDau = false;

if ($coSuKienDangChon && !empty($suKien['thoi_gian_bat_dau'])) {
    $daDenGioBatDau =
        strtotime($suKien['thoi_gian_bat_dau']) <= time();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css"
    >

    <title>Đăng ký tham gia</title>

    <style>
        /* Chỉ bổ sung phần nội dung của trang này.
           Bố cục chính sử dụng ban_to_chuc.css. */

        .dang-ky-container {
            min-width: 0;
            margin-bottom: 24px;
            padding: 25px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .dang-ky-container h2 {
            margin-top: 0;
            color: var(--green);
        }

        .dang-ky-mo-ta {
            margin-top: 0;
            color: var(--muted);
        }

        .dang-ky-danh-sach {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .dang-ky-su-kien {
            min-width: 0;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--white);
        }

        .dang-ky-su-kien h3 {
            margin: 0 0 12px;
            color: var(--green);
            font-size: 17px;
        }

        .dang-ky-su-kien p {
            margin: 7px 0;
            color: var(--muted);
        }

        .dang-ky-su-kien strong {
            color: var(--text);
        }

        .dang-ky-thanh-tien-do {
            overflow: hidden;
            width: 100%;
            height: 9px;
            margin: 14px 0;
            border-radius: 20px;
            background: #edf0e9;
        }

        .dang-ky-thanh-tien-do > div {
            height: 100%;
            border-radius: inherit;
            background: #4e896a;
        }

        .dang-ky-nut {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            min-height: 38px;
            margin-top: 12px;
            padding: 9px 15px;
            border: 0;
            border-radius: 8px;
            background: var(--green);
            color: var(--white);
            font-weight: 600;
            text-decoration: none;
        }

        .dang-ky-nut:hover {
            background: var(--green-hover);
            color: var(--white);
        }

        .dang-ky-bang {
            overflow-x: auto;
            width: 100%;
            margin-top: 20px;
        }

        .dang-ky-bang table {
            min-width: 700px;
        }

        .dang-ky-trang-thai {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: var(--green-light);
            color: var(--green);
            font-size: 12px;
            font-weight: 600;
        }

        .dang-ky-trang-thai.da-tham-gia {
            background: #e7eee8;
            color: #285748;
        }

        .dang-ky-trang-thai.da-huy {
            background: #fff0ee;
            color: #a63c36;
        }

        .dang-ky-trong {
            padding: 30px;
            color: var(--muted);
            text-align: center;
        }

        .dang-ky-quay-lai {
            display: inline-block;
            margin-bottom: 18px;
            color: var(--green);
            font-weight: 600;
        }
        .page-heading {
    margin-bottom: 24px;
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

        @media (max-width: 900px) {
            .dang-ky-danh-sach {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .dang-ky-container {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

<div class="layout-ban-to-chuc">

    <!-- SIDEBAR: giữ nguyên bố cục trang chủ -->
    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">
            <div class="sidebar-logo-header">

                <h2>
            <?= htmlspecialchars(
                $tenCauLacBo ?? 'Câu lạc bộ',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h2>

                <a
                    href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
                    class="sidebar-logout-icon"
                    title="Đăng xuất"
                    aria-label="Đăng xuất"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
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

    <a 
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

    <a class="active"
     href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky">
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

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-ban-to-chuc">

        <!-- TOPBAR: giữ nguyên bố cục trang chủ -->
        <header class="topbar-ban-to-chuc">

            <div class="page-heading">
                <!-- Trang chủ không hiển thị lời chào ở khu vực này -->
            </div>

            <div class="topbar-actions">

                <a
                    class="notification-link"
                    href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
                    title="Thông báo"
                    aria-label="Thông báo"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
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
                        <?= eDangKy(
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
                            <?= eDangKy($nguoiDung['ho_ten'] ?? '') ?>
                        </strong>
                        <small>Ban tổ chức</small>
                    </div>

                </div>

            </div>

        </header>

        <!-- TIÊU ĐỀ TRANG -->
        <div class="page-heading">
            <h1>Đăng ký tham gia</h1>
            
        </div>

        <?php if (!$coSuKienDangChon): ?>

            <!-- DANH SÁCH SỰ KIỆN -->
            <section class="dang-ky-container">

                <h2>Sự kiện cần quản lý đăng ký</h2>

                <p class="dang-ky-mo-ta">
                    Chọn một sự kiện để xem danh sách người đăng ký
                    và thực hiện điểm danh.
                </p>

                <?php if (empty($suKienList)): ?>

                    <div class="dang-ky-trong">
                        Hiện chưa có sự kiện cần quản lý đăng ký.
                    </div>

                <?php else: ?>

                    <div class="dang-ky-danh-sach">

                        <?php foreach ($suKienList as $mucSuKien): ?>

                            <?php
                            $idSuKien = (int) ($mucSuKien['id'] ?? 0);
                            $tenSuKien = $mucSuKien['ten_su_kien'] ?? '';
                            $diaDiem = $mucSuKien['dia_diem'] ?? 'Chưa cập nhật';
                            $thoiGian = $mucSuKien['thoi_gian_bat_dau'] ?? '';
                            $soLuongToiDa = (int) ($mucSuKien['so_luong_toi_da'] ?? 0);
                            $soNguoiDangKy = (int) ($mucSuKien['so_nguoi_dang_ky'] ?? 0);
                            $soChoConLai = (int) ($mucSuKien['so_cho_con_lai'] ?? max(0, $soLuongToiDa - $soNguoiDangKy));

                            $tyLeLapDay = $soLuongToiDa > 0
                                ? min(100, round($soNguoiDangKy / $soLuongToiDa * 100))
                                : 0;
                            ?>

                            <article class="dang-ky-su-kien">

                                <h3><?= eDangKy($tenSuKien) ?></h3>

                                <p>
                                    📍
                                    <?= eDangKy($diaDiem) ?>
                                </p>

                                <p>
    🕒
    <?= eDangKy($thoiGian) ?>
</p>
                                <p>
    <strong>Trạng thái:</strong>
    <?= eDangKy(
        ($mucSuKien['trang_thai'] ?? '') === 'cong_khai'
            ? 'Công khai'
            : ($mucSuKien['trang_thai'] ?? '')
    ) ?>
</p>

                                <div class="dang-ky-thanh-tien-do">
                                    <div style="width: <?= $tyLeLapDay ?>%;"></div>
                                </div>

                                <p>
                                    <strong>
                                        <?= $soNguoiDangKy ?>/<?= $soLuongToiDa ?>
                                        người đăng ký
                                    </strong>
                                    · Còn <?= $soChoConLai ?> chỗ
                                </p>

                                <a
                                    class="dang-ky-nut"
                                    href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id=<?= $idSuKien ?>"
                                >
                                    Xem danh sách →
                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

        <?php else: ?>

            <!-- DANH SÁCH NGƯỜI ĐĂNG KÝ CỦA SỰ KIỆN -->
            <section class="dang-ky-container">

                <a
                    class="dang-ky-quay-lai"
                    href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky"
                >
                    ← Quay lại danh sách sự kiện
                </a>

                <h2>
                    <?= eDangKy($suKien['ten_su_kien'] ?? 'Danh sách đăng ký') ?>
                </h2>

                <p class="dang-ky-mo-ta">
                    Danh sách người đăng ký và trạng thái tham gia sự kiện.
                </p>

                <?php if (!empty($thongKe)): ?>

                    <section class="stats">

                        <div class="stat-card">
                            <p>Lượt đăng ký</p>
                            <strong>
                                <?= (int) ($thongKe['tong_dang_ky'] ?? $thongKe['so_nguoi_dang_ky'] ?? 0) ?>
                            </strong>
                        </div>

                        <div class="stat-card">
                            <p>Đã tham gia</p>
                            <strong>
                                <?= (int) ($thongKe['da_tham_gia'] ?? $thongKe['so_nguoi_da_tham_gia'] ?? 0) ?>
                            </strong>
                        </div>

                        <div class="stat-card">
                            <p>Đã hủy</p>
                            <strong>
                                <?= (int) ($thongKe['da_huy'] ?? $thongKe['so_nguoi_da_huy'] ?? 0) ?>
                            </strong>
                        </div>

                    </section>

                <?php endif; ?>

                <?php if (empty($dangKyList)): ?>

                    <div class="dang-ky-trong">
                        Chưa có người đăng ký sự kiện này.
                    </div>

                <?php else: ?>

                    <div class="dang-ky-bang">

                        <table>

                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Thời gian đăng ký</th>
                                    <th>Trạng thái</th>
                                    <th>Điểm danh</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($dangKyList as $index => $dangKy): ?>

                                    <?php
                                    $trangThai = $dangKy['trang_thai'] ?? '';
                                    $tenTrangThai = 'Đã đăng ký';
                                    $classTrangThai = '';

                                    if ($trangThai === 'da_tham_gia') {
                                        $tenTrangThai = 'Đã tham gia';
                                        $classTrangThai = 'da-tham-gia';
                                    } elseif ($trangThai === 'da_huy') {
                                        $tenTrangThai = 'Đã hủy';
                                        $classTrangThai = 'da-huy';
                                    }
                                    ?>

                                    <tr>

                                        <td><?= $index + 1 ?></td>

                                        <td>
                                            <?= eDangKy($dangKy['ho_ten'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= eDangKy($dangKy['email'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= eDangKy($dangKy['thoi_gian_dang_ky'] ?? '') ?>
                                        </td>

                                        <td>
                                            <span class="dang-ky-trang-thai <?= eDangKy($classTrangThai) ?>">
                                                <?= eDangKy($tenTrangThai) ?>
                                            </span>
                                        </td>

                                        <td>

                                            <?php if ($trangThai === 'da_dang_ky'): ?>

    <?php if ($daDenGioBatDau): ?>

        <form
            method="POST"
            action="/Nhom-4-LTW/public/index.php?trang=diem_danh"
        >

            <input
                type="hidden"
                name="dang_ky_id"
                value="<?= (int) ($dangKy['dang_ky_id'] ?? $dangKy['id'] ?? 0) ?>"
            >

            <input
                type="hidden"
                name="su_kien_id"
                value="<?= (int) ($suKien['id'] ?? 0) ?>"
            >

            <?php if (function_exists('taoCsrfToken')): ?>
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= eDangKy(taoCsrfToken()) ?>"
                >
            <?php endif; ?>

            <button type="submit" class="btn">
                Điểm danh
            </button>

        </form>

    <?php else: ?>

        <span class="dang-ky-trang-thai">
            Chưa đến giờ
        </span>

    <?php endif; ?>

<?php elseif ($trangThai === 'da_tham_gia'): ?>

    <span class="dang-ky-trang-thai da-tham-gia">
        Đã điểm danh
    </span>

<?php else: ?>

    <span>—</span>

<?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    </main>

</div>

</body>
</html>