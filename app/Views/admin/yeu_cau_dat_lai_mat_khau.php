<?php
$danhSachYeuCauDatLaiMatKhau =
    $danhSachYeuCauDatLaiMatKhau ?? [];

$soYeuCauChoXuLy = count($danhSachYeuCauDatLaiMatKhau);

$tenQuanTriVien = $_SESSION['nguoi_dung']['ho_ten']
    ?? 'Quản trị viên';

$trangHienTai = $_GET['action'] ?? '';

function eYeuCau($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Yêu cầu cấp lại mật khẩu - Admin</title>

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
            --do-nhat: #FCEAE8;
            --do: #C6534B;
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
            text-decoration: none;
        }

        /* Thanh bên */

        .thanh-ben {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 230px;
            padding: 18px 12px;
            background: #075747;
            color: #fff;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 10;
        }

        .thuong-hieu {
            margin: 0 8px 25px;
            padding: 8px 0 20px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
        }

        .nhom-menu {
            margin: 14px 8px 8px;
            color: #c6d9d0;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu-ben {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-ben a {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 10px;
            border-radius: 8px;
            color: #f5faf6;
            font-size: 13px;
            transition: background .2s;
        }

        .menu-ben a:hover,
        .menu-ben a.active {
            background: #13866f;
        }

        .menu-ben a.active {
            border-left: 3px solid var(--vang);
            padding-left: 7px;
            font-weight: 700;
        }

        .menu-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 20px;
            flex: 0 0 20px;
            font-size: 16px;
        }

        .chan-ben {
            margin-top: auto;
            padding: 18px 8px 0;
            color: #d6e4dc;
            font-size: 11px;
            line-height: 1.7;
        }

        /* Nội dung chính */

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
            font-size: 27px;
        }

        .header-description {
            color: var(--chu-phu);
            font-size: 13px;
            line-height: 1.6;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 10px;
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
            margin-bottom: 4px;
            font-size: 13px;
            font-weight: bold;
        }

        .muted {
            color: var(--chu-phu);
            font-size: 11px;
        }

        /* Phần giới thiệu */

        .welcome {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 25px;
            margin-bottom: 24px;
            border-radius: 13px;
            background: var(--xanh-chinh);
            color: var(--trang);
        }

        .welcome h2 {
            margin: 0 0 9px;
            font-size: 21px;
        }

        .welcome p {
            margin: 0;
            color: #D7E7DF;
            font-size: 13px;
            line-height: 1.7;
        }

        .welcome-icon {
            font-size: 45px;
            opacity: .9;
        }

        /* Thẻ thống kê */

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 20px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 3px 12px #174F4208;
        }

        .stat-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .stat-card-title {
            color: var(--chu-phu);
            font-size: 13px;
            line-height: 1.5;
        }

        .stat-card-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--xanh-nhat);
            font-size: 19px;
        }

        .stat-card-icon.orange {
            background: var(--cam-nhat);
        }

        .stat-number {
            margin: 15px 0 7px;
            color: var(--xanh-chinh);
            font-size: 30px;
            font-weight: bold;
        }

        .stat-note {
            color: var(--chu-phu);
            font-size: 11px;
        }

        /* Bảng danh sách */

        .panel {
            min-width: 0;
            padding: 23px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 3px 12px #174F4208;
        }

        .panel-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 8px;
        }

        .panel-heading h2 {
            margin: 0;
            color: var(--xanh-chinh);
            font-size: 17px;
        }

        .panel-description {
            margin: 0 0 20px;
            color: var(--chu-phu);
            font-size: 12px;
            line-height: 1.6;
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 20px;
            background: var(--xanh-nhat);
            color: var(--xanh-chinh);
            font-size: 12px;
            font-weight: bold;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--trang);
        }

        thead {
            background: var(--xanh-nhat);
        }

        th {
            color: var(--xanh-chinh);
            font-size: 12px;
            font-weight: 700;
            text-align: left;
            white-space: nowrap;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--vien);
            vertical-align: middle;
        }

        td {
            color: var(--chu);
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f8fbf8;
        }

        .request-id {
            color: var(--xanh-phu);
            font-weight: bold;
        }

        .email {
            color: var(--chu-phu);
            overflow-wrap: anywhere;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 20px;
            background: var(--cam-nhat);
            color: #94651e;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #d49a35;
        }

        /* Trạng thái không có dữ liệu */

        .empty-state {
            padding: 45px 20px;
            text-align: center;
        }

        .empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: var(--xanh-nhat);
            color: var(--xanh-chinh);
            font-size: 30px;
        }

        .empty-state h3 {
            margin: 0 0 9px;
            color: var(--xanh-chinh);
            font-size: 17px;
        }

        .empty-state p {
            max-width: 430px;
            margin: 0 auto;
            color: var(--chu-phu);
            font-size: 13px;
            line-height: 1.7;
        }

        .footer {
            margin-top: 25px;
            padding: 18px 0 0;
            border-top: 1px solid var(--vien);
            color: var(--chu-phu);
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 1000px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .main {
                padding: 22px;
            }
        }

        @media (max-width: 760px) {
            .thanh-ben {
                position: relative;
                width: 100%;
                height: auto;
                padding: 16px;
            }

            .menu-ben {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .chan-ben {
                margin-top: 16px;
            }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .header h1 {
                font-size: 23px;
            }

            .welcome {
                padding: 20px;
            }

            .welcome-icon {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .panel {
                padding: 16px;
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
        SỰ KIỆN SINH VIÊN
    </div>

    <nav class="menu-ben">
        <div class="nhom-menu">Tổng quan</div>

        <a href="?action=dashboard">
            <span class="menu-icon">▦</span>
            <span>Dashboard Admin</span>
        </a>

        <div class="nhom-menu">Quản lý hệ thống</div>

        <a href="?action=quan-ly-su-kien">
            <span class="menu-icon">▣</span>
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

        <div class="nhom-menu">Thông báo</div>

        <a
            href="?action=yeu-cau-dat-lai-mat-khau"
            class="active"
        >
            <span class="menu-icon">♧</span>
            <span>Yêu cầu mật khẩu</span>
        </a>
    </nav>

    <div class="chan-ben">
        Hệ thống quản lý sự kiện sinh viên
    </div>
</aside>

<main class="main">

    <header class="header">
        <div>
            <h1>Yêu cầu cấp lại mật khẩu</h1>

            <div class="header-description">
                Theo dõi và quản lý các yêu cầu cấp lại mật khẩu từ ban tổ chức.
            </div>
        </div>

        <div class="header-actions">
            <div class="admin-info">
                <div class="avatar">A</div>

                <div>
                    <div class="admin-name">
                        <?= eYeuCau($tenQuanTriVien) ?>
                    </div>

                    <div class="muted">
                        Quản trị hệ thống
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="welcome">
        <div>
            <h2>Quản lý yêu cầu tài khoản</h2>

            <p>
                Xem các yêu cầu đang chờ xử lý và theo dõi thời điểm
                ban tổ chức gửi yêu cầu cấp lại mật khẩu.
            </p>
        </div>

        <div class="welcome-icon">♧</div>
    </section>

    <section class="stats">
        <article class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-title">
                    Yêu cầu đang chờ xử lý
                </div>

                <div class="stat-card-icon orange">◷</div>
            </div>

            <div class="stat-number">
                <?= $soYeuCauChoXuLy ?>
            </div>

            <div class="stat-note">
                Yêu cầu cần được quản trị viên xem xét
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-title">
                    Trạng thái hệ thống
                </div>

                <div class="stat-card-icon">✓</div>
            </div>

            <div class="stat-number" style="font-size: 23px;">
                Hoạt động
            </div>

            <div class="stat-note">
                Chức năng theo dõi yêu cầu đang hoạt động
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-title">
                    Danh sách hiện tại
                </div>

                <div class="stat-card-icon">▤</div>
            </div>

            <div class="stat-number">
                <?= $soYeuCauChoXuLy ?>
            </div>

            <div class="stat-note">
                Số yêu cầu đang hiển thị trên trang
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <h2>Danh sách yêu cầu</h2>

            <span class="count-badge">
                <?= $soYeuCauChoXuLy ?> yêu cầu
            </span>
        </div>

        <p class="panel-description">
            Danh sách các yêu cầu cấp lại mật khẩu chưa được xử lý.
        </p>

        <?php if (empty($danhSachYeuCauDatLaiMatKhau)): ?>

            <div class="empty-state">
                <div class="empty-icon">✓</div>

                <h3>Không có yêu cầu đang chờ xử lý</h3>

                <p>
                    Hiện tại chưa có yêu cầu cấp lại mật khẩu nào cần xử lý.
                    Các yêu cầu mới sẽ được hiển thị tại đây.
                </p>
            </div>

        <?php else: ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mã yêu cầu</th>
                            <th>Người gửi</th>
                            <th>Email</th>
                            <th>Nội dung</th>
                            <th>Ngày gửi</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (
                            $danhSachYeuCauDatLaiMatKhau as $yeuCau
                        ): ?>
                            <tr>
                                <td class="request-id">
                                    #<?= eYeuCau($yeuCau['id'] ?? '') ?>
                                </td>

                                <td>
                                    <?= eYeuCau($yeuCau['ho_ten'] ?? '') ?>
                                </td>

                                <td class="email">
                                    <?= eYeuCau($yeuCau['email'] ?? '') ?>
                                </td>

                                <td>
                                    <?= eYeuCau(
                                        $yeuCau['noi_dung']
                                            ?? 'Không có nội dung'
                                    ) ?>
                                </td>

                                <td>
                                    <?= eYeuCau($yeuCau['ngay_gui'] ?? '') ?>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        <span class="status-dot"></span>
                                        Chờ xử lý
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

    <footer class="footer">
        © Hệ thống quản lý sự kiện sinh viên
    </footer>

</main>

</body>
</html>