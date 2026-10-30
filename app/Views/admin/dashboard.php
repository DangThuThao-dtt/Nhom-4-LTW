
<?php
$tongCauLacBo = $tongCauLacBo ?? 0;
$tongTaiKhoan = $tongTaiKhoan ?? 0;
$tongSuKien = $tongSuKien ?? 0;

$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Nhóm 4</title>

    <style>
        :root {
            --trang: #FFFFFF;
            --nen: #F5F1E5;
            --xanh-chinh: #0B4D3E;
            --xanh-phu: #176653;
            --vien: #E9E2D2;
            --chu: #174F42;
            --chu-phu: #64766E;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--nen);
            color: var(--chu);
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            padding: 28px 18px;
            background: var(--xanh-chinh);
            color: var(--trang);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 35px;
            padding: 0 10px;
        }

        .brand span {
            display: block;
            margin-top: 5px;
            color: var(--vien);
            font-size: 11px;
            letter-spacing: 1px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 8px;
            color: var(--trang);
            text-decoration: none;
            transition: background .2s;
        }

        .menu a:hover,
        .menu a.active {
            background: var(--xanh-phu);
        }

        .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 25px 10px 0;
            color: var(--vien);
            font-size: 10px;
            line-height: 1.6;
        }

        .main {
            margin-left: 250px;
            padding: 32px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0 0 8px;
            color: var(--xanh-chinh);
        }

        .muted {
            color: var(--chu-phu);
            line-height: 1.6;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            padding: 24px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 3px 12px #174F4208;
        }

        .card h3 {
            margin-top: 0;
            color: var(--xanh-chinh);
            font-size: 15px;
        }

        .number {
            margin: 12px 0;
            color: var(--xanh-phu);
            font-size: 32px;
            font-weight: bold;
        }

        .section {
            margin-top: 25px;
            padding: 24px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
        }

        .section h2 {
            margin-top: 0;
            color: var(--xanh-chinh);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 7px;
            background: var(--xanh-chinh);
            color: var(--trang);
            text-decoration: none;
            transition: background .2s;
        }

        .btn:hover { background: var(--xanh-phu); }

        .btn.secondary {
            background: var(--nen);
            border: 1px solid var(--vien);
            color: var(--xanh-chinh);
        }

        .btn.secondary:hover { background: var(--vien); }

        @media (max-width: 1000px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .sidebar {
                position: relative;
                width: 100%;
                padding: 18px;
            }

            .brand { margin-bottom: 18px; }

            .menu {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .menu a { margin-bottom: 0; }

            .sidebar-footer { margin-top: 18px; }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/partials/menu.php'; ?>

    <main class="main">
        <header class="header">
            <div>
                <h1>Dashboard Admin</h1>
                <div class="muted">
                    Hệ thống quản lý sự kiện sinh viên
                </div>
            </div>
            <strong>Quản trị hệ thống</strong>
        </header>

        <section class="cards">
            <article class="card">
                <h3>Tổng câu lạc bộ</h3>
                <div class="number"><?= (int) $tongCauLacBo ?></div>
                <div class="muted">Số câu lạc bộ trong hệ thống</div>
            </article>

            <article class="card">
                <h3>Tổng tài khoản</h3>
                <div class="number"><?= (int) $tongTaiKhoan ?></div>
                <div class="muted">Tài khoản thành viên trong hệ thống</div>
            </article>

            <article class="card">
                <h3>Tổng sự kiện</h3>
                <div class="number"><?= (int) $tongSuKien ?></div>
                <div class="muted">Số sự kiện toàn hệ thống</div>
            </article>
        </section>

        <section class="section">
            <h2>Chức năng quản trị</h2>
            <p class="muted">Chọn chức năng bạn muốn quản lý.</p>

            <div class="actions">
                <a class="btn"
                   href="<?= $duongDanGoc ?>/?page=cau-lac-bo">
                    Quản lý câu lạc bộ
                </a>

                <a class="btn secondary"
                   href="<?= $duongDanGoc ?>/?page=tai-khoan">
                    Quản lý tài khoản
                </a>
            </div>
        </section>
    </main>
</body>
</html>
