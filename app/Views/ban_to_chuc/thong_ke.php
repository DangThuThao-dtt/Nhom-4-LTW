<?php
require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../Core/xac_thuc.php';

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

if (!$nguoiDung) {
    header('Location: /Nhom-4-LTW/public/index.php?trang=dang_nhap');
    exit;
}

$laAdmin = (($nguoiDung['vai_tro'] ?? '') === 'admin');

/*
|--------------------------------------------------------------------------
| DỮ LIỆU THỐNG KÊ
|--------------------------------------------------------------------------
| Các biến này được tính từ Controller:
| $thongKeList
| $tongSuKien
| $tongNguoiDangKy
| $tongNguoiDaThamGia
*/

$thongKeList = $thongKeList ?? [];

$tongSuKien = (int) ($tongSuKien ?? count($thongKeList));
$tongNguoiDangKy = (int) ($tongNguoiDangKy ?? 0);
$tongNguoiDaThamGia = (int) ($tongNguoiDaThamGia ?? 0);

$tyLeThamGia = $tongNguoiDangKy > 0
    ? round(($tongNguoiDaThamGia / $tongNguoiDangKy) * 100)
    : 0;

/*
|--------------------------------------------------------------------------
| HÀM HIỂN THỊ AN TOÀN
|--------------------------------------------------------------------------
*/

function thongKeEsc($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| TÍNH CÁC CHỈ SỐ CHO TỪNG SỰ KIỆN
|--------------------------------------------------------------------------
*/

foreach ($thongKeList as &$suKien) {
    $soDangKy = (int) ($suKien['so_nguoi_dang_ky'] ?? 0);
    $soThamGia = (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0);
    $soLuongToiDa = (int) ($suKien['so_luong_toi_da'] ?? 0);

    $suKien['ty_le_tham_gia'] = $soDangKy > 0
        ? round(($soThamGia / $soDangKy) * 100)
        : 0;

    $suKien['ty_le_lap_day'] = $soLuongToiDa > 0
        ? min(100, round(($soDangKy / $soLuongToiDa) * 100))
        : 0;
}
unset($suKien);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Thống kê sự kiện</title>

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css"
    >

    <style>
        :root {
            --tk-green: #183f35;
            --tk-green-hover: #285748;
            --tk-green-light: #e7eee8;
            --tk-beige: #f7f5ed;
            --tk-white: #ffffff;
            --tk-text: #26352e;
            --tk-muted: #758078;
            --tk-border: #e5e7df;
            --tk-gold: #c99a50;
            --tk-red: #c6534b;
            --tk-shadow: 0 5px 20px rgba(31, 55, 43, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--tk-beige);
            color: var(--tk-text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* BỐ CỤC */

        .tk-layout {
            display: flex;
            min-height: 100vh;
        }

        .tk-sidebar {
            position: sticky;
            top: 0;
            align-self: flex-start;
            display: flex;
            flex-direction: column;

            width: 250px;
            min-width: 250px;
            height: 100vh;

            padding: 28px 18px;
            overflow-y: auto;

            background: var(--tk-green);
            color: var(--tk-white);
        }

        .tk-sidebar-logo {
            padding: 4px 12px 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, .16);
        }

        .tk-sidebar-logo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .tk-sidebar-logo h2 {
            flex: 1;
            min-width: 0;
            margin: 0;
            color: #fff;
            font-size: 19px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .tk-sidebar-logo p {
            margin: 5px 0 0;
    color: #c6d6cd;
    font-size: 12px;
    font-weight: 400;
    letter-spacing: 0px;
    
        }

        .tk-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 8px;
            color: #c6d6cd;
            transition: .2s;
        }

        .tk-logout:hover {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

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
            transition: background .2s, color .2s;
        }

        .tk-menu a:hover,
        .tk-menu a.active {
            background: rgba(255, 255, 255, .13);
            color: #fff;
        }

        .tk-menu a.active {
            box-shadow: inset 3px 0 var(--tk-gold);
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
            width: 21px;
            height: 21px;
            stroke: currentColor;
        }

        /* NỘI DUNG CHÍNH */

        .tk-main {
            flex: 1;
            min-width: 0;
            padding: 30px 32px;
        }

        /* TOPBAR */

        .tk-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 28px;
        }

        .tk-page-heading h1 {
            margin: 0;
            color: var(--tk-green);
            font-size: 26px;
            font-weight: 750;
            line-height: 1.35;
        }

        .tk-page-heading p {
            margin: 7px 0 0;
            color: var(--tk-muted);
            font-size: 13px;
        }

        .tk-topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .tk-notification {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;

            border: 1px solid var(--tk-border);
            border-radius: 12px;
            background: #fff;
            color: var(--tk-green);
        }

        .tk-notification:hover {
            background: var(--tk-green-light);
        }

        .tk-notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;

            display: flex;
            align-items: center;
            justify-content: center;

            min-width: 18px;
            height: 18px;
            padding: 0 4px;

            border-radius: 20px;
            background: var(--tk-red);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
        }

        .tk-account {
            display: flex;
            align-items: center;
            gap: 10px;

            padding: 7px 12px;
            border: 1px solid var(--tk-border);
            border-radius: 12px;
            background: #fff;
        }

        .tk-avatar {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 38px;
            height: 38px;
            border-radius: 50%;

            background: var(--tk-green-light);
            color: var(--tk-green);
            font-weight: 750;
        }

        .tk-account strong {
            display: block;
            color: var(--tk-text);
            font-size: 12px;
        }

        .tk-account small {
            display: block;
            color: var(--tk-muted);
            font-size: 11px;
        }

        /* PHẦN NỘI DUNG */

        .tk-content {
            min-width: 0;
        }

        .tk-intro {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .tk-eyebrow {
            color: #668a73;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.6px;
        }

        .tk-intro h2 {
            margin: 6px 0;
            color: var(--tk-green);
            font-size: 25px;
            font-weight: 750;
        }

        .tk-intro p {
            margin: 0;
            color: var(--tk-muted);
            font-size: 13px;
        }

        .tk-date-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 9px 13px;
            border: 1px solid var(--tk-border);
            border-radius: 9px;
            background: #fff;

            color: var(--tk-muted);
            font-size: 12px;
            white-space: nowrap;
        }

        /* THẺ CHỈ SỐ */

        .tk-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .tk-stat-card {
            position: relative;
            min-width: 0;
            padding: 21px;

            border: 1px solid var(--tk-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--tk-shadow);

            transition: transform .2s, box-shadow .2s;
        }

        .tk-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 24px rgba(31, 55, 43, .09);
        }

        .tk-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .tk-stat-label {
            color: var(--tk-muted);
            font-size: 12px;
            font-weight: 650;
        }

        .tk-stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;
            flex-shrink: 0;

            border-radius: 12px;
        }

        .tk-stat-icon svg {
            width: 22px;
            height: 22px;
        }

        .tk-icon-green {
            background: #e7eee8;
            color: #315d48;
        }

        .tk-icon-gold {
            background: #fbf1df;
            color: #b8873c;
        }

        .tk-icon-blue {
            background: #e9f0f2;
            color: #527d8b;
        }

        .tk-icon-teal {
            background: #e4f3ed;
            color: #39856c;
        }

        .tk-stat-number {
            display: block;
            margin: 17px 0 7px;
            color: var(--tk-green);
            font-size: 32px;
            font-weight: 800;
            line-height: 1.1;
        }

        .tk-stat-description {
            color: #9aa29b;
            font-size: 11px;
        }

        /* KHỐI TỶ LỆ */

        .tk-summary-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
            align-items: center;
            gap: 25px;

            padding: 23px;
            margin-bottom: 24px;

            border: 1px solid var(--tk-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--tk-shadow);
        }

        .tk-summary-title {
            margin: 0 0 7px;
            color: var(--tk-green);
            font-size: 15px;
            font-weight: 750;
        }

        .tk-summary-text {
            margin: 0;
            color: var(--tk-muted);
            font-size: 12px;
        }

        .tk-progress-area {
            min-width: 0;
        }

        .tk-progress-label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 9px;
            color: var(--tk-muted);
            font-size: 12px;
        }

        .tk-progress-label strong {
            color: var(--tk-green);
            font-size: 13px;
        }

        .tk-progress-track {
            width: 100%;
            height: 11px;
            overflow: hidden;
            border-radius: 20px;
            background: #edf0e9;
        }

        .tk-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #6c9a79, #183f35);
            transition: width .3s ease;
        }

        /* BẢNG */

        .tk-table-panel {
            min-width: 0;
            padding: 24px;

            border: 1px solid var(--tk-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--tk-shadow);
        }

        .tk-panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tk-panel-heading h3 {
            margin: 0 0 5px;
            color: var(--tk-green);
            font-size: 16px;
            font-weight: 750;
        }

        .tk-panel-heading p {
            margin: 0;
            color: var(--tk-muted);
            font-size: 12px;
        }

        .tk-table-count {
            padding: 7px 11px;
            border-radius: 8px;
            background: var(--tk-green-light);
            color: var(--tk-green);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .tk-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .tk-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .tk-table thead {
            background: #f2f5ef;
        }

        .tk-table th {
            padding: 13px 14px;
            color: #607167;
            font-size: 11px;
            font-weight: 750;
            text-align: left;
            white-space: nowrap;
        }

        .tk-table td {
            padding: 15px 14px;
            border-bottom: 1px solid #edf0e9;
            color: var(--tk-text);
            font-size: 12px;
            vertical-align: middle;
        }

        .tk-table tbody tr:hover {
            background: #fbfcf9;
        }

        .tk-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .tk-event-name {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 180px;
        }

        .tk-event-symbol {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 36px;
            height: 36px;
            flex-shrink: 0;

            border-radius: 10px;
            background: var(--tk-green-light);
            color: var(--tk-green);
        }

        .tk-event-symbol svg {
            width: 18px;
            height: 18px;
        }

        .tk-event-name strong {
            display: block;
            max-width: 220px;
            overflow: hidden;
            color: var(--tk-green);
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tk-event-name small {
            display: block;
            margin-top: 3px;
            color: #9aa29b;
            font-size: 10px;
        }

        .tk-number-cell {
            color: var(--tk-green);
            font-size: 13px;
            font-weight: 750;
        }

        .tk-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            padding: 6px 9px;
            border-radius: 20px;

            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .tk-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .tk-status.public {
            background: #e7f3e9;
            color: #39764d;
        }

        .tk-status.ended {
            background: #edf0f1;
            color: #68757a;
        }

        .tk-status.cancelled {
            background: #fff0ee;
            color: #b84e45;
        }

        .tk-status.draft {
            background: #fbf1df;
            color: #a77b32;
        }

        .tk-mini-progress {
            width: 90px;
            height: 6px;
            overflow: hidden;
            border-radius: 20px;
            background: #edf0e9;
        }

        .tk-mini-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: #5e9270;
        }

        .tk-rate {
            color: var(--tk-green);
            font-size: 12px;
            font-weight: 750;
        }

        .tk-empty {
            padding: 45px 20px;
            text-align: center;
            color: var(--tk-muted);
        }

        .tk-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 54px;
            height: 54px;
            margin: 0 auto 14px;

            border-radius: 16px;
            background: var(--tk-green-light);
            color: var(--tk-green);
        }

        .tk-empty-icon svg {
            width: 26px;
            height: 26px;
        }

        .tk-empty strong {
            display: block;
            margin-bottom: 5px;
            color: var(--tk-green);
            font-size: 14px;
        }

        .tk-empty p {
            margin: 0;
            font-size: 12px;
        }

        /* RESPONSIVE */

        @media (max-width: 1200px) {
            .tk-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 850px) {
            .tk-layout {
                display: block;
            }

            .tk-sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
                height: auto;
                min-height: 0;
                padding: 17px;
            }

            .tk-sidebar-logo {
                margin-bottom: 15px;
                padding-bottom: 15px;
            }

            .tk-menu {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tk-main {
                padding: 22px;
            }

            .tk-summary-panel {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .tk-main {
                padding: 15px;
            }

            .tk-topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .tk-topbar-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .tk-intro {
                align-items: flex-start;
                flex-direction: column;
            }

            .tk-intro h2 {
                font-size: 22px;
            }

            .tk-stats-grid {
                grid-template-columns: 1fr;
            }

            .tk-stat-card,
            .tk-summary-panel,
            .tk-table-panel {
                padding: 17px;
            }

            .tk-panel-heading {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="tk-layout">

    <!-- SIDEBAR -->
    <aside class="tk-sidebar">

        <div class="tk-sidebar-logo">

            <div class="tk-sidebar-logo-header">

                <h2>
                    <?= thongKeEsc($tenCauLacBo) ?>
                </h2>

                <a
                    href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
                    class="tk-logout"
                    title="Đăng xuất"
                    aria-label="Đăng xuất"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        width="20"
                        height="20"
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
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m3 10 9-7 9 7"/>
                        <path d="M5 9v12h14V9"/>
                        <path d="M9 21v-7h6v7"/>
                    </svg>
                </span>

                <span>Tổng quan</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </span>

                <span>Sự kiện của CLB</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </span>

                <span>Đăng ký tham gia</span>

            </a>

            <a
                class="active"
                href="/Nhom-4-LTW/public/index.php?trang=thong_ke"
            >

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M3 3v18h18"/>
                        <path d="m7 14 4-4 4 4 6-7"/>
                    </svg>
                </span>

                <span>Thống kê sự kiện</span>

            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=thanh_vien_clb">

                <span class="tk-menu-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
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
    <main class="tk-main">

        <!-- TOPBAR -->
        <header class="tk-topbar">

            <div class="tk-page-heading">

                <h1>Thống kê sự kiện</h1>

                <p>
                    Tổng hợp số liệu và theo dõi hiệu quả tổ chức sự kiện.
                </p>

            </div>

            <div class="tk-topbar-actions">

                <a
                    class="tk-notification"
                    href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
                    title="Thông báo"
                    aria-label="Thông báo"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="21"
                        height="21"
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

                    <?php if ($soThongBaoChuaDoc > 0): ?>

                        <span class="tk-notification-badge">
                            <?= $soThongBaoChuaDoc ?>
                        </span>

                    <?php endif; ?>

                </a>

                <div class="tk-account">

                    <div class="tk-avatar">
                        <?= thongKeEsc(
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
                            <?= thongKeEsc($nguoiDung['ho_ten'] ?? '') ?>
                        </strong>

                        <small>
                            <?= $laAdmin ? 'Quản trị viên' : 'Ban tổ chức' ?>
                        </small>

                    </div>

                </div>

            </div>

        </header>


        <div class="tk-content">

            <!-- TIÊU ĐỀ -->
            <section class="tk-intro">

                <div>

                    <span class="tk-eyebrow">
                        EVENT ANALYTICS
                    </span>

                    <h2>
                        Báo cáo hoạt động
                    </h2>

                    <p>
                        Theo dõi quy mô sự kiện, lượt đăng ký và tỷ lệ tham gia thực tế.
                    </p>

                </div>

                <div class="tk-date-label">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>

                    Cập nhật theo dữ liệu hiện tại

                </div>

            </section>


            <!-- 4 THẺ CHỈ SỐ -->
            <section class="tk-stats-grid">

                <!-- Tổng sự kiện -->
                <article class="tk-stat-card">

                    <div class="tk-stat-top">

                        <span class="tk-stat-label">
                            Tổng số sự kiện
                        </span>

                        <span class="tk-stat-icon tk-icon-green">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>

                        </span>

                    </div>

                    <strong class="tk-stat-number">
                        <?= number_format($tongSuKien) ?>
                    </strong>

                    <span class="tk-stat-description">
                        Sự kiện trong phạm vi quản lý
                    </span>

                </article>


                <!-- Tổng lượt đăng ký -->
                <article class="tk-stat-card">

                    <div class="tk-stat-top">

                        <span class="tk-stat-label">
                            Tổng lượt đăng ký
                        </span>

                        <span class="tk-stat-icon tk-icon-gold">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="10" cy="7" r="4"/>
                                <path d="M20 8v6M17 11h6"/>
                            </svg>

                        </span>

                    </div>

                    <strong class="tk-stat-number">
                        <?= number_format($tongNguoiDangKy) ?>
                    </strong>

                    <span class="tk-stat-description">
                        Không tính lượt đăng ký đã hủy
                    </span>

                </article>


                <!-- Tổng lượt tham gia -->
                <article class="tk-stat-card">

                    <div class="tk-stat-top">

                        <span class="tk-stat-label">
                            Lượt tham gia thực tế
                        </span>

                        <span class="tk-stat-icon tk-icon-blue">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="10"/>
                                <path d="m8 12 3 3 5-6"/>
                            </svg>

                        </span>

                    </div>

                    <strong class="tk-stat-number">
                        <?= number_format($tongNguoiDaThamGia) ?>
                    </strong>

                    <span class="tk-stat-description">
                        Đã được xác nhận điểm danh
                    </span>

                </article>


                <!-- Tỷ lệ tham gia -->
                <article class="tk-stat-card">

                    <div class="tk-stat-top">

                        <span class="tk-stat-label">
                            Tỷ lệ tham gia
                        </span>

                        <span class="tk-stat-icon tk-icon-teal">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M3 3v18h18"/>
                                <path d="m7 14 4-4 4 4 6-7"/>
                            </svg>

                        </span>

                    </div>

                    <strong class="tk-stat-number">
                        <?= $tyLeThamGia ?>%
                    </strong>

                    <span class="tk-stat-description">
                        Tỷ lệ người đăng ký đã tham gia
                    </span>

                </article>

            </section>


            <!-- TỶ LỆ THAM GIA -->
            <section class="tk-summary-panel">

                <div>

                    <h3 class="tk-summary-title">
                        Hiệu quả tham gia sự kiện
                    </h3>

                    <p class="tk-summary-text">
                        Trong tổng số
                        <strong><?= number_format($tongNguoiDangKy) ?></strong>
                        lượt đăng ký hợp lệ, có
                        <strong><?= number_format($tongNguoiDaThamGia) ?></strong>
                        lượt đã được điểm danh.
                    </p>

                </div>

                <div class="tk-progress-area">

                    <div class="tk-progress-label">

                        <span>
                            Tỷ lệ tham gia thực tế
                        </span>

                        <strong>
                            <?= $tyLeThamGia ?>%
                        </strong>

                    </div>

                    <div class="tk-progress-track">

                        <div
                            class="tk-progress-fill"
                            style="width: <?= min(100, max(0, $tyLeThamGia)) ?>%;"
                        ></div>

                    </div>

                </div>

            </section>


            <!-- BẢNG THỐNG KÊ CHI TIẾT -->
            <section class="tk-table-panel">

                <div class="tk-panel-heading">

                    <div>

                        <h3>
                            Thống kê chi tiết theo sự kiện
                        </h3>

                        <p>
                            So sánh lượt đăng ký, lượt tham gia và tỷ lệ tham dự của từng sự kiện.
                        </p>

                    </div>

                    <span class="tk-table-count">
                        <?= number_format(count($thongKeList)) ?> sự kiện
                    </span>

                </div>


                <?php if (empty($thongKeList)): ?>

                    <div class="tk-empty">

                        <div class="tk-empty-icon">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>

                        </div>

                        <strong>
                            Chưa có dữ liệu thống kê
                        </strong>

                        <p>
                            Khi có sự kiện và lượt đăng ký, số liệu sẽ hiển thị tại đây.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="tk-table-wrap">

                        <table class="tk-table">

                            <thead>

                                <tr>
                                    <th>STT</th>
                                    <th>Sự kiện</th>
                                    <th>Ngày tổ chức</th>
                                    <th>Lượt đăng ký</th>
                                    <th>Đã tham gia</th>
                                    <th>Tỷ lệ tham gia</th>
                                    <th>Trạng thái</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($thongKeList as $i => $suKien): ?>

                                    <?php
                                    $trangThai = $suKien['trang_thai'] ?? '';

                                    if ($trangThai === 'cong_khai') {
                                        $tenTrangThai = 'Công khai';
                                        $classTrangThai = 'public';
                                    } elseif ($trangThai === 'ket_thuc') {
                                        $tenTrangThai = 'Đã kết thúc';
                                        $classTrangThai = 'ended';
                                    } elseif ($trangThai === 'da_huy') {
                                        $tenTrangThai = 'Đã hủy';
                                        $classTrangThai = 'cancelled';
                                    } else {
                                        $tenTrangThai = 'Bản nháp';
                                        $classTrangThai = 'draft';
                                    }

                                    $ngayToChuc = !empty($suKien['thoi_gian_bat_dau'])
                                        ? date(
                                            'd/m/Y',
                                            strtotime($suKien['thoi_gian_bat_dau'])
                                        )
                                        : 'Chưa cập nhật';

                                    $soDangKy = (int) ($suKien['so_nguoi_dang_ky'] ?? 0);
                                    $soThamGia = (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0);
                                    $tyLeSuKien = (int) ($suKien['ty_le_tham_gia'] ?? 0);
                                    ?>

                                    <tr>

                                        <td>
                                            <?= $i + 1 ?>
                                        </td>

                                        <td>

                                            <div class="tk-event-name">

                                                <span class="tk-event-symbol">

                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                                                        <path d="M16 2v4M8 2v4M3 10h18"/>
                                                    </svg>

                                                </span>

                                                <div>

                                                    <strong>
                                                        <?= thongKeEsc($suKien['ten_su_kien'] ?? '') ?>
                                                    </strong>

                                                    <small>
                                                        Mã sự kiện #<?= (int) ($suKien['id'] ?? 0) ?>
                                                    </small>

                                                </div>

                                            </div>

                                        </td>

                                        <td>
                                            <?= thongKeEsc($ngayToChuc) ?>
                                        </td>

                                        <td class="tk-number-cell">
                                            <?= number_format($soDangKy) ?>
                                        </td>

                                        <td class="tk-number-cell">
                                            <?= number_format($soThamGia) ?>
                                        </td>

                                        <td>

                                            <div class="tk-mini-progress">

                                                <div
                                                    class="tk-mini-progress-fill"
                                                    style="width: <?= min(100, max(0, $tyLeSuKien)) ?>%;"
                                                ></div>

                                            </div>

                                            <span class="tk-rate">
                                                <?= $tyLeSuKien ?>%
                                            </span>

                                        </td>

                                        <td>

                                            <span class="tk-status <?= $classTrangThai ?>">
                                                <?= thongKeEsc($tenTrangThai) ?>
                                            </span>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </section>

        </div>

    </main>

</div>

</body>
</html>