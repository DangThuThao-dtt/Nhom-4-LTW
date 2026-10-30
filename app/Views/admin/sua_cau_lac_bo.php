
<?php
$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = 'cau-lac-bo';

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
    <title>Sửa câu lạc bộ</title>

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

        .form-box {
            max-width: 800px;
            margin: 0 auto;
            padding: 28px;
            background: var(--trang);
            border: 1px solid var(--vien);
            border-radius: 12px;
            box-shadow: 0 2px 10px #174F4208;
        }

        h1 {
            margin-top: 0;
            color: var(--xanh-chinh);
        }

        .muted {
            color: var(--chu-phu);
            line-height: 1.6;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
            color: var(--xanh-chinh);
        }

        input, textarea {
            display: block;
            width: 100%;
            padding: 11px 12px;
            margin-top: 7px;
            border: 1px solid var(--vien);
            border-radius: 6px;
            background: var(--trang);
            color: var(--chu);
            font: inherit;
        }

        input:focus, textarea:focus {
            outline: 2px solid var(--xanh-phu);
            outline-offset: 1px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
        }

        button, .back {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 6px;
            font: inherit;
            text-decoration: none;
        }

        button {
            background: var(--xanh-chinh);
            color: var(--trang);
            cursor: pointer;
        }

        button:hover { background: var(--xanh-phu); }

        .back {
            background: var(--nen);
            border: 1px solid var(--vien);
            color: var(--xanh-chinh);
        }

        .back:hover { background: var(--vien); }

        .error {
            padding: 12px;
            border: 1px solid #D9A7A7;
            border-radius: 6px;
            background: #FFF0F0;
            color: #8B1E1E;
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

            .form-box { padding: 20px; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/partials/menu.php'; ?>

    <main class="main">
        <section class="form-box">
            <h1>Sửa câu lạc bộ #<?= (int) $id ?></h1>
            <p class="muted">
                Cập nhật thông tin câu lạc bộ.
            </p>

            <?php if (!empty($loi)): ?>
                <p class="error"><?= e($loi) ?></p>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= $duongDanGoc ?>/?page=sua-cau-lac-bo&id=<?= (int) $id ?>"
            >
                <label for="ten_cau_lac_bo">
                    Tên câu lạc bộ *
                </label>
                <input
                    type="text"
                    id="ten_cau_lac_bo"
                    name="ten_cau_lac_bo"
                    maxlength="255"
                    value="<?= e($ten ?? '') ?>"
                    required
                >

                <label for="mo_ta">Mô tả</label>
                <textarea
                    id="mo_ta"
                    name="mo_ta"
                    rows="5"
                ><?= e($moTa ?? '') ?></textarea>

                <label for="nguoi_quan_ly_id">
                    Mã người quản lý (không bắt buộc)
                </label>
                <input
                    type="number"
                    id="nguoi_quan_ly_id"
                    name="nguoi_quan_ly_id"
                    min="1"
                    value="<?= e($nguoiQuanLyId ?? '') ?>"
                >

                <p class="muted">
                    Để trống nếu chưa phân công người quản lý.
                </p>

                <div class="actions">
                    <button type="submit">Lưu thay đổi</button>

                    <a class="back"
                       href="<?= $duongDanGoc ?>/?page=cau-lac-bo">
                        Quay lại danh sách
                    </a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
