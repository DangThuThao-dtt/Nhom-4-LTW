<?php
$e = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$danhSachSuKien = $danhSachSuKien ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
$boLoc = $boLoc ?? [];
$tongSoSuKien = (int) ($tongSoSuKien ?? 0);
$tongSoTrang = max(1, (int) ($tongSoTrang ?? 1));
$trangHienTai = max(1, (int) ($boLoc['trang'] ?? 1));

$cacTrangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Công khai',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Đã kết thúc',
];

$thamSoPhanTrang = $_GET;
unset($thamSoPhanTrang['trang']);
$urlCoSo = 'index.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quản lý sự kiện</title>
<style>
:root {
    --xanh-dam: #075747;
    --xanh-chinh: #08745e;
    --xanh-nhat: #e1f4ec;
    --nen: #f5f7f5;
    --vien: #dce4df;
    --chu: #24332d;
    --chu-phu: #687770;
    --trang: #fff;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    color: var(--chu);
    background: var(--nen);
    font: 14px/1.5 Arial, Helvetica, sans-serif;
}

/* ===== BỐ CỤC CHUNG ===== */

.app-layout {
    min-height: 100vh;
}

/* ===== THANH ĐIỀU HƯỚNG DÙNG CHUNG VỚI DASHBOARD ===== */

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

/* ===== THƯƠNG HIỆU ===== */

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

/* ===== NÚT ĐĂNG XUẤT ===== */

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

/* ===== NHÓM MENU ===== */

.nhom-menu {
    margin: 14px 8px 6px;
    color: #c6d9d0;
    font-size: 10px;
    letter-spacing: 1px;
    line-height: 16px;
    text-transform: uppercase;
}

/* ===== DANH SÁCH MENU ===== */

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    margin: 0;
    padding: 0;
}

/* ===== TỪNG MỤC MENU ===== */

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

/* ===== ICON MENU ===== */

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

/* ===== CHÂN MENU ===== */

.chan-ben {
    margin-top: auto;
    padding: 18px 8px 0;
    color: #d6e4dc;
    font-size: 11px;
    line-height: 1.7;
}

/* ===== KHU VỰC NỘI DUNG CHÍNH ===== */

.noi-dung-chinh {
    width: calc(100% - 230px);
    min-width: 0;
    margin-left: 230px;
    padding: 28px;
}

.tieu-de-trang {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
}

h1 {
    margin: 0;
    font-size: 27px;
    line-height: 1.25;
    color: #173b30;
}

.mo-ta-trang {
    margin: 7px 0 0;
    color: var(--chu-phu);
}

/* ===== THANH CÔNG CỤ ===== */

.thanh-cong-cu {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
}

.tim-kiem {
    display: flex;
    flex: 1 1 300px;
    gap: 8px;
    min-width: 0;
}

.tim-kiem input[type="search"] {
    flex: 1 1 260px;
}

.tim-kiem select {
    flex: 0 1 220px;
    width: 220px;
}

input,
select,
button {
    font: inherit;
}

input,
select {
    width: 100%;
    min-width: 0;
    min-height: 42px;
    padding: 9px 11px;
    border: 1px solid #cfd9d3;
    border-radius: 7px;
    background: white;
    color: var(--chu);
}

input:focus,
select:focus {
    outline: 2px solid #b9e3d3;
    border-color: var(--xanh-chinh);
}

/* ===== NÚT BẤM ===== */

.nut {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-height: 40px;
    padding: 9px 14px;
    border: 1px solid var(--vien);
    border-radius: 7px;
    background: white;
    color: var(--chu);
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
}

.nut:hover {
    background: #edf4f0;
}

.nut-chinh {
    color: white;
    background: var(--xanh-dam);
    border-color: var(--xanh-dam);
}

.nut-chinh:hover {
    background: var(--xanh-chinh);
}

.tong-su-kien {
    margin-left: auto;
    color: var(--chu-phu);
}

/* ===== KHUNG BẢNG ===== */

.khung-bang {
    width: 100%;
    max-width: 100%;
    overflow: visible;
    border: 1px solid var(--vien);
    border-radius: 11px;
    background: white;
    box-shadow: 0 3px 12px rgba(20, 50, 35, 0.04);
}

.dau-bang {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 17px 18px;
    border-bottom: 1px solid var(--vien);
}

.dau-bang h2 {
    margin: 0;
    font-size: 17px;
}

.vung-bang {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1200px;
    table-layout: auto;
    border-collapse: collapse;
}

thead {
    background: #edf5f0;
}

th,
td {
    padding: 12px 10px;
    text-align: left;
    vertical-align: middle;
    border-bottom: 1px solid #e8eeea;
    overflow-wrap: normal;
}

td {
    font-size: 13px;
}

th {
    color: #365348;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    white-space: nowrap;
}

/* Giữ độ rộng phù hợp cho các cột số liệu */
th:nth-child(6),
th:nth-child(7),
th:nth-child(8),
th:nth-child(9) {
    min-width: 95px;
}

tbody tr:hover {
    background: #f8fbf9;
}

tbody tr:last-child td {
    border-bottom: 0;
}

/* ===== ĐỘ RỘNG CỘT ADMIN ===== */

.cot-stt {
    width: 4%;
    text-align: center;
}

.ten-su-kien {
    width: 15%;
    font-weight: 700;
    color: #213e32;
    overflow-wrap: normal;
    word-break: normal;
}

.cot-clb {
    width: 10%;
}

.dia-diem {
    width: 9%;
}

.cot-thoi-gian {
    width: 12%;
}

.so-luong {
    width: 6.5%;
    text-align: center;
}

.cot-trang-thai {
    width: 8%;
}

.cot-thao-tac {
    width: 19%;
}

/* ===== TÊN SỰ KIỆN ===== */

td.ten-su-kien {
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: break-word;
    word-break: normal;
}

td.ten-su-kien strong {
    display: block;
    font-weight: 700;
    line-height: 1.5;
}
/* ===== THỜI GIAN ===== */

.thoi-gian {
    display: block;
    white-space: normal;
    line-height: 1.5;
}

.thoi-gian + .thoi-gian {
    margin-top: 5px;
    padding-top: 5px;
    border-top: 1px dashed #dce4df;
}

.nhan-thoi-gian {
    display: block;
    margin-bottom: 2px;
    color: var(--chu-phu);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

/* ===== NHÃN TRẠNG THÁI ===== */

.nhan {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.nhan-cong_khai {
    color: #176b43;
    background: #dff4e7;
}

.nhan-nhap {
    color: #6c5b24;
    background: #fff1c9;
}

.nhan-da_huy {
    color: #9a3f3f;
    background: #fde7e7;
}

.nhan-ket_thuc {
    color: #53645c;
    background: #e9eeeb;
}

.khong-co {
    padding: 38px 18px;
    text-align: center;
    color: var(--chu-phu);
}

/* ===== THAO TÁC SỰ KIỆN ===== */

.actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    min-width: 0;
    max-width: 100%;
}

.actions form {
    display: inline-flex;
    margin: 0;
}

.actions a,
.actions button {
    width: fit-content;
    min-width: 0;
    min-height: 28px;
    padding: 5px 8px;
    font-size: 11px;
    white-space: nowrap;
}

.actions a:hover,
.actions button:hover {
    background: #edf4f0;
}
.actions a,
.actions a:hover,
.actions a:focus,
.actions a:visited {
    text-decoration: none;
}
.actions a.btn-warning {
    color: #805b0b;
    background: #fff4d6;
    border: 1px solid #f0d99a;
    border-radius: 6px;
}

.actions a.btn-warning:hover {
    background: #ffedbd;
}
.actions a,
.actions button {
    border-radius: 6px;
    text-decoration: none;
}

/* Chi tiết */
.actions .btn-primary {
    color: #075747;
    border-color: #b9d9cc;
    background: #e1f4ec;
}

/* Sửa */
.actions .btn-warning {
    color: #805b0b;
    border-color: #f0d99a;
    background: #fff4d6;
}

/* Hủy */
.actions .btn-danger {
    color: #9a3f3f;
    border-color: #f1caca;
    background: #fde7e7;
}

/* Xóa */
.actions .btn-secondary {
    color: #53645c;
    border-color: #dce4df;
    background: #f0f3f1;
}

/* Xem người đăng ký */
.actions .btn-info {
    color: #176b43;
    border-color: #b9e3d3;
    background: #e1f4ec;
}
/* ===== CHÂN BẢNG VÀ PHÂN TRANG ===== */

.chan-bang {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 18px;
    border-top: 1px solid var(--vien);
    color: var(--chu-phu);
}

.phan-trang {
    display: flex;
    align-items: center;
    gap: 7px;
}

.trang-hien-tai {
    padding: 8px 10px;
    border-radius: 7px;
    background: var(--xanh-dam);
    color: white;
}

/* ===== FORM THÊM / SỬA SỰ KIỆN ===== */
.cot-anh {
    width: 110px;
    min-width: 110px;
    text-align: center;
}

.anh-su-kien {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    display: block;
    margin: 0 auto;
}

.chua-co-anh {
    color: #9ca3af;
    font-size: 12px;
}
.modal-su-kien {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    padding: 20px;
    align-items: center;
    justify-content: center;
    background: rgba(15, 35, 27, 0.55);
    backdrop-filter: blur(3px);
}

.modal-su-kien.dang-mo {
    display: flex;
}

.modal-su-kien-noi-dung {
    width: min(760px, 100%);
    max-height: 92vh;
    overflow-y: auto;
    padding: 28px;
    border: 1px solid #e0e8e2;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 20px 60px rgba(15, 35, 27, 0.2);
}

.dau-form-su-kien {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    padding-bottom: 20px;
    margin-bottom: 22px;
    border-bottom: 1px solid #e7eee9;
}

.nhan-form {
    display: inline-block;
    margin-bottom: 7px;
    color: #527760;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
}

.dau-form-su-kien h2 {
    margin: 0;
    color: #244b35;
    font-size: 24px;
}

.dau-form-su-kien p {
    margin: 7px 0 0;
    color: #7b8980;
    font-size: 13px;
}

.nut-dong-modal {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border: 1px solid #e0e8e2;
    border-radius: 9px;
    background: #f6f8f6;
    color: #52675a;
    font-size: 25px;
    cursor: pointer;
}

.nut-dong-modal:hover {
    background: #eaf1ec;
}

.luoi-form-su-kien {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.form-nhom {
    min-width: 0;
}

.form-rong {
    grid-column: 1 / -1;
}

.form-nhom label {
    display: block;
    margin-bottom: 7px;
    color: #344d3c;
    font-size: 13px;
    font-weight: 700;
}

.form-nhom input,
.form-nhom select,
.form-nhom textarea {
    width: 100%;
    min-height: 43px;
    padding: 11px 12px;
    border: 1px solid #d6e1d8;
    border-radius: 8px;
    background: #fbfcfb;
    color: #283b2e;
    font: inherit;
    font-size: 13px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-nhom input:focus,
.form-nhom select:focus,
.form-nhom textarea:focus {
    outline: none;
    border-color: #5d896b;
    box-shadow: 0 0 0 3px rgba(93, 137, 107, 0.12);
    background: #fff;
}

.form-nhom textarea {
    min-height: 110px;
    resize: vertical;
}

.khung-tai-anh {
    padding: 14px;
    border: 1px dashed #b8cdbd;
    border-radius: 10px;
    background: #f7faf7;
}

.khung-tai-anh input[type="file"] {
    padding: 8px;
    background: #fff;
}

.khung-tai-anh small {
    display: block;
    margin-top: 8px;
    color: #7a897e;
    font-size: 12px;
}

.xem-truoc-anh {
    display: block;
    max-width: 100%;
    max-height: 220px;
    margin-top: 12px;
    border-radius: 9px;
    object-fit: contain;
}

.chan-form-su-kien {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e7eee9;
}

.nut-huy-form,
.nut-luu-form {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    min-height: 43px;
    padding: 11px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.nut-huy-form {
    border: 1px solid #d9e2dc;
    background: #fff;
    color: #52675a;
}

.nut-huy-form:hover {
    background: #f0f4f1;
}

.nut-luu-form {
    border: 1px solid #2F6B45;
    background: #2F6B45;
    color: #fff;
}

.nut-luu-form:hover {
    background: #245638;
}

@media (max-width: 650px) {
    .modal-su-kien-noi-dung {
        padding: 18px;
    }

    .luoi-form-su-kien {
        grid-template-columns: 1fr;
    }

    .form-rong {
        grid-column: auto;
    }

    .chan-form-su-kien {
        flex-direction: column-reverse;
    }

    .nut-huy-form,
    .nut-luu-form {
        width: 100%;
    }
}
.nut-them-su-kien {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    padding: 12px 20px;
    border: none;
    border-radius: 8px;

    background-color: #2F6B45;
    color: #ffffff;

    font-size: 15px;
    font-weight: 600;

    cursor: pointer;
    transition: background-color 0.2s ease,
                transform 0.2s ease;
}

.nut-them-su-kien:hover {
     background-color: #245638;
    transform: translateY(-1px);
}

.nut-them-su-kien:active {
    transform: translateY(0);
}

.nut-them-su-kien:focus-visible {
    outline: 3px solid #86efac;
    outline-offset: 3px;
}

.nut-them-su-kien .dau-cong {
    font-size: 20px;
    line-height: 1;
}


.luoi-su-kien {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.truong-rong {
    grid-column: 1 / -1;
}

.modal-su-kien textarea {
    width: 100%;
    min-height: 100px;
    margin-top: 6px;
    padding: 10px;
    border: 1px solid #cfd9d3;
    border-radius: 7px;
    font: inherit;
    resize: vertical;
}

.modal-su-kien textarea:focus {
    outline: 2px solid #b9e3d3;
    border-color: var(--xanh-chinh);
}

@media (max-width: 650px) {
    .luoi-su-kien {
        grid-template-columns: 1fr;
    }

    .truong-rong {
        grid-column: auto;
    }
}
/* ===== MODAL BỘ LỌC ===== */

.bo-loc {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 20;
    padding: 18px;
    align-items: center;
    justify-content: center;
    background: rgba(13, 36, 27, 0.45);
}

.bo-loc.dang-mo {
    display: flex;
}

.bo-loc-noi-dung {
    width: min(650px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    padding: 22px;
    border-radius: 12px;
    background: white;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
}

.dau-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}

.dau-modal h2 {
    margin: 0;
    font-size: 20px;
}

.luoi {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
}

label {
    display: block;
    color: #354b40;
    font-weight: 600;
}

label input,
label select {
    display: block;
    margin-top: 6px;
    font-weight: 400;
}

.nhom-nut {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
    margin-top: 20px;
}

/* ===== RESPONSIVE ===== */

@media (max-width: 1000px) and (min-width: 651px) {
    .thanh-ben {
        width: 200px;
    }

    .noi-dung-chinh {
        width: calc(100% - 200px);
        margin-left: 200px;
        padding: 22px;
    }
}

@media (max-width: 650px) {
    .app-layout {
        display: block;
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
        margin: 0;
        padding: 10px 8px;
        font-size: 12px;
    }

    .chan-ben {
        margin-top: 16px;
        padding: 0 8px;
    }

    .noi-dung-chinh {
        width: 100%;
        margin-left: 0;
        padding: 18px;
    }

    .menu-cuoi {
        margin-top: 8px;
        padding-top: 0;
    }

    h1 {
        font-size: 23px;
    }

    .luoi {
        grid-template-columns: 1fr;
    }

    .tong-su-kien {
        width: 100%;
        margin-left: 0;
    }

    .tim-kiem {
        flex-basis: 100%;
    }}
  


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
                     width="22" height="22"
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

        <a href="?action=quan-ly-su-kien"
           class="active"
           aria-current="page">
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

        <a href="?action=thong-ke">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>
</aside>

    <main class="noi-dung-chinh">
        <section>
            <div class="tieu-de-trang">
    <div>
        <h1>Quản lý sự kiện</h1>
        <p class="mo-ta-trang">
            Tra cứu, xem thông tin và theo dõi tình trạng các sự kiện.
        </p>
    </div>

   <button
    type="button"
    id="nutThemSuKien"
    class="nut-them-su-kien">
    <span class="dau-cong">+</span>
    Thêm sự kiện
</button>
</div>
            <div class="thanh-cong-cu">
                <form class="tim-kiem" method="get" action="<?= $e($urlCoSo) ?>">
    <input type="hidden" name="action" value="quan-ly-su-kien">

    <input
        type="search"
        name="tu_khoa"
        value="<?= $e($_GET['tu_khoa'] ?? '') ?>"
        placeholder="Tìm theo tên sự kiện..."
        aria-label="Tìm theo tên sự kiện"
    >

    <select
        name="cau_lac_bo_id"
        aria-label="Lọc theo câu lạc bộ"
    >
        <option value="0">Tất cả câu lạc bộ</option>

        <?php foreach ($danhSachCauLacBo as $clb): ?>
            <option
                value="<?= (int) $clb['id'] ?>"
                <?= (int) ($_GET['cau_lac_bo_id'] ?? 0) === (int) $clb['id'] ? 'selected' : '' ?>
            >
                <?= $e($clb['ten_cau_lac_bo'] ?? '') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (isset($_GET['trang_thai'])): ?>
        <input
            type="hidden"
            name="trang_thai"
            value="<?= $e($_GET['trang_thai']) ?>"
        >
    <?php endif; ?>

    <?php if (isset($_GET['tu_ngay'])): ?>
        <input
            type="hidden"
            name="tu_ngay"
            value="<?= $e($_GET['tu_ngay']) ?>"
        >
    <?php endif; ?>

    <?php if (isset($_GET['den_ngay'])): ?>
        <input
            type="hidden"
            name="den_ngay"
            value="<?= $e($_GET['den_ngay']) ?>"
        >
    <?php endif; ?>

    <button class="nut nut-chinh" type="submit">
        Tìm kiếm
    </button>
</form>
                <button class="nut" type="button" id="nutMoLoc" aria-expanded="false" aria-controls="khuVucBoLoc">☷ Bộ lọc</button>
                <span class="tong-su-kien">Tìm thấy <strong><?= $tongSoSuKien ?></strong> sự kiện</span>
            </div>
            <div class="khung-bang">
                <div class="dau-bang">
                    <h2>Danh sách sự kiện</h2>
                </div>
                <div class="vung-bang">
                    <table>
    <thead>
    <tr>
        <th class="cot-stt">STT</th>
        <th class="ten-su-kien">Tên sự kiện</th>
        <th>Ảnh</th>
        <th class="cot-clb">Câu lạc bộ</th>
        <th class="dia-diem">Địa điểm</th>
        <th class="cot-thoi-gian">Thời gian</th>
        <th class="so-luong">Số lượng tối đa</th>
        <th class="so-luong">Đã đăng ký</th>
        <th class="so-luong">Đã tham gia</th>
        <th class="so-luong">Còn chỗ</th>
        <th class="cot-trang-thai">Trạng thái</th>
        <th class="cot-thao-tac">Thao tác</th>
    </tr>
</thead>

    <tbody>
        <?php if (empty($danhSachSuKien)): ?>
            <tr>
                <td colspan="11" class="text-center">
                    Không tìm thấy sự kiện nào.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($danhSachSuKien as $index => $suKien): ?>
                <?php
                    $id = (int) ($suKien['id'] ?? 0);
                    $soLuongToiDa = (int) ($suKien['so_luong_toi_da'] ?? 0);
                    $soNguoiDangKy = (int) ($suKien['so_nguoi_dang_ky'] ?? 0);
                    $soNguoiDaThamGia = (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0);
                    $soChoConLai = (int) ($suKien['so_cho_con_lai'] ?? 0);

                    $trangThai = $suKien['trang_thai'] ?? '';
                    $nhanTrangThai = $cacTrangThai[$trangThai] ?? $trangThai;
                ?>

                <tr>
    <!-- STT -->
    <td class="cot-stt">
        <?= $index + 1 ?>
    </td>

    <!-- Tên sự kiện -->
    <td class="ten-su-kien">
        <strong>
            <?= $e($suKien['ten_su_kien'] ?? '') ?>
        </strong>
    </td>
    <td class="cot-anh">
    <?php if (!empty($suKien['hinh_anh'])): ?>
        <img
    src="<?= $e($suKien['hinh_anh']) ?>"
    alt="<?= $e($suKien['ten_su_kien'] ?? 'Ảnh sự kiện') ?>"
    class="anh-su-kien"
>
    <?php else: ?>
        <span class="chua-co-anh">Chưa có ảnh</span>
    <?php endif; ?>
</td>

                    <!-- Câu lạc bộ -->
<td class="cot-clb">
    <?= $e($suKien['ten_cau_lac_bo'] ?? '') ?>
</td>

<!-- Địa điểm -->
<td class="dia-diem">
    <?= $e($suKien['dia_diem'] ?? '') ?>
</td>

<!-- Thời gian -->
<td class="cot-thoi-gian">
    <?= $e($suKien['thoi_gian_bat_dau'] ?? '') ?>
</td>

                    <td><?= $soLuongToiDa ?></td>

                    <td><?= $soNguoiDangKy ?></td>

                    <td><?= $soNguoiDaThamGia ?></td>

                    <td><?= $soChoConLai ?></td>

                    <td>
                        <?= $e($nhanTrangThai) ?>
                    </td>

                   <td>
    <div class="actions">
    <!-- Hàng 1: Xem người đăng ký và Chi tiết -->
    <a
        href="index.php?trang=danh_sach_dang_ky&id=<?= $id ?>"
        class="btn btn-info"
    >
        Xem người đăng ký
    </a>

    <a
        href="/Nhom-4-LTW/public/index.php?action=chi-tiet-su-kien&id=<?= (int) $suKien['id'] ?>&nguon=admin"
        class="btn btn-primary"
    >
        Chi tiết
    </a>

    
    <!-- Hàng 2: Sửa, Hủy, Xóa -->
    <button
    type="button"
    class="btn btn-warning nutSuaSuKien"
    data-id="<?= $id ?>"
    data-ten="<?= $e($suKien['ten_su_kien'] ?? '') ?>"
    data-clb="<?= (int) ($suKien['cau_lac_bo_id'] ?? 0) ?>"
    data-dia-diem="<?= $e($suKien['dia_diem'] ?? '') ?>"
    data-so-luong="<?= $soLuongToiDa ?>"
    data-bat-dau="<?= $e($suKien['thoi_gian_bat_dau'] ?? '') ?>"
    data-ket-thuc="<?= $e($suKien['thoi_gian_ket_thuc'] ?? '') ?>"
    data-han-dang-ky="<?= $e($suKien['han_dang_ky'] ?? '') ?>"
    data-trang-thai="<?= $e($trangThai) ?>"
    data-mo-ta="<?= $e($suKien['mo_ta'] ?? '') ?>"
    data-hinh="<?= $e($suKien['hinh_anh'] ?? '') ?>"
>
    Sửa
</button>
    <?php if ($trangThai !== 'da_huy'): ?>
        <form
            action="index.php?action=doi-trang-thai-su-kien"
            method="POST"
            onsubmit="return confirm('Bạn có chắc chắn muốn hủy sự kiện này?');"
        >
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="trang_thai" value="da_huy">

            <button type="submit" class="btn btn-danger">
                Hủy
            </button>
        </form>
    <?php endif; ?>

    <form
        action="index.php?action=xoa-su-kien"
        method="POST"
        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?');"
    >
        <input type="hidden" name="id" value="<?= $id ?>">

        <button type="submit" class="btn btn-secondary">
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
                <div class="chan-bang">
                    <span>Trang <?= $trangHienTai ?> / <?= $tongSoTrang ?></span>
                    <nav class="phan-trang" aria-label="Phân trang sự kiện">
                        <?php if ($trangHienTai > 1): ?>
                            <?php $thamSoPhanTrang['trang'] = $trangHienTai - 1; ?>
                            <a class="nut" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">← Trước</a>
                        <?php endif; ?>
                        <span class="trang-hien-tai"><?= $trangHienTai ?></span>
                        <?php if ($trangHienTai < $tongSoTrang): ?>
                            <?php $thamSoPhanTrang['trang'] = $trangHienTai + 1; ?>
                            <a class="nut" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">Sau →</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </section>
    </main>
</div>
<div class="bo-loc" id="khuVucBoLoc" role="dialog" aria-modal="true" aria-labelledby="tieuDeBoLoc">
    <div class="bo-loc-noi-dung">
        <div class="dau-modal">
            <h2 id="tieuDeBoLoc">Bộ lọc sự kiện</h2>
            <button class="nut" type="button" id="nutDongLoc" aria-label="Đóng bộ lọc">×</button>
        </div>
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="quan-ly-su-kien">
            <div class="luoi">
                <label>Câu lạc bộ
                    <select name="cau_lac_bo_id">
                        <option value="0">Tất cả câu lạc bộ</option>
                        <?php foreach ($danhSachCauLacBo as $clb): ?>
                            <option value="<?= (int) $clb['id'] ?>" <?= (int) ($_GET['cau_lac_bo_id'] ?? 0) === (int) $clb['id'] ? 'selected' : '' ?>><?= $e($clb['ten_cau_lac_bo'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Trạng thái
                    <select name="trang_thai">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($cacTrangThai as $ma => $ten): ?>
                            <option value="<?= $e($ma) ?>" <?= ($_GET['trang_thai'] ?? '') === $ma ? 'selected' : '' ?>><?= $e($ten) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Từ ngày
                    <input type="date" name="tu_ngay" value="<?= $e($_GET['tu_ngay'] ?? '') ?>">
                </label>
                <label>Đến ngày
                    <input type="date" name="den_ngay" value="<?= $e($_GET['den_ngay'] ?? '') ?>">
                </label>
                <label>Sắp xếp theo
                    <select name="sap_xep">
                        <option value="thoi_gian" <?= ($_GET['sap_xep'] ?? 'thoi_gian') === 'thoi_gian' ? 'selected' : '' ?>>Thời gian bắt đầu</option>
                        <option value="dang_ky" <?= ($_GET['sap_xep'] ?? '') === 'dang_ky' ? 'selected' : '' ?>>Số lượt đăng ký</option>
                    </select>
                </label>
                <label>Thứ tự
                    <select name="huong">
                        <option value="ASC" <?= strtoupper($_GET['huong'] ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                        <option value="DESC" <?= strtoupper($_GET['huong'] ?? '') === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                    </select>
                </label>
                <label>Từ khóa
                    <input type="search" name="tu_khoa" value="<?= $e($_GET['tu_khoa'] ?? '') ?>" placeholder="Tên sự kiện...">
                </label>
            </div>
            <div class="nhom-nut">
                <button class="nut nut-chinh" type="submit">Áp dụng bộ lọc</button>
                <a class="nut" href="?action=quan-ly-su-kien">Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>
<!-- MODAL THÊM / SỬA SỰ KIỆN -->
<div class="modal-su-kien" id="modalSuKien">
    <div class="modal-su-kien-noi-dung">

        <div class="dau-form-su-kien">
            <div>
                <span class="nhan-form">QUẢN LÝ SỰ KIỆN</span>
                <h2 id="tieuDeFormSuKien">Thêm sự kiện</h2>
                <p>Nhập thông tin để tạo sự kiện mới.</p>
            </div>

            <button type="button"
                    class="nut-dong-modal"
                    id="nutDongSuKien"
                    aria-label="Đóng form">
                &times;
            </button>
        </div>

        <form
    method="POST"
    id="formSuKien"
    class="form-su-kien"
    action="index.php?action=them-su-kien"
    enctype="multipart/form-data"
>

            <input type="hidden" name="id" id="su_kien_id">

            <div class="luoi-form-su-kien">

                <div class="form-nhom form-rong">
                    <label for="ten_su_kien">Tên sự kiện *</label>
                    <input
                        type="text"
                        name="ten_su_kien"
                        id="ten_su_kien"
                        maxlength="200"
                        placeholder="Nhập tên sự kiện"
                        required
                    >
                </div>

                <div class="form-nhom">
                    <label for="cau_lac_bo_id">Câu lạc bộ *</label>
                    <select
                        name="cau_lac_bo_id"
                        id="cau_lac_bo_id"
                        required
                    >
                        <option value="">-- Chọn câu lạc bộ --</option>

                        <?php foreach ($danhSachCauLacBo as $clb): ?>
                            <option value="<?= (int) $clb['id'] ?>">
                                <?= $e($clb['ten_cau_lac_bo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-nhom">
                    <label for="dia_diem">Địa điểm</label>
                    <input
                        type="text"
                        name="dia_diem"
                        id="dia_diem"
                        maxlength="255"
                        placeholder="Ví dụ: Hội trường A"
                    >
                </div>

                <div class="form-nhom">
                    <label for="thoi_gian_bat_dau">
                        Thời gian bắt đầu *
                    </label>
                    <input
                        type="datetime-local"
                        name="thoi_gian_bat_dau"
                        id="thoi_gian_bat_dau"
                        required
                    >
                </div>

                <div class="form-nhom">
                    <label for="thoi_gian_ket_thuc">
                        Thời gian kết thúc
                    </label>
                    <input
                        type="datetime-local"
                        name="thoi_gian_ket_thuc"
                        id="thoi_gian_ket_thuc"
                    >
                </div>

                <div class="form-nhom">
                    <label for="han_dang_ky">Hạn đăng ký</label>
                    <input
                        type="datetime-local"
                        name="han_dang_ky"
                        id="han_dang_ky"
                    >
                </div>

                <div class="form-nhom">
                    <label for="so_luong_toi_da">
                        Số lượng tối đa *
                    </label>
                    <input
                        type="number"
                        name="so_luong_toi_da"
                        id="so_luong_toi_da"
                        min="1"
                        value="100"
                        required
                    >
                </div>

                <div class="form-nhom">
                    <label for="trang_thai">Trạng thái *</label>
                    <select
                        name="trang_thai"
                        id="trang_thai"
                        required
                    >
                        <option value="nhap">Bản nháp</option>
                        <option value="cong_khai">Công khai</option>
                        <option value="da_huy">Đã hủy</option>
                        <option value="ket_thuc">Đã kết thúc</option>
                    </select>
                </div>

                <!-- TẢI ẢNH -->
                <div class="form-nhom form-rong">
                    <label for="tep_hinh_anh">Ảnh sự kiện</label>

                    <div class="khung-tai-anh">
                        <input
    type="file"
    name="hinh_anh"
    id="tep_hinh_anh"
    accept="image/jpeg,image/png,image/webp"
>

                        <small>
                            Chọn ảnh JPG, PNG hoặc WEBP.
                        </small>
                        

                        <img
                            id="xemTruocAnh"
                            class="xem-truoc-anh"
                            src=""
                            alt="Ảnh xem trước"
                            style="display:none"
                        >
                    </div>
                </div>

                <div class="form-nhom form-rong">
                    <label for="mo_ta">Mô tả sự kiện</label>
                    <textarea
                        name="mo_ta"
                        id="mo_ta"
                        rows="4"
                        placeholder="Nhập nội dung mô tả sự kiện..."
                    ></textarea>
                </div>

            </div>

            <div class="chan-form-su-kien">
                <button
                    type="button"
                    class="nut-huy-form"
                    id="nutHuySuKien"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="nut-luu-form"
                    id="nutLuuSuKien"
                >
                    <span>✓</span> Lưu sự kiện
                </button>
            </div>

        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalSuKien');
    const form = document.getElementById('formSuKien');

    const tieuDeForm = document.getElementById('tieuDeFormSuKien');
    const moTaForm = document.querySelector('.dau-form-su-kien p');
    const nutLuu = document.getElementById('nutLuuSuKien');

    const inputId = document.getElementById('su_kien_id');
    const inputTen = document.getElementById('ten_su_kien');
    const inputCLB = document.getElementById('cau_lac_bo_id');
    const inputDiaDiem = document.getElementById('dia_diem');
    const inputBatDau = document.getElementById('thoi_gian_bat_dau');
    const inputKetThuc = document.getElementById('thoi_gian_ket_thuc');
    const inputHanDangKy = document.getElementById('han_dang_ky');
    const inputSoLuong = document.getElementById('so_luong_toi_da');
    const inputTrangThai = document.getElementById('trang_thai');
    const inputMoTa = document.getElementById('mo_ta');

    const inputAnh = document.getElementById('tep_hinh_anh');
    const xemTruocAnh = document.getElementById('xemTruocAnh');

    // Đổi ngày giờ MySQL thành định dạng datetime-local
    function chuanHoaThoiGian(value) {
        if (!value) return '';

        return String(value)
            .replace(' ', 'T')
            .slice(0, 16);
    }

    // Mở modal
    function moModal() {
        if (modal) {
            modal.classList.add('dang-mo');
        }
    }

    // Đóng modal
    function dongModal() {
        if (modal) {
            modal.classList.remove('dang-mo');
        }
    }

    // Xóa ảnh xem trước
    function xoaAnhXemTruoc() {
        if (xemTruocAnh) {
            xemTruocAnh.removeAttribute('src');
            xemTruocAnh.style.display = 'none';
        }
    }

    // Mở form thêm sự kiện
    function moFormThemSuKien() {
        if (!form) return;

        form.reset();

        inputId.value = '';
        inputSoLuong.value = '100';
        inputTrangThai.value = 'nhap';

        // Chuyển sang route thêm
        form.action = 'index.php?action=them-su-kien';

        tieuDeForm.textContent = 'Thêm sự kiện';
        moTaForm.textContent = 'Nhập thông tin để tạo sự kiện mới.';
        nutLuu.innerHTML = '<span>✓</span> Lưu sự kiện';

        xoaAnhXemTruoc();
        moModal();
    }

    // Gắn hàm vào window vì nút Thêm có thể gọi từ HTML
    window.moFormThemSuKien = moFormThemSuKien;

    // Nút Thêm
    const nutThem = document.getElementById('nutThemSuKien');

    if (nutThem) {
        nutThem.addEventListener('click', function (event) {
            event.preventDefault();
            moFormThemSuKien();
        });
    }

    // Nút đóng và nút Hủy
    const nutDong = document.getElementById('nutDongSuKien');
    const nutHuy = document.getElementById('nutHuySuKien');

    if (nutDong) {
        nutDong.addEventListener('click', function (event) {
            event.preventDefault();
            dongModal();
        });
    }

    if (nutHuy) {
        nutHuy.addEventListener('click', function (event) {
            event.preventDefault();
            dongModal();
        });
    }

    // Bấm ra ngoài modal để đóng
    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                dongModal();
            }
        });
    }

    // Nhấn Escape để đóng
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            dongModal();
        }
    });

    // Nút Sửa: đổ dữ liệu sự kiện vào form
    document.querySelectorAll('.nutSuaSuKien').forEach(function (nut) {
        nut.addEventListener('click', function (event) {
            event.preventDefault();

            if (!form) return;

            form.reset();

            inputId.value = nut.dataset.id || '';
            inputTen.value = nut.dataset.ten || '';
            inputCLB.value = nut.dataset.clb || '';
            inputDiaDiem.value = nut.dataset.diaDiem || '';
            inputSoLuong.value = nut.dataset.soLuong || '1';

            inputBatDau.value = chuanHoaThoiGian(
                nut.dataset.batDau
            );

            inputKetThuc.value = chuanHoaThoiGian(
                nut.dataset.ketThuc
            );

            inputHanDangKy.value = chuanHoaThoiGian(
                nut.dataset.hanDangKy
            );

            inputTrangThai.value = nut.dataset.trangThai || 'nhap';
            inputMoTa.value = nut.dataset.moTa || '';

            // Chuyển sang route cập nhật
            form.action = 'index.php?action=cap-nhat-su-kien';

            tieuDeForm.textContent = 'Sửa sự kiện';
            moTaForm.textContent = 'Cập nhật thông tin sự kiện đã chọn.';
            nutLuu.innerHTML = '<span>✓</span> Lưu thay đổi';

            // Hiển thị ảnh hiện tại nếu có
            const anhCu = nut.dataset.hinh || '';

if (anhCu && xemTruocAnh) {
    const duongDanAnh = anhCu.startsWith('http')
        ? anhCu
        : '/Nhom-4-LTW/public/' + anhCu.replace(/^\/+/, '');

    xemTruocAnh.src = duongDanAnh;
    xemTruocAnh.style.display = 'block';
} else {
    xoaAnhXemTruoc();
}

            moModal();
        });
    });

    // Xem trước ảnh mới được chọn
    if (inputAnh && xemTruocAnh) {
        inputAnh.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file) {
                xoaAnhXemTruoc();
                return;
            }

            const loaiAnhChoPhep = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!loaiAnhChoPhep.includes(file.type)) {
                alert('Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
                this.value = '';
                xoaAnhXemTruoc();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ảnh không được vượt quá 5MB.');
                this.value = '';
                xoaAnhXemTruoc();
                return;
            }

            xemTruocAnh.src = URL.createObjectURL(file);
            xemTruocAnh.style.display = 'block';
        });
    }
});
</script>
</body>
</html>
