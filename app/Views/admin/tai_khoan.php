<?php
$e = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$thongBao = $_GET['thong_bao'] ?? '';

$noiDungThongBao = [
    'tao_tai_khoan_thanh_cong' => 'Tạo tài khoản ban tổ chức thành công.',
    'cap_nhat_tai_khoan_thanh_cong' => 'Cập nhật tài khoản thành công.',
    'dat_lai_mat_khau_thanh_cong' => 'Đặt lại mật khẩu thành công.',
    'email_da_ton_tai' => 'Email này đã được sử dụng. Vui lòng nhập email khác.',
    'clb_da_co_ban_to_chuc' => 'Câu lạc bộ này đã có tài khoản ban tổ chức phụ trách.',
    'clb_khong_ton_tai' => 'Câu lạc bộ không tồn tại hoặc đã được thay đổi.',
    'mat_khau_cu_khong_dung' => 'Mật khẩu hiện tại không chính xác.',
    'mat_khau_moi_khong_khop' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.',
    'cap_nhat_that_bai' => 'Không thể cập nhật tài khoản. Vui lòng kiểm tra dữ liệu.',
    'tao_tai_khoan_that_bai' => 'Không thể tạo tài khoản. Vui lòng kiểm tra dữ liệu và thử lại.',
    'dat_lai_mat_khau_that_bai' => 'Không thể đặt lại mật khẩu. Vui lòng kiểm tra tài khoản.',
];
$danhSachTaiKhoan = $danhSachTaiKhoan ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
$tuKhoa = $tuKhoa ?? '';
$cauLacBoId = $cauLacBoId ?? 0;
$tongTaiKhoan = $tongTaiKhoan ?? 0;
$tongKetQua = $tongKetQua ?? 0;
$trang = $trang ?? 1;
$tongTrang = $tongTrang ?? 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý tài khoản Ban tổ chức</title>
    
<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #263b34;
    background: #f5f6f2;
    line-height: 1.5;
}
.app-layout {
    display: grid;
    grid-template-columns: 230px minmax(0, 1fr);
    min-height: 100vh;
}

.thanh-ben {
    box-sizing: border-box;
    width: 230px;
    min-width: 230px;
    min-height: 100vh;
    padding: 22px 14px;
    margin: 0;
    background: #075747;
    color: #fff;
    font-family: Arial, Helvetica, sans-serif;
}

.thuong-hieu {
    box-sizing: border-box;
    height: 26px;
    margin: 0 8px 28px;
    color: #fff;
    font-size: 17px;
    line-height: 26px;
    font-weight: 700;
    white-space: nowrap;
}

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    margin: 0;
    padding: 0;
}

.menu-ben a {
    box-sizing: border-box;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    height: 45px;
    min-height: 45px;
    margin: 0;
    padding: 0 12px;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: #f5faf6;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: 400;
    line-height: 20px;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s;
}

.menu-ben a:hover {
    background: #106d5a;
}

.menu-ben a.dang-chon {
    background: #13866f;
    color: #fff;
    font-weight: 600;
}

.menu-icon {
    display: inline-flex;
    flex: 0 0 16px;
    width: 16px;
    height: 20px;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    line-height: 20px;
}
.noi-dung-chinh {
    box-sizing: border-box;
    min-width: 0;
}
@media (max-width: 600px) {
    .app-layout {
        grid-template-columns: minmax(0, 1fr);
    }

    .thanh-ben {
        width: 100%;
        min-width: 0;
        min-height: auto;
        padding: 14px;
    }

    .thuong-hieu {
        height: 26px;
        margin: 0 4px 12px;
        line-height: 26px;
    }

    .menu-ben {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
    }

    .menu-ben a {
        flex: 1 1 auto;
        width: auto;
        min-width: 0;
        height: 40px;
        min-height: 40px;
        padding: 0 9px;
        font-size: 12px;
        white-space: normal;
    }
}
.noi-dung-chinh {
    min-width: 0;
    padding: 30px 28px;
    background: #f5f6f2;
}

.page-intro {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}

.page-intro h1 {
    margin: 0 0 8px;
    color: #174b3d;
    font-size: 28px;
}

.page-intro p {
    margin: 0;
    color: #68766f;
    font-size: 14px;
}

h2 {
    margin: 24px 0 12px;
    color: #234c3e;
    font-size: 19px;
}

/* Nút */
button {
    min-height: 40px;
    padding: 9px 14px;
    border: 1px solid #d8dfd8;
    border-radius: 7px;
    background: #fff;
    color: #28483d;
    font: inherit;
    cursor: pointer;
    transition: background .2s;
}

button:hover {
    background: #edf3ed;
}

button.primary,
button[type="submit"].primary {
    border-color: #075747;
    background: #075747;
    color: #fff;
}

button.primary:hover,
button[type="submit"].primary:hover {
    background: #0b6c57;
}
.summary {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 18px 0;
}

.summary p {
    margin: 0;
    padding: 12px 16px;
    border: 1px solid #e0e5dc;
    border-radius: 9px;
    background: #fffdf7;
    font-size: 14px;
}
.filter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
    align-items: end;
    gap: 14px;
    margin: 18px 0 24px;
    padding: 18px;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fffdf8;
}

label {
    display: block;
    color: #344b40;
    font-size: 14px;
    font-weight: 600;
}

input,
select {
    width: 100%;
    min-width: 0;
    min-height: 42px;
    margin-top: 6px;
    padding: 9px 11px;
    border: 1px solid #d2dbd2;
    border-radius: 7px;
    background: #fff;
    color: #263b34;
    font: inherit;
}

input:focus,
select:focus {
    outline: 2px solid #a9d4c1;
    border-color: #23856d;
}
.table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e0e4db;
    border-radius: 10px;
    background: #fffdf8;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

th,
td {
    padding: 13px 12px;
    border-bottom: 1px solid #e4e8e0;
    text-align: left;
    vertical-align: middle;
    overflow-wrap: anywhere;
}

th {
    background: #e8f0e8;
    color: #244b3d;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

tbody tr:nth-child(even) {
    background: #f8f8f2;
}

tbody tr:hover {
    background: #eef4eb;
}

tbody tr:last-child td {
    border-bottom: 0;
}

td {
    font-size: 14px;
}

.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.empty {
    padding: 28px;
    text-align: center;
    color: #68766f;
}

/* Thông báo */
.message {
    margin: 12px 0;
    padding: 12px 15px;
    border: 1px solid #cbded0;
    border-radius: 8px;
    background: #edf5eb;
    color: #245b43;
}

/* Cửa sổ tạo và sửa tài khoản */
dialog {
    width: min(600px, calc(100% - 28px));
    max-height: 88vh;
    overflow-y: auto;
    padding: 24px;
    border: 1px solid #dce3d9;
    border-radius: 13px;
    background: #fffdf8;
    color: #263b34;
}

dialog::backdrop {
    background: rgba(13, 43, 33, .48);
}

dialog h2 {
    margin: 0 0 16px;
}

.dialog-form {
    display: grid;
    gap: 14px;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
}

.muted {
    color: #68766f;
    font-size: 13px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 18px;
    margin-top: 22px;
}

.pagination a {
    color: #08644f;
    text-decoration: none;
    font-weight: 600;
}

.pagination a:hover {
    text-decoration: underline;
}
@media (max-width: 1000px) {
    .app-layout {
        grid-template-columns: 195px minmax(0, 1fr);
    }

    .noi-dung-chinh {
        padding: 22px 18px;
    }

    .filter-form {
        grid-template-columns: 1fr 1fr;
    }

    .filter-form button {
        grid-column: 1 / -1;
    }
}
@media (max-width: 700px) {
    .app-layout {
        grid-template-columns: 1fr;
    }

    .thanh-ben {
        min-height: auto;
        padding: 14px;
    }

    .thuong-hieu {
        margin-bottom: 12px;
    }

    .menu-ben {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .menu-ben a {
        flex: 1 1 auto;
    }

    .noi-dung-chinh {
        padding: 18px 12px;
    }

    .filter-form {
        grid-template-columns: 1fr;
        padding: 14px;
    }

    .filter-form button {
        grid-column: auto;
    }

    .table-wrap table {
        min-width: 700px;
    }

    .page-intro h1 {
        font-size: 24px;
    }
}
</style>
</head>
<body>
<div class="app-layout">
<aside class="thanh-ben">
    <div class="thuong-hieu">Sự kiện sinh viên</div>
    <nav class="menu-ben" aria-label="Điều hướng">
        <a href="?action=quan-ly-su-kien">
            <span class="menu-icon">▦</span>
            <span>Quản lý sự kiện</span>
        </a>
        <a class="dang-chon" href="?action=quan-ly-tai-khoan">
            <span class="menu-icon">♙</span>
            <span>Quản lý Ban tổ chức</span>
        </a>
        <a href="?action=thong-ke">
            <span class="menu-icon">▤</span>
            <span>Thống kê</span>
        </a>
    </nav>
</aside>
<main class="noi-dung-chinh">
    <div class="page-intro">
        <div>
            <h1>Quản lý ban tổ chức</h1>
            <p>Quản lý tài khoản đăng nhập và câu lạc bộ phụ trách.</p>
        </div>
        <button type="button" class="primary" id="moFormTaoTaiKhoan">
            + Tạo tài khoản ban tổ chức
        </button>
    </div>
    <?php if (isset($noiDungThongBao[$thongBao])): ?>
        <p class="message" role="status"><?= $e($noiDungThongBao[$thongBao]) ?></p>
    <?php endif; ?>
    <div class="summary">
        <p>Tổng tài khoản ban tổ chức: <strong><?= (int) $tongTaiKhoan ?></strong></p>
        <p>Kết quả tìm được: <strong><?= (int) $tongKetQua ?></strong></p>
    </div>
    <form class="filter-form" method="get" action="">
        <input type="hidden" name="action" value="quan-ly-tai-khoan">
        <label>Tìm theo họ tên hoặc email
            <input type="search" name="q" value="<?= $e($tuKhoa) ?>" placeholder="Nhập họ tên hoặc email">
        </label>
        <label>Lọc theo câu lạc bộ
            <select name="cau_lac_bo_id">
                <option value="0">Tất cả câu lạc bộ</option>
                <?php foreach ($danhSachCauLacBo as $clb): ?>
                    <option value="<?= (int) $clb['id'] ?>" <?= (int)$cauLacBoId === (int)$clb['id'] ? 'selected' : '' ?>>
                        <?= $e($clb['ten_cau_lac_bo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="primary">Tìm kiếm</button>
    </form>
    <h2>Danh sách tài khoản</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Họ tên</th><th>Email đăng nhập</th><th>Câu lạc bộ phụ trách</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$danhSachTaiKhoan): ?>
                <tr><td colspan="5" class="empty">Chưa có tài khoản ban tổ chức phù hợp.</td></tr>
            <?php else: ?>
                <?php foreach ($danhSachTaiKhoan as $tk): ?>
                    <tr>
                        <td><?= (int)$tk['id'] ?></td>
                        <td><?= $e($tk['ho_ten']) ?></td>
                        <td><?= $e($tk['email']) ?></td>
                        <td><?= $e($tk['ten_cau_lac_bo'] ?? 'Chưa gán câu lạc bộ') ?></td>
                        <td>
                            <div class="actions">
                                <button type="button" class="primary" data-mo-sua="<?= (int)$tk['id'] ?>">Sửa thông tin</button>
                            </div>
                            <dialog id="suaTaiKhoan<?= (int)$tk['id'] ?>">
                                <h2>Sửa tài khoản ban tổ chức</h2>
                                <p class="muted">Để đổi mật khẩu, nhập mật khẩu hiện tại và mật khẩu mới. Nếu không đổi mật khẩu, để trống hai ô mật khẩu.</p>
                                <form class="dialog-form" method="post" action="?action=cap-nhat-tai-khoan">
                                    <input type="hidden" name="id" value="<?= (int)$tk['id'] ?>">
                                    <label>Họ tên
                                        <input name="ho_ten" maxlength="100" required value="<?= $e($tk['ho_ten']) ?>">
                                    </label>
                                    <label>Email đăng nhập
                                        <input type="email" name="email" maxlength="150" required value="<?= $e($tk['email']) ?>">
                                    </label>
                                    <label>Câu lạc bộ phụ trách
                                        <select name="cau_lac_bo_id" required>
                                            <option value="">-- Chọn câu lạc bộ --</option>
                                            <?php foreach ($danhSachCauLacBo as $clb): ?>
                                                <?php if (empty($clb['nguoi_quan_ly_id']) || (int)$clb['nguoi_quan_ly_id'] === (int)$tk['id']): ?>
                                                    <option value="<?= (int)$clb['id'] ?>" <?= (int)($tk['cau_lac_bo_id'] ?? 0) === (int)$clb['id'] ? 'selected' : '' ?>>
                                                        <?= $e($clb['ten_cau_lac_bo']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label>Mật khẩu hiện tại (chỉ cần khi đổi mật khẩu)
                                        <input type="password" name="mat_khau_cu" autocomplete="current-password">
                                    </label>
                                    <label>Mật khẩu mới (để trống nếu không đổi)
                                        <input type="password" name="mat_khau_moi" minlength="8" autocomplete="new-password">
                                    </label>
                                    <label>Xác nhận mật khẩu mới
                                        <input type="password" name="xac_nhan_mat_khau" minlength="8" autocomplete="new-password">
                                    </label>
                                    <div class="dialog-actions">
                                        <button type="button" data-dong-dialog="<?= (int)$tk['id'] ?>">Hủy</button>
                                        <button type="submit" class="primary">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination" aria-label="Phân trang tài khoản">
        <?php
        $thamSoTrang = ['action' => 'quan-ly-tai-khoan', 'q' => $tuKhoa, 'cau_lac_bo_id' => $cauLacBoId];
        ?>
        <?php if ($trang > 1): $thamSoTrang['trang'] = $trang - 1; ?>
            <a href="?<?= $e(http_build_query($thamSoTrang)) ?>">Trang trước</a>
        <?php endif; ?>
        <span>Trang <?= (int)$trang ?> / <?= (int)$tongTrang ?></span>
        <?php if ($trang < $tongTrang): $thamSoTrang['trang'] = $trang + 1; ?>
            <a href="?<?= $e(http_build_query($thamSoTrang)) ?>">Trang sau</a>
        <?php endif; ?>
    </nav>
    <dialog id="formTaoTaiKhoan">
        <h2>Tạo tài khoản Ban tổ chức</h2>
        <form class="dialog-form" method="post" action="?action=them-tai-khoan">
            <label>Họ tên
                <input name="ho_ten" maxlength="100" required>
            </label>
            <label>Email đăng nhập
                <input type="email" name="email" maxlength="150" required>
            </label>
            <label>Mật khẩu ban đầu
                <input type="password" name="mat_khau" minlength="8" autocomplete="new-password" required>
            </label>
            <label>Câu lạc bộ cần cấp tài khoản
                <select name="cau_lac_bo_id" required>
                    <option value="">-- Chọn câu lạc bộ --</option>
                    <?php foreach ($danhSachCauLacBo as $clb): ?>
                        <?php if (empty($clb['nguoi_quan_ly_id'])): ?>
                            <option value="<?= (int)$clb['id'] ?>"><?= $e($clb['ten_cau_lac_bo']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="dialog-actions">
                <button type="button" id="dongFormTaoTaiKhoan">Hủy</button>
                <button type="submit" class="primary">Tạo tài khoản</button>
            </div>
        </form>
    </dialog>

    

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formTao = document.getElementById('formTaoTaiKhoan');
    const nutMoTao = document.getElementById('moFormTaoTaiKhoan');
    const nutDongTao = document.getElementById('dongFormTaoTaiKhoan');

    // Mở cửa sổ tạo tài khoản
    if (formTao && nutMoTao) {
        nutMoTao.addEventListener('click', function () {
            formTao.showModal();
        });
    }

    // Đóng cửa sổ tạo tài khoản
    if (formTao && nutDongTao) {
        nutDongTao.addEventListener('click', function () {
            formTao.close();
        });
    }

    // Mở cửa sổ sửa đúng tài khoản
    document.querySelectorAll('[data-mo-sua]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.moSua;
            const dialog = document.getElementById('suaTaiKhoan' + id);

            if (dialog) {
                dialog.showModal();
            }
        });
    });

    // Đóng cửa sổ sửa tài khoản
    document.querySelectorAll('[data-dong-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.dongDialog;
            const dialog = document.getElementById('suaTaiKhoan' + id);

            if (dialog) {
                dialog.close();
            }
        });
    });

    // Kiểm tra mật khẩu xác nhận
    document.querySelectorAll(
        'form[action="?action=cap-nhat-tai-khoan"]'
    ).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const moi = form.querySelector('[name="mat_khau_moi"]').value;
            const xacNhan = form.querySelector('[name="xac_nhan_mat_khau"]').value;

            if ((moi || xacNhan) && moi !== xacNhan) {
                event.preventDefault();
                alert('Mật khẩu mới và xác nhận mật khẩu không khớp.');
            }
        });
    });
});
</script>
</main>
</div>
</body>
</html>
