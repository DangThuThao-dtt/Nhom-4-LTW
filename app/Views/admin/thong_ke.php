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

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #222;
            background: #fff;
        }

        .app-layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 245px minmax(0, 1fr);
        }

        .thanh-ben {
            padding: 24px 16px;
            border-right: 1px solid #ddd;
        }

        .thuong-hieu {
            display: none;
        }

        .menu-ben {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-ben a {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 6px;
            color: #222;
            text-decoration: none;
        }

        .menu-ben a:hover,
        .menu-ben a.dang-chon {
            background: #eee;
        }

        .noi-dung-chinh {
            min-width: 0;
            padding: 28px;
        }

        .dau-trang {
            margin-bottom: 30px;
        }

        .tieu-de-trang {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        h2 {
            margin: 0 0 18px;
            font-size: 21px;
        }

        .mo-ta {
            margin: 6px 0 0;
        }

        .tong-quan {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin: 24px 0 30px;
        }

        .the {
            padding: 22px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }

        .the-tieu-de {
            font-size: 15px;
        }

        .so {
            margin-top: 12px;
            font-size: 32px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .khu-vuc {
            margin-bottom: 28px;
            padding: 22px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .cuon {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 14px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            font-weight: bold;
            white-space: nowrap;
        }

        tbody tr:hover {
            background: #f7f7f7;
        }

        .khong-co-du-lieu {
            padding: 24px;
            text-align: center;
        }

        .lien-ket {
            display: inline-block;
            padding: 9px 14px;
            border: 1px solid #aaa;
            border-radius: 5px;
            color: #222;
            text-decoration: none;
        }

        .lien-ket:hover {
            background: #eee;
        }

        @media (max-width: 900px) {
            .app-layout {
                grid-template-columns: 190px minmax(0, 1fr);
            }

            .noi-dung-chinh {
                padding: 20px;
            }
        }

        @media (max-width: 650px) {
            .app-layout {
                grid-template-columns: 1fr;
            }

            .thanh-ben {
                padding: 12px;
                border-right: 0;
                border-bottom: 1px solid #ddd;
            }

            .menu-ben {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .menu-ben a {
                padding: 9px;
            }

            .noi-dung-chinh {
                padding: 14px;
            }

            .tong-quan {
                grid-template-columns: 1fr;
            }

            .khu-vuc {
                padding: 14px;
            }

            th,
            td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
<div class="app-layout">

    <aside class="thanh-ben">
        <div class="thuong-hieu">FlowFest</div>

        <nav class="menu-ben" aria-label="Điều hướng">
            <a href="?action=quan-ly-su-kien">
                Quản lý sự kiện
            </a>

            <a href="?action=quan-ly-tai-khoan">
                Quản lý Ban tổ chức
            </a>

            <a class="dang-chon" href="?action=thong-ke">
                Thống kê
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
                        <?php foreach ($dangKyTheoSuKien as $dong): ?>
                            <tr>
                                <td><?= $e($dong['id']) ?></td>
                                <td><?= $e($dong['ten_su_kien']) ?></td>
                                <td>
                                    <?= $e($dong['so_luot_dang_ky']) ?>
                                </td>
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
