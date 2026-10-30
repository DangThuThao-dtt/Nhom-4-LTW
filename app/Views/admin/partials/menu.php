
<?php
$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = $trangHienTai ?? '';
?>

<aside class="sidebar">
    <div class="brand">
        NHÓM 4
        <span>QUẢN TRỊ HỆ THỐNG</span>
    </div>

    <nav class="menu">
        <a class="<?= $trangHienTai === 'dashboard' ? 'active' : '' ?>"
           href="<?= $duongDanGoc ?>/?page=dashboard">
            <span class="menu-icon">▦</span>
            <span>Dashboard</span>
        </a>

        <a class="<?= $trangHienTai === 'cau-lac-bo' ? 'active' : '' ?>"
           href="<?= $duongDanGoc ?>/?page=cau-lac-bo">
            <span class="menu-icon">♧</span>
            <span>Quản lý câu lạc bộ</span>
        </a>

        <a class="<?= $trangHienTai === 'tai-khoan' ? 'active' : '' ?>"
           href="<?= $duongDanGoc ?>/?page=tai-khoan">
            <span class="menu-icon">♙</span>
            <span>Quản lý tài khoản</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        HỆ THỐNG QUẢN LÝ SỰ KIỆN SINH VIÊN
    </div>
</aside>
