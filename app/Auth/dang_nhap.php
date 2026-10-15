
<?php

session_start();

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

$thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';

    if ($email === '' || $mat_khau === '') {
        $thong_bao = 'Vui long nhap day du email va mat khau.';
    } else {
        $sql = 'SELECT id, ho_ten, email, mat_khau, vai_tro
                FROM nguoi_dung
                WHERE email = :email
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $nguoi_dung = $stmt->fetch();

        if (
            $nguoi_dung &&
            password_verify($mat_khau, $nguoi_dung['mat_khau'])
        ) {
            session_regenerate_id(true);

            $_SESSION['nguoi_dung'] = [
                'id' => $nguoi_dung['id'],
                'ho_ten' => $nguoi_dung['ho_ten'],
                'email' => $nguoi_dung['email'],
                'vai_tro' => $nguoi_dung['vai_tro'],
            ];

            $thong_bao = 'Dang nhap thanh cong!';
        } else {
            $thong_bao = 'Email hoac mat khau khong dung.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <h1>Đăng nhập</h1>

    <?php if ($thong_bao !== ''): ?>
        <p><?= htmlspecialchars($thong_bao, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                required
            >
        </div>

        <br>

        <div>
            <label for="mat_khau">Mật khẩu:</label>
            <input
                type="password"
                id="mat_khau"
                name="mat_khau"
                required
            >
        </div>

        <br>

        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
