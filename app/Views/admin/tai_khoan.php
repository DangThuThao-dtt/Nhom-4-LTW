
<?php
$danhSach = $danhSach ?? [];
$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = 'tai-khoan';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý tài khoản</title>

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
        }

        .menu a:hover,
        .menu a.active { background: var(--xanh-phu); }

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
            padding: 30px;
        }

        .header { margin-bottom: 24px; }

        h1 {
            margin: 0 0 8px;
            color: var(--xanh-chinh);
        }

        .muted {
            color: var(--chu-phu);
            line-height: 1.6;
        }

        .panel {
            padding: 22px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 2px 10px #174F4208;
            overflow-x: auto;
        }

        .count { margin-top: 0; }
        .count strong { color: var(--xanh-chinh); }

        table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid var(--vien);
        }

        th {
            background: var(--nen);
            color: var(--xanh-chinh);
            white-space: nowrap;
        }

        tbody tr:hover { background: #F9F7F0; }

        .empty {
            padding: 28px;
            text-align: center;
            color: var(--chu-phu);
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

            .panel { padding: 14px; }
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/partials/menu.php'; ?>

    <main class="main">
        <header class="header">
            <h1>Quản lý tài khoản</h1>
            <div class="muted">
                Danh sách tài khoản trong hệ thống
            </div>
        </header>

        <section class="panel">
            <p class="count">
                Tổng số tài khoản:
                <strong><?= count($danhSach) ?></strong>
            </p>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($danhSach)): ?>
                        <tr>
                            <td colspan="5" class="empty">
                                Chưa có tài khoản nào trong hệ thống.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSach as $tk): ?>
                            <tr>
                                <td><?= (int) ($tk['id'] ?? 0) ?></td>
                                <td><?= e($tk['ho_ten'] ?? '') ?></td>
                                <td><?= e($tk['email'] ?? '') ?></td>
                                <td><?= e($tk['vai_tro'] ?? '') ?></td>
                                <td><?= e($tk['ngay_tao'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
