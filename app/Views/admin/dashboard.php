
<?php
// Du lieu thong ke se duoc Controller truyen vao.
$tongCauLacBo = $tongCauLacBo ?? 0;
$tongTaiKhoan = $tongTaiKhoan ?? 0;
$tongSuKien = $tongSuKien ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Nhóm 4</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #243247;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 235px;
            padding: 28px 18px;
            background: #172b4d;
            color: white;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .menu a {
            display: block;
            color: #e2e8f0;
            text-decoration: none;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 7px;
        }

        .menu a:hover,
        .menu a.active {
            background: #2f5597;
            color: white;
        }

        .main {
            margin-left: 235px;
            padding: 32px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0 0 8px;
        }

        .muted {
            color: #718096;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0000000a;
        }

        .card h3 {
            margin-top: 0;
            color: #64748b;
            font-size: 15px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            margin: 12px 0;
            color: #2457a7;
        }

        .section {
            margin-top: 25px;
            background: white;
            border-radius: 12px;
            padding: 24px;
        }

        .section h2 {
            margin-top: 0;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-block;
            padding: 12px 16px;
            background: #2457a7;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        .btn.secondary {
            background: #e8eef8;
            color: #2457a7;
        }

        @media (max-width: 760px) {
            .sidebar {
                position: static;
                width: 100%;
            }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="brand">NHÓM 4 - ADMIN</div>

        <nav class="menu">
            <a class="active" href="index.php?page=dashboard">
                Dashboard
            </a>

            <a href="index.php?page=cau-lac-bo">
                Quản lý câu lạc bộ
            </a>

            <a href="index.php?page=tai-khoan">
                Quản lý tài khoản
            </a>

            <a href="../dang_nhap.php">
                Đăng nhập
            </a>
        </nav>
    </aside>

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
                <div class="number">
                    <?= (int) $tongCauLacBo ?>
                </div>
                <div class="muted">
                    Số câu lạc bộ trong hệ thống
                </div>
            </article>

            <article class="card">
                <h3>Tổng tài khoản</h3>
                <div class="number">
                    <?= (int) $tongTaiKhoan ?>
                </div>
                <div class="muted">
                    Tài khoản thành viên đã đăng ký
                </div>
            </article>

            <article class="card">
                <h3>Tổng sự kiện</h3>
                <div class="number">
                    <?= (int) $tongSuKien ?>
                </div>
                <div class="muted">
                    Số sự kiện toàn hệ thống
                </div>
            </article>
        </section>

        <section class="section">
            <h2>Chức năng quản trị</h2>

            <p class="muted">
                Chọn chức năng bạn muốn quản lý.
            </p>

            <div class="actions">
                <a class="btn"
                   href="index.php?page=cau-lac-bo">
                    Quản lý câu lạc bộ
                </a>

                <a class="btn secondary"
                   href="index.php?page=tai-khoan">
                    Quản lý tài khoản
                </a>
            </div>
        </section>
    </main>
</body>
</html>
