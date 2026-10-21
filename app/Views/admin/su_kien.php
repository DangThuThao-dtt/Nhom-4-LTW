
<?php
$e = static fn($value) => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$danhSachSuKien = $danhSachSuKien ?? [];
$danhSachCauLacBo = $danhSachCauLacBo ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sự kiện</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #222;
    background: #fff;
}

.app-layout {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 245px minmax(0, 1fr);
}

.thanh-ben {
    padding: 24px 16px;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.thuong-hieu {
    display: none;
}

.menu-ben {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.menu-ben a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    color: #222;
    text-decoration: none;
    border-radius: 6px;
}

.menu-ben a:hover,
.menu-ben a.dang-chon {
    background: #eee;
}

.noi-dung-chinh {
    min-width: 0;
    padding: 28px;
}

.dau-trang {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 28px;
}

.tim-kiem-nhanh {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.tim-kiem-nhanh input {
    flex: 1;
    min-width: 0;
    border: 0;
    outline: none;
}

.tieu-de-trang {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.tieu-de-trang h1 {
    margin: 0;
    font-size: 28px;
}

.mo-ta-trang {
    margin: 5px 0 0;
}

.thanh-cong-cu {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 16px 0;
}

.tong-su-kien {
    margin-left: auto;
}

.khung {
    padding: 18px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 24px;
}

.khung h2 {
    margin-top: 0;
}

.luoi {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

label {
    display: block;
}

input,
select,
textarea,
button {
    font: inherit;
    max-width: 100%;
}

input,
select,
textarea {
    width: 100%;
    min-height: 40px;
    padding: 9px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #fff;
    color: #222;
}

button,
.nut,
.nut-phu,
.nut-xoa,
.nut-loc,
.nut-trang {
    padding: 9px 14px;
    border: 1px solid #aaa;
    border-radius: 5px;
    background: #fff;
    color: #222;
    cursor: pointer;
    text-decoration: none;
}

button:hover,
.nut:hover,
.nut-phu:hover,
.nut-xoa:hover,
.nut-loc:hover,
.nut-trang:hover {
    background: #eee;
}

.thanh-tac-vu {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: auto;
    padding-top: 8px;
}

.thanh-tac-vu form {
    margin: 0;
}

.nut-sua {
    padding: 8px 0;
    border: 0;
    background: transparent;
    text-decoration: underline;
}

.danh-sach-the {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.the-su-kien {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
}

.the-su-kien h3 {
    margin: 0;
    font-size: 20px;
    overflow-wrap: anywhere;
}

.the-dau {
    display: flex;
    justify-content: flex-end;
}

.nhan-trang-thai {
    display: inline-block;
    padding: 4px 9px;
    border: 1px solid #ccc;
    border-radius: 20px;
    font-size: 12px;
}

.thong-tin-the {
    margin: 0;
    overflow-wrap: anywhere;
}

.khong-co-su-kien {
    padding: 30px;
    text-align: center;
    border: 1px dashed #ccc;
    border-radius: 8px;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    padding: 20px;
    overflow-y: auto;
    background: rgba(0, 0, 0, 0.35);
    align-items: center;
    justify-content: center;
}

.modal.dang-mo {
    display: flex;
}

.modal-noi-dung {
    width: 100%;
    max-width: 760px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 24px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: #fff;
}

.modal-dau,
.dau-modal-loc {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
}

.modal-dau h2,
.dau-modal-loc h2 {
    margin: 0;
}

.nhom-nut {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.bo-loc {
    display: none;
}

.bo-loc.dang-mo,
#khuVucBoLoc.bo-loc.dang-mo {
    display: flex;
}

#khuVucBoLoc.bo-loc {
    position: fixed;
    inset: 0;
    z-index: 2000;
    padding: 20px;
    overflow-y: auto;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.35);
}

#khuVucBoLoc .bo-loc-noi-dung {
    width: 100%;
    max-width: 650px;
    max-height: 85vh;
    overflow-y: auto;
    padding: 24px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: #fff;
}

.phan-trang {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin: 28px 0 8px;
}

.trang-hien-tai {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.tai-khoan-admin,
.ngay-hien-tai {
    display: none;
}

@media (max-width: 1100px) {
    .danh-sach-the {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .app-layout {
        grid-template-columns: 190px minmax(0, 1fr);
    }

    .noi-dung-chinh {
        padding: 20px;
    }
}

@media (max-width: 700px) {
    .app-layout {
        grid-template-columns: 1fr;
    }

    .thanh-ben {
        padding: 12px;
    }

    .menu-ben {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .menu-ben a {
        padding: 9px;
    }

    .noi-dung-chinh {
        padding: 14px;
    }

    .danh-sach-the,
    .luoi {
        grid-template-columns: 1fr;
    }

    .dau-trang {
        flex-wrap: wrap;
    }

    .tim-kiem-nhanh {
        flex-basis: 100%;
    }

    .tong-su-kien {
        margin-left: 0;
    }

    .modal,
    #khuVucBoLoc.bo-loc {
        padding: 10px;
    }

    .modal-noi-dung,
    #khuVucBoLoc .bo-loc-noi-dung {
        padding: 14px;
    }
}
</style>
</head>
<body>
<div class="app-layout">
    
<aside class="thanh-ben">
    <nav class="menu-ben" aria-label="Điều hướng">
    <a class="dang-chon" href="?action=quan-ly-su-kien">
        <span>Quản lý sự kiện</span>
    </a>
    <a href="?action=quan-ly-tai-khoan">
        <span>Quản lý Ban tổ chức</span>
    </a>
    <a href="?action=thong-ke">
        <span>Thống kê</span>
    </a>
</nav>
</aside>
    <main class="noi-dung-chinh">
        <header class="dau-trang">
            <form class="tim-kiem-nhanh" method="get" action="index.php">
                <input type="hidden" name="action" value="quan-ly-su-kien">
                <span aria-hidden="true">⌕</span>
                <input type="search" name="tu_khoa" value="<?= $e($_GET['tu_khoa'] ?? '') ?>" placeholder="Tìm kiếm sự kiện...">
                <button type="submit" aria-label="Tìm kiếm">Tìm</button>
            </form>
            <button class="nut-loc nut-loc-dau" type="button" id="nutMoLoc" aria-expanded="false" aria-controls="khuVucBoLoc">☷ Bộ lọc</button>
        </header>

        <div class="tieu-de-trang">
            <div>
                <h1>Quản lý sự kiện</h1>
<p class="mo-ta-trang">Theo dõi, thêm và cập nhật các sự kiện</p>
            </div>
            <button class="nut" type="button" id="moModalThem">+ Thêm sự kiện</button>
        </div>

        <div class="thanh-cong-cu">
            <span class="tong-su-kien">Tìm thấy <strong><?= (int)$tongSoSuKien ?></strong> sự kiện</span>
        </div>

<div class="modal" id="modalThemSuKien"
     role="dialog" aria-modal="true"
     aria-labelledby="tieuDeModalThem">

    <div class="modal-noi-dung">
        <div class="modal-dau">
            <h2 id="tieuDeModalThem">Thêm sự kiện</h2>
            <button class="nut-dong" type="button"
                    id="dongModalThem"
                    aria-label="Đóng cửa sổ">×</button>
        </div>

        <form method="post" action="?action=them-su-kien">
            <div class="luoi">
                <label>
                    Tên sự kiện *
                    <input name="ten_su_kien"
                           required maxlength="200">
                </label>

                <label>
                    Câu lạc bộ *
                    <select name="cau_lac_bo_id" required>
                        <option value="">-- Chọn câu lạc bộ --</option>
                        <?php foreach ($danhSachCauLacBo as $clb): ?>
                            <option value="<?= $e($clb['id']) ?>">
                                <?= $e($clb['ten_cau_lac_bo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Địa điểm
                    <input name="dia_diem" maxlength="255">
                </label>

                <label>
                    Thời gian bắt đầu *
                    <input type="datetime-local"
                           name="thoi_gian_bat_dau" required>
                </label>

                <label>
                    Thời gian kết thúc
                    <input type="datetime-local"
                           name="thoi_gian_ket_thuc">
                </label>

                <label>
                    Số lượng tối đa *
                    <input type="number" name="so_luong_toi_da"
                           min="1" value="100" required>
                </label>

                <label>
                    Trạng thái
                    <select name="trang_thai">
                        <option value="nhap">Bản nháp</option>
                        <option value="cong_khai">Công khai</option>
                        <option value="da_huy">Đã hủy</option>
                        <option value="ket_thuc">Kết thúc</option>
                    </select>
                </label>
            </div>

            <p>
                <label>
                    Mô tả
                    <textarea name="mo_ta" rows="3"></textarea>
                </label>
            </p>

            <button class="nut" type="submit">Thêm sự kiện</button>
            <button class="nut-phu" type="button"
                    id="huyModalThem">Đóng</button>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modalThemSuKien');
    const nutMo = document.getElementById('moModalThem');
    const nutDong = document.getElementById('dongModalThem');
    const nutHuy = document.getElementById('huyModalThem');

    function moModal() {
        modal.classList.add('dang-mo');
        const inputDau = modal.querySelector('input[name="ten_su_kien"]');
        if (inputDau) inputDau.focus();
    }

    function dongModal() {
        modal.classList.remove('dang-mo');
        nutMo.focus();
    }

    nutMo.addEventListener('click', moModal);
    nutDong.addEventListener('click', dongModal);
    nutHuy.addEventListener('click', dongModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) dongModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('dang-mo')) {
            dongModal();
        }
    });
})();
</script>
    <section class="khung">
        <h2>Danh sách sự kiện</h2>


<div class="modal bo-loc" id="khuVucBoLoc"
     role="dialog" aria-modal="true"
     aria-labelledby="tieuDeBoLoc">

    <div class="bo-loc-noi-dung">
        <div class="dau-modal-loc">
            <h2 id="tieuDeBoLoc">Bộ lọc sự kiện</h2>
            <button class="nut-dong-loc" type="button"
                    id="nutDongLoc" aria-label="Đóng bộ lọc">×</button>
        </div>

        <form method="get" action="index.php">
            <input type="hidden" name="action" value="quan-ly-su-kien">

            <div class="luoi">
                <label>Câu lạc bộ
                    <select name="cau_lac_bo_id">
                        <option value="0">Tất cả câu lạc bộ</option>
                        <?php foreach ($danhSachCauLacBo as $clb): ?>
                            <option value="<?= $e($clb['id']) ?>"
                                <?= (int)($_GET['cau_lac_bo_id'] ?? 0) === (int)$clb['id'] ? 'selected' : '' ?>>
                                <?= $e($clb['ten_cau_lac_bo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Trạng thái
                    <select name="trang_thai">
                        <option value="">Tất cả trạng thái</option>
                        <?php
                        $cacTrangThai = [
                            'nhap' => 'Bản nháp',
                            'cong_khai' => 'Công khai',
                            'da_huy' => 'Đã hủy',
                            'ket_thuc' => 'Kết thúc'
                        ];
                        foreach ($cacTrangThai as $ma => $ten):
                        ?>
                            <option value="<?= $e($ma) ?>"
                                <?= ($_GET['trang_thai'] ?? '') === $ma ? 'selected' : '' ?>>
                                <?= $e($ten) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Bắt đầu từ ngày
                    <input type="date" name="tu_ngay"
                           value="<?= $e($_GET['tu_ngay'] ?? '') ?>">
                </label>

                <label>Đến ngày
                    <input type="date" name="den_ngay"
                           value="<?= $e($_GET['den_ngay'] ?? '') ?>">
                </label>

                <label>Sắp xếp theo
                    <select name="sap_xep">
                        <option value="thoi_gian" <?= ($_GET['sap_xep'] ?? 'thoi_gian') === 'thoi_gian' ? 'selected' : '' ?>>Thời gian bắt đầu</option>
                        <option value="dang_ky" <?= ($_GET['sap_xep'] ?? '') === 'dang_ky' ? 'selected' : '' ?>>Số lượt đăng ký</option>
                    </select>
                </label>

                <label>Thứ tự
                    <select name="huong">
                        <option value="ASC" <?= ($_GET['huong'] ?? 'ASC') === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                        <option value="DESC" <?= ($_GET['huong'] ?? 'ASC') === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                    </select>
                </label>
            </div>

            <div class="nhom-nut" style="margin-top:20px">
                <button class="nut" type="submit">Áp dụng bộ lọc</button>
                <a class="nut-phu" href="index.php?action=quan-ly-su-kien"
                   style="text-decoration:none">Xóa bộ lọc</a>
                
            </div>
        </form>
    </div>
</div>


<div class="danh-sach-the">
<?php if (!$danhSachSuKien): ?>
    <div class="khong-co-su-kien">Chưa có sự kiện phù hợp. Hãy thử thay đổi bộ lọc.</div>
<?php else: foreach ($danhSachSuKien as $sk): ?>
    <article class="the-su-kien">
        <div class="the-dau"><span class="nhan-trang-thai"><?= $e(['nhap'=>'Bản nháp','cong_khai'=>'Công khai','da_huy'=>'Đã hủy','ket_thuc'=>'Kết thúc'][$sk['trang_thai']] ?? $sk['trang_thai']) ?></span></div>
        <h3><?= $e($sk['ten_su_kien']) ?></h3>
        <p class="thong-tin-the"><strong>Câu lạc bộ:</strong> <?= $e($sk['ten_cau_lac_bo']) ?></p>
        <p class="thong-tin-the"><strong>Thời gian:</strong> <?= $e($sk['thoi_gian_bat_dau']) ?></p>
        <p class="thong-tin-the"><strong>Đăng ký:</strong> <?= $e($sk['so_luot_dang_ky']) ?> / <?= $e($sk['so_luong_toi_da']) ?></p>
        <div class="thanh-tac-vu">
            <button class="nut-sua" type="button" data-mo-modal="modalSua<?= $e($sk['id']) ?>">✎ Sửa thông tin</button>
            <form method="post" action="?action=xoa-su-kien" onsubmit="return confirm('Bạn có chắc muốn xóa sự kiện này?');">
                <input type="hidden" name="id" value="<?= $e($sk['id']) ?>">
                <button class="nut-xoa" type="submit">Xóa</button>
            </form>
        </div>
        <div class="modal modal-sua" id="modalSua<?= $e($sk['id']) ?>" role="dialog" aria-modal="true" aria-labelledby="tieuDeSua<?= $e($sk['id']) ?>">
            <div class="modal-noi-dung">
                <div class="modal-dau">
                    <h2 id="tieuDeSua<?= $e($sk['id']) ?>">Sửa thông tin sự kiện</h2>
                    <button class="nut-dong" type="button" data-dong-modal aria-label="Đóng cửa sổ">×</button>
                </div>
                <form method="post" action="?action=cap-nhat-su-kien">
                    <input type="hidden" name="id" value="<?= $e($sk['id']) ?>">
                    <p>Tên sự kiện<input name="ten_su_kien" required value="<?= $e($sk['ten_su_kien']) ?>"></p>
                    <p>Câu lạc bộ<select name="cau_lac_bo_id" required><?php foreach ($danhSachCauLacBo as $clb): ?><option value="<?= $e($clb['id']) ?>" <?= (int)$clb['id'] === (int)$sk['cau_lac_bo_id'] ? 'selected' : '' ?>><?= $e($clb['ten_cau_lac_bo']) ?></option><?php endforeach; ?></select></p>
                    <p>Mô tả<textarea name="mo_ta" rows="3"><?= $e($sk['mo_ta']) ?></textarea></p>
                    <p>Địa điểm<input name="dia_diem" value="<?= $e($sk['dia_diem']) ?>"></p>
                    <p>Bắt đầu<input type="datetime-local" name="thoi_gian_bat_dau" required value="<?= $e(date('Y-m-d\TH:i', strtotime($sk['thoi_gian_bat_dau']))) ?>"></p>
                    <p>Kết thúc<input type="datetime-local" name="thoi_gian_ket_thuc" value="<?= empty($sk['thoi_gian_ket_thuc']) ? '' : $e(date('Y-m-d\TH:i', strtotime($sk['thoi_gian_ket_thuc']))) ?>"></p>
                    <p>Số lượng tối đa<input type="number" min="1" name="so_luong_toi_da" required value="<?= $e($sk['so_luong_toi_da']) ?>"></p>
                    <p>Trạng thái<select name="trang_thai" required><?php foreach (['nhap'=>'Bản nháp','cong_khai'=>'Công khai','da_huy'=>'Đã hủy','ket_thuc'=>'Kết thúc'] as $ma=>$ten): ?><option value="<?= $e($ma) ?>" <?= ($sk['trang_thai'] ?? '') === $ma ? 'selected' : '' ?>><?= $e($ten) ?></option><?php endforeach; ?></select></p>
                    <button class="nut" type="submit">Lưu thay đổi</button>
                </form>
                <button class="nut-phu" type="button" data-dong-modal>Đóng</button>
            </div>
        </div>
    </article>
<?php endforeach; endif; ?>
</div>

<?php
$thamSoPhanTrang = $_GET;
unset($thamSoPhanTrang['trang']);
?>

<nav class="phan-trang" aria-label="Phân trang sự kiện">
    <?php if (($boLoc['trang'] ?? 1) > 1): ?>
        <?php $thamSoPhanTrang['trang'] = $boLoc['trang'] - 1; ?>
        <a class="nut-trang" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">← Trang trước</a>
    <?php endif; ?>
    <span class="trang-hien-tai">Trang <strong><?= (int) ($boLoc['trang'] ?? 1) ?></strong> / <?= (int) ($tongSoTrang ?? 1) ?></span>
    <?php if (($boLoc['trang'] ?? 1) < ($tongSoTrang ?? 1)): ?>
        <?php $thamSoPhanTrang['trang'] = $boLoc['trang'] + 1; ?>
        <a class="nut-trang" href="?<?= $e(http_build_query($thamSoPhanTrang)) ?>">Trang sau →</a>
    <?php endif; ?>
</nav>

    </section>
    </main>
</div>
<script>
(function () {
    
const boLoc = document.getElementById('khuVucBoLoc');
const nutLoc = document.getElementById('nutMoLoc');
const nutDongLoc = document.getElementById('nutDongLoc');

function moBoLoc() {
    boLoc.classList.add('dang-mo');
    nutLoc.setAttribute('aria-expanded', 'true');
}

function dongBoLoc() {
    boLoc.classList.remove('dang-mo');
    nutLoc.setAttribute('aria-expanded', 'false');
}

if (boLoc && nutLoc) {
    nutLoc.addEventListener('click', moBoLoc);

    nutDongLoc.addEventListener('click', dongBoLoc);

    boLoc.addEventListener('click', function (event) {
        if (event.target === boLoc) {
            dongBoLoc();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            dongBoLoc();
        }
    });
}


    document.querySelectorAll('[data-mo-modal]').forEach(function (nut) {
        nut.addEventListener('click', function () {
            const modalSua = document.getElementById(nut.getAttribute('data-mo-modal'));
            if (modalSua) {
                modalSua.classList.add('dang-mo');
                const inputDau = modalSua.querySelector('input[name="ten_su_kien"]');
                if (inputDau) inputDau.focus();
            }
        });
    });

    document.querySelectorAll('.modal-sua [data-dong-modal]').forEach(function (nut) {
        nut.addEventListener('click', function () {
            const modalSua = nut.closest('.modal-sua');
            if (modalSua) modalSua.classList.remove('dang-mo');
        });
    });

    document.querySelectorAll('.modal-sua').forEach(function (modalSua) {
        modalSua.addEventListener('click', function (event) {
            if (event.target === modalSua) modalSua.classList.remove('dang-mo');
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-sua.dang-mo').forEach(function (modalSua) {
                modalSua.classList.remove('dang-mo');
            });
        }
    });
})();
</script>
</body>
</html>