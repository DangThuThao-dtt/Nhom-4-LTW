
<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/../app/Core/xac_thuc.php';

$thong_bao = '';
$loai_thong_bao = 'error';

$email = '';
$ho_ten = '';
$so_dien_thoai = '';
$khoa = '';
$nganh = '';
$noi_dung = '';

$vai_tro_chon = $_GET['vai_tro'] ?? $_POST['vai_tro'] ?? '';

if (!in_array($vai_tro_chon, ['thanh_vien', 'ban_to_chuc'], true)) {
    $vai_tro_chon = '';
}

function eQuenMatKhau(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Kiểm tra phiên xác minh Thành viên
$xac_minh = $_SESSION['dat_lai_mat_khau_thanh_vien'] ?? null;

$da_xac_minh = $vai_tro_chon === 'thanh_vien'
    && $xac_minh
    && $xac_minh['het_han'] > time();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $hanh_dong = $_POST['hanh_dong'] ?? '';

    // ==========================================
    // THÀNH VIÊN: XÁC MINH THÔNG TIN
    // ==========================================
    if (
        $vai_tro_chon === 'thanh_vien'
        && $hanh_dong === 'xac_minh_thanh_vien'
    ) {
        $email = trim($_POST['email'] ?? '');
$ho_ten = trim($_POST['ho_ten'] ?? '');
$so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
$khoa = trim($_POST['khoa'] ?? '');
$nganh = trim($_POST['nganh'] ?? '');

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $ho_ten === ''
            || $so_dien_thoai === ''
        ) {
            $thong_bao = 'Vui lòng nhập đầy đủ và chính xác thông tin.';
        } else {
            $stmt = $pdo->prepare(
    "SELECT id
     FROM nguoi_dung
     WHERE email = :email
       AND ho_ten = :ho_ten
       AND so_dien_thoai = :so_dien_thoai
       AND khoa = :khoa
       AND nganh = :nganh
       AND vai_tro = 'thanh_vien'
     LIMIT 1"
);

            $stmt->execute([
    'email' => $email,
    'ho_ten' => $ho_ten,
    'so_dien_thoai' => $so_dien_thoai,
    'khoa' => $khoa,
    'nganh' => $nganh
]);

            $nguoi_dung = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nguoi_dung) {
                $_SESSION['dat_lai_mat_khau_thanh_vien'] = [
                    'nguoi_dung_id' => (int) $nguoi_dung['id'],
                    'het_han' => time() + 300
                ];

                $da_xac_minh = true;
                $thong_bao = 'Xác minh thành công. Hãy nhập mật khẩu mới.';
                $loai_thong_bao = 'success';
            } else {
                $thong_bao = 'Thông tin không khớp với tài khoản Thành viên.';
            }
        }
    }

    // ==========================================
    // THÀNH VIÊN: ĐỔI MẬT KHẨU
    // ==========================================
    elseif (
        $vai_tro_chon === 'thanh_vien'
        && $hanh_dong === 'doi_mat_khau_thanh_vien'
    ) {
        $xac_minh = $_SESSION['dat_lai_mat_khau_thanh_vien'] ?? null;

        if (!$xac_minh || $xac_minh['het_han'] <= time()) {
            unset($_SESSION['dat_lai_mat_khau_thanh_vien']);
            $da_xac_minh = false;
            $thong_bao = 'Phiên xác minh đã hết hạn. Vui lòng xác minh lại.';
        } else {
            $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
            $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';

            if (strlen($mat_khau_moi) < 8) {
                $thong_bao = 'Mật khẩu phải có ít nhất 8 ký tự.';
                $da_xac_minh = true;
            } elseif ($mat_khau_moi !== $xac_nhan_mat_khau) {
                $thong_bao = 'Mật khẩu xác nhận không khớp.';
                $da_xac_minh = true;
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE nguoi_dung
                     SET mat_khau = :mat_khau
                     WHERE id = :id
                       AND vai_tro = 'thanh_vien'"
                );

                $stmt->execute([
                    'mat_khau' => password_hash($mat_khau_moi, PASSWORD_DEFAULT),
                    'id' => $xac_minh['nguoi_dung_id']
                ]);

                unset($_SESSION['dat_lai_mat_khau_thanh_vien']);

                $da_xac_minh = false;
                $thong_bao = 'Đổi mật khẩu thành công. Bạn có thể đăng nhập.';
                $loai_thong_bao = 'success';
            }
        }
    }

    // ==========================================
    // BAN TỔ CHỨC: GỬI YÊU CẦU ADMIN
    // ==========================================
    elseif (
        $vai_tro_chon === 'ban_to_chuc'
        && $hanh_dong === 'gui_yeu_cau'
    ) {
        $email = trim($_POST['email_btc'] ?? '');
        $noi_dung = trim($_POST['noi_dung'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $thong_bao = 'Vui lòng nhập email hợp lệ.';
        } else {
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM nguoi_dung
                 WHERE email = :email
                   AND vai_tro = 'ban_to_chuc'
                 LIMIT 1"
            );

            $stmt->execute(['email' => $email]);
            $nguoi_dung = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nguoi_dung) {
                $thong_bao = 'Không thể tiếp nhận yêu cầu với thông tin này.';
            } else {
                $stmt = $pdo->prepare(
                    "SELECT id
                     FROM yeu_cau_dat_lai_mat_khau
                     WHERE nguoi_dung_id = :id
                       AND trang_thai = 'cho_xu_ly'
                     LIMIT 1"
                );

                $stmt->execute(['id' => $nguoi_dung['id']]);

                if ($stmt->fetch()) {
                    $thong_bao = 'Bạn đã có yêu cầu đang chờ Admin xử lý.';
                } else {
                    $stmt = $pdo->prepare(
                        "INSERT INTO yeu_cau_dat_lai_mat_khau
                            (nguoi_dung_id, noi_dung)
                         VALUES (:nguoi_dung_id, :noi_dung)"
                    );

                    $stmt->execute([
                        'nguoi_dung_id' => $nguoi_dung['id'],
                        'noi_dung' => $noi_dung
                    ]);

                    $thong_bao = 'Gửi yêu cầu thành công. Vui lòng chờ Admin xử lý.';
                    $loai_thong_bao = 'success';
                }
            }
        }
    }
}

$xac_minh = $_SESSION['dat_lai_mat_khau_thanh_vien'] ?? null;

$da_xac_minh = $vai_tro_chon === 'thanh_vien'
    && $xac_minh
    && $xac_minh['het_han'] > time();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | Hệ thống quản lý sự kiện</title>

    <link rel="stylesheet" href="assets/css/quen_mat_khau.css">
</head>

<body>

<div class="forgot-page">

    <div class="forgot-card">

        <a class="brand" href="/Nhom-4-LTW/public/dang_nhap.php">
            <span class="brand-icon">✦</span>
            <span>
    <strong>KHÔI PHỤC MẬT KHẨU</strong>
</span>
        </a>

        <div class="forgot-content">

            <?php if ($vai_tro_chon === ''): ?>

                <div class="heading">
                    <div class="heading-icon">🔐</div>
                    <span class="eyebrow">KHÔI PHỤC TÀI KHOẢN</span>
                    <h1>Bạn là ai?</h1>
                    <p>
                        Vui lòng chọn đúng vai trò tài khoản để
                        tiếp tục quá trình khôi phục mật khẩu.
                    </p>
                </div>

                <div class="role-list">

                    <a class="role-card"
                       href="?vai_tro=thanh_vien">

                        <div class="role-icon member-icon">👤</div>

                        <div class="role-info">
                            <h2>Thành viên</h2>
                            <p>
                                Xác minh bằng email, họ tên và
                                số điện thoại đã đăng ký.
                            </p>
                            <span class="role-action">
                                Tiếp tục <span>→</span>
                            </span>
                        </div>

                    </a>

                    <a class="role-card"
                       href="?vai_tro=ban_to_chuc">

                        <div class="role-icon organizer-icon">🏢</div>

                        <div class="role-info">
                            <h2>Ban tổ chức</h2>
                            <p>
                                Gửi yêu cầu hỗ trợ cấp lại mật khẩu
                                đến Quản trị viên.
                            </p>
                            <span class="role-action">
                                Tiếp tục <span>→</span>
                            </span>
                        </div>

                    </a>

                </div>

            <?php else: ?>

                
                

                <div class="heading heading-left">
                    <div class="heading-icon">
                        <?= $vai_tro_chon === 'thanh_vien' ? '👤' : '🏢' ?>
                    </div>

                    <span class="eyebrow">KHÔI PHỤC TÀI KHOẢN</span>

                    <h1>
                        <?= $vai_tro_chon === 'thanh_vien'
                            ? 'Dành cho Thành viên'
                            : 'Dành cho Ban tổ chức' ?>
                    </h1>

                    <p>
                        <?= $vai_tro_chon === 'thanh_vien'
                            ? 'Xác minh thông tin tài khoản để đặt lại mật khẩu.'
                            : 'Gửi yêu cầu để Admin hỗ trợ cấp lại mật khẩu.' ?>
                    </p>
                </div>

                <?php if ($thong_bao !== ''): ?>
                    <div class="alert <?= $loai_thong_bao ?>">
                        <?= eQuenMatKhau($thong_bao) ?>
                    </div>
                <?php endif; ?>

                <?php if ($vai_tro_chon === 'thanh_vien'): ?>

                    <?php if (!$da_xac_minh): ?>

                        <form method="POST" class="forgot-form">
                            <input type="hidden"
                                   name="vai_tro"
                                   value="thanh_vien">

                            <input type="hidden"
                                   name="hanh_dong"
                                   value="xac_minh_thanh_vien">

                            <div class="field">
                                <label for="email">Email đã đăng ký</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       placeholder="example@gmail.com"
                                       value="<?= eQuenMatKhau($email) ?>"
                                       required>
                            </div>

                            <div class="field">
                                <label for="ho_ten">Họ và tên</label>
                                <input type="text"
                                       id="ho_ten"
                                       name="ho_ten"
                                       placeholder="Nhập họ và tên của bạn"
                                       value="<?= eQuenMatKhau($ho_ten) ?>"
                                       required>
                            </div>

                            <div class="field">
                                <label for="so_dien_thoai">Số điện thoại</label>
                                <input type="tel"
                                       id="so_dien_thoai"
                                       name="so_dien_thoai"
                                       placeholder="Nhập số điện thoại đã đăng ký"
                                       value="<?= eQuenMatKhau($so_dien_thoai) ?>"
                                       required>
                            </div>
                            <div class="field">
    <label for="khoa">Khoa</label>
    <input type="text"
           id="khoa"
           name="khoa"
           placeholder="Nhập khoa của bạn"
           value="<?= eQuenMatKhau($khoa) ?>"
           required>
</div>

<div class="field">
    <label for="nganh">Ngành</label>
    <input type="text"
           id="nganh"
           name="nganh"
           placeholder="Nhập ngành học của bạn"
           value="<?= eQuenMatKhau($nganh) ?>"
           required>
</div>

                            <button type="submit" class="primary-btn">
                                Xác minh thông tin
                                <span>→</span>
                            </button>
                        </form>

                    <?php else: ?>

                        <form method="POST" class="forgot-form">
                            <input type="hidden"
                                   name="vai_tro"
                                   value="thanh_vien">

                            <input type="hidden"
                                   name="hanh_dong"
                                   value="doi_mat_khau_thanh_vien">

                            <div class="verified-note">
                                <span>✓</span>
                                Thông tin tài khoản đã được xác minh.
                            </div>

                            <div class="field">
                                <label for="mat_khau_moi">Mật khẩu mới</label>
                                <input type="password"
                                       id="mat_khau_moi"
                                       name="mat_khau_moi"
                                       minlength="8"
                                       placeholder="Ít nhất 8 ký tự"
                                       required>
                            </div>

                            <div class="field">
                                <label for="xac_nhan_mat_khau">
                                    Xác nhận mật khẩu mới
                                </label>
                                <input type="password"
                                       id="xac_nhan_mat_khau"
                                       name="xac_nhan_mat_khau"
                                       minlength="8"
                                       placeholder="Nhập lại mật khẩu mới"
                                       required>
                            </div>

                            <button type="submit" class="primary-btn">
                                Cập nhật mật khẩu
                                <span>→</span>
                            </button>
                        </form>

                    <?php endif; ?>

                <?php else: ?>

                    <form method="POST" class="forgot-form">
                        <input type="hidden"
                               name="vai_tro"
                               value="ban_to_chuc">

                        <input type="hidden"
                               name="hanh_dong"
                               value="gui_yeu_cau">

                        <div class="info-note">
                            <strong>Quy trình hỗ trợ</strong>
                            <p>
                                Sau khi nhận yêu cầu, Admin sẽ xác minh
                                và hỗ trợ cấp lại mật khẩu cho tài khoản.
                            </p>
                        </div>

                        <div class="field">
                            <label for="email_btc">Email tài khoản</label>
                            <input type="email"
                                   id="email_btc"
                                   name="email_btc"
                                   placeholder="Nhập email Ban tổ chức"
                                   value="<?= eQuenMatKhau($email) ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label for="noi_dung">Nội dung yêu cầu</label>
                            <textarea id="noi_dung"
                                      name="noi_dung"
                                      rows="4"
                                      maxlength="500"
                                      placeholder="Mô tả ngắn gọn vấn đề bạn đang gặp phải..."><?= eQuenMatKhau($noi_dung) ?></textarea>
                        </div>

                        <button type="submit" class="primary-btn">
                            Gửi yêu cầu đến Admin
                            <span>→</span>
                        </button>
                        
                    </form>

                                <?php endif; ?>

                <a href="quen_mat_khau.php"
                   class="change-role-btn"
                   title="Quay lại chọn vai trò"
                   aria-label="Quay lại chọn vai trò">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M9 14 4 9l5-5"/>
                        <path d="M4 9h10a6 6 0 0 1 0 12h-2"/>
                    </svg>
                </a>

            <?php endif; ?>

        </div>

        <div class="forgot-footer">
            <a href="/Nhom-4-LTW/public/dang_nhap.php">
                ← Quay lại đăng nhập
            </a>
            <span>© Hệ thống quản lý sự kiện sinh viên</span>
        </div>

    </div>

</div>

</body>
</html>
