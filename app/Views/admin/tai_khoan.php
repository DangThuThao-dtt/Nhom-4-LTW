
<?php
$danhSach = $danhSach ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý tài khoản</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            color: #243247;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        h1 { margin: 0 0 8px; }

        .muted { color: #64748b; }

        .panel {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 10px #0000000a;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        th { background: #edf2f7; }

        a {
            color: #2459a9;
            text-decoration: none;
        }

        .empty {
            text-align: center;
            padding: 28px;
            color: #64748b;
        }

        .count { margin-bottom: 18px; }
    </style>
</head>

<body>
<main class="container">
    <div class="header">
        <div>
            <h1>Quản lý tài khoản</h1>
            <div class="muted">
                Danh sách tài khoản trong hệ thống
            </div>
        </div>

        <a href="index.php?page=dashboard">
            ← Quay lại Dashboard
        </a>
    </div>

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
                        <td><?= (int) $tk['id'] ?></td>
                        <td>
                            <?= htmlspecialchars(
                                $tk['ho_ten'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $tk['email'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $tk['vai_tro'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $tk['ngay_tao'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>
