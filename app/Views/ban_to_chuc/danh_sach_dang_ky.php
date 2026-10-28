
<?php
if (!isset($dangKyList)) {
    $dangKyList = [];
}

function eDangKy($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$nhanTrangThai = [
    'da_dang_ky' => 'Đã đăng ký',
    'da_tham_gia' => 'Đã tham gia'
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách người đăng ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f5f7fb;
            color: #222;
        }

        h2 {
            margin-bottom: 8px;
        }

        .thong-tin {
            margin-bottom: 20px;
            color: #555;
        }

        .bang-container {
            overflow-x: auto;
            background: #fff;
            border-radius: 8px;
            padding: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #eef2f7;
        }

        .nut {
            display: inline-block;
            margin-top: 18px;
            padding: 10px 16px;
            background: #e5e7eb;
            color: #222;
            text-decoration: none;
            border-radius: 5px;
        }

        .trong {
            text-align: center;
            color: #666;
            padding: 24px;
        }
    </style>
</head>
<body>

    <h2>Danh sách người đăng ký</h2>

    <div class="thong-tin">
        <strong>Sự kiện:</strong>
        <?= eDangKy($suKien['ten_su_kien'] ?? '') ?>
        <br>
        <strong>Số người đăng ký:</strong>
        <?= count($dangKyList) ?>
    </div>

    <div class="bang-container">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Thời gian đăng ký</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dangKyList)): ?>
                    <tr>
                        <td colspan="5" class="trong">
                            Chưa có người đăng ký sự kiện này.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dangKyList as $index => $dangKy): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= eDangKy($dangKy['ho_ten']) ?></td>
                            <td><?= eDangKy($dangKy['email']) ?></td>
                            <td><?= eDangKy($dangKy['thoi_gian_dang_ky']) ?></td>
                            
<td>
    <?= eDangKy(
        $nhanTrangThai[$dangKy['trang_thai']]
        ?? $dangKy['trang_thai']
    ) ?>

    <?php if ($dangKy['trang_thai'] === 'da_dang_ky'): ?>
        <form
            action="/Nhom-4-LTW/public/index.php?trang=diem_danh"
            method="POST"
            onsubmit="return confirm('Xác nhận người này đã tham gia sự kiện?');"
            style="margin-top: 8px;"
        >
            <input
                type="hidden"
                name="dang_ky_id"
                value="<?= (int) $dangKy['dang_ky_id'] ?>"
            >

            <input
                type="hidden"
                name="su_kien_id"
                value="<?= (int) $suKien['id'] ?>"
            >
            <input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars(taoCsrfToken(), ENT_QUOTES, 'UTF-8') ?>"
>

            <button
                type="submit"
                class="nut"
            >
                Điểm danh
            </button>
        </form>
    <?php endif; ?>
</td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a class="nut"
       href="/Nhom-4-LTW/public/index.php?trang=danh_sach">
        ← Quay lại danh sách sự kiện
    </a>

</body>
</html>
