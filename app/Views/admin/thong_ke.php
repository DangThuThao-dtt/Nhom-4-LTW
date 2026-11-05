
<?php
$e = static fn($value): string => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

// Dữ liệu do AdminController::hienThiThongKe() truyền sang.
$tongSoSuKien = (int) ($tongSoSuKien ?? 0);
$tongLuotDangKy = (int) ($tongLuotDangKy ?? 0);
$thongKeTrangThai = $thongKeTrangThai ?? [];
$dangKyTheoSuKien = $dangKyTheoSuKien ?? [];

// Các trạng thái sự kiện đang sử dụng trong dự án.
$tenTrangThai = [
    'nhap'       => 'Bản nháp',
    'cong_khai'  => 'Công khai',
    'da_huy'     => 'Đã hủy',
    'ket_thuc'   => 'Kết thúc',
];

// Khởi tạo đủ trạng thái để trạng thái chưa có sự kiện vẫn hiện số 0.
$soLuongTheoTrangThai = array_fill_keys(
    array_keys($tenTrangThai),
    0
);

foreach ($thongKeTrangThai as $dong) {
    $trangThai = $dong['trang_thai'] ?? '';
    $soLuong = (int) ($dong['so_luong'] ?? 0);

    if (array_key_exists($trangThai, $soLuongTheoTrangThai)) {
        $soLuongTheoTrangThai[$trangThai] = $soLuong;
    } elseif ($trangThai !== '') {
        // Không làm mất dữ liệu nếu cơ sở dữ liệu có trạng thái khác.
        $soLuongTheoTrangThai[$trangThai] = $soLuong;
        $tenTrangThai[$trangThai] = $trangThai;
    }
}

// Sắp xếp danh sách theo ID sự kiện tăng dần.
usort($dangKyTheoSuKien, static function ($a, $b): int {
    return (int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0);
});
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thống kê hệ thống</title>

    <style>
:root {
    --xanh: #075747;
    --xanh-dam: #174b3d;
    --xanh-nhat: #eaf2eb;
    --nen: #f5f6f2;
    --trang: #fffdf8;
    --vien: #e0e5dc;
    --chu-phu: #68766f;
    --cam-nhat: #fff3dd;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #263b34;
    background: var(--nen);
}

/* ===== BỐ CỤC CHÍNH ===== */

.app-layout {
    min-height: 100vh;
}

/* ===== SIDEBAR GIỐNG DASHBOARD ADMIN ===== */

.thanh-ben {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    box-sizing: border-box;
    width: 230px;
    padding: 14px 12px;
    background: #075747;
    color: #fff;
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    overflow-y: auto;
    z-index: 10;
}

.thuong-hieu {
    margin: 0 8px 20px;
    padding: 5px 0;
    color: #fff;
}

.thuong-hieu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
}

.thuong-hieu-title {
    color: #fff;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0;
    line-height: 1.4;
    white-space: nowrap;
    flex: 1;
}

.sidebar-logout-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 6px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    transition: background 0.2s;
}

.sidebar-logout-icon:hover {
    background: #13866f;
}

.sidebar-logout-icon svg {
    width: 22px;
    height: 22px;
}

.nhom-menu {
    margin: 14px 8px 6px;
    color: #c6d9d0;
    font-size: 10px;
    letter-spacing: 1px;
    line-height: 16px;
    text-transform: uppercase;
}

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    margin: 0;
    padding: 0;
}

.menu-ben a {
    box-sizing: border-box;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    min-width: 0;
    min-height: 40px;
    margin: 0;
    padding: 9px 10px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #f5faf6;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    font-weight: 400;
    line-height: 20px;
    text-decoration: none;
    transition: background 0.2s;
}

.menu-ben a:hover,
.menu-ben a.active {
    background: #13866f;
}

.menu-ben a.active {
    border-left: 3px solid #f1c76b;
    padding-left: 9px;
    color: #fff;
    font-weight: 700;
}

.menu-icon {
    display: inline-flex;
    flex: 0 0 20px;
    width: 20px;
    height: 20px;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 20px;
}

.chan-ben {
    margin-top: auto;
    padding: 18px 8px 0;
    color: #d6e4dc;
    font-size: 11px;
    line-height: 1.7;
}

/* ===== NỘI DUNG TRANG ===== */

.noi-dung-chinh {
    margin-left: 230px;
    min-width: 0;
    padding: 30px 28px;
    background: #f5f6f2;
}

.dau-trang {
    margin-bottom: 25px;
}

.tieu-de-trang h1 {
    margin: 0 0 7px;
    color: var(--xanh-dam);
    font-size: 27px;
}

.mo-ta {
    margin: 0;
    color: var(--chu-phu);
    font-size: 13px;
}

.nhan-de {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin: 25px 0 14px;
}

.nhan-de h2 {
    margin: 0;
    color: var(--xanh-dam);
    font-size: 18px;
}

.ghi-chu {
    color: var(--chu-phu);
    font-size: 12px;
}

/* ===== THẺ TỔNG QUAN ===== */

.tong-quan {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.the {
    min-width: 0;
    padding: 23px;
    border: 1px solid var(--vien);
    border-radius: 12px;
    background: var(--trang);
    box-shadow: 0 3px 12px rgba(25, 65, 48, .04);
}

.the-dau {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.the-tieu-de {
    color: #52645a;
    font-size: 13px;
}

.the-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 10px;
    background: var(--xanh-nhat);
    font-size: 20px;
}

.the-icon.cam {
    background: var(--cam-nhat);
}

.so {
    margin-top: 13px;
    color: #08705a;
    font-size: 34px;
    font-weight: 700;
    line-height: 1.3;
}

.the-mo-ta {
    margin-top: 7px;
    color: var(--chu-phu);
    font-size: 12px;
}

/* ===== KHU VỰC BẢNG ===== */

.khu-vuc {
    min-width: 0;
    margin-top: 23px;
    padding: 22px;
    border: 1px solid var(--vien);
    border-radius: 12px;
    background: var(--trang);
    box-shadow: 0 2px 7px rgba(25, 65, 48, .03);
}

.khu-vuc h2 {
    margin: 0 0 6px;
    color: var(--xanh-dam);
    font-size: 18px;
}

.mo-ta-bang {
    margin: 0 0 18px;
    color: var(--chu-phu);
    font-size: 12px;
}

.cuon {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: var(--trang);
}

th,
td {
    padding: 13px 15px;
    border: 1px solid var(--vien);
    text-align: left;
    vertical-align: middle;
    overflow-wrap: anywhere;
}

th {
    background: var(--xanh-nhat);
    color: #244b3d;
    font-size: 12px;
    font-weight: 700;
}

td {
    color: #34473d;
    font-size: 13px;
}

tbody tr:nth-child(even) {
    background: #f6f7f0;
}

tbody tr:hover {
    background: #edf4eb;
}

.cot-so {
    width: 130px;
    text-align: center;
}

.ma-trang-thai {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    background: var(--xanh-nhat);
    color: var(--xanh-dam);
    font-size: 12px;
    font-weight: 600;
}

.khong-co-du-lieu {
    padding: 22px;
    color: var(--chu-phu);
    text-align: center;
}

/* ===== CHÂN TRANG ===== */

.chan-trang {
    margin-top: 28px;
    padding-top: 18px;
    border-top: 1px solid var(--vien);
    color: var(--chu-phu);
    font-size: 11px;
    text-align: center;
}

/* ===== MÁY TÍNH BẢNG ===== */

@media (max-width: 1000px) and (min-width: 651px) {
    .thanh-ben {
        width: 200px;
    }

    .noi-dung-chinh {
        margin-left: 200px;
        padding: 22px 18px;
    }

    .the {
        padding: 18px;
    }
}

/* ===== ĐIỆN THOẠI ===== */

@media (max-width: 650px) {
    .app-layout {
        min-height: 100vh;
    }

    .thanh-ben {
        position: relative;
        top: auto;
        left: auto;
        bottom: auto;
        width: 100%;
        height: auto;
        min-height: 0;
        padding: 16px;
        overflow: visible;
    }

    .thuong-hieu {
        margin: 0 5px 15px;
    }

    .nhom-menu {
        margin: 15px 10px 10px;
    }

    .menu-ben {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
    }

    .menu-ben a {
        width: 100%;
        min-width: 0;
        min-height: 44px;
        padding: 10px 8px;
        font-size: 12px;
    }

    .chan-ben {
        margin-top: 16px;
    }

    .noi-dung-chinh {
        margin-left: 0;
        padding: 20px 12px;
    }

    .tieu-de-trang h1 {
        font-size: 23px;
    }

    .tong-quan {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .khu-vuc {
        padding: 16px 12px;
    }

    .cuon table {
        min-width: 420px;
    }
}
</style>
</head>

<body>
<div class="app-layout">

<aside class="thanh-ben">
    <div class="thuong-hieu">
        <div class="thuong-hieu-header">
            <span class="thuong-hieu-title">SỰ KIỆN SINH VIÊN</span>

            <a href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
               class="sidebar-logout-icon"
               title="Đăng xuất"
               aria-label="Đăng xuất">

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
    </div>

    <nav class="menu-ben">
        <div class="nhom-menu">Tổng quan</div>

        <a href="?action=dashboard">
            <span class="menu-icon">▦</span>
            <span>Dashboard Admin</span>
        </a>

        <div class="nhom-menu">Quản lý hệ thống</div>

        <a href="?action=quan-ly-su-kien">
            <span class="menu-icon">▦</span>
            <span>Quản lý sự kiện</span>
        </a>

        <a href="?action=quan-ly-cau-lac-bo">
            <span class="menu-icon">♧</span>
            <span>Quản lý câu lạc bộ</span>
        </a>

        <a href="?action=quan-ly-tai-khoan">
            <span class="menu-icon">♙</span>
            <span>Quản lý ban tổ chức</span>
        </a>

        <a href="?action=quan-ly-tai-khoan-thanh-vien">
            <span class="menu-icon">♟</span>
            <span>Quản lý thành viên</span>
        </a>

        <div class="nhom-menu">Báo cáo</div>

        <a href="?action=thong-ke"
           class="active"
           aria-current="page">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>
</aside>
    <main class="noi-dung-chinh">
        <header class="dau-trang">
            <div class="tieu-de-trang">
                <h1>Thống kê hệ thống</h1>
                <p class="mo-ta">
                    Tổng hợp số lượng sự kiện, trạng thái và lượt đăng ký
                    trên toàn hệ thống.
                </p>
            </div>
        </header>

        <div class="nhan-de">
            <h2>Tổng quan</h2>
            <span class="ghi-chu">Số liệu từ cơ sở dữ liệu</span>
        </div>

        <section class="tong-quan">
            <article class="the">
                <div class="the-dau">
                    <div class="the-tieu-de">Tổng số sự kiện</div>
                    <div class="the-icon">▣</div>
                </div>
                <div class="so"><?= $e($tongSoSuKien) ?></div>
                <div class="the-mo-ta">
                    Tổng số sự kiện được ghi nhận trong hệ thống
                </div>
            </article>

            <article class="the">
                <div class="the-dau">
                    <div class="the-tieu-de">Tổng lượt đăng ký</div>
                    <div class="the-icon cam">♙</div>
                </div>
                <div class="so"><?= $e($tongLuotDangKy) ?></div>
                <div class="the-mo-ta">
                    Tổng lượt đăng ký sự kiện được ghi nhận
                </div>
            </article>
        </section>

        <section class="khu-vuc">
            <h2>Số lượng sự kiện theo trạng thái</h2>
            <p class="mo-ta-bang">
                Theo dõi số sự kiện ở từng trạng thái quản lý.
            </p>

            <div class="cuon">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Trạng thái</th>
                            <th class="cot-so">Số sự kiện</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$thongKeTrangThai): ?>
                        <?php
                        // Vẫn hiển thị đủ các trạng thái với số lượng 0.
                        ?>
                    <?php endif; ?>

                    <?php $stt = 1; ?>
                    <?php foreach ($soLuongTheoTrangThai as $ma => $soLuong): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td>
                                <span class="ma-trang-thai">
                                    <?= $e($tenTrangThai[$ma] ?? $ma) ?>
                                </span>
                            </td>
                            <td class="cot-so"><?= $e($soLuong) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="khu-vuc">
            <h2>Lượt đăng ký theo từng sự kiện</h2>
            <p class="mo-ta-bang">
                Danh sách sự kiện và số lượt đăng ký tương ứng.
            </p>

            <div class="cuon">
                <table>
                    <thead>
                        <tr>
                            <th class="cot-so">ID sự kiện</th>
                            <th>Tên sự kiện</th>
                            <th class="cot-so">Lượt đăng ký</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$dangKyTheoSuKien): ?>
                        <tr>
                            <td colspan="3" class="khong-co-du-lieu">
                                Chưa có dữ liệu đăng ký sự kiện.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dangKyTheoSuKien as $dong): ?>
                            <tr>
                                <td class="cot-so">
                                    <?= $e($dong['id'] ?? '') ?>
                                </td>
                                <td>
                                    <?= $e($dong['ten_su_kien'] ?? '') ?>
                                </td>
                                <td class="cot-so">
                                    <?= $e($dong['so_luot_dang_ky'] ?? 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="chan-trang">
            © Nhóm 4 - Hệ thống quản lý sự kiện sinh viên
        </footer>
    </main>
</div>
</body>
</html>
