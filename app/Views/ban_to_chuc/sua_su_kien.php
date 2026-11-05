<?php
require_once __DIR__ . '/../../Core/xac_thuc.php';

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}


/*
|--------------------------------------------------------------------------
| Đếm thông báo chưa đọc
|--------------------------------------------------------------------------
*/
global $pdo;
$soThongBaoChuaDoc = 0;

try {
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
} catch (PDOException $ex) {
    $soThongBaoChuaDoc = 0;
}

/*
|--------------------------------------------------------------------------
| Định dạng ngày giờ cho input datetime-local
|--------------------------------------------------------------------------
*/
$ngayBatDau = !empty($suKien['thoi_gian_bat_dau'])
    ? date('Y-m-d\TH:i', strtotime($suKien['thoi_gian_bat_dau']))
    : '';

$ngayKetThuc = !empty($suKien['thoi_gian_ket_thuc'])
    ? date('Y-m-d\TH:i', strtotime($suKien['thoi_gian_ket_thuc']))
    : '';

$hanDangKy = !empty($suKien['han_dang_ky'])
    ? date('Y-m-d\TH:i', strtotime($suKien['han_dang_ky']))
    : '';

$vaiTroHienThi = ($nguoiDung['vai_tro'] ?? '') === 'admin'
    ? 'Admin'
    : 'Ban tổ chức';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Chỉnh sửa sự kiện</title>

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css"
    >

    <style>
        .edit-event-container {
            padding: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .edit-event-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .edit-event-title {
            margin: 0 0 8px;
            font-size: 24px;
            color: #222;
        }

        .edit-event-description {
            color: #777;
            margin-bottom: 28px;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .edit-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .edit-form-group.full-width {
            grid-column: 1 / -1;
        }

        .edit-form-group label {
            font-weight: 600;
            color: #333;
        }

        .edit-form-group input,
        .edit-form-group textarea,
        .edit-form-group select {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        .edit-form-group input:focus,
        .edit-form-group textarea:focus,
        .edit-form-group select:focus {
            border-color: #183f35;
            box-shadow: 0 0 0 3px rgba(24, 63, 53, 0.1);
        }

        .edit-form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .edit-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .edit-btn {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .edit-btn-cancel {
            background: #f1f1f1;
            color: #444;
        }

        .edit-btn-save {
            background: #183f35;
            color: #fff;
        }

        .edit-btn-save:hover {
            background: #285748;
        }

        .edit-btn-cancel:hover {
            background: #e5e5e5;
        }

        @media (max-width: 768px) {
            .edit-event-container {
                padding: 15px;
            }

            .edit-event-card {
                padding: 20px;
            }

            .edit-form-grid {
                grid-template-columns: 1fr;
            }

            .edit-form-group.full-width {
                grid-column: auto;
            }
        }
        .edit-current-image {
    margin-bottom: 10px;
}

.edit-current-image p {
    margin: 0 0 10px;
    color: #555;
}

.edit-event-image {
    width: 220px;
    height: 140px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.edit-no-image {
    color: #888;
    font-size: 14px;
}

.edit-form-group input[type="file"] {
    padding: 10px;
    background: #fafafa;
}

.edit-form-group small {
    color: #777;
    font-size: 13px;
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
    </style>
</head>

<body>

<div class="layout-ban-to-chuc">

    <!-- SIDEBAR: GIỮ CẤU TRÚC TỪ TRANG CHỦ -->
    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">

            <div class="sidebar-logo-header">

                <h2>
                    <?= e($tenCauLacBo ?? 'Câu lạc bộ') ?>
                </h2>

                <a
                    href="/Nhom-4-LTW/public/index.php?trang=dang_xuat"
                    class="sidebar-logout-icon"
                    title="Đăng xuất"
                    aria-label="Đăng xuất"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
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

        <!-- TOPBAR: GIỮ CẤU TRÚC TỪ TRANG CHỦ -->
        <header class="topbar-ban-to-chuc">

            <div class="page-heading">
                <h2>Chỉnh sửa sự kiện</h2>
            </div>

            <div class="topbar-actions">

                <!-- THÔNG BÁO -->
                <a
                    class="notification-link"
                    href="/Nhom-4-LTW/public/index.php?trang=thong_bao"
                    title="Thông báo"
                    aria-label="Thông báo"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <?php if (($soThongBaoChuaDoc ?? 0) > 0): ?>
                        <span class="notification-badge">
                            <?= (int) $soThongBaoChuaDoc ?>
                        </span>
                    <?php endif; ?>

                </a>

                <!-- THÔNG TIN TÀI KHOẢN -->
                <div class="account-info">

                    <div class="account-avatar">
                        <?= e(
                            mb_substr(
                                $nguoiDung['ho_ten'] ?? 'U',
                                0,
                                1,
                                'UTF-8'
                            )
                        ) ?>
                    </div>

                    <div>

                        <strong>
                            <?= e($nguoiDung['ho_ten'] ?? '') ?>
                        </strong>

                        <small>
                            <?= e($vaiTroHienThi) ?>
                        </small>

                    </div>

                </div>

            </div>

        </header>

        <!-- FORM CHỈNH SỬA SỰ KIỆN -->
        <div class="edit-event-container">

            <div class="edit-event-card">

                <h2 class="edit-event-title">
                    Chỉnh sửa thông tin sự kiện
                </h2>

                <p class="edit-event-description">
                    Vui lòng cập nhật các thông tin cần thiết bên dưới.
                </p>

                <form
    action="/Nhom-4-LTW/public/index.php?trang=luu_sua_su_kien"
    method="POST"
    enctype="multipart/form-data"
>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(taoCsrfToken()) ?>"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= e($suKien['id'] ?? '') ?>"
                    >

                    <div class="edit-form-grid">

                        <!-- TÊN SỰ KIỆN -->
                        <div class="edit-form-group full-width">

                            <label for="ten_su_kien">
                                Tên sự kiện
                            </label>

                            <input
                                type="text"
                                id="ten_su_kien"
                                name="ten_su_kien"
                                value="<?= e($suKien['ten_su_kien'] ?? '') ?>"
                                required
                            >

                        </div>
                        <!-- ẢNH SỰ KIỆN -->
<div class="edit-form-group full-width">

    <label for="hinh_anh">
        Ảnh sự kiện
    </label>

    <?php if (!empty($suKien['hinh_anh'])): ?>

        <div class="edit-current-image">

            <p>Ảnh hiện tại:</p>

            <img
    src="<?= e($suKien['hinh_anh']) ?>"
    alt="Ảnh sự kiện hiện tại"
    class="edit-event-image"
>

        </div>

    <?php else: ?>

        <p class="edit-no-image">
            Sự kiện chưa có ảnh.
        </p>

    <?php endif; ?>

    <input
        type="file"
        id="hinh_anh"
        name="hinh_anh"
        accept="image/jpeg,image/png,image/webp"
    >

    <small>
        Chọn ảnh mới nếu muốn thay đổi ảnh hiện tại.
        Nếu không chọn, ảnh cũ sẽ được giữ nguyên.
    </small>

</div>

                        <!-- MÔ TẢ -->
                        <div class="edit-form-group full-width">

                            <label for="mo_ta">
                                Mô tả sự kiện
                            </label>

                            <textarea
                                id="mo_ta"
                                name="mo_ta"
                            ><?= e($suKien['mo_ta'] ?? '') ?></textarea>

                        </div>

                        <!-- ĐỊA ĐIỂM -->
                        <div class="edit-form-group full-width">

                            <label for="dia_diem">
                                Địa điểm
                            </label>

                            <input
                                type="text"
                                id="dia_diem"
                                name="dia_diem"
                                value="<?= e($suKien['dia_diem'] ?? '') ?>"
                                required
                            >

                        </div>

                        <!-- THỜI GIAN BẮT ĐẦU -->
                        <div class="edit-form-group">

                            <label for="thoi_gian_bat_dau">
                                Thời gian bắt đầu
                            </label>

                            <input
                                type="datetime-local"
                                id="thoi_gian_bat_dau"
                                name="thoi_gian_bat_dau"
                                value="<?= e($ngayBatDau) ?>"
                                required
                            >

                        </div>

                        <!-- THỜI GIAN KẾT THÚC -->
                        <div class="edit-form-group">

                            <label for="thoi_gian_ket_thuc">
                                Thời gian kết thúc
                            </label>

                            <input
                                type="datetime-local"
                                id="thoi_gian_ket_thuc"
                                name="thoi_gian_ket_thuc"
                                value="<?= e($ngayKetThuc) ?>"
                                required
                            >

                        </div>

                        <!-- HẠN ĐĂNG KÝ -->
                        <div class="edit-form-group">

                            <label for="han_dang_ky">
                                Hạn đăng ký
                            </label>

                            <input
                                type="datetime-local"
                                id="han_dang_ky"
                                name="han_dang_ky"
                                value="<?= e($hanDangKy) ?>"
                                required
                            >

                        </div>

                        <!-- SỐ LƯỢNG TỐI ĐA -->
                        <div class="edit-form-group">

                            <label for="so_luong_toi_da">
                                Số lượng người tham gia tối đa
                            </label>

                            <input
                                type="number"
                                id="so_luong_toi_da"
                                name="so_luong_toi_da"
                                min="1"
                                value="<?= e($suKien['so_luong_toi_da'] ?? '') ?>"
                                required
                            >

                        </div>

                        <!-- TRẠNG THÁI -->
                        <div class="edit-form-group full-width">

                            <label for="trang_thai">
                                Trạng thái sự kiện
                            </label>

                            <select
                                id="trang_thai"
                                name="trang_thai"
                                required
                            >

                                <option
                                    value="cong_khai"
                                    <?= ($suKien['trang_thai'] ?? '') === 'cong_khai'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Công khai
                                </option>

                                <option
                                    value="nhap"
                                    <?= ($suKien['trang_thai'] ?? '') === 'nhap'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Bản nháp
                                </option>

                                <option
                                    value="da_dong"
                                    <?= ($suKien['trang_thai'] ?? '') === 'da_dong'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Đã đóng
                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- NÚT HỦY VÀ LƯU -->
                    <div class="edit-form-actions">

                        <a
                            href="/Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb"
                            class="edit-btn edit-btn-cancel"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="edit-btn edit-btn-save"
                        >
                            Lưu thay đổi
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

</body>
</html>