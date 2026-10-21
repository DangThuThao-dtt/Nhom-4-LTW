
<?php
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
    <title>Thêm câu lạc bộ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 35px auto;
            padding: 0 20px;
            color: #222;
        }
        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
        }
        input, textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 7px;
        }
        button, .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            cursor: pointer;
        }
        .error {
            color: #b00020;
            background: #fff0f0;
            padding: 12px;
        }
    </style>
</head>
<body>
    <h1>Thêm câu lạc bộ</h1>

    <?php if ($loi !== ''): ?>
        <p class="error"><?= e($loi) ?></p>
    <?php endif; ?>

    <form method="POST" action="/?page=them-cau-lac-bo">
        <label for="ten_cau_lac_bo">Tên câu lạc bộ *</label>
        <input
            type="text"
            id="ten_cau_lac_bo"
            name="ten_cau_lac_bo"
            maxlength="255"
            value="<?= e($ten) ?>"
            required
        >

        <label for="mo_ta">Mô tả</label>
        <textarea
            id="mo_ta"
            name="mo_ta"
            rows="5"
        ><?= e($moTa) ?></textarea>

        <label for="nguoi_quan_ly_id">Mã người quản lý (không bắt buộc)</label>
        <input
            type="number"
            id="nguoi_quan_ly_id"
            name="nguoi_quan_ly_id"
            min="1"
            value="<?= e($nguoiQuanLyId) ?>"
        >

        <p>
            Nếu chưa có tài khoản quản lý trong hệ thống, hãy để trống
            trường này.
        </p>

        <button type="submit">Lưu câu lạc bộ</button>
        <a class="back" href="/?page=cau-lac-bo">Quay lại danh sách</a>
    </form>
</body>
</html>
