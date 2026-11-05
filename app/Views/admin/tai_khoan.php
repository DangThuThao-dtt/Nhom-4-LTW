<?php
$e = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$thongBao = $_GET['thong_bao'] ?? '';
$danhSachTaiKhoanVuaTao = $_SESSION['tai_khoan_vua_tao'] ?? [];
unset($_SESSION['tai_khoan_vua_tao']);

$noiDungThongBao = [
    'tao_tai_khoan_thanh_cong' => 'Tạo tài khoản ban tổ chức thành công.',
    'cap_nhat_tai_khoan_thanh_cong' => 'Cập nhật tài khoản thành công.',
    'xoa_tai_khoan_thanh_cong' => 'Xóa tài khoản thành công!',
    'dat_lai_mat_khau_thanh_cong' => 'Đặt lại mật khẩu thành công.',
    'da_cap_lai_tai_khoan' => 'Đã cấp lại tài khoản cho Ban tổ chức.',
    'email_da_ton_tai' => 'Email này đã được sử dụng. Vui lòng nhập email khác.',
    'clb_da_co_ban_to_chuc' => 'Câu lạc bộ này đã có tài khoản ban tổ chức phụ trách.',
    'clb_khong_ton_tai' => 'Câu lạc bộ không tồn tại hoặc đã được thay đổi.',
    'mat_khau_cu_khong_dung' => 'Mật khẩu hiện tại không chính xác.',
    'mat_khau_moi_khong_khop' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.',
    'cap_nhat_that_bai' => 'Không thể cập nhật tài khoản. Vui lòng kiểm tra dữ liệu.',
    'tao_tai_khoan_that_bai' => 'Không thể tạo tài khoản. Vui lòng kiểm tra dữ liệu và thử lại.',
    'dat_lai_mat_khau_that_bai' => 'Không thể đặt lại mật khẩu. Vui lòng kiểm tra tài khoản.',
'khong_co_clb_chua_phan_cong' => 'Tất cả câu lạc bộ hiện đã có người quản lý. Không có tài khoản nào được tạo.',
'chua_chon_clb' => 'Vui lòng chọn ít nhất một câu lạc bộ.',
'tu_dong_tao_thanh_cong' => 'Đã tạo tài khoản cho các câu lạc bộ được chọn.',
'tu_dong_tao_that_bai' => 'Tạo tài khoản không thành công. Vui lòng thử lại.',
];
$danhSachTaiKhoan = $danhSachTaiKhoan ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
$tuKhoa = $tuKhoa ?? '';
$cauLacBoId = $cauLacBoId ?? 0;
$tongTaiKhoan = $tongTaiKhoan ?? 0;
$tongKetQua = $tongKetQua ?? 0;
$trang = $trang ?? 1;
$tongTrang = $tongTrang ?? 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý tài khoản Ban tổ chức</title>
    
<style>
:root {
    --trang: #fff;
    --nen: #f5f7f5;
    --xanh-chinh: #0B4D3E;
    --xanh-phu: #176653;
    --vien: #dce4df;
    --chu: #174F42;
    --chu-phu: #64766E;
    --vang: #F1C76B;
    --xanh-nhat: #E7F3EC;
    --cam-nhat: #FFF3DD;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #263b34;
    background: #f5f7f5;
    line-height: 1.5;
}

.app-layout {
    min-height: 100vh;
}

/* ===== THANH MENU BÊN TRÁI - GIỐNG DASHBOARD ===== */

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

/* ===== NỘI DUNG CHÍNH ===== */

.noi-dung-chinh {
    margin-left: 230px;
    min-width: 0;
    padding: 30px 28px;
    background: #f5f6f2;
}

.page-intro {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}

.page-intro h1 {
    margin: 0 0 8px;
    color: #174b3d;
    font-size: 28px;
}

.page-intro p {
    margin: 0;
    color: #68766f;
    font-size: 14px;
}

h2 {
    margin: 24px 0 12px;
    color: #234c3e;
    font-size: 19px;
}

/* ===== NÚT ===== */

button {
    min-height: 40px;
    padding: 9px 14px;
    border: 1px solid #d8dfd8;
    border-radius: 7px;
    background: #fff;
    color: #28483d;
    font: inherit;
    cursor: pointer;
    transition: background 0.2s;
}

button:hover {
    background: #edf3ed;
}

button.primary,
button[type="submit"].primary {
    border-color: #075747;
    background: #075747;
    color: #fff;
}

button.primary:hover,
button[type="submit"].primary:hover {
    background: #0b6c57;
}

/* ===== THỐNG KÊ TỔNG QUAN ===== */

.summary {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 18px 0;
}

.summary p {
    margin: 0;
    padding: 12px 16px;
    border: 1px solid #e0e5dc;
    border-radius: 9px;
    background: #fffdf7;
    font-size: 14px;
}

/* ===== BỘ LỌC ===== */

.filter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
    align-items: end;
    gap: 14px;
    margin: 18px 0 24px;
    padding: 18px;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fffdf8;
}

label {
    display: block;
    color: #344b40;
    font-size: 14px;
    font-weight: 600;
}

input,
select {
    width: 100%;
    min-width: 0;
    min-height: 42px;
    margin-top: 6px;
    padding: 9px 11px;
    border: 1px solid #d2dbd2;
    border-radius: 7px;
    background: #fff;
    color: #263b34;
    font: inherit;
}

input:focus,
select:focus {
    outline: 2px solid #a9d4c1;
    border-color: #23856d;
}

/* ===== Ô MẬT KHẨU ===== */

.password-field {
    position: relative;
    width: 100%;
}

.password-field input {
    width: 100%;
    box-sizing: border-box;
    padding-right: 44px;
}

.password-field button,
.password-field .toggle-password,
.password-field .password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    padding: 0;
    margin: 0;
    border: none;
    background: transparent;
    cursor: pointer;
    z-index: 2;
}

.password-field span {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 2;
}

/* ===== BẢNG ===== */

.table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fffdf8;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

th,
td {
    padding: 13px 12px;
    border-bottom: 1px solid #e4e8e0;
    text-align: left;
    vertical-align: middle;
    overflow-wrap: anywhere;
}

th {
    background: #e8f0e8;
    color: #244b3d;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

tbody tr:nth-child(even) {
    background: #f8f8f2;
}

tbody tr:hover {
    background: #eef4eb;
}

tbody tr:last-child td {
    border-bottom: 0;
}

td {
    font-size: 14px;
}

.actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
}

.actions button {
    white-space: nowrap;
    flex: 0 0 auto;
}

th:first-child,
td:first-child {
    width: 55px;
    min-width: 55px;
    white-space: nowrap;
}

.empty {
    padding: 28px;
    text-align: center;
    color: #68766f;
}

/* ===== THÔNG BÁO ===== */

.message {
    margin: 12px 0;
    padding: 12px 15px;
    border: 1px solid #cbded0;
    border-radius: 8px;
    background: #edf5eb;
    color: #245b43;
}

.dialog-thong-bao {
    width: min(420px, calc(100% - 28px));
    padding: 24px;
    border: 1px solid #cbded0;
    border-radius: 12px;
    background: #fffdf8;
    color: #263b34;
    text-align: center;
}

.dialog-thong-bao::backdrop {
    background: rgba(13, 43, 33, 0.48);
}

.dialog-thong-bao .nut-dong {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 34px;
    min-height: 34px;
    padding: 0;
    font-size: 22px;
}

.dialog-thong-bao h2 {
    margin: 8px 0 12px;
    color: #075747;
}

.dialog-thong-bao p {
    margin: 0 0 20px;
}

/* ===== MODAL ===== */

dialog {
    width: min(600px, calc(100% - 28px));
    max-height: 88vh;
    overflow-y: auto;
    padding: 24px;
    border: 1px solid #dce3d9;
    border-radius: 13px;
    background: #fffdf8;
    color: #263b34;
}

dialog::backdrop {
    background: rgba(13, 43, 33, 0.48);
}

dialog h2 {
    margin: 0 0 16px;
}

.dialog-tao-tai-khoan {
    width: min(650px, calc(100% - 28px));
}

.dialog-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.dialog-heading h2 {
    margin: 0 0 5px;
}

.dialog-heading p {
    margin: 0;
    color: #68766f;
    font-size: 13px;
}

.dialog-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    min-height: 34px;
    padding: 0;
    border: 1px solid #e0e5dc;
    border-radius: 8px;
    background: #fff;
    font-size: 20px;
}

/* ===== CHỌN CÂU LẠC BỘ ===== */

.clb-selection {
    max-height: 330px;
    overflow-y: auto;
    margin: 14px 0;
    border: 1px solid #e0e5dc;
    border-radius: 10px;
    background: #fff;
}

.clb-selection-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 15px;
    border-bottom: 1px solid #e0e5dc;
    background: #f5f8f3;
}

.clb-selection-header label {
    display: flex;
    align-items: center;
    gap: 9px;
    cursor: pointer;
}

.clb-selection-header input,
.clb-option input {
    width: 17px;
    min-height: 17px;
    margin: 0;
    accent-color: #075747;
}

.clb-option {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    padding: 13px 15px;
    border-bottom: 1px solid #edf0e9;
    cursor: pointer;
}

.clb-option:last-child {
    border-bottom: 0;
}

.clb-option:hover {
    background: #f5f8f3;
}

.clb-option-info {
    flex: 1;
    min-width: 0;
}

.clb-option-name {
    display: block;
    font-weight: 600;
    color: #263b34;
}

.clb-option-code {
    display: block;
    color: #68766f;
    font-size: 12px;
    font-weight: 400;
}

.clb-count {
    color: #68766f;
    font-size: 12px;
}

.tai-khoan-moi-table code {
    display: inline-block;
    padding: 5px 8px;
    border: 1px solid #e0e5dc;
    border-radius: 6px;
    background: #f5f8f3;
    color: #174b3d;
    overflow-wrap: anywhere;
}

/* ===== FORM TRONG MODAL ===== */

.dialog-form {
    display: grid;
    gap: 14px;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
}

.muted {
    color: #68766f;
    font-size: 13px;
}

/* ===== PHÂN TRANG ===== */

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 18px;
    margin-top: 22px;
}

.pagination a {
    color: #08644f;
    text-decoration: none;
    font-weight: 600;
}

.pagination a:hover {
    text-decoration: underline;
}

.pagination .nut-trang {
    color: #075747;
    font-size: 20px;
    text-decoration: none;
    font-weight: 400;
}

.pagination .nut-trang:hover {
    color: #0b6c57;
}

.pagination .nut-trang.disabled {
    color: #aab5ad;
    cursor: default;
}

/* ===== MÁY TÍNH BẢNG ===== */

@media (max-width: 1000px) and (min-width: 651px) {
    .noi-dung-chinh {
        padding: 22px 18px;
    }

    .filter-form {
        grid-template-columns: 1fr 1fr;
    }

    .filter-form button {
        grid-column: 1 / -1;
    }
}

/* ===== ĐIỆN THOẠI ===== */

@media (max-width: 650px) {
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
        white-space: normal;
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
        padding: 0 8px;
    }

    .noi-dung-chinh {
        margin-left: 0;
        padding: 18px 12px;
    }

    .filter-form {
        grid-template-columns: 1fr;
        padding: 14px;
    }

    .filter-form button {
        grid-column: auto;
    }

    .table-wrap table {
        min-width: 700px;
    }

    .page-intro h1 {
        font-size: 24px;
    }

    .dialog-tao-tai-khoan {
        padding: 18px;
    }

    .clb-option {
        padding: 12px;
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

        <a href="?action=quan-ly-tai-khoan"
           class="active"
           aria-current="page">
            <span class="menu-icon">♙</span>
            <span>Quản lý ban tổ chức</span>
        </a>

        <a href="?action=quan-ly-tai-khoan-thanh-vien">
            <span class="menu-icon">♟</span>
            <span>Quản lý thành viên</span>
        </a>

        <div class="nhom-menu">Báo cáo</div>

        <a href="?action=thong-ke">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>
</aside>
<main class="noi-dung-chinh">
    <div class="page-intro">
        <div>
            <h1>Quản lý ban tổ chức</h1>
            <p>Quản lý tài khoản đăng nhập và câu lạc bộ phụ trách.</p>
        </div>
        <div class="actions">
    <button type="button" class="primary" id="moFormTuDongTao">
        + Tự động tạo tài khoản
    </button>
</div>
    </div>
    <?php if (isset($noiDungThongBao[$thongBao])): ?>
    <dialog id="dialogThongBao" class="dialog-thong-bao">
        <button
            type="button"
            class="nut-dong"
            id="dongDialogThongBao"
            aria-label="Đóng thông báo"
        >
            ×
        </button>

        <h2>Thông báo</h2>

        <p role="status">
            <?= $e($noiDungThongBao[$thongBao]) ?>
        </p>

        <button
            type="button"
            class="primary"
            id="nutDongThongBao"
        >
            Đóng
        </button>
    </dialog>
<?php endif; ?>
    <?php if (!empty($danhSachTaiKhoanVuaTao)): ?>
    <dialog
        id="thongTinTaiKhoanVuaTao"
        class="dialog-tao-tai-khoan"
    >
        <div class="dialog-heading">
            <div>
                <h2>Thông tin tài khoản vừa tạo</h2>
                <p>
                    Hãy lưu lại hoặc bàn giao thông tin đăng nhập.
                    Mật khẩu ban đầu chỉ hiển thị trong lần này.
                </p>
            </div>

            <button
                type="button"
                class="dialog-close"
                data-dong-thong-bao
                aria-label="Đóng thông báo"
            >
                ×
            </button>
        </div>

        <div class="table-wrap tai-khoan-moi-table">
            <table>
                <thead>
                    <tr>
                        <th>Câu lạc bộ</th>
                        <th>Email đăng nhập</th>
                        <th>Mật khẩu ban đầu</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($danhSachTaiKhoanVuaTao as $tkMoi): ?>
                        <tr>
                            <td><?= $e($tkMoi['cau_lac_bo']) ?></td>
                            <td>
                                <code><?= $e($tkMoi['email']) ?></code>
                            </td>
                            <td>
                                <code><?= $e($tkMoi['mat_khau']) ?></code>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <<div class="dialog-actions">
    <button
        type="button"
        id="nutCapTaiKhoanBanToChuc"
    >
        Cấp tài khoản cho Ban tổ chức
    </button>

    <button type="button" data-dong-thong-bao>
        Đóng
    </button>
</div>
    </dialog>
<?php endif; ?>
    <div class="summary">
        <p>Tổng tài khoản ban tổ chức: <strong><?= (int) $tongTaiKhoan ?></strong></p>
        <p>Kết quả tìm được: <strong><?= (int) $tongKetQua ?></strong></p>
    </div>
    <form class="filter-form" method="get" action="">
        <input type="hidden" name="action" value="quan-ly-tai-khoan">
        <label>Tìm theo họ tên hoặc email
            <input type="search" name="q" value="<?= $e($tuKhoa) ?>" placeholder="Nhập họ tên hoặc email">
        </label>
        <label>Lọc theo câu lạc bộ
            <select name="cau_lac_bo_id">
                <option value="0">Tất cả câu lạc bộ</option>
                <?php foreach ($danhSachCauLacBo as $clb): ?>
                    <option value="<?= (int) $clb['id'] ?>" <?= (int)$cauLacBoId === (int)$clb['id'] ? 'selected' : '' ?>>
                        <?= $e($clb['ten_cau_lac_bo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="primary">Tìm kiếm</button>
    </form>
    
<dialog id="formTuDongTao" class="dialog-tao-tai-khoan">
    <div class="dialog-heading">
        <div>
            <h2>Tạo tài khoản ban tổ chức</h2>
            <p>Chọn câu lạc bộ cần cấp tài khoản đăng nhập.</p>
        </div>

        <button
            type="button"
            class="dialog-close"
            id="dongFormTuDongTao"
            aria-label="Đóng"
        >
            ×
        </button>
    </div>

    <?php if (!empty($danhSachClbChuaCoBanToChuc)): ?>

        <form
            id="formChonClb"
            method="post"
            action="?action=tu-dong-tao-tai-khoan"
        >
            <div class="clb-selection-header">
                <label>
                    <input type="checkbox" id="chonTatCaClb">
                    <span>Chọn tất cả</span>
                </label>

                <span class="clb-count">
                    <?= count($danhSachClbChuaCoBanToChuc) ?>
                    CLB chưa phân công
                </span>
            </div>

            <div class="clb-selection">
                <?php foreach ($danhSachClbChuaCoBanToChuc as $clb): ?>
                    <label class="clb-option">
                        <input
                            type="checkbox"
                            name="clb_ids[]"
                            value="<?= (int) $clb['id'] ?>"
                            class="checkbox-clb"
                        >

                        <span class="clb-option-info">
                            <span class="clb-option-name">
                                <?= $e($clb['ten_cau_lac_bo']) ?>
                            </span>

                            <span class="clb-option-code">
                                Mã câu lạc bộ:
                                CLB<?= str_pad(
                                    (string) $clb['id'],
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                ) ?>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <p class="muted" id="soClbDaChon">
                Chưa chọn câu lạc bộ nào.
            </p>

            <div class="dialog-actions">
                <button type="button" id="huyTuDongTao">
                    Hủy
                </button>

                <button
                    type="submit"
                    class="primary"
                    id="nutTaoClbDaChon"
                    disabled
                >
                    Tạo tài khoản đã chọn
                </button>
            </div>
        </form>

    <?php else: ?>

        <p class="muted">
            Tất cả câu lạc bộ hiện đã có người quản lý.
        </p>

        <div class="dialog-actions">
            <button type="button" id="huyTuDongTao">
                Đóng
            </button>
        </div>

    <?php endif; ?>
</dialog>

    <h2>Danh sách tài khoản</h2>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
<th>Họ tên</th>
<th>Email đăng nhập</th>
<th>Câu lạc bộ phụ trách</th>
<th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$danhSachTaiKhoan): ?>
                <tr>
    <td colspan="5" class="empty">
        Chưa có tài khoản ban tổ chức phù hợp.
    </td>
</tr>
            <?php else: ?>
                <?php foreach ($danhSachTaiKhoan as $tk): ?>
                    <tr>
                        <td><?= (int)$tk['id'] ?></td>
                        <td><?= $e($tk['ho_ten']) ?></td>
                        <td><?= $e($tk['email']) ?></td>
                        <td><?= $e($tk['ten_cau_lac_bo'] ?? 'Chưa gán câu lạc bộ') ?></td>

<td>
    <div class="actions">
    <button type="button" class="primary"
        data-mo-sua="<?= (int)$tk['id'] ?>">
        Sửa thông tin
    </button>

    <button type="button"
        data-mo-cap-lai="<?= (int)$tk['id'] ?>">
        Cấp lại tài khoản
    </button>

    <form method="post"
          action="?action=xoa-tai-khoan"
          onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản <?= $e($tk['ho_ten']) ?> không?');">

        <input type="hidden" name="id"
               value="<?= (int)$tk['id'] ?>">

        <button type="submit">
            Xóa
        </button>
    </form>
</div>
<dialog id="capLaiTaiKhoan<?= (int)$tk['id'] ?>">
    <h2>Cấp lại tài khoản</h2>

    <p class="muted">
        Cấp mật khẩu mới cho tài khoản
        <?= $e($tk['email']) ?>.
    </p>

    <form class="dialog-form"
          method="post"
          action="?action=dat-lai-mat-khau-tai-khoan">

        <input type="hidden" name="id"
               value="<?= (int)$tk['id'] ?>">

        <label>Mật khẩu mới
            <input type="password"
                   name="mat_khau_moi"
                   minlength="8"
                   maxlength="4096"
                   required
                   autocomplete="new-password">
        </label>

        <label>Xác nhận mật khẩu mới
            <input type="password"
                   name="xac_nhan_mat_khau"
                   minlength="8"
                   maxlength="4096"
                   required
                   autocomplete="new-password">
        </label>

        <div class="dialog-actions">
            <button type="button"
                    data-dong-cap-lai="<?= (int)$tk['id'] ?>">
                Hủy
            </button>

            <button type="submit" class="primary">
                Cấp lại mật khẩu
            </button>
        </div>
    </form>
</dialog>
                            <dialog id="suaTaiKhoan<?= (int)$tk['id'] ?>">
                                <h2>Sửa tài khoản ban tổ chức</h2>
                                <p class="muted">Để đổi mật khẩu, nhập mật khẩu hiện tại và mật khẩu mới. Nếu không đổi mật khẩu, để trống hai ô mật khẩu.</p>
                                <form class="dialog-form" method="post" action="?action=cap-nhat-tai-khoan">
                                    <input type="hidden" name="id" value="<?= (int)$tk['id'] ?>">
                                    <label>Họ tên
                                        <input name="ho_ten" maxlength="100" required value="<?= $e($tk['ho_ten']) ?>">
                                    </label>
                                    <label>Email đăng nhập
                                        <input type="email" name="email" maxlength="150" required value="<?= $e($tk['email']) ?>">
                                    </label>
                                    <label>Câu lạc bộ phụ trách
                                        <select name="cau_lac_bo_id" required>
                                            <option value="">-- Chọn câu lạc bộ --</option>
                                            <?php foreach ($danhSachCauLacBo as $clb): ?>
                                                <?php if (empty($clb['nguoi_quan_ly_id']) || (int)$clb['nguoi_quan_ly_id'] === (int)$tk['id']): ?>
                                                    <option value="<?= (int)$clb['id'] ?>" <?= (int)($tk['cau_lac_bo_id'] ?? 0) === (int)$clb['id'] ? 'selected' : '' ?>>
                                                        <?= $e($clb['ten_cau_lac_bo']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    
                                    <div class="dialog-actions">
                                        <button type="button" data-dong-dialog="<?= (int)$tk['id'] ?>">Hủy</button>
                                        <button type="submit" class="primary">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination" aria-label="Phân trang tài khoản">
    <?php
    $thamSoTrang = [
        'action' => 'quan-ly-tai-khoan',
        'q' => $tuKhoa,
        'cau_lac_bo_id' => $cauLacBoId
    ];
    ?>

    <?php if ($trang > 1): ?>
        <?php $thamSoTrang['trang'] = $trang - 1; ?>
        <a class="nut-trang"
           href="?<?= $e(http_build_query($thamSoTrang)) ?>"
           aria-label="Trang trước">&lt;</a>
    <?php else: ?>
        <span class="nut-trang disabled">&lt;</span>
    <?php endif; ?>

    <span>Trang <?= (int)$trang ?> / <?= (int)$tongTrang ?></span>

    <?php if ($trang < $tongTrang): ?>
        <?php $thamSoTrang['trang'] = $trang + 1; ?>
        <a class="nut-trang"
           href="?<?= $e(http_build_query($thamSoTrang)) ?>"
           aria-label="Trang sau">&gt;</a>
    <?php else: ?>
        <span class="nut-trang disabled">&gt;</span>
    <?php endif; ?>
</nav>
    
<script>
document.addEventListener('DOMContentLoaded', function () {
const nutCapTaiKhoanBanToChuc = document.getElementById(
    'nutCapTaiKhoanBanToChuc'
);

if (nutCapTaiKhoanBanToChuc) {
    nutCapTaiKhoanBanToChuc.addEventListener('click', function () {
        const dialogThongTin = document.getElementById(
            'thongTinTaiKhoanVuaTao'
        );

        if (dialogThongTin) {
            dialogThongTin.close();
        }

        const dialogTaoTaiKhoan = document.getElementById(
            'dialogTuDongTaoTaiKhoan'
        );

        if (dialogTaoTaiKhoan) {
            dialogTaoTaiKhoan.showModal();
        }
    });
}
    // Tự mở cửa sổ thông báo
const dialogThongBao = document.getElementById('dialogThongBao');

if (dialogThongBao) {
    dialogThongBao.showModal();

    // Đóng bằng nút X hoặc nút Đóng
    document.querySelectorAll(
        '#dongDialogThongBao, #nutDongThongBao'
    ).forEach(function (button) {
        button.addEventListener('click', function () {
            dialogThongBao.close();
        });
    });
}

    /* Mở và đóng modal tự động tạo tài khoản */
    const formTuDongTao = document.getElementById('formTuDongTao');
    const nutMoTuDongTao = document.getElementById('moFormTuDongTao');
    const nutDongTuDongTao = document.getElementById('dongFormTuDongTao');
    const nutHuyTuDongTao = document.getElementById('huyTuDongTao');

    if (formTuDongTao && nutMoTuDongTao) {
        nutMoTuDongTao.addEventListener('click', function () {
            formTuDongTao.showModal();
        });
    }

    [nutDongTuDongTao, nutHuyTuDongTao].forEach(function (button) {
        if (button && formTuDongTao) {
            button.addEventListener('click', function () {
                formTuDongTao.close();
            });
        }
    });

    /* Chọn tất cả hoặc chọn riêng từng câu lạc bộ */
    const chonTatCaClb = document.getElementById('chonTatCaClb');
    const checkboxClb = document.querySelectorAll('.checkbox-clb');
    const nutTaoClbDaChon = document.getElementById('nutTaoClbDaChon');
    const soClbDaChon = document.getElementById('soClbDaChon');
    const formChonClb = document.getElementById('formChonClb');

    function capNhatLuaChonClb() {
        const soLuongDaChon = document.querySelectorAll(
            '.checkbox-clb:checked'
        ).length;

        if (nutTaoClbDaChon) {
            nutTaoClbDaChon.disabled = soLuongDaChon === 0;
        }

        if (soClbDaChon) {
            soClbDaChon.textContent = soLuongDaChon === 0
                ? 'Chưa chọn câu lạc bộ nào.'
                : 'Đã chọn ' + soLuongDaChon + ' câu lạc bộ.';
        }

        if (chonTatCaClb) {
            chonTatCaClb.checked =
                checkboxClb.length > 0 &&
                soLuongDaChon === checkboxClb.length;

            chonTatCaClb.indeterminate =
                soLuongDaChon > 0 &&
                soLuongDaChon < checkboxClb.length;
        }
    }

    if (chonTatCaClb) {
        chonTatCaClb.addEventListener('change', function () {
            checkboxClb.forEach(function (checkbox) {
                checkbox.checked = chonTatCaClb.checked;
            });

            capNhatLuaChonClb();
        });
    }

    checkboxClb.forEach(function (checkbox) {
        checkbox.addEventListener('change', capNhatLuaChonClb);
    });

    if (formChonClb) {
        formChonClb.addEventListener('submit', function (event) {
            const soLuongDaChon = document.querySelectorAll(
                '.checkbox-clb:checked'
            ).length;

            if (soLuongDaChon === 0) {
                event.preventDefault();
                alert('Vui lòng chọn ít nhất một câu lạc bộ.');
                return;
            }

            if (!confirm(
                'Bạn có chắc muốn tạo tài khoản cho ' +
                soLuongDaChon +
                ' câu lạc bộ đã chọn?'
            )) {
                event.preventDefault();
            }
        });
    }

    capNhatLuaChonClb();

    /* Mở modal thông tin tài khoản vừa tạo */
    const thongTinTaiKhoanVuaTao = document.getElementById(
        'thongTinTaiKhoanVuaTao'
    );

    if (thongTinTaiKhoanVuaTao) {
        thongTinTaiKhoanVuaTao.showModal();

        document.querySelectorAll('[data-dong-thong-bao]').forEach(
            function (button) {
                button.addEventListener('click', function () {
                    thongTinTaiKhoanVuaTao.close();
                });
            }
        );
    }

    /* Mở dialog sửa tài khoản */
    document.querySelectorAll('[data-mo-sua]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.moSua;
            const dialog = document.getElementById('suaTaiKhoan' + id);

            if (dialog) {
                dialog.showModal();
            }
        });
    });

    /* Đóng dialog sửa tài khoản */
    document.querySelectorAll('[data-dong-dialog]').forEach(
        function (button) {
            button.addEventListener('click', function () {
                const id = button.dataset.dongDialog;
                const dialog = document.getElementById('suaTaiKhoan' + id);

                if (dialog) {
                    dialog.close();
                }
            });
        }
    );

    /* Mắt ẩn/hiện mật khẩu */
    document.querySelectorAll('.toggle-password').forEach(
        function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement.querySelector('input');

                if (!input) {
                    return;
                }

                const dangHien = input.type === 'text';

                input.type = dangHien ? 'password' : 'text';
                button.textContent = dangHien ? '👁' : '🙈';

                button.setAttribute(
                    'aria-label',
                    dangHien ? 'Hiện mật khẩu' : 'Ẩn mật khẩu'
                );
            });
        }
    );

    /* Kiểm tra xác nhận mật khẩu mới */
    document.querySelectorAll(
        'form[action="?action=cap-nhat-tai-khoan"]'
    ).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const moi = form.querySelector(
                '[name="mat_khau_moi"]'
            ).value;

            const xacNhan = form.querySelector(
                '[name="xac_nhan_mat_khau"]'
            ).value;

            if ((moi || xacNhan) && moi !== xacNhan) {
                event.preventDefault();
                alert('Mật khẩu mới và xác nhận mật khẩu không khớp.');
            }
        });
    });
// Mở hộp thoại cấp lại tài khoản
document.querySelectorAll('[data-mo-cap-lai]').forEach(function (button) {
    button.addEventListener('click', function () {
        const id = button.dataset.moCapLai;
        const dialog = document.getElementById('capLaiTaiKhoan' + id);

        if (dialog) {
            dialog.showModal();
        }
    });
});

// Đóng hộp thoại cấp lại tài khoản
document.querySelectorAll('[data-dong-cap-lai]').forEach(function (button) {
    button.addEventListener('click', function () {
        const id = button.dataset.dongCapLai;
        const dialog = document.getElementById('capLaiTaiKhoan' + id);

        if (dialog) {
            dialog.close();
        }
    });
});

// Kiểm tra mật khẩu xác nhận
document.querySelectorAll(
    'form[action="?action=dat-lai-mat-khau-tai-khoan"]'
).forEach(function (form) {
    form.addEventListener('submit', function (event) {
        const matKhauMoi =
            form.querySelector('[name="mat_khau_moi"]').value;

        const xacNhan =
            form.querySelector('[name="xac_nhan_mat_khau"]').value;

        if (matKhauMoi !== xacNhan) {
            event.preventDefault();
            alert('Mật khẩu mới và xác nhận mật khẩu không khớp.');
        }
    });
});
});
</script>
</main>
</div>
</body>
</html>
