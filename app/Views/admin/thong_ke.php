<?php
$e = static fn($value) => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$tongSoSuKien = $tongSoSuKien ?? 0;
$tongLuotDangKy = $tongLuotDangKy ?? 0;
$thongKeTrangThai = $thongKeTrangThai ?? [];
$dangKyTheoSuKien = $dangKyTheoSuKien ?? [];

$tenTrangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Công khai',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Kết thúc',
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thống kê</title>

    <style>
    * {
    box-sizing: border-box;
}

html {
    min-height: 100%;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #263b34;
    background: #f5f6f2;
}
.app-layout {
    display: grid;
    grid-template-columns: 230px minmax(0, 1fr);
    min-height: 100vh;
}

.thanh-ben {
    box-sizing: border-box;
    width: 230px;
    min-width: 230px;
    min-height: 100vh;
    padding: 22px 14px;
    margin: 0;
    background: #075747;
    color: #fff;
    font-family: Arial, Helvetica, sans-serif;
}

.thuong-hieu {
    box-sizing: border-box;
    height: 26px;
    margin: 0 8px 28px;
    color: #fff;
    font-size: 17px;
    line-height: 26px;
    font-weight: 700;
    white-space: nowrap;
}

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 8px;
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
    height: 45px;
    min-height: 45px;
    margin: 0;
    padding: 0 12px;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: #f5faf6;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: 400;
    line-height: 20px;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s;
}

.menu-ben a:hover {
    background: #106d5a;
}

.menu-ben a.dang-chon {
    background: #13866f;
    color: #fff;
    font-weight: 600;
}

.menu-icon {
    display: inline-flex;
    flex: 0 0 16px;
    width: 16px;
    height: 20px;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    line-height: 20px;
}
.noi-dung-chinh {
    box-sizing: border-box;
    min-width: 0;
}
@media (max-width: 600px) {
    .app-layout {
        grid-template-columns: minmax(0, 1fr);
    }

    .thanh-ben {
        width: 100%;
        min-width: 0;
        min-height: auto;
        padding: 14px;
    }

    .thuong-hieu {
        height: 26px;
        margin: 0 4px 12px;
        line-height: 26px;
    }

    .menu-ben {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
    }

    .menu-ben a {
        flex: 1 1 auto;
        width: auto;
        min-width: 0;
        height: 40px;
        min-height: 40px;
        padding: 0 9px;
        font-size: 12px;
        white-space: normal;
    }
}

/* Khu vực nội dung chính */
.noi-dung-chinh {
    min-width: 0;
    padding: 28px;
    background: #f5f6f2;
}

/* Tiêu đề */
.dau-trang {
    margin-bottom: 24px;
}

.tieu-de-trang h1 {
    margin: 0 0 7px;
    color: #174b3d;
    font-size: 27px;
    line-height: 1.3;
}

.mo-ta {
    margin: 0;
    color: #68766f;
    font-size: 14px;
}
.tong-quan {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.the {
    min-width: 0;
    padding: 23px 22px;
    border: 1px solid #e0e5dc;
    border-radius: 11px;
    background: #fffdf7;
    box-shadow: 0 2px 7px rgba(25, 65, 48, .04);
}

.the-tieu-de {
    margin-bottom: 12px;
    color: #52645a;
    font-size: 14px;
}

.so {
    color: #08705a;
    font-size: 32px;
    font-weight: 700;
    line-height: 1.3;
}
.khu-vuc {
    min-width: 0;
    margin-bottom: 24px;
    padding: 21px 22px 22px;
    border: 1px solid #e0e5dc;
    border-radius: 11px;
    background: #fffdf8;
    box-shadow: 0 2px 7px rgba(25, 65, 48, .03);
}

.khu-vuc h2 {
    margin: 0 0 17px;
    color: #174b3d;
    font-size: 19px;
    line-height: 1.4;
}
.cuon {
    width: 100%;
    min-width: 0;
    overflow-x: auto;
    border-radius: 5px;
}

.cuon table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
    background: #fffdf8;
}

.cuon th,
.cuon td {
    padding: 12px 15px;
    border: 1px solid #dfe6dc;
    text-align: left;
    vertical-align: middle;
    overflow-wrap: anywhere;
}

.cuon th {
    background: #eaf2eb;
    color: #244b3d;
    font-size: 13px;
    font-weight: 700;
}

.cuon td {
    color: #34473d;
    font-size: 14px;
}

.cuon tbody tr:nth-child(even) {
    background: #f6f7f0;
}

.cuon tbody tr:hover {
    background: #edf4eb;
}
.cuon th:first-child,
.cuon td:first-child {
    width: 100px;
}
.khong-co-du-lieu {
    padding: 22px !important;
    text-align: center !important;
    color: #68766f !important;
}
@media (max-width: 900px) {
    .app-layout {
        grid-template-columns: 200px minmax(0, 1fr);
    }

    .noi-dung-chinh {
        padding: 22px 18px;
    }

    .khu-vuc {
        padding: 17px;
    }

    .cuon th,
    .cuon td {
        padding: 10px;
    }
}
@media (max-width: 600px) {
    .app-layout {
        grid-template-columns: minmax(0, 1fr);
    }

    .thanh-ben {
        min-height: auto;
        padding: 14px;
    }

    .thuong-hieu {
        margin: 0 4px 12px;
    }

    .menu-ben {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
    }

    .menu-ben a {
        flex: 1 1 auto;
        justify-content: center;
        min-height: 40px;
        padding: 9px;
        font-size: 12px;
    }

    .noi-dung-chinh {
        padding: 20px 12px;
    }

    .tieu-de-trang h1 {
        font-size: 23px;
    }

    .tong-quan {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .the {
        padding: 18px;
    }

    .so {
        font-size: 28px;
    }

    .khu-vuc {
        padding: 15px 12px;
        margin-bottom: 18px;
    }

    .khu-vuc h2 {
        font-size: 17px;
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
    <div class="thuong-hieu">Sự kiện sinh viên</div>

    <nav class="menu-ben" aria-label="Điều hướng">
        <a href="?action=quan-ly-su-kien">
            <span class="menu-icon">▦</span>
            <span>Quản lý sự kiện</span>
        </a>
        <a href="?action=quan-ly-tai-khoan">
            <span class="menu-icon">♙</span>
            <span>Quản lý Ban tổ chức</span>
        </a>
        <a class="dang-chon" href="?action=thong-ke">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>
</aside>
    <main class="noi-dung-chinh">
        <header class="dau-trang">
            <div class="tieu-de-trang">
                <div>
                    <h1>Thống kê hệ thống</h1>
                    <p class="mo-ta">
                        Theo dõi số lượng sự kiện và tình hình đăng ký.
                    </p>
                </div>
            </div>
        </header>
        <section class="tong-quan">
            <div class="the">
                <div class="the-tieu-de">Tổng số sự kiện</div>
                <div class="so">
                    <?= $e($tongSoSuKien) ?>
                </div>
            </div>
            <div class="the">
                <div class="the-tieu-de">
                    Tổng lượt đăng ký chưa hủy
                </div>
                <div class="so">
                    <?= $e($tongLuotDangKy) ?>
                </div>
            </div>
        </section>
        <section class="khu-vuc">
            <h2>Số sự kiện theo trạng thái</h2>
            <div class="cuon">
                <table>
                    <thead>
                        <tr>
                            <th>Trạng thái</th>
                            <th>Số sự kiện</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$thongKeTrangThai): ?>
                        <tr>
                            <td colspan="2" class="khong-co-du-lieu">
                                Chưa có dữ liệu thống kê.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($thongKeTrangThai as $dong): ?>
                            <tr>
                                <td>
                                    <?= $e(
                                        $tenTrangThai[$dong['trang_thai']]
                                        ?? $dong['trang_thai']
                                    ) ?>
                                </td>
                                <td>
                                    <?= $e($dong['so_luong']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="khu-vuc">
            <h2>Lượt đăng ký theo từng sự kiện</h2>
            <div class="cuon">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sự kiện</th>
                            <th>Lượt đăng ký</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$dangKyTheoSuKien): ?>
                        <tr>
                            <td colspan="3" class="khong-co-du-lieu">
                                Chưa có sự kiện nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
usort($dangKyTheoSuKien, static function ($a, $b) {
    return (int) $a['id'] <=> (int) $b['id'];
});
?>

<?php foreach ($dangKyTheoSuKien as $dong): ?>
    <tr>
        <td><?= $e($dong['id']) ?></td>
        <td><?= $e($dong['ten_su_kien']) ?></td>
        <td><?= $e($dong['so_luot_dang_ky']) ?></td>
    </tr>
<?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
