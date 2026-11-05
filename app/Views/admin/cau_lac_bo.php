<?php
$duongDanGoc = '/Nhom-4-LTW/public';
$trangHienTai = $_GET['action'] ?? 'quan-ly-cau-lac-bo';
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
$cheDo = $cheDo ?? 'danh_sach';
$danhSach = $danhSach ?? [];
$loi = $loi ?? '';
$ten = $ten ?? '';
$moTa = $moTa ?? '';
$nguoiQuanLyId = $nguoiQuanLyId ?? '';
$id = $id ?? 0;
$thongBao = $_SESSION['thong_bao'] ?? '';
$loaiThongBao = $_SESSION['loai_thong_bao'] ?? 'success';
unset($_SESSION['thong_bao'], $_SESSION['loai_thong_bao']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu lạc bộ</title>

    <style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    color: #24332d;
    background: #f5f7f5;
    font: 14px/1.5 Arial, Helvetica, sans-serif;
}

/* ===== BỐ CỤC CHUNG ===== */

.layout {
    min-height: 100vh;
}

/* ===== THANH ĐIỀU HƯỚNG ADMIN ===== */

.thanh-ben {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 230px;
    padding: 14px 12px;
    background: #075747;
    color: #fff;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    overflow-y: auto;
    z-index: 10;
}

/* ===== THƯƠNG HIỆU ===== */

.thuong-hieu {
    margin: 0 8px 20px;
    padding: 5px 0;
    color: #fff;
}

.thuong-hieu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
}

.thuong-hieu-title {
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
    white-space: nowrap;
    flex: 1;
}

/* ===== NÚT ĐĂNG XUẤT ===== */

.sidebar-logout-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 6px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    transition: background 0.2s;
}

.sidebar-logout-icon:hover {
    background: #13866f;
}

/* ===== NHÓM MENU ===== */

.nhom-menu {
    margin: 14px 8px 6px;
    color: #c6d9d0;
    font-size: 10px;
    letter-spacing: 1px;
    line-height: 16px;
    text-transform: uppercase;
}

/* ===== DANH SÁCH MENU ===== */

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    margin: 0;
    padding: 0;
}

/* ===== TỪNG MỤC MENU ===== */

.menu-ben a {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    min-width: 0;
    min-height: 40px;
    margin: 0;
    padding: 9px 10px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #f5faf6;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    font-weight: 400;
    line-height: 20px;
    text-decoration: none;
    transition: background 0.2s;
}

.menu-ben a:hover,
.menu-ben a.active {
    background: #13866f;
}

.menu-ben a.active {
    border-left: 3px solid #f1c76b;
    padding-left: 9px;
    color: #fff;
    font-weight: 700;
}

/* ===== ICON MENU ===== */

.menu-icon {
    display: inline-flex;
    flex: 0 0 20px;
    width: 20px;
    height: 20px;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 20px;
}

/* ===== KHU VỰC NỘI DUNG CHÍNH ===== */

.main {
    width: calc(100% - 230px);
    margin-left: 230px;
    padding: 30px;
    min-height: 100vh;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 22px;
}

h1 {
    margin: 0;
    font-size: 27px;
    line-height: 1.3;
    color: #0B4D3E;
}

.description {
    margin: 7px 0 0;
    color: #64756D;
    font-size: 14px;
}

/* ===== NÚT BẤM ===== */

.btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    min-height: 40px;
    padding: 9px 13px;
    border: 1px solid transparent;
    border-radius: 7px;
    text-decoration: none;
    text-align: center;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.btn-primary {
    background: #075747;
    color: #fff;
    border-color: #075747;
}

.btn-primary:hover {
    background: #13866F;
}

.btn-edit {
    background: #E7F3ED;
    color: #075747;
    border-color: #C8E1D4;
}

.btn-edit:hover {
    background: #D5EBDD;
}

.btn-delete {
    background: #FFF0ED;
    color: #A93226;
    border-color: #F0D5CF;
}

.btn-delete:hover {
    background: #FBE0DB;
}

/* ===== KHUNG BẢNG ===== */

.table-container {
    width: 100%;
    min-width: 0;
    background: #fff;
    border: 1px solid #DCE4DF;
    border-radius: 11px;
    overflow-x: auto;
    box-shadow: 0 3px 12px rgba(23, 79, 66, 0.05);
}

.table-container table {
    width: 100%;
    min-width: 1000px;
    table-layout: fixed;
    border-collapse: collapse;
}

.table-container thead {
    background: #EDF5F0;
}

.table-container th,
.table-container td {
    padding: 12px 10px;
    border-bottom: 1px solid #E5ECE7;
    text-align: left;
    vertical-align: top;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.table-container th {
    color: #365348;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.table-container tbody tr:hover {
    background: #F7FAF8;
}

.table-container tbody tr:last-child td {
    border-bottom: none;
}

/* ===== ĐỘ RỘNG TỪNG CỘT ===== */

.number-cell {
    width: 55px;
    text-align: center !important;
}

.id-cell {
    width: 65px;
}

.table-container th:nth-child(3) {
    width: 190px;
}

.description-cell {
    width: 290px;
    min-width: 0;
    color: #52665C;
    overflow-wrap: anywhere;
}

.table-container th:nth-child(5) {
    width: 145px;
}

.table-container th:nth-child(6) {
    width: 125px;
}

.table-container th:nth-child(7) {
    width: 145px;
}

/* ===== NÚT SỬA / XÓA ===== */

.actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

.actions .btn {
    min-height: 32px;
    padding: 6px 9px;
    font-size: 12px;
}

.delete-form {
    display: inline-flex;
    margin: 0;
}

.delete-form input[type="hidden"] {
    display: none;
}

/* ===== THÔNG BÁO VÀ DỮ LIỆU TRỐNG ===== */

.notice {
    padding: 12px 15px;
    margin-bottom: 16px;
    border: 1px solid #B8D9C4;
    border-radius: 8px;
    background: #E5F3E9;
    color: #176653;
}

.notice.error {
    border-color: #F0C7BF;
    background: #FFF0ED;
    color: #A93226;
}

.empty {
    padding: 35px !important;
    text-align: center !important;
    color: #64756D;
}

/* ===== FORM THÊM / SỬA ===== */

.form-box {
    max-width: 800px;
    margin: 0 auto;
    padding: 28px;
    background: #FFFFFF;
    border: 1px solid #E9E2D2;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(23, 79, 66, 0.05);
}

.form-box label {
    display: block;
    margin-top: 18px;
    font-weight: bold;
    color: #0B4D3E;
}

.form-box input,
.form-box textarea {
    display: block;
    width: 100%;
    padding: 11px 12px;
    margin-top: 7px;
    border: 1px solid #E9E2D2;
    border-radius: 6px;
    background: #FFFFFF;
    color: #174F42;
    font: inherit;
}

.form-box input:focus,
.form-box textarea:focus {
    outline: 2px solid #176653;
    outline-offset: 1px;
}

.form-box .actions {
    margin-top: 22px;
}

/* ===== THANH TÌM KIẾM ===== */

.toolbar-clb {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
}

.search-clb {
    display: flex;
    flex: 1 1 320px;
    gap: 8px;
    min-width: 0;
}

.search-clb input {
    flex: 1;
    width: 100%;
    min-width: 0;
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid #CFD9D3;
    border-radius: 7px;
    background: #fff;
    font: inherit;
}

.result-count {
    color: #64756D;
    white-space: nowrap;
}

/* ===== RESPONSIVE ===== */

@media (max-width: 1000px) and (min-width: 761px) {
    .thanh-ben {
        width: 200px;
    }

    .main {
        width: calc(100% - 200px);
        margin-left: 200px;
        padding: 22px;
    }
}

@media (max-width: 760px) {
    .thanh-ben {
        position: relative;
        top: auto;
        left: auto;
        bottom: auto;
        width: 100%;
        height: auto;
        min-height: 0;
        padding: 16px;
        overflow: visible;
    }

    .thuong-hieu {
        margin: 0 5px 15px;
        white-space: normal;
    }

    .nhom-menu {
        margin: 15px 10px 10px;
    }

    .menu-ben {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
    }

    .menu-ben a {
        width: 100%;
        min-width: 0;
        min-height: 44px;
        margin: 0;
        padding: 10px 8px;
        font-size: 12px;
    }

    .main {
        width: 100%;
        margin-left: 0;
        padding: 18px;
    }

    .page-header {
        align-items: flex-start;
    }

    h1 {
        font-size: 23px;
    }

    .form-box {
        padding: 20px;
    }

    .search-clb {
        flex-basis: 100%;
    }
}
</style>
</head>

<body>
<div class="layout">

<aside class="thanh-ben">
    <div class="thuong-hieu">
        <div class="thuong-hieu-header">
            <span class="thuong-hieu-title">SỰ KIỆN SINH VIÊN</span>

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
    </div>

    <nav class="menu-ben">
        <div class="nhom-menu">Tổng quan</div>

        <a href="?action=dashboard"
           class="<?= $trangHienTai === 'dashboard' ? 'active' : '' ?>">
            <span class="menu-icon">▦</span>
            <span>Dashboard Admin</span>
        </a>

        <div class="nhom-menu">Quản lý hệ thống</div>

        <a href="?action=quan-ly-su-kien"
           class="<?= $trangHienTai === 'quan-ly-su-kien' ? 'active' : '' ?>">
            <span class="menu-icon">▦</span>
            <span>Quản lý sự kiện</span>
        </a>

        <a href="?action=quan-ly-cau-lac-bo"
           class="<?= $trangHienTai === 'quan-ly-cau-lac-bo' ? 'active' : '' ?>">
            <span class="menu-icon">♧</span>
            <span>Quản lý câu lạc bộ</span>
        </a>

        <a href="?action=quan-ly-tai-khoan"
           class="<?= $trangHienTai === 'quan-ly-tai-khoan' ? 'active' : '' ?>">
            <span class="menu-icon">♙</span>
            <span>Quản lý ban tổ chức</span>
        </a>

        <a href="?action=quan-ly-tai-khoan-thanh-vien"
           class="<?= $trangHienTai === 'quan-ly-tai-khoan-thanh-vien' ? 'active' : '' ?>">
            <span class="menu-icon">♟</span>
            <span>Quản lý thành viên</span>
        </a>

        <div class="nhom-menu">Báo cáo</div>

        <a href="?action=thong-ke"
           class="<?= $trangHienTai === 'thong-ke' ? 'active' : '' ?>">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>

</aside>
    <main class="main">
        
<?php if ($cheDo === 'danh_sach'): ?>

    <div class="page-header">
        <div>
            <h1>Quản lý câu lạc bộ</h1>
            <div class="description">
                Danh sách các câu lạc bộ trong hệ thống
            </div>
        </div>
<a class="btn btn-primary" href="?action=them-cau-lac-bo">
    + Thêm câu lạc bộ
</a>
    </div>

    <?php if ($thongBao !== ''): ?>
        <div class="notice <?= $loaiThongBao === 'error' ? 'error' : '' ?>">
            <?= e($thongBao) ?>
        </div>
    <?php endif; ?>

<div class="toolbar-clb">
    <div class="search-clb">
        <input
            type="search"
            id="tuKhoaCLB"
            placeholder="Tìm theo tên câu lạc bộ..."
            aria-label="Tìm theo tên câu lạc bộ"
        >
        <button type="button" class="btn btn-primary" id="nutTimCLB">
            Tìm kiếm
        </button>
    </div>

    <span class="result-count">
        Tìm thấy <?= count($danhSach) ?> câu lạc bộ
    </span>
</div>

    <div class="table-container">
        <table id="bangCauLacBo">
            <thead>
                <tr>
                    <th class="number-cell">STT</th>
                    <th class="id-cell">ID</th>
                    <th>Tên câu lạc bộ</th>
                    <th>Mô tả</th>
                    <th>Người quản lý</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($danhSach)): ?>
                <tr>
                    <td colspan="7" class="empty">
                        Chưa có câu lạc bộ nào.
                    </td>
                </tr>
            <?php else: ?>
                <?php $stt = 1; ?>

                <?php foreach ($danhSach as $clb): ?>
                    <tr class="dong-clb">
                        <td class="number-cell"><?= $stt++ ?></td>
                        <td class="id-cell"><?= e($clb['id'] ?? '') ?></td>
                        <td><?= e($clb['ten_cau_lac_bo'] ?? '') ?></td>
                        <td class="description-cell">
                            <?= e($clb['mo_ta'] ?? '') ?>
                        </td>
                        <td>
                            <?= e($clb['ten_nguoi_quan_ly'] ?? 'Chưa có') ?>
                        </td>
                        <td><?= e($clb['ngay_tao'] ?? '') ?></td>
                        <td>
                            <div class="actions">
                                <a
    class="btn btn-edit"
    href="?action=sua-cau-lac-bo&id=<?= urlencode((string) ($clb['id'] ?? '')) ?>"
>
    Sửa
</a>
                                <form
                                    class="delete-form"
                                    method="POST"
                                    action="<?= e($duongDanGoc) ?>/?action=xoa-cau-lac-bo"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu lạc bộ này không?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= e($clb['id'] ?? '') ?>"
                                    >

                                    <button type="submit" class="btn btn-delete">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($cheDo === 'them' || $cheDo === 'sua'): ?>

    <section class="form-box">
        <?php if ($cheDo === 'sua'): ?>
            <h1>Sửa câu lạc bộ #<?= (int) $id ?></h1>
            <p class="description">Cập nhật thông tin câu lạc bộ.</p>
        <?php else: ?>
            <h1>Thêm câu lạc bộ</h1>
            <p class="description">Điền thông tin để tạo câu lạc bộ mới.</p>
        <?php endif; ?>

        <?php if (!empty($loi)): ?>
            <p class="notice error"><?= e($loi) ?></p>
        <?php endif; ?>

<?php
$urlForm = $cheDo === 'sua'
    ? '?action=cap-nhat-cau-lac-bo&id=' . (int) $id
    : '?action=them-cau-lac-bo';
?>



<form method="POST" action="<?= e($urlForm) ?>">
    <?php if ($cheDo === 'sua'): ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
    <?php endif; ?>

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
    <textarea id="mo_ta" name="mo_ta" rows="5"><?= e($moTa) ?></textarea>

    <label for="nguoi_quan_ly_id">Mã người quản lý (không bắt buộc)</label>
    <input
        type="number"
        id="nguoi_quan_ly_id"
        name="nguoi_quan_ly_id"
        min="1"
        value="<?= e($nguoiQuanLyId) ?>"
    >

    <p class="description">Để trống nếu chưa phân công người quản lý.</p>

    <div class="actions">
        <button type="submit" class="btn btn-primary">
            <?= $cheDo === 'sua' ? 'Lưu thay đổi' : 'Lưu câu lạc bộ' ?>
        </button>

        <a class="btn btn-edit" href="?action=quan-ly-cau-lac-bo">
            Quay lại danh sách
        </a>
    </div>
</form>

    </section>

<?php endif; ?>
</main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('tuKhoaCLB');
    const button = document.getElementById('nutTimCLB');
    const rows = document.querySelectorAll(
        '#bangCauLacBo tbody tr.dong-clb'
    );
    const resultCount = document.querySelector('.result-count');

    if (!input || !button) return;

    function timCauLacBo() {
        const keyword = input.value
            .trim()
            .toLocaleLowerCase('vi');

        let soKetQua = 0;

        rows.forEach(function (row) {
            const noiDung = row.textContent
                .toLocaleLowerCase('vi');

            const hienThi = noiDung.includes(keyword);

            row.hidden = !hienThi;

            if (hienThi) {
                soKetQua++;
            }
        });

        if (resultCount) {
            resultCount.textContent =
                'Tìm thấy ' + soKetQua + ' câu lạc bộ';
        }
    }

    button.addEventListener('click', timCauLacBo);

    input.addEventListener('input', timCauLacBo);

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            timCauLacBo();
        }
    });
});
</script>

</body>
</html>
