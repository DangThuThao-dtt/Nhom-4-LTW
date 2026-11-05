
<?php
// Các số liệu được truyền từ AdminController.
// Nếu controller chưa truyền dữ liệu, tạm thời hiển thị 0.
$tongSuKien = (int) ($tongSuKien ?? 0);
$tongBanToChuc = (int) ($tongBanToChuc ?? 0);
$tongSuKienChoDuyet = (int) ($tongSuKienChoDuyet ?? 0);
$tongSuKienDangDienRa = (int) ($tongSuKienDangDienRa ?? 0);

$trangHienTai = $_GET['action'] ?? 'dashboard';

$tenQuanTriVien = $_SESSION['nguoi_dung']['ho_ten']
    ?? 'Quản trị viên';

function eDashboard($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Quản lý sự kiện</title>

    <style>
        :root {
            --trang: #FFFFFF;
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
            background: var(--nen);
            color: var(--chu);
        }

        a {
            color: inherit;
        }

/* ===== THANH ĐIỀU HƯỚNG DÙNG CHUNG ===== */
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

        .main {
            margin-left: 230px;
            padding: 30px;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0 0 9px;
            color: var(--xanh-chinh);
            font-size: 28px;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 15px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 10px;
        }
        /* Chuông thông báo Admin */
.thong-bao-admin {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border: 1px solid var(--vien);
    border-radius: 10px;
    background: var(--trang);
    color: var(--xanh-chinh);
    text-decoration: none;
    transition: background 0.2s;
}

.thong-bao-admin:hover {
    background: var(--xanh-nhat);
}

.so-thong-bao {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 999px;
    background: #e53935;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    line-height: 18px;
    text-align: center;
}

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--xanh-nhat);
            color: var(--xanh-chinh);
            font-weight: bold;
        }

        .admin-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .welcome {
            padding: 25px;
            margin-bottom: 24px;
            border-radius: 13px;
            background: var(--xanh-chinh);
            color: var(--trang);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            overflow: hidden;
        }

        .welcome h2 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .welcome p {
            margin: 0;
            color: #D7E7DF;
            font-size: 13px;
            line-height: 1.7;
        }

        .welcome-icon {
            font-size: 55px;
            opacity: .9;
            padding: 0 12px;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 26px 0 15px;
        }

        .section-heading h2 {
            margin: 0;
            color: var(--xanh-chinh);
            font-size: 18px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            min-width: 0;
            padding: 20px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 3px 12px #174F4208;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .card h3 {
            margin: 0;
            color: var(--chu-phu);
            font-size: 13px;
            font-weight: normal;
            line-height: 1.5;
        }

        .card-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 10px;
            background: var(--xanh-nhat);
            font-size: 19px;
        }

        .card-icon.orange {
            background: var(--cam-nhat);
        }

        .number {
            margin: 17px 0 8px;
            color: var(--xanh-chinh);
            font-size: 32px;
            font-weight: bold;
        }

        .card-note {
            color: var(--chu-phu);
            font-size: 11px;
            line-height: 1.5;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(260px, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        .panel {
            min-width: 0;
            padding: 23px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
        }

        .panel h2 {
            margin: 0 0 8px;
            color: var(--xanh-chinh);
            font-size: 17px;
        }

        .panel-description {
            margin: 0 0 20px;
            color: var(--chu-phu);
            font-size: 12px;
            line-height: 1.6;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 76px;
            padding: 14px;
            border: 1px solid var(--vien);
            border-radius: 9px;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }

        .quick-link:hover {
            background: var(--nen);
            border-color: var(--xanh-phu);
        }

        .quick-link-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 9px;
            background: var(--xanh-nhat);
            font-size: 18px;
        }

        .quick-link strong {
            display: block;
            margin-bottom: 5px;
            color: var(--xanh-chinh);
            font-size: 12px;
            line-height: 1.5;
        }

        .quick-link span {
            display: block;
            color: var(--chu-phu);
            font-size: 11px;
            line-height: 1.4;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .task {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .task-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 9px;
            background: var(--xanh-nhat);
        }

        .task strong {
            display: block;
            margin: 2px 0 5px;
            color: var(--xanh-chinh);
            font-size: 12px;
        }

        .task p {
            margin: 0;
            color: var(--chu-phu);
            font-size: 11px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 25px;
            padding: 18px 0 0;
            border-top: 1px solid var(--vien);
            color: var(--chu-phu);
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

@media (max-width: 1000px) and (min-width: 761px) {
    .thanh-ben {
        width: 200px;
    }

    .main {
        margin-left: 200px;
    }

    .main {
        padding: 22px;
    }
}

@media (max-width: 760px) {
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

    .main {
        margin-left: 0;
        padding: 18px;
    }

    .header h1 {
        font-size: 24px;
    }

    .welcome {
        padding: 20px;
    }

    .welcome-icon {
        display: none;
    }

    .cards {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .cards,
    .quick-links {
        grid-template-columns: 1fr;
    }

    .card {
        padding: 17px;
    }

    .panel {
        padding: 17px;
    }

    .admin-info {
        width: 100%;
    }
}

    </style>
</head>

<body>

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

        <a href="?action=dashboard"
           class="<?= $trangHienTai === 'dashboard' ? 'active' : '' ?>">
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

        <a href="?action=thong-ke">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>


</aside>

<main class="main">
    <header class="header">
        <div>
            <h1>Dashboard Admin</h1>
            <div class="muted">
                Tổng quan và quản lý hoạt động sự kiện toàn hệ thống
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px;">

    <!-- Chuông thông báo -->
    <a href="?action=yeu-cau-dat-lai-mat-khau"
       class="thong-bao-admin"
       title="Thông báo đặt lại mật khẩu"
       aria-label="Thông báo đặt lại mật khẩu">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="22"
             height="22"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="1.8"
             stroke-linecap="round"
             stroke-linejoin="round">

            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
            <path d="M10 21h4"/>
        </svg>

        <?php if (($soYeuCauDatLaiMatKhau ?? 0) > 0): ?>
            <span class="so-thong-bao">
                <?= (int) $soYeuCauDatLaiMatKhau ?>
            </span>
        <?php endif; ?>

    </a>

    <!-- Thông tin Admin -->
    <div class="admin-info">
        <div class="avatar">A</div>
        <div>
            <div class="admin-name">
                <?= eDashboard($tenQuanTriVien) ?>
            </div>
            <div class="muted">Quản trị hệ thống</div>
        </div>
    </div>

</div>
    </header>

    <section class="welcome">
        <div>
            <h2>Chào mừng đến với hệ thống quản trị!</h2>
            <p>
                Theo dõi sự kiện, quản lý ban tổ chức và xem các số liệu
                thống kê tại một nơi.
            </p>
        </div>
        <div class="welcome-icon">▦</div>
    </section>

    <div class="section-heading">
        <h2>Tổng quan hệ thống</h2>
    </div>

    <section class="cards">
        <article class="card">
            <div class="card-top">
                <h3>Tổng số sự kiện</h3>
                <div class="card-icon">▣</div>
            </div>
            <div class="number"><?= $tongSuKien ?></div>
            <div class="card-note">Sự kiện trong toàn hệ thống</div>
        </article>

        <article class="card">
            <div class="card-top">
                <h3>Ban tổ chức</h3>
                <div class="card-icon">♧</div>
            </div>
            <div class="number"><?= $tongBanToChuc ?></div>
            <div class="card-note">Tổng số ban tổ chức</div>
        </article>

        <article class="card">
            <div class="card-top">
                <h3>Sự kiện chờ duyệt</h3>
                <div class="card-icon orange">◷</div>
            </div>
            <div class="number"><?= $tongSuKienChoDuyet ?></div>
            <div class="card-note">Sự kiện đang chờ xử lý</div>
        </article>

        <article class="card">
            <div class="card-top">
                <h3>Sự kiện đang diễn ra</h3>
                <div class="card-icon">✓</div>
            </div>
            <div class="number"><?= $tongSuKienDangDienRa ?></div>
            <div class="card-note">Sự kiện đang được tổ chức</div>
        </article>
    </section>

    <section class="content-grid">
        <div class="panel">
            <h2>Truy cập nhanh</h2>
            <p class="panel-description">
                Chọn chức năng để thực hiện công việc quản trị.
            </p>

            <div class="quick-links">
                <a class="quick-link" href="?action=quan-ly-su-kien">
                    <div class="quick-link-icon">▦</div>
                    <div>
                        <strong>Quản lý sự kiện</strong>
                        <span>Xem và cập nhật sự kiện</span>
                    </div>
                </a>

                <a class="quick-link" href="?action=quan-ly-cau-lac-bo">
                    <div class="quick-link-icon">♧</div>
                    <div>
                        <strong>Quản lý câu lạc bộ</strong>
                        <span>Xem và cập nhật thông tin câu lạc bộ</span>
                    </div>
                </a>

                <a class="quick-link" href="?action=quan-ly-tai-khoan">
                    <div class="quick-link-icon">♙</div>
                    <div>
                        <strong>Quản lý ban tổ chức</strong>
                        <span>Theo dõi thông tin ban tổ chức</span>
                    </div>
                </a>

                <a class="quick-link" href="?action=thong-ke">
                    <div class="quick-link-icon">▤</div>
                    <div>
                        <strong>Thống kê</strong>
                        <span>Xem số liệu và báo cáo</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="panel">
            <h2>Các chức năng chính</h2>
            <p class="panel-description">
                Những nhiệm vụ quản trị trọng tâm của hệ thống.
            </p>

<div class="task-list">
    <div class="task">
        <div class="task-icon">01</div>
        <div>
            <strong>Quản lý sự kiện toàn hệ thống</strong>
            <p>
                Theo dõi danh sách, chỉnh sửa thông tin
                và quản lý trạng thái sự kiện.
            </p>
        </div>
    </div>

    <div class="task">
        <div class="task-icon">02</div>
        <div>
            <strong>Quản lý ban tổ chức</strong>
            <p>
                Theo dõi và quản lý thông tin ban tổ chức
                theo chức năng đã triển khai.
            </p>
        </div>
    </div>

    <div class="task">
        <div class="task-icon">03</div>
        <div>
            <strong>Quản lý câu lạc bộ</strong>
            <p>
                Theo dõi và quản lý thông tin câu lạc bộ
                theo chức năng đã triển khai.
            </p>
        </div>
    </div>

    <div class="task">
        <div class="task-icon">04</div>
        <div>
            <strong>Thống kê và báo cáo</strong>
            <p>
                Tổng hợp số liệu hỗ trợ quản trị
                và theo dõi hoạt động sự kiện.
            </p>
        </div>
    </div>
</div>
</div>
</div>
</section>
    <footer class="footer">
        © Hệ thống quản lý sự kiện sinh viên
    </footer>
</main>

</body>
</html>
