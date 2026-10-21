
<?php
$danhSach = $danhSach ?? [];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
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

        .add-button, .edit-button {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .add-button {
            background: #1769aa;
            color: white;
            margin-bottom: 20px;
        }

        .edit-button {
            background: #e8f2ff;
            color: #1557a0;
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

        
.success {
    background: #e8f7ed;
    color: #176b35;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 16px;
}

.error {
    background: #fdecec;
    color: #a61b1b;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 16px;
}

.warning {
    background: #fff4db;
    color: #805500;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 16px;
}

.delete-button {
    display: inline-block;
    padding: 9px 13px;
    border: none;
    border-radius: 5px;
    background: #dc3545;
    color: white;
    cursor: pointer;
    font-size: 14px;
}

.delete-button:hover {
    background: #b02a37;
}

    </style>
</head>

<body>
<main class="container">
    <h1>Quản lý câu lạc bộ</h1>

    <p class="description">
        Danh sách các câu lạc bộ trong hệ thống quản lý sự kiện sinh viên.
    </p>

    <?php if (($_GET['them'] ?? '') === 'thanh-cong'): ?>
        <p class="success">Thêm câu lạc bộ thành công!</p>
    <?php endif; ?>

    <?php if (($_GET['sua'] ?? '') === 'thanh-cong'): ?>
        <p class="success">Cập nhật câu lạc bộ thành công!</p>
    <?php endif; ?>
    
<?php
$thongBaoXoa = $_GET['xoa'] ?? '';
?>

<?php if ($thongBaoXoa === 'thanh-cong'): ?>
    <p class="success">Xóa câu lạc bộ thành công!</p>
<?php elseif ($thongBaoXoa === 'bi-rang-buoc'): ?>
    <p class="warning">
        Không thể xóa câu lạc bộ vì vẫn còn dữ liệu liên quan,
        chẳng hạn như sự kiện đang sử dụng câu lạc bộ này.
    </p>
<?php elseif ($thongBaoXoa === 'loi-id'): ?>
    <p class="error">ID câu lạc bộ không hợp lệ.</p>
<?php elseif ($thongBaoXoa === 'that-bai'): ?>
    <p class="error">
        Không thể xóa câu lạc bộ. Câu lạc bộ có thể không còn tồn tại.
    </p>
<?php endif; ?>


    <a class="add-button" href="/?page=them-cau-lac-bo">
        + Thêm câu lạc bộ
    </a>

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
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($danhSach)): ?>
                <tr>
                    <td colspan="6" class="empty">
                        Chưa có câu lạc bộ nào trong hệ thống.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($danhSach as $clb): ?>
                    <tr>
                        <td><?= (int) $clb['id'] ?></td>

                        <td><?= e($clb['ten_cau_lac_bo'] ?? '') ?></td>

                        <td><?= nl2br(e($clb['mo_ta'] ?? '')) ?></td>

                        <td>
                            <?= e(
                                $clb['ten_nguoi_quan_ly']
                                ?? 'Chưa phân công'
                            ) ?>
                        </td>

                        <td><?= e($clb['ngay_tao'] ?? '') ?></td>

                        <td>
                            <a href="/?page=sua-cau-lac-bo&id=<?= (int) $clb['id'] ?>">
    Sửa
</a>

<form
    method="POST"
    action="/?page=xoa-cau-lac-bo"
    style="display:inline"
    onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu lạc bộ này không?');"
>
    <input
        type="hidden"
        name="id"
        value="<?= (int) $clb['id'] ?>"
    >
    <button type="submit" class="delete-button">Xóa</button>
</form>
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
