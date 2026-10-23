
<?php
require_once __DIR__ . '/../app/Core/ket_noi_csdl.php';

$loi = [];
$hoTen = '';
$email = '';
$soDienThoai = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ biểu mẫu
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
    $matKhau = $_POST['mat_khau'] ?? '';
    $nhapLaiMatKhau = $_POST['nhap_lai_mat_khau'] ?? '';

    // 1. Kiểm tra họ và tên
    if ($hoTen === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên.';
    } elseif (mb_strlen($hoTen) < 2 || mb_strlen($hoTen) > 100) {
        $loi['ho_ten'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
    }

    // 2. Kiểm tra email
    if ($email === '') {
        $loi['email'] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không đúng định dạng.';
    } elseif (mb_strlen($email) > 150) {
        $loi['email'] = 'Email không được vượt quá 150 ký tự.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id FROM nguoi_dung WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            $loi['email'] = 'Email này đã được sử dụng.';
        }
    }

    // 3. Kiểm tra số điện thoại
if ($soDienThoai === '') {
    $loi['so_dien_thoai'] = 'Vui lòng nhập số điện thoại.';
} elseif (!preg_match('/^[0-9]{10,11}$/', $soDienThoai)) {
    $loi['so_dien_thoai'] =
        'Số điện thoại phải gồm 10 hoặc 11 chữ số.';
} else {
    $stmt = $pdo->prepare(
        'SELECT id FROM nguoi_dung
         WHERE so_dien_thoai = :so_dien_thoai
         LIMIT 1'
    );

    $stmt->execute([
        'so_dien_thoai' => $soDienThoai
    ]);

    if ($stmt->fetch()) {
        $loi['so_dien_thoai'] =
            'Số điện thoại này đã được sử dụng.';
    }
}

    // 4. Kiểm tra mật khẩu
    if ($matKhau === '') {
        $loi['mat_khau'] = 'Vui lòng nhập mật khẩu.';
    } elseif (strlen($matKhau) < 8) {
        $loi['mat_khau'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
    } elseif (
        !preg_match('/[A-Za-z]/', $matKhau) ||
        !preg_match('/[0-9]/', $matKhau)
    ) {
        $loi['mat_khau'] =
            'Mật khẩu phải có ít nhất một chữ cái và một chữ số.';
    }

    // 5. Kiểm tra nhập lại mật khẩu
    if ($nhapLaiMatKhau === '') {
        $loi['nhap_lai_mat_khau'] = 'Vui lòng nhập lại mật khẩu.';
    } elseif ($nhapLaiMatKhau !== $matKhau) {
        $loi['nhap_lai_mat_khau'] = 'Mật khẩu nhập lại không khớp.';
    }

    // Nếu không có lỗi thì tạo tài khoản
    if (empty($loi)) {
        try {
            $matKhauDaMaHoa = password_hash($matKhau, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO nguoi_dung
                    (ho_ten, email, mat_khau, vai_tro, so_dien_thoai)
                 VALUES
                    (:ho_ten, :email, :mat_khau, :vai_tro, :so_dien_thoai)'
            );

            $stmt->execute([
                'ho_ten' => $hoTen,
                'email' => $email,
                'mat_khau' => $matKhauDaMaHoa,
                'vai_tro' => 'thanh_vien',
                'so_dien_thoai' => $soDienThoai,
            ]);

            // Chuyển sang trang đăng nhập sau khi đăng ký thành công
            header('Location: dang_nhap.php?dang_ky=thanh_cong');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $loi['chung'] =
                'Không thể tạo tài khoản lúc này. Vui lòng kiểm tra email hoặc thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #222;
        }

        .khung-dang-ky {
            width: 100%;
            max-width: 460px;
            margin: 20px auto;
            padding: 28px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 26px;
        }

        .mo-ta {
            margin: 0 0 24px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .nhom-truong {
            margin-bottom: 17px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .thong-bao-loi {
            margin-top: 6px;
            color: #c62828;
            font-size: 13px;
        }

        .loi-chung {
            margin-bottom: 16px;
            padding: 10px;
            border-radius: 6px;
            background: #ffebee;
            color: #b71c1c;
            font-size: 14px;
        }

        .ghi-chu {
            margin-top: 6px;
            color: #666;
            font-size: 12px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .lien-ket {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .lien-ket a {
            color: #2563eb;
            text-decoration: none;
        }

        .lien-ket a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="khung-dang-ky">
    <h1>Đăng ký thành viên</h1>
    <p class="mo-ta">Tạo tài khoản để tham gia các sự kiện</p>

    <?php if (isset($loi['chung'])): ?>
        <div class="loi-chung">
            <?= htmlspecialchars($loi['chung'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- 1. Họ và tên -->
        <div class="nhom-truong">
            <label for="ho_ten">1. Họ và tên</label>
            <input
                type="text"
                id="ho_ten"
                name="ho_ten"
                maxlength="100"
                autocomplete="name"
                value="<?= htmlspecialchars($hoTen, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
            <?php if (isset($loi['ho_ten'])): ?>
                <div class="thong-bao-loi">
                    <?= htmlspecialchars($loi['ho_ten'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Email -->
        <div class="nhom-truong">
            <label for="email">2. Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                autocomplete="email"
                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
            <?php if (isset($loi['email'])): ?>
                <div class="thong-bao-loi">
                    <?= htmlspecialchars($loi['email'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. Số điện thoại -->
        <div class="nhom-truong">
            <label for="so_dien_thoai">3. Số điện thoại</label>
            <input
                type="tel"
                id="so_dien_thoai"
                name="so_dien_thoai"
                inputmode="numeric"
                pattern="[0-9]{10,11}"
                maxlength="11"
                autocomplete="tel"
                value="<?= htmlspecialchars($soDienThoai, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
            <div class="ghi-chu">Nhập 10 hoặc 11 chữ số, không nhập dấu cách.</div>
            <?php if (isset($loi['so_dien_thoai'])): ?>
                <div class="thong-bao-loi">
                    <?= htmlspecialchars($loi['so_dien_thoai'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 4. Mật khẩu -->
        <div class="nhom-truong">
            <label for="mat_khau">4. Mật khẩu</label>
            <input
                type="password"
                id="mat_khau"
                name="mat_khau"
                minlength="8"
                autocomplete="new-password"
                required
            >
            <div class="ghi-chu">
                Ít nhất 8 ký tự, gồm chữ cái và chữ số.
            </div>
            <?php if (isset($loi['mat_khau'])): ?>
                <div class="thong-bao-loi">
                    <?= htmlspecialchars($loi['mat_khau'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 5. Nhập lại mật khẩu -->
        <div class="nhom-truong">
            <label for="nhap_lai_mat_khau">5. Nhập lại mật khẩu</label>
            <input
                type="password"
                id="nhap_lai_mat_khau"
                name="nhap_lai_mat_khau"
                minlength="8"
                autocomplete="new-password"
                required
            >
            <?php if (isset($loi['nhap_lai_mat_khau'])): ?>
                <div class="thong-bao-loi">
                    <?= htmlspecialchars(
                        $loi['nhap_lai_mat_khau'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit">Đăng ký</button>
    </form>

    <div class="lien-ket">
        Đã có tài khoản?
        <a href="dang_nhap.php">Đăng nhập</a>
        
    </div>
</div>

</body>
</html>
