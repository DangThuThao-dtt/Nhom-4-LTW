
<?php

$e = static fn($value) => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$danhSachTaiKhoanThanhVien = $danhSachTaiKhoanThanhVien ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
$tuKhoa = $tuKhoa ?? ($_GET['q'] ?? '');
$cauLacBoId = (int) ($cauLacBoId ?? ($_GET['cau_lac_bo_id'] ?? 0));
$tongTaiKhoan = (int) ($tongTaiKhoan ?? count($danhSachTaiKhoanThanhVien));
$tongKetQua = (int) ($tongKetQua ?? count($danhSachTaiKhoanThanhVien));
$trang = max(1, (int) ($trang ?? 1));
$tongTrang = max(1, (int) ($tongTrang ?? 1));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý thành viên</title>

    <style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    color: #263b34;
    background: #f5f6f2;
    line-height: 1.5;
}

/* ===== BỐ CỤC ===== */

.app-layout {
    min-height: 100vh;
}

/* ===== SIDEBAR - GIỐNG DASHBOARD ADMIN ===== */

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
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
}

.page-intro h1 {
    margin: 0 0 7px;
    color: #174b3d;
    font-size: 28px;
    line-height: 1.3;
}

.page-intro p {
    margin: 0;
    color: #68766f;
    font-size: 14px;
}

/* ===== THỐNG KÊ ===== */

.summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.summary-card {
    padding: 18px 20px;
    border: 1px solid #e0e5dc;
    border-radius: 10px;
    background: #fff;
}

.summary-card .label {
    margin: 0 0 8px;
    color: #68766f;
    font-size: 13px;
}

.summary-card .number {
    margin: 0;
    color: #075747;
    font-size: 27px;
    font-weight: 700;
}

/* ===== BỘ LỌC ===== */

.filter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
    align-items: end;
    gap: 14px;
    margin-bottom: 22px;
    padding: 18px;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fff;
}

label {
    display: block;
    color: #344b40;
    font-size: 13px;
    font-weight: 600;
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
    margin-top: 6px;
    padding: 9px 11px;
    border: 1px solid #d2dbd2;
    border-radius: 7px;
    background: #fff;
    color: #263b34;
}

input:focus,
select:focus {
    outline: 2px solid #a9d4c1;
    border-color: #23856d;
}

/* ===== NÚT ===== */

.nut {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-height: 42px;
    padding: 9px 15px;
    border: 1px solid #d8dfd8;
    border-radius: 7px;
    background: #fff;
    color: #28483d;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
}

.nut:hover {
    background: #edf3ed;
}

.nut-chinh {
    border-color: #075747;
    background: #075747;
    color: #fff;
}

.nut-chinh:hover {
    background: #0b6c57;
    color: #fff;
}

/* ===== TIÊU ĐỀ BẢNG ===== */

.dau-bang {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.dau-bang h2 {
    margin: 0;
    color: #234c3e;
    font-size: 19px;
}

.tong-ket-qua {
    color: #68766f;
    font-size: 13px;
}

/* ===== BẢNG DỮ LIỆU ===== */

.table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fff;
}

table {
    width: 100%;
    min-width: 720px;
    border-collapse: collapse;
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
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

td {
    font-size: 13px;
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

/* ===== TRẠNG THÁI ===== */

.nhan-trang-thai {
    display: inline-block;
    padding: 4px 9px;
    border-radius: 20px;
    background: #dff4e7;
    color: #176b43;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

/* ===== BẢNG TRỐNG ===== */

.empty {
    padding: 36px 18px;
    text-align: center;
    color: #68766f;
}

.empty-icon {
    display: block;
    margin-bottom: 8px;
    font-size: 28px;
}

/* ===== PHÂN TRANG ===== */

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 18px;
    margin-top: 22px;
}

.pagination a {
    color: #08644f;
    font-weight: 600;
    text-decoration: none;
}

.pagination a:hover {
    text-decoration: underline;
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
        padding: 20px 12px;
    }

    .page-intro h1 {
        font-size: 24px;
    }

    .summary {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .filter-form {
        grid-template-columns: 1fr;
        padding: 14px;
    }

    .filter-form button {
        width: 100%;
    }
}
</style>
</head>

<body>
<div class="app-layout">

    <!-- Thanh điều hướng dùng chung theo giao diện hiện tại -->
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

        <a href="?action=quan-ly-tai-khoan-thanh-vien"
           class="active"
           aria-current="page">
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

    <!-- Nội dung quản lý thành viên -->
    <main class="noi-dung-chinh">

        <section class="page-intro">
            <div>
                <h1>Quản lý thành viên</h1>
                <p>
                    Tra cứu và theo dõi tài khoản thành viên
                    thuộc các câu lạc bộ.
                </p>
            </div>
        </section>

        <!-- Thẻ thống kê -->
        <section class="summary">
            <div class="summary-card">
                <p class="label">Tổng tài khoản thành viên</p>
                <p class="number">
                    <?= $tongTaiKhoan ?>
                </p>
            </div>

            <div class="summary-card">
                <p class="label">Kết quả tìm được</p>
                <p class="number">
                    <?= $tongKetQua ?>
                </p>
            </div>
        </section>

        <!-- Tìm kiếm và lọc -->
        <form class="filter-form"
              method="get"
              action="index.php">

            <input type="hidden"
                   name="action"
                   value="quan-ly-tai-khoan-thanh-vien">

            <label>
                Tìm theo họ tên hoặc email
                <input
                    type="search"
                    name="q"
                    value="<?= $e($tuKhoa) ?>"
                    placeholder="Nhập họ tên hoặc email..."
                >
            </label>

            <label>
                Lọc theo câu lạc bộ
                <select name="cau_lac_bo_id">
                    <option value="0">Tất cả câu lạc bộ</option>

                    <?php foreach ($danhSachCauLacBo as $clb): ?>
                        <option
                            value="<?= (int) ($clb['id'] ?? 0) ?>"
                            <?= $cauLacBoId === (int) ($clb['id'] ?? 0)
                                ? 'selected'
                                : '' ?>
                        >
                            <?= $e($clb['ten_cau_lac_bo'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button class="nut nut-chinh" type="submit">
                Tìm kiếm
            </button>
        </form>

        <!-- Bảng danh sách -->
        <section>
            <div class="dau-bang">
                <h2>Danh sách thành viên</h2>

                <span class="tong-ket-qua">
                    Tổng kết quả: <strong><?= $tongKetQua ?></strong>
                </span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ và tên</th>
                            <th>Email đăng nhập</th>
                            <th>Câu lạc bộ</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($danhSachTaiKhoanThanhVien)): ?>
                            <tr>
                                <td colspan="5" class="empty">
                                    <span class="empty-icon">♟</span>
                                    <strong>Chưa có dữ liệu thành viên</strong>
                                    <br>
                                    Danh sách sẽ hiển thị khi kết nối
                                    với dữ liệu tài khoản thành viên.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachTaiKhoanThanhVien as $tk): ?>
                                <tr>
                                    <td>
                                        <?= (int) ($tk['id'] ?? 0) ?>
                                    </td>

                                    <td>
                                        <?= $e($tk['ho_ten'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= $e($tk['email'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= $e(
                                            $tk['ten_cau_lac_bo']
                                            ?? 'Chưa gán câu lạc bộ'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="nhan-trang-thai">
                                            <?= $e(
                                                $tk['trang_thai']
                                                ?? 'Chưa xác định'
                                            ) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <nav class="pagination" aria-label="Phân trang thành viên">
                <?php
                $thamSoTrang = [
                    'action' => 'quan-ly-tai-khoan-thanh-vien',
                    'q' => $tuKhoa,
                    'cau_lac_bo_id' => $cauLacBoId,
                ];
                ?>

                <?php if ($trang > 1): ?>
                    <?php $thamSoTrang['trang'] = $trang - 1; ?>

                    <a href="?<?= $e(http_build_query($thamSoTrang)) ?>">
                        ← Trang trước
                    </a>
                <?php endif; ?>

                <span>
                    Trang <?= $trang ?> / <?= $tongTrang ?>
                </span>

                <?php if ($trang < $tongTrang): ?>
                    <?php $thamSoTrang['trang'] = $trang + 1; ?>

                    <a href="?<?= $e(http_build_query($thamSoTrang)) ?>">
                        Trang sau →
                    </a>
                <?php endif; ?>
            </nav>
        </section>

    </main>
</div>
</body>
</html>
