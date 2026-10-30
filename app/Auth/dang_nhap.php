
<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../Core/xac_thuc.php';

$thong_bao = '';
$thong_bao_dang_ky = '';

if (($_GET['dang_ky'] ?? '') === 'thanh_cong') {
    $thong_bao_dang_ky = 'Đăng ký thành công! Vui lòng đăng nhập.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';

    if ($email === '' || $mat_khau === '') {
        $thong_bao = 'Vui lòng nhập email và mật khẩu.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, ho_ten, email, mat_khau, vai_tro
             FROM nguoi_dung WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $nguoi_dung = $stmt->fetch();

        if ($nguoi_dung && password_verify($mat_khau, $nguoi_dung['mat_khau'])) {
            session_regenerate_id(true);

            $_SESSION['nguoi_dung'] = [
                'id' => $nguoi_dung['id'],
                'ho_ten' => $nguoi_dung['ho_ten'],
                'email' => $nguoi_dung['email'],
                'vai_tro' => $nguoi_dung['vai_tro'],
            ];

            try {
                if (($_POST['ghi_nho'] ?? '') === '1') {
                    taoGhiNhoDangNhap((int) $nguoi_dung['id']);
                } else {
                    thuHoiGhiNho();
                }
            } catch (Throwable $e) {
                error_log($e->getMessage());
                unset($_SESSION['nguoi_dung']);
                $thong_bao = 'Không thể ghi nhớ đăng nhập. Vui lòng thử lại.';
            }

            if ($thong_bao === '') {
                if ($nguoi_dung['vai_tro'] === 'admin') {
                    header('Location: /Nhom-4-LTW/public/index.php?trang=admin');
                } elseif ($nguoi_dung['vai_tro'] === 'ban_to_chuc') {
                    header('Location: /Nhom-4-LTW/public/index.php?trang=ban_to_chuc');
                } else {
                    header('Location: /Nhom-4-LTW/public/index.php?trang=thanh_vien');
                }
                exit;
            }
        } else {
            $thong_bao = 'Email hoặc mật khẩu không đúng.';
        }
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Sự kiện sinh viên</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8faf6;
            color: #173f35;
            min-height: 100vh;
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            height: 76px;
            padding: 0 7%;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border-bottom: 1px solid #e6eee4;
        }

        .logo {
            width: 42px;
            height: 42px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            background: #0d5947;
            color: #d8eab8;
            font-size: 25px;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            color: #0d5947;
        }

        /* Nội dung chính */
        .main {
            flex: 1;
            width: min(1100px, 92%);
            margin: 32px auto;
            display: grid;
            grid-template-columns: 1fr 0.85fr;
            align-items: center;
            gap: 50px;
        }

        /* Hình minh họa bên trái */
        .visual-panel {
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .panel-top,
        .panel-bottom {
            width: 100%;
            text-align: center;
        }

        .small-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #527c65;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .small-label span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #a9cc86;
        }

        .artwork {
            width: 100%;
            max-width: 480px;
            margin: 16px auto;
        }

        .artwork svg {
            display: block;
            width: 100%;
            height: auto;
            max-height: 420px;
        }

        .panel-caption {
            color: #68846d;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        /* Khung đăng nhập bên phải */
        .form-panel {
            width: 100%;
            min-width: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            padding: 36px;
            background: #ffffff;
            border: 1px solid #e7eee3;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(20, 65, 48, 0.07);
        }

        .login-box h1 {
            margin-bottom: 10px;
            color: #104f40;
            font-size: 30px;
            line-height: 1.3;
        }

        .field {
            margin-top: 22px;
        }

        .field > label {
            display: block;
            margin-bottom: 9px;
            color: #294b3e;
            font-size: 14px;
            font-weight: 600;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            height: 48px;
            padding: 0 13px;
            border: 1px solid #dce6da;
            border-radius: 9px;
            background: #fbfcf9;
        }

        .input-wrap:focus-within {
            border-color: #39816a;
            box-shadow: 0 0 0 3px rgba(57, 129, 106, 0.10);
        }

        .input-wrap svg {
            flex: 0 0 20px;
            width: 20px;
            height: 20px;
            color: #72917d;
        }

        .input-wrap input {
            width: 100%;
            min-width: 0;
            height: 100%;
            border: none;
            outline: none;
            background: transparent;
            color: #173f35;
            font-family: inherit;
            font-size: 14px;
        }

        .input-wrap input::placeholder {
            color: #a0ada1;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 18px 0 22px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }

        .remember input {
            accent-color: #0d5947;
        }

        a {
            color: #0d7054;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            min-height: 48px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            border-radius: 9px;
            background: #0d5947;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: #084737;
        }

        .submit-btn svg {
            width: 19px;
            height: 19px;
        }

        .register {
            margin-top: 22px;
            text-align: center;
            color: #68796c;
            font-size: 13px;
            line-height: 1.6;
        }

        .register a {
            font-weight: 700;
        }

        /* Thông báo */
        .message {
            margin-top: 15px;
            padding: 11px 13px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.5;
        }

        .success {
            background: #edf7e8;
            color: #27613d;
        }

        .error {
            background: #fff0ed;
            color: #a33c31;
        }

        /* Màn hình nhỏ */
        @media (max-width: 760px) {
            .header {
                height: 65px;
                padding: 0 5%;
            }

            .brand {
                font-size: 18px;
            }

            .main {
                width: min(440px, 92%);
                margin: 24px auto;
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .visual-panel {
                padding: 0;
            }

            .artwork {
                max-width: 280px;
                margin: 8px auto;
            }

            .artwork svg {
                max-height: 240px;
            }

            .panel-caption {
                font-size: 10px;
            }

            .login-box {
                padding: 26px 22px;
            }

            .login-box h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 360px) {
            .options {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>


<body>
<main class="page">

    <header class="header">
        <div class="logo">✦</div>
        <div class="brand">Sự kiện sinh viên</div>
    </header>

    <div class="main">

        <!-- HÌNH MINH HỌA BÊN TRÁI -->
        <section class="visual-panel">

            <div class="panel-top">
                <div class="small-label">
                    <span></span>
                    STUDENT EVENTS</div>
            </div>

            <div class="artwork">
                <svg viewBox="0 0 440 420"
                     xmlns="http://www.w3.org/2000/svg"
                     role="img"
                     aria-label="Minh họa sân khấu sự kiện sinh viên">

                    <defs>
                        <linearGradient id="cardBg" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#f5f1df"/>
                            <stop offset="100%" stop-color="#d8e7c6"/>
                        </linearGradient>
                        <linearGradient id="stage" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#f2dca6"/>
                            <stop offset="100%" stop-color="#d7b76f"/>
                        </linearGradient>
                    </defs>

                    <!-- Vòng trang trí -->
                    <circle cx="220" cy="205" r="164"
                            fill="none" stroke="#b7d9b5"
                            stroke-opacity=".35" stroke-width="1.2"/>
                    <circle cx="220" cy="205" r="139"
                            fill="none" stroke="#b7d9b5"
                            stroke-opacity=".2" stroke-width="1.2"/>

                    <!-- Thẻ sự kiện nghiêng -->
                    <g transform="rotate(-7 220 200)">
                        <rect x="87" y="79" width="267" height="244"
                              rx="19" fill="#062f29" opacity=".2"/>
                        <rect x="78" y="68" width="267" height="244"
                              rx="19" fill="url(#cardBg)"/>

                        <rect x="98" y="88" width="227" height="36"
                              rx="8" fill="#0d5947"/>

                        <circle cx="115" cy="106" r="4" fill="#cce7a8"/>
                        <rect x="127" y="102" width="92" height="7"
                              rx="3.5" fill="#f6f4e9"/>
                        <rect x="286" y="101" width="24" height="9"
                              rx="4.5" fill="#cce7a8"/>

                        <!-- Sân khấu -->
                        <rect x="98" y="139" width="227" height="127"
                              rx="11" fill="#e8e8d4"/>

                        <circle cx="212" cy="180" r="48"
                                fill="#c7dcb5"/>

                        <path d="M140 240L174 191L201 221L228 183L283 240Z"
                              fill="#0e6c51"/>

                        <path d="M140 240L174 211L195 240Z"
                              fill="#0a4d3d"/>

                        <path d="M228 183L283 240H245L201 221Z"
                              fill="#76a66f"/>

                        <!-- Người trên sân khấu -->
                        <circle cx="211" cy="168" r="12" fill="#073f35"/>
                        <path d="M194 187Q211 174 228 187L235 222H187Z"
                              fill="#073f35"/>

                        <path d="M192 195L177 216"
                              stroke="#073f35" stroke-width="8"
                              stroke-linecap="round"/>
                        <path d="M228 195L242 211"
                              stroke="#073f35" stroke-width="8"
                              stroke-linecap="round"/>

                        <!-- Dòng thông tin -->
                        <rect x="99" y="281" width="125" height="8"
                              rx="4" fill="#0d5947"/>
                        <rect x="99" y="295" width="82" height="5"
                              rx="2.5" fill="#8cae8c"/>
                    </g>

                    <!-- Thẻ nổi phía trên -->
                    <g>
                        <rect x="267" y="44" width="113" height="67"
                              rx="13" fill="#fffdf2"/>
                        <circle cx="288" cy="65" r="7" fill="#c9e5a8"/>
                        <path d="M284 65L287 68L293 61"
                              fill="none" stroke="#0b5745"
                              stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <rect x="281" y="81" width="80" height="6"
                              rx="3" fill="#0b5745"/>
                        <rect x="281" y="92" width="49" height="4"
                              rx="2" fill="#c3d8b9"/>
                    </g>

                    <!-- Hình trang trí bên dưới -->
                    <g>
                        <rect x="43" y="273" width="103" height="76"
                              rx="13" fill="#d4e5c7"/>
                        <rect x="55" y="285" width="79" height="52"
                              rx="8" fill="#0b5745"/>
                        <path d="M69 321L83 304L94 314L108 296L121 321Z"
                              fill="#cce7a8"/>
                        <circle cx="112" cy="296" r="4" fill="#f5e5b9"/>
                    </g>

                    <!-- Các điểm sáng -->
                    <g fill="#dcebc7">
                        <path d="M359 157L363 168L374 172L363 176L359 187L355 176L344 172L355 168Z"/>
                        <path d="M85 133L88 141L96 144L88 147L85 155L82 147L74 144L82 141Z"/>
                        <circle cx="335" cy="282" r="4"/>
                        <circle cx="162" cy="54" r="3"/>
                        <circle cx="64" cy="214" r="3"/>
                    </g>

                    <!-- Vạch trang trí -->
                    <path d="M323 335H375" stroke="#cce7a8"
                          stroke-width="2" stroke-linecap="round"/>
                    <path d="M338 344H375" stroke="#cce7a8"
                          stroke-width="2" stroke-linecap="round" opacity=".55"/>
                </svg>
            </div>
            <div class="panel-bottom">
                <div class="panel-caption">KẾT NỐI · SÁNG TẠO · LAN TỎA</div>
            </div>
        </section>

        <!-- FORM ĐĂNG NHẬP -->
        <section class="form-panel">
            <div class="login-box">

                <h1>Đăng nhập</h1>

                <?php if ($thong_bao_dang_ky !== ''): ?>
                    <div class="message success" role="status">
                        <?= e($thong_bao_dang_ky) ?>
                    </div>
                <?php endif; ?>

                <?php if ($thong_bao !== ''): ?>
                    <div class="message error" role="alert">
                        <?= e($thong_bao) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="field">
                        <label for="email">Email đăng nhập</label>

                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14"
                                      rx="2" stroke="currentColor"
                                      stroke-width="1.8"/>
                                <path d="M4 7L12 13L20 7"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Nhập email của bạn"
                                value="<?= e($_POST['email'] ?? '') ?>"
                                autocomplete="username"
                                required
                            >
                        </div>
                    </div>

                    <div class="field">
                        <label for="mat_khau">Mật khẩu</label>

                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11"
                                      rx="2" stroke="currentColor"
                                      stroke-width="1.8"/>
                                <path d="M8 10V7A4 4 0 0 1 16 7V10"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="1.3"
                                        fill="currentColor"/>
                            </svg>

                            <input
                                type="password"
                                id="mat_khau"
                                name="mat_khau"
                                placeholder="Nhập mật khẩu"
                                autocomplete="current-password"
                                required
                            >
                        </div>
                    </div>

                    <div class="options">
                        <label class="remember">
                            <input
                                type="checkbox"
                                name="ghi_nho"
                                value="1"
                                <?= isset($_POST['ghi_nho']) ? 'checked' : '' ?>
                            >
                            <span>Nhớ tài khoản</span>
                        </label>

                        <a href="quen_mat_khau.php">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span>Đăng nhập</span>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19M13 6L19 12L13 18"
                                  stroke="currentColor"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <p class="register">
                        Chưa có tài khoản?
                        <a href="/Nhom-4-LTW/public/thanh_vien/dang_ky.php">Đăng ký ngay</a>
                    </p>

                </form>
            </div>
        </section>

    </div>
</main>
</body>
</html>
