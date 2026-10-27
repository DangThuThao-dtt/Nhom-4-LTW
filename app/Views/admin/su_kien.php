<?php
$e = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$danhSachSuKien = $danhSachSuKien ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
$boLoc = $boLoc ?? [];
$tongSoSuKien = (int) ($tongSoSuKien ?? 0);
$tongSoTrang = max(1, (int) ($tongSoTrang ?? 1));
$trangHienTai = max(1, (int) ($boLoc['trang'] ?? 1));

$cacTrangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Công khai',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Đã kết thúc',
];

$thamSoPhanTrang = $_GET;
unset($thamSoPhanTrang['trang']);
$urlCoSo = 'index.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quản lý sự kiện</title>
<style>
:root {
    --xanh-dam: #075747;
    --xanh-chinh: #08745e;
    --xanh-nhat: #e1f4ec;
    --nen: #f5f7f5;
    --vien: #dce4df;
    --chu: #24332d;
    --chu-phu: #687770;
    --trang: #fff;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    color: var(--chu);
    background: var(--nen);
    font: 14px/1.5 Arial, Helvetica, sans-serif;
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
    margin: 0;
    padding: 22px 14px;
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

    .noi-dung-chinh {
        padding: 18px;
    }

    .menu-cuoi {
        margin-top: 8px;
        padding-top: 0;
    }
}
.menu-cuoi { margin-top: auto; padding-top: 28px; }
.noi-dung-chinh { min-width: 0; padding: 28px; }
.dau-trang {
    display: flex; justify-content: flex-end; align-items: center;
    gap: 12px; margin-bottom: 22px;
}
.tai-khoan { color: var(--chu-phu); font-size: 13px; }
.tieu-de-trang {
    display: flex; justify-content: space-between; align-items: flex-end;
    flex-wrap: wrap; gap: 12px; margin-bottom: 22px;
}
h1 { margin: 0; font-size: 27px; line-height: 1.25; color: #173b30; }
.mo-ta-trang { margin: 7px 0 0; color: var(--chu-phu); }
.thanh-cong-cu {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
    margin-bottom: 16px;
}
.tim-kiem { display: flex; flex: 1 1 300px; gap: 8px; min-width: 0; }
input, select, button { font: inherit; }
input, select {
    width: 100%; min-width: 0; min-height: 42px; padding: 9px 11px;
    border: 1px solid #cfd9d3; border-radius: 7px; background: white; color: var(--chu);
}
input:focus, select:focus { outline: 2px solid #b9e3d3; border-color: var(--xanh-chinh); }
.nut {
    display: inline-flex; justify-content: center; align-items: center;
    min-height: 40px; padding: 9px 14px; border: 1px solid var(--vien);
    border-radius: 7px; background: white; color: var(--chu);
    text-decoration: none; cursor: pointer; white-space: nowrap;
}
.nut:hover { background: #edf4f0; }
.nut-chinh { color: white; background: var(--xanh-dam); border-color: var(--xanh-dam); }
.nut-chinh:hover { background: var(--xanh-chinh); }
.tong-su-kien { margin-left: auto; color: var(--chu-phu); }
.khung-bang {
    overflow: hidden; border: 1px solid var(--vien); border-radius: 11px;
    background: white; box-shadow: 0 3px 12px rgba(20, 50, 35, .04);
}
.dau-bang {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 8px; padding: 17px 18px; border-bottom: 1px solid var(--vien);
}
.dau-bang h2 { margin: 0; font-size: 17px; }
.vung-bang { width: 100%; overflow-x: hidden; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
thead { background: #edf5f0; }
th, td {
    padding: 10px 9px; text-align: left; vertical-align: top;
    border-bottom: 1px solid #e8eeea; overflow-wrap: anywhere;
}
td { font-size: 12px; }
th { color: #365348; font-size: 12px; text-transform: uppercase; letter-spacing: .025em; white-space: nowrap; }
tbody tr:hover { background: #f8fbf9; }
tbody tr:last-child td { border-bottom: 0; }
.cot-stt { width: 4%; color: var(--chu-phu); }
.ten-su-kien { width: 15%; font-weight: 700; color: #213e32; }
.cot-clb { width: 12%; }
.mo-ta { width: 16%; color: var(--chu-phu); }
.dia-diem { width: 10%; }
.cot-thoi-gian { width: 18%; }
.thoi-gian { display: block; white-space: normal; line-height: 1.45; }
.thoi-gian + .thoi-gian { margin-top: 6px; padding-top: 6px; border-top: 1px dashed #dce4df; }
.nhan-thoi-gian { display: block; color: var(--chu-phu); font-size: 10px; font-weight: 700; text-transform: uppercase; }
.dang-ky { width: 13%; }
.cot-trang-thai { width: 12%; }
.nhan {
    display: inline-block; padding: 4px 9px; border-radius: 20px;
    font-size: 12px; font-weight: 700; white-space: nowrap;
}
.nhan-cong_khai { color: #176b43; background: #dff4e7; }
.nhan-nhap { color: #6c5b24; background: #fff1c9; }
.nhan-da_huy { color: #9a3f3f; background: #fde7e7; }
.nhan-ket_thuc { color: #53645c; background: #e9eeeb; }
.khong-co { padding: 38px 18px; text-align: center; color: var(--chu-phu); }
.chan-bang {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 12px; padding: 14px 18px; border-top: 1px solid var(--vien);
    color: var(--chu-phu);
}
.phan-trang { display: flex; align-items: center; gap: 7px; }
.trang-hien-tai { padding: 8px 10px; border-radius: 7px; background: var(--xanh-dam); color: white; }
.bo-loc {
    display: none; position: fixed; inset: 0; z-index: 20;
    padding: 18px; align-items: center; justify-content: center;
    background: rgba(13, 36, 27, .45);
}
.bo-loc.dang-mo { display: flex; }
.bo-loc-noi-dung {
    width: min(650px, 100%); max-height: 90vh; overflow-y: auto;
    padding: 22px; border-radius: 12px; background: white;
    box-shadow: 0 15px 50px rgba(0,0,0,.2);
}
.dau-modal { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; }
.dau-modal h2 { margin: 0; font-size: 20px; }
.luoi { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
label { display: block; color: #354b40; font-weight: 600; }
label input, label select { display: block; margin-top: 6px; font-weight: 400; }
.nhom-nut { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 20px; }
@media (max-width: 560px) {
    .noi-dung-chinh { padding: 13px; }
    h1 { font-size: 23px; }
    .luoi { grid-template-columns: 1fr; }
    .tong-su-kien { width: 100%; margin-left: 0; }
    .tim-kiem { flex-basis: 100%; }
    .dau-trang { justify-content: flex-start; }
}
table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}
table th:nth-child(1),
table td:nth-child(1) {
    width: 4%;
}
table th:nth-child(2),
table td:nth-child(2) {
    width: 15%;
}
table th:nth-child(3),
table td:nth-child(3) {
    width: 12%;
}
table th:nth-child(4),
table td:nth-child(4) {
    width: 15%;
}
table th:nth-child(5),
table td:nth-child(5) {
    width: 10%;
}
table th:nth-child(6),
table td:nth-child(6) {
    width: 15%;
}
table th:nth-child(7),
table td:nth-child(7) {
    width: 17%;
}
table th:nth-child(8),
table td:nth-child(8) {
    width: 12%;
}
table th,
table td {
    box-sizing: border-box;
    padding: 10px 8px;
    vertical-align: top;
    overflow-wrap: break-word;
    word-wrap: break-word;
}
table td:nth-child(6) {
    font-size: 12px;
    line-height: 1.5;
}
table td:nth-child(7) {
    white-space: nowrap;
    font-size: 12px;
}
table td:nth-child(8) {
    text-align: center;
}
</style>
</head>
<body>
<div class="app-layout"> 
<aside class="thanh-ben">
    <div class="thuong-hieu">Sự kiện sinh viên</div>
    <nav class="menu-ben" aria-label="Điều hướng">
        <a class="dang-chon" href="?action=quan-ly-su-kien">
            <span class="menu-icon">▦</span>
            <span>Quản lý sự kiện</span>
        </a>
        <a href="?action=quan-ly-tai-khoan">
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
        <header class="dau-trang">
            <span class="tai-khoan">Trang quản trị · Chế độ xem</span>
        </header>
        <section>
            <div class="tieu-de-trang">
                <div>
                    <h1>Quản lý sự kiện</h1>
                    <p class="mo-ta-trang">Tra cứu, xem thông tin và theo dõi tình trạng các sự kiện.</p>
                </div>
            </div>
            <div class="thanh-cong-cu">
                <form class="tim-kiem" method="get" action="<?= $e($urlCoSo) ?>">
                    <input type="hidden" name="action" value="quan-ly-su-kien">
                    <input type="search" name="tu_khoa" value="<?= $e($_GET['tu_khoa'] ?? '') ?>" placeholder="Tìm theo tên sự kiện..." aria-label="Tìm theo tên sự kiện">
                    <?php if (isset($_GET['cau_lac_bo_id'])): ?><input type="hidden" name="cau_lac_bo_id" value="<?= (int) $_GET['cau_lac_bo_id'] ?>"><?php endif; ?>
                    <?php if (isset($_GET['trang_thai'])): ?><input type="hidden" name="trang_thai" value="<?= $e($_GET['trang_thai']) ?>"><?php endif; ?>
                    <?php if (isset($_GET['tu_ngay'])): ?><input type="hidden" name="tu_ngay" value="<?= $e($_GET['tu_ngay']) ?>"><?php endif; ?>
                    <?php if (isset($_GET['den_ngay'])): ?><input type="hidden" name="den_ngay" value="<?= $e($_GET['den_ngay']) ?>"><?php endif; ?>
                    <button class="nut nut-chinh" type="submit">Tìm kiếm</button>
                </form>
                <button class="nut" type="button" id="nutMoLoc" aria-expanded="false" aria-controls="khuVucBoLoc">☷ Bộ lọc</button>
                <span class="tong-su-kien">Tìm thấy <strong><?= $tongSoSuKien ?></strong> sự kiện</span>
            </div>
            <div class="khung-bang">
                <div class="dau-bang">
                    <h2>Danh sách sự kiện</h2>
                </div>
                <div class="vung-bang">
                    <table>
                        <thead>
                            <tr>
                                <th class="cot-stt">STT</th>
                                <th class="ten-su-kien">Tên sự kiện</th>
                                <th class="cot-clb">Câu lạc bộ</th>
                                <th class="mo-ta">Mô tả</th>
                                <th class="dia-diem">Địa điểm</th>
                                <th class="cot-thoi-gian">Thời gian</th>
                                <th class="dang-ky">Đăng ký / Sức chứa</th>
                                <th class="cot-trang-thai">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$danhSachSuKien): ?>
                            <tr><td colspan="8" class="khong-co">Không tìm thấy sự kiện phù hợp. Hãy thử thay đổi từ khóa hoặc bộ lọc.</td></tr>
                        <?php else: ?>
                            <?php foreach ($danhSachSuKien as $i => $sk): ?>
                                <?php
                                $maTrangThai = (string) ($sk['trang_thai'] ?? '');
                                $tenTrangThai = $cacTrangThai[$maTrangThai] ?? $maTrangThai;
                                $batDau = !empty($sk['thoi_gian_bat_dau']) ? date('d/m/Y H:i', strtotime($sk['thoi_gian_bat_dau'])) : '—';
                                $ketThuc = !empty($sk['thoi_gian_ket_thuc']) ? date('d/m/Y H:i', strtotime($sk['thoi_gian_ket_thuc'])) : '—';
                                ?>
                                <tr>
                                    <td class="cot-stt"><?= (($trangHienTai - 1) * (int) ($boLoc['so_dong'] ?? 10)) + $i + 1 ?></td>
                                    <td class="ten-su-kien"><?= $e($sk['ten_su_kien'] ?? '') ?></td>
                                    <td><?= $e($sk['ten_cau_lac_bo'] ?? '—') ?></td>
                                    <td class="mo-ta"><?= $e($sk['mo_ta'] ?? '—') ?></td>
                                    <td class="dia-diem"><?= $e($sk['dia_diem'] ?? '—') ?></td>
                                    <td class="cot-thoi-gian">
                                        <span class="thoi-gian"><span class="nhan-thoi-gian">Bắt đầu</span><?= $e($batDau) ?></span>
                                        <span class="thoi-gian"><span class="nhan-thoi-gian">Kết thúc</span><?= $e($ketThuc) ?></span>
                                    </td>
                                    <td class="dang-ky"><?= (int) ($sk['so_luot_dang_ky'] ?? 0) ?> / <?= (int) ($sk['so_luong_toi_da'] ?? 0) ?></td>
                                    <td><span class="nhan nhan-<?= $e($maTrangThai) ?>"><?= $e($tenTrangThai) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="chan-bang">
                    <span>Trang <?= $trangHienTai ?> / <?= $tongSoTrang ?></span>
                    <nav class="phan-trang" aria-label="Phân trang sự kiện">
                        <?php if ($trangHienTai > 1): ?>
                            <?php $thamSoPhanTrang['trang'] = $trangHienTai - 1; ?>
                            <a class="nut" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">← Trước</a>
                        <?php endif; ?>
                        <span class="trang-hien-tai"><?= $trangHienTai ?></span>
                        <?php if ($trangHienTai < $tongSoTrang): ?>
                            <?php $thamSoPhanTrang['trang'] = $trangHienTai + 1; ?>
                            <a class="nut" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">Sau →</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </section>
    </main>
</div>
<div class="bo-loc" id="khuVucBoLoc" role="dialog" aria-modal="true" aria-labelledby="tieuDeBoLoc">
    <div class="bo-loc-noi-dung">
        <div class="dau-modal">
            <h2 id="tieuDeBoLoc">Bộ lọc sự kiện</h2>
            <button class="nut" type="button" id="nutDongLoc" aria-label="Đóng bộ lọc">×</button>
        </div>
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="quan-ly-su-kien">
            <div class="luoi">
                <label>Câu lạc bộ
                    <select name="cau_lac_bo_id">
                        <option value="0">Tất cả câu lạc bộ</option>
                        <?php foreach ($danhSachCauLacBo as $clb): ?>
                            <option value="<?= (int) $clb['id'] ?>" <?= (int) ($_GET['cau_lac_bo_id'] ?? 0) === (int) $clb['id'] ? 'selected' : '' ?>><?= $e($clb['ten_cau_lac_bo'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Trạng thái
                    <select name="trang_thai">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($cacTrangThai as $ma => $ten): ?>
                            <option value="<?= $e($ma) ?>" <?= ($_GET['trang_thai'] ?? '') === $ma ? 'selected' : '' ?>><?= $e($ten) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Từ ngày
                    <input type="date" name="tu_ngay" value="<?= $e($_GET['tu_ngay'] ?? '') ?>">
                </label>
                <label>Đến ngày
                    <input type="date" name="den_ngay" value="<?= $e($_GET['den_ngay'] ?? '') ?>">
                </label>
                <label>Sắp xếp theo
                    <select name="sap_xep">
                        <option value="thoi_gian" <?= ($_GET['sap_xep'] ?? 'thoi_gian') === 'thoi_gian' ? 'selected' : '' ?>>Thời gian bắt đầu</option>
                        <option value="dang_ky" <?= ($_GET['sap_xep'] ?? '') === 'dang_ky' ? 'selected' : '' ?>>Số lượt đăng ký</option>
                    </select>
                </label>
                <label>Thứ tự
                    <select name="huong">
                        <option value="ASC" <?= strtoupper($_GET['huong'] ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                        <option value="DESC" <?= strtoupper($_GET['huong'] ?? '') === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                    </select>
                </label>
                <label>Từ khóa
                    <input type="search" name="tu_khoa" value="<?= $e($_GET['tu_khoa'] ?? '') ?>" placeholder="Tên sự kiện...">
                </label>
            </div>
            <div class="nhom-nut">
                <button class="nut nut-chinh" type="submit">Áp dụng bộ lọc</button>
                <a class="nut" href="?action=quan-ly-su-kien">Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    const modal = document.getElementById('khuVucBoLoc');
    const nutMo = document.getElementById('nutMoLoc');
    const nutDong = document.getElementById('nutDongLoc');
    function moLoc() {
        modal.classList.add('dang-mo');
        nutMo.setAttribute('aria-expanded', 'true');
    }
    function dongLoc() {
        modal.classList.remove('dang-mo');
        nutMo.setAttribute('aria-expanded', 'false');
    }
    nutMo.addEventListener('click', moLoc);
    nutDong.addEventListener('click', dongLoc);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) dongLoc();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') dongLoc();
    });
})();
</script>
</body>
</html>
