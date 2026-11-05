<?php
require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../Core/xac_thuc.php';

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

if (!$nguoiDung) {
    header('Location: /Nhom-4-LTW/public/index.php?trang=dang_nhap');
    exit;
}

/* Lấy tên câu lạc bộ của người quản lý */
$tenCauLacBo = 'Câu lạc bộ';

$stmtTenClb = $pdo->prepare("
    SELECT ten_cau_lac_bo
    FROM cau_lac_bo
    WHERE nguoi_quan_ly_id = :nguoi_dung_id
    LIMIT 1
");

$stmtTenClb->execute([
    'nguoi_dung_id' => (int) $nguoiDung['id']
]);

$tenCauLacBo = $stmtTenClb->fetchColumn() ?: 'Câu lạc bộ';

/* Đếm thông báo chưa đọc */
$stmtDemThongBao = $pdo->prepare("
    SELECT COUNT(*)
    FROM thong_bao
    WHERE nguoi_dung_id = :nguoi_dung_id
      AND da_doc = 0
");

$stmtDemThongBao->execute([
    'nguoi_dung_id' => (int) $nguoiDung['id']
]);

$soThongBaoChuaDoc = (int) $stmtDemThongBao->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sự kiện</title>

    <link rel="stylesheet"
          href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css">

    <style>
        .form-su-kien {
            max-width: 850px;
            margin-bottom: 24px;
            padding: 28px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .form-su-kien label {
            display: block;
            margin-top: 18px;
            color: var(--text);
            font-weight: 600;
        }

        .form-su-kien input,
        .form-su-kien textarea,
        .form-su-kien select {
            width: 100%;
            margin-top: 7px;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--white);
            color: var(--text);
        }

        .form-su-kien textarea {
            resize: vertical;
        }

        .form-su-kien input:focus,
        .form-su-kien textarea:focus,
        .form-su-kien select:focus {
            outline: 2px solid rgba(24, 63, 53, .15);
            border-color: var(--green);
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .btn-quay-lai {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 15px;
            border-radius: 8px;
            background: var(--green-light);
            color: var(--green);
            font-weight: 600;
        }

        .btn-quay-lai:hover {
            background: #d6e4d9;
        }
        .tk-menu {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.tk-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 46px;
    padding: 11px 13px;
    border-radius: 9px;
    color: #e3ebe5;
    font-size: 14px;
    text-decoration: none;
    transition: background .2s, color .2s;
}

.tk-menu a:hover,
.tk-menu a.active {
    background: rgba(255, 255, 255, .13);
    color: #fff;
}

.tk-menu a.active {
    box-shadow: inset 3px 0 var(--gold);
    font-weight: 650;
}

.tk-menu-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
}

.tk-menu-icon svg {
    display: block;
    width: 21px;
    height: 21px;
    stroke: currentColor;
}

        @media (max-width: 600px) {
            .form-su-kien {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="layout-ban-to-chuc">

    <!-- SIDEBAR -->
    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">
            <div class="sidebar-logo-header">
                <h2>
                    <?= htmlspecialchars(
                        $tenCauLacBo,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <a href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
                   class="sidebar-logout-icon"
                   title="Đăng xuất"
                   aria-label="Đăng xuất">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22" height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M13 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7"/>
                        <path d="M10 12h11"/>
                        <path d="m17 8 4 4-4 4"/>
                    </svg>
                </a>
            </div>

            <p>HỆ THỐNG QUẢN LÝ</p>
        </div>

        <nav class="tk-menu">

    <a href="/Nhom-4-LTW/public/index.php?trang=ban_to_chuc">
        <span class="tk-menu-icon">
            <!-- Icon tổng quan -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="m3 10 9-7 9 7"/>
                <path d="M5 9v12h14V9"/>
                <path d="M9 21v-7h6v7"/>
            </svg>
        </span>
        <span>Tổng quan</span>
    </a>

    <a class="active"
       href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb">
        <span class="tk-menu-icon">
            <!-- Icon sự kiện -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
        </span>
        <span>Sự kiện của CLB</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M9 11 12 14 22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </span>
        <span>Đăng ký tham gia</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=thong_ke">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M3 3v18h18"/>
                <path d="m7 14 4-4 4 4 6-7"/>
            </svg>
        </span>
        <span>Thống kê sự kiện</span>
    </a>

    <a href="/Nhom-4-LTW/public/index.php?trang=thanh_vien_clb">
        <span class="tk-menu-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="10" cy="7" r="4"/>
                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </span>
        <span>Thành viên CLB</span>
    </a>

</nav>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-ban-to-chuc">

        <!-- THANH TRÊN -->
        <header class="topbar-ban-to-chuc">

            <div class="page-heading">
                <h1>Thêm sự kiện</h1>
                <p>Tạo sự kiện mới cho câu lạc bộ của bạn.</p>
            </div>

            <div class="topbar-actions">

                <a class="notification-link"
                   href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
                   title="Thông báo"
                   aria-label="Thông báo">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22" height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <?php if ($soThongBaoChuaDoc > 0): ?>
                        <span class="notification-badge">
                            <?= $soThongBaoChuaDoc ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="account-info">
                    <div class="account-avatar">
                        <?= htmlspecialchars(
                            mb_substr(
                                $nguoiDung['ho_ten'] ?? 'U',
                                0,
                                1,
                                'UTF-8'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                    <div>
                        <strong>
                            <?= htmlspecialchars(
                                $nguoiDung['ho_ten'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                        <small>Ban tổ chức</small>
                    </div>
                </div>

            </div>
        </header>

        <!-- FORM THÊM SỰ KIỆN -->
        <section class="form-su-kien">

            <form action="/Nhom-4-LTW/public/index.php?trang=luu_su_kien"
      method="POST"
      enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars(
                           taoCsrfToken(),
                           ENT_QUOTES,
                           'UTF-8'
                       ) ?>">

                <label for="ten_su_kien">Tên sự kiện</label>
                <input id="ten_su_kien"
                       name="ten_su_kien"
                       type="text"
                       maxlength="200"
                       required>

                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta"
                          name="mo_ta"
                          rows="4"></textarea>
                
                <label for="hinh_anh">Ảnh sự kiện</label>
<input id="hinh_anh"
       name="hinh_anh"
       type="file"
       accept="image/jpeg,image/png,image/webp">

<small style="display:block; margin-top:6px;">
    Chọn ảnh JPG, PNG hoặc WEBP. Dung lượng tối đa 3 MB.
</small>


                <label for="dia_diem">Địa điểm</label>
                <input id="dia_diem"
                       name="dia_diem"
                       type="text"
                       maxlength="255">

                <label for="thoi_gian_bat_dau">
                    Thời gian bắt đầu
                </label>
                <input id="thoi_gian_bat_dau"
                       name="thoi_gian_bat_dau"
                       type="datetime-local"
                       required>

                <label for="thoi_gian_ket_thuc">
                    Thời gian kết thúc
                </label>
                <input id="thoi_gian_ket_thuc"
                       name="thoi_gian_ket_thuc"
                       type="datetime-local">

                <label for="han_dang_ky">Hạn đăng ký</label>
                <input id="han_dang_ky"
                       name="han_dang_ky"
                       type="datetime-local">

                <label for="so_luong_toi_da">
                    Số lượng tối đa
                </label>
                <input id="so_luong_toi_da"
                       name="so_luong_toi_da"
                       type="number"
                       min="1"
                       value="100"
                       required>

                <label for="trang_thai">Trạng thái</label>
                <select id="trang_thai" name="trang_thai">
                    <option value="nhap">Bản nháp</option>
                    <option value="cong_khai">Công khai</option>
                </select>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        Lưu sự kiện
                    </button>

                    <a class="btn-quay-lai"
                       href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb">
                        Quay lại danh sách
                    </a>
                </div>

            </form>
        </section>

    </main>
</div>

</body>
</html>