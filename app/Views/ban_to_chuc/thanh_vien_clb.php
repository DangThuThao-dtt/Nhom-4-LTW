
<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Core/xac_thuc.php';

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

$laAdmin = (($nguoiDung['vai_tro'] ?? '') === 'admin');

$tenCauLacBo = 'Câu lạc bộ';
$thanhVienCLB = [];
$loiTruyVan = '';
$tongThanhVien = 0;
// Dem so thong bao chua doc
$soThongBaoChuaDoc = 0;

if (!empty($nguoiDung['id'])) {
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
}

// ==========================================
// LẤY DANH SÁCH THÀNH VIÊN CLB
// ==========================================

try {

    if ($laAdmin) {

        // Admin xem thành viên của tất cả CLB.
        $sql = "
            SELECT
                clb.ten_cau_lac_bo,
                nd.id,
                nd.ho_ten,
                nd.email,
                nd.so_dien_thoai,
                nd.khoa,
                nd.nganh,
                tvclb.ngay_tham_gia
            FROM thanh_vien_cau_lac_bo tvclb
            JOIN nguoi_dung nd
                ON nd.id = tvclb.nguoi_dung_id
            JOIN cau_lac_bo clb
                ON clb.id = tvclb.cau_lac_bo_id
            WHERE nd.vai_tro = 'thanh_vien'
            ORDER BY
                clb.ten_cau_lac_bo ASC,
                tvclb.ngay_tham_gia DESC
        ";

        $stmt = $pdo->query($sql);

    } else {

        // Ban tổ chức chỉ lấy CLB do mình quản lý.
        $nguoiDungId = (int) $nguoiDung['id'];

        $stmtCLB = $pdo->prepare("
            SELECT id, ten_cau_lac_bo
            FROM cau_lac_bo
            WHERE nguoi_quan_ly_id = :nguoi_dung_id
            LIMIT 1
        ");

        $stmtCLB->execute([
            'nguoi_dung_id' => $nguoiDungId
        ]);

        $clb = $stmtCLB->fetch(PDO::FETCH_ASSOC);

        if ($clb) {

            $cauLacBoId = (int) $clb['id'];

            $tenCauLacBo = $clb['ten_cau_lac_bo'];

            $stmt = $pdo->prepare("
                SELECT
                    nd.id,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai,
                    nd.khoa,
                    nd.nganh,
                    tvclb.ngay_tham_gia
                FROM thanh_vien_cau_lac_bo tvclb
                JOIN nguoi_dung nd
                    ON nd.id = tvclb.nguoi_dung_id
                WHERE tvclb.cau_lac_bo_id = :cau_lac_bo_id
                  AND nd.vai_tro = 'thanh_vien'
                ORDER BY tvclb.ngay_tham_gia DESC
            ");

            $stmt->execute([
                'cau_lac_bo_id' => $cauLacBoId
            ]);

        } else {

            // Tài khoản chưa được gán CLB.
            $stmt = null;
        }
    }

    if ($stmt !== null) {
        $thanhVienCLB = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $tongThanhVien = count($thanhVienCLB);

} catch (PDOException $e) {

    error_log('Loi lay danh sach thanh vien CLB: ' . $e->getMessage());

    $loiTruyVan = 'Không thể tải danh sách thành viên. Vui lòng thử lại sau.';
}

// ==========================================
// HÀM HIỂN THỊ DỮ LIỆU AN TOÀN
// ==========================================

function eThanhVienCLB($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thành viên CLB | Ban tổ chức</title>

    <link
        rel="stylesheet"
        href="/Nhom-4-LTW/public/assets/css/ban_to_chuc.css"
    >

    <style>
        /* ==========================================
           TRANG THÀNH VIÊN CLB
        ========================================== */

        .member-list-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin: 24px 0;
        }

        .member-list-heading h2 {
            margin: 0 0 6px;
            color: #1e293b;
            font-size: 24px;
        }

        .member-list-heading p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .member-count {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .member-count strong {
            color: #526b5b;
            font-size: 22px;
        }

        .member-count span {
            color: #64748b;
            font-size: 14px;
        }

        .member-list-panel {
            padding: 24px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .member-list-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 20px;
        }

        .member-list-toolbar h3 {
            margin: 0;
            color: #1e293b;
            font-size: 17px;
        }

        .member-search {
            width: min(100%, 320px);
            height: 42px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            background: #fff;
        }

        .member-search:focus {
            border-color: #789080;
            box-shadow: 0 0 0 3px rgba(82, 107, 91, 0.12);
        }

        .member-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .member-table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        .member-table thead {
            background: #f8fafc;
        }

        .member-table th {
            padding: 14px 12px;
            color: #475569;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            border-bottom: 1px solid #e2e8f0;
        }

        .member-table td {
            padding: 15px 12px;
            color: #334155;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .member-table tbody tr:hover {
            background: #fafcfb;
        }

        .member-table tbody tr:last-child td {
            border-bottom: none;
        }

        .member-name {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
        }

        .member-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e7eee8;
            color: #526b5b;
            font-weight: 700;
            font-size: 14px;
        }

        .member-name strong {
            color: #1e293b;
            font-weight: 600;
        }

        .member-email {
            color: #64748b;
            overflow-wrap: anywhere;
        }

        .member-club-label {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 6px;
            background: #eef3ef;
            color: #526b5b;
            font-size: 12px;
            font-weight: 600;
        }

        .member-empty {
            padding: 48px 20px;
            text-align: center;
            color: #64748b;
        }

        .member-empty strong {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 16px;
        }

        .member-empty p {
            margin: 0;
            font-size: 14px;
        }

        .member-alert {
            margin: 20px 0;
            padding: 14px 18px;
            border-radius: 9px;
            font-size: 14px;
        }

        .member-alert.error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .member-alert.warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .member-no-result {
            display: none;
            padding: 24px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        /* Menu sidebar dùng chung với trang chủ */

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

        @media (max-width: 768px) {

            .member-list-heading {
                align-items: flex-start;
            }

            .member-list-heading h2 {
                font-size: 21px;
            }

            .member-list-panel {
                padding: 16px;
            }

            .member-list-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .member-search {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<div class="layout-ban-to-chuc">

    <!-- ==========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar-ban-to-chuc">

        <div class="sidebar-logo">

            <div class="sidebar-logo-header">

                <h2>
                    <?= eThanhVienCLB(
                        $tenCauLacBo
                    ) ?>
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

    <a 
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

    <a class="active" 
    href="/Nhom-4-LTW/public/index.php?trang=thanh_vien_clb">
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

    <!-- ==========================================
         NỘI DUNG CHÍNH
    ========================================== -->

    <main class="main-ban-to-chuc">

        <!-- Topbar -->

        <header class="topbar-ban-to-chuc">

            <div class="page-heading"></div>

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

    <?php if (($soThongBaoChuaDoc ?? 0) > 0): ?>
        <span class="notification-badge">
            <?= (int) $soThongBaoChuaDoc ?>
        </span>
    <?php endif; ?>
</a>

                <div class="account-info">

                    <div class="account-avatar">
                        <?= eThanhVienCLB(
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
                            <?= eThanhVienCLB(
                                $nguoiDung['ho_ten'] ?? ''
                            ) ?>
                        </strong>

                        <small>
                            <?= $laAdmin ? 'Admin' : 'Ban tổ chức' ?>
                        </small>

                    </div>

                </div>

            </div>

        </header>

        <!-- Tiêu đề trang -->

        <section class="member-list-heading">

            <div>

                <h2>Thành viên CLB</h2>

                <p>
                    Danh sách thành viên
                    <?= $laAdmin
                        ? 'của hệ thống'
                        : 'của ' . eThanhVienCLB($tenCauLacBo)
                    ?>.
                </p>

            </div>

            <div class="member-count">

                <strong>
                    <?= (int) $tongThanhVien ?>
                </strong>

                <span>Thành viên</span>

            </div>

        </section>

        <!-- Thông báo lỗi -->

        <?php if ($loiTruyVan !== ''): ?>

            <div class="member-alert error">
                <?= eThanhVienCLB($loiTruyVan) ?>
            </div>

        <?php endif; ?>

        <!-- Ban tổ chức chưa được gán CLB -->

        <?php if (!$laAdmin && $stmt === null): ?>

            <div class="member-alert warning">
                Tài khoản Ban tổ chức hiện chưa được gán câu lạc bộ.
                Vui lòng liên hệ Admin để được phân công CLB.
            </div>

        <?php else: ?>

            <!-- Bảng danh sách thành viên -->

            <section class="member-list-panel">

                <div class="member-list-toolbar">

                    <h3>Danh sách thành viên</h3>

                    <input
                        type="search"
                        id="memberSearch"
                        class="member-search"
                        placeholder="Tìm tên, email, khoa hoặc ngành..."
                        aria-label="Tìm kiếm thành viên"
                    >

                </div>

                <?php if (empty($thanhVienCLB)): ?>

                    <div class="member-empty">

                        <strong>Chưa có thành viên</strong>

                        <p>
                            Hiện tại chưa có thành viên nào tham gia
                            câu lạc bộ này.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="member-table-wrapper">

                        <table class="member-table">

                            <thead>

                                <tr>

                                    <th>STT</th>

                                    <?php if ($laAdmin): ?>
                                        <th>Câu lạc bộ</th>
                                    <?php endif; ?>

                                    <th>Thành viên</th>

                                    <th>Email</th>

                                    <th>Số điện thoại</th>

                                    <th>Khoa</th>

                                    <th>Ngành</th>

                                    <th>Ngày tham gia</th>

                                </tr>

                            </thead>

                            <tbody id="memberTableBody">

                                <?php foreach (
                                    $thanhVienCLB as $index => $thanhVien
                                ): ?>

                                    <?php
                                    $ten = $thanhVien['ho_ten'] ?? '';
                                    $chuCaiDau = mb_substr(
                                        $ten,
                                        0,
                                        1,
                                        'UTF-8'
                                    );
                                    ?>

                                    <tr class="member-row">

                                        <td>
                                            <?= $index + 1 ?>
                                        </td>

                                        <?php if ($laAdmin): ?>

                                            <td>
                                                <span class="member-club-label">
                                                    <?= eThanhVienCLB(
                                                        $thanhVien['ten_cau_lac_bo']
                                                    ) ?>
                                                </span>
                                            </td>

                                        <?php endif; ?>

                                        <td>

                                            <div class="member-name">

                                                <div class="member-avatar">
                                                    <?= eThanhVienCLB(
                                                        $chuCaiDau
                                                    ) ?>
                                                </div>

                                                <strong>
                                                    <?= eThanhVienCLB($ten) ?>
                                                </strong>

                                            </div>

                                        </td>

                                        <td class="member-email">
                                            <?= eThanhVienCLB(
                                                $thanhVien['email']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= eThanhVienCLB(
                                                $thanhVien['so_dien_thoai']
                                                ?: 'Chưa cập nhật'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= eThanhVienCLB(
                                                $thanhVien['khoa']
                                                ?: 'Chưa cập nhật'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= eThanhVienCLB(
                                                $thanhVien['nganh']
                                                ?: 'Chưa cập nhật'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= eThanhVienCLB(
                                                $thanhVien['ngay_tham_gia']
                                                ?: 'Chưa cập nhật'
                                            ) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <div
                        class="member-no-result"
                        id="memberNoResult"
                    >
                        Không tìm thấy thành viên phù hợp.
                    </div>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    </main>

</div>

<!-- ==========================================
     TÌM KIẾM THÀNH VIÊN
========================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('memberSearch');
    const memberRows = document.querySelectorAll('.member-row');
    const noResult = document.getElementById('memberNoResult');

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener('input', function () {

        const keyword = searchInput.value
            .trim()
            .toLowerCase();

        let soDongHienThi = 0;

        memberRows.forEach(function (row) {

            const noiDung = row.textContent.toLowerCase();

            const phuHop = noiDung.includes(keyword);

            row.style.display = phuHop ? '' : 'none';

            if (phuHop) {
                soDongHienThi++;
            }

        });

        if (noResult) {
            noResult.style.display =
                soDongHienThi === 0 ? 'block' : 'none';
        }

    });

});
</script>

</body>
</html>
