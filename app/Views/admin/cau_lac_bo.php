
<?php
$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = 'cau-lac-bo';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$danhSach = $danhSach ?? [];

$thongBao = $_SESSION['thong_bao'] ?? '';
$loaiThongBao = $_SESSION['loai_thong_bao'] ?? 'success';

unset($_SESSION['thong_bao'], $_SESSION['loai_thong_bao']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu lạc bộ</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #F5F1E5;
            color: #174F42;
        }

        .layout { display: flex; min-height: 100vh; }

        .sidebar {
            width: 235px;
            min-width: 235px;
            background: #0B4D3E;
            color: #FFFFFF;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            text-align: center;
            padding: 10px 5px 28px;
            border-bottom: 1px solid rgba(255,255,255,.2);
        }

        .menu {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu a {
            display: block;
            color: #FFFFFF;
            text-decoration: none;
            padding: 13px 14px;
            border-radius: 8px;
        }

        .menu a:hover,
        .menu a.active { background: #176653; }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 25px;
            color: #E9E2D2;
            font-size: 13px;
            text-align: center;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
            overflow-x: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            color: #0B4D3E;
        }

        .description {
            margin-top: 8px;
            color: #64756D;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary { background: #0B4D3E; color: #FFFFFF; }
        .btn-primary:hover { background: #176653; }
        .btn-edit { background: #E9E2D2; color: #174F42; }
        .btn-delete { background: #fff0ed; color: #a93226; }

        .table-container {
            background: #FFFFFF;
            border: 1px solid #E9E2D2;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 3px 12px rgba(23,79,66,.05);
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        thead { background: #E9E2D2; }

        th, td {
            padding: 15px 14px;
            border-bottom: 1px solid #E9E2D2;
            text-align: left;
            vertical-align: middle;
            font-size: 14px;
        }

        th { white-space: nowrap; }

        tbody tr:hover { background: #FAF8F2; }
        tbody tr:last-child td { border-bottom: none; }

        .number-cell {
            width: 65px;
            text-align: center;
        }

        .id-cell { width: 65px; }

        .description-cell {
            min-width: 200px;
            max-width: 320px;
            overflow-wrap: anywhere;
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .actions .btn { padding: 8px 11px; }
        .delete-form { display: inline; margin: 0; }

        .notice {
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #e5f3e9;
            color: #176653;
            border: 1px solid #b8d9c4;
        }

        .notice.error {
            background: #fff0ed;
            color: #a93226;
            border-color: #f0c7bf;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #64756D;
        }

        @media (max-width: 768px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; min-width: 0; padding: 15px; }
            .brand { padding-bottom: 15px; }
            .menu { margin-top: 15px; }
            .sidebar-footer { display: none; }
            .main { padding: 20px 14px; }
            h1 { font-size: 23px; }
        }
    </style>
</head>

<body>
<div class="layout">

    <?php require __DIR__ . '/partials/menu.php'; ?>

    <main class="main">
        <div class="page-header">
            <div>
                <h1>Quản lý câu lạc bộ</h1>
                <div class="description">
                    Danh sách các câu lạc bộ trong hệ thống
                </div>
            </div>

            <a
                class="btn btn-primary"
                href="<?= e($duongDanGoc) ?>/?page=them-cau-lac-bo"
            >
                + Thêm câu lạc bộ
            </a>
        </div>

        <?php if ($thongBao !== ''): ?>
            <div class="notice <?= $loaiThongBao === 'error' ? 'error' : '' ?>">
                <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="number-cell">STT</th>
                        <th class="id-cell">ID</th>
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
                        <td colspan="7" class="empty">
                            Chưa có câu lạc bộ nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $stt = 1; ?>

                    <?php foreach ($danhSach as $clb): ?>
                        <tr>
                            <td class="number-cell"><?= $stt++ ?></td>
                            <td class="id-cell"><?= e($clb['id'] ?? '') ?></td>
                            <td><?= e($clb['ten_cau_lac_bo'] ?? '') ?></td>
                            <td class="description-cell">
                                <?= e($clb['mo_ta'] ?? '') ?>
                            </td>
                            <td>
                                <?= e($clb['ten_nguoi_quan_ly'] ?? 'Chưa có') ?>
                            </td>
                            <td><?= e($clb['ngay_tao'] ?? '') ?></td>
                            <td>
                                <div class="actions">
                                    <a
                                        class="btn btn-edit"
                                        href="<?= e($duongDanGoc) ?>/?page=sua-cau-lac-bo&id=<?= urlencode((string) ($clb['id'] ?? '')) ?>"
                                    >Sửa</a>

                                    <form
                                        class="delete-form"
                                        method="POST"
                                        action="<?= e($duongDanGoc) ?>/?page=xoa-cau-lac-bo"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu lạc bộ này không?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e($clb['id'] ?? '') ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-delete"
                                        >Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
