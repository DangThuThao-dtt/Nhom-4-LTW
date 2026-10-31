
<?php
// Du lieu $danhSach duoc truyen tu Controller.
$danhSach = $danhSach ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu lạc bộ</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
            color: #263238;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            margin-bottom: 8px;
        }

        .description {
            color: #667085;
            margin-bottom: 24px;
        }

        .table-wrapper {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 8px #0000000d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #edf2f7;
        }

        .empty {
            text-align: center;
            color: #667085;
            padding: 30px;
        }

        .count {
            margin-bottom: 15px;
            color: #475467;
        }
    </style>
</head>

<body>
    <main class="container">
        <h1>Quản lý câu lạc bộ</h1>

        <p class="description">
            Danh sách các câu lạc bộ trong hệ thống quản lý sự kiện sinh viên.
        </p>

        <section class="table-wrapper">
            <p class="count">
                Tổng số câu lạc bộ:
                <strong><?= count($danhSach) ?></strong>
            </p>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên câu lạc bộ</th>
                        <th>Mô tả</th>
                        <th>Người quản lý</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($danhSach)): ?>
                        <tr>
                            <td colspan="5" class="empty">
                                Chưa có câu lạc bộ nào trong hệ thống.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSach as $clb): ?>
                            <tr>
                                <td>
                                    <?= (int) $clb['id'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $clb['ten_cau_lac_bo'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= nl2br(htmlspecialchars(
                                        $clb['mo_ta'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $clb['ten_nguoi_quan_ly'] ?? 'Chưa phân công',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $clb['ngay_tao'] ?? '',
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
