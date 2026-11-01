<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sự kiện của tôi</title>
    <style>
        /* CSS TẠM ĐỂ CHẠY THỬ */
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; margin: 0; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .search-form { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-input { padding: 10px 15px; border: 1px solid #ccc; border-radius: 50px; width: 350px; outline: none; }
        .btn-search { padding: 10px 24px; background: #007bff; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; }
        .btn-clear { padding: 10px 20px; background: #e0e0e0; color: #333; text-decoration: none; border-radius: 50px; font-weight: bold; }
        .btn-huy { background: #dc3545; color: white; border: none; padding: 10px 24px; cursor: pointer; border-radius: 50px; font-weight: bold; }
        .trang-thai-chu { font-weight: bold; font-size: 14px; }
        .trang-thai-chu.da-dang-ky { color: #28a745; }
        .trang-thai-chu.da-huy { color: #dc3545; }
        .message { padding: 12px; margin: 10px 0; border-radius: 8px; display: none; font-weight: bold; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        .empty { text-align: center; padding: 40px; background: white; border-radius: 12px; color: #666; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 9999; justify-content: center; align-items: center; }
        .modal-overlay.show { display: flex; }
        .modal-box { background: white; border-radius: 12px; padding: 20px 24px; max-width: 340px; width: 90%; }
        .modal-buttons { display: flex; gap: 8px; justify-content: flex-end; margin-top: 15px;}
        .modal-btn { padding: 10px 20px; border: none; border-radius: 50px; font-weight: bold; cursor: pointer; }
        .modal-btn-cancel { background: #e0e0e0; color: #333; }
        .modal-btn-confirm { background: #dc3545; color: white; }

        /* CSS CHO HỘP THÔNG BÁO NHẮC NHỞ */
        .thong-bao-nhac-nho {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            border-left: 5px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .thong-bao-nhac-nho.danger { background-color: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .thong-bao-nhac-nho.warning { background-color: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .thong-bao-nhac-nho.success { background-color: #d4edda; color: #155724; border-left-color: #28a745; }
    </style>
</head>
<body>
    <h1>📋 Sự kiện của tôi</h1>
    
    <!-- HỘP THÔNG BÁO NHẮC NHỞ -->
    <?php if (!empty($thongBaoNhacNho)): ?>
        <div class="thong-bao-nhac-nho <?= $loaiThongBao ?>">
            <?= $thongBaoNhacNho ?>
        </div>
    <?php endif; ?>

    <div id="thongBao" class="message"></div>

    <!-- Form tìm kiếm -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="tu_khoa" class="search-input" placeholder="Tìm theo tên sự kiện hoặc CLB..." value="<?= htmlspecialchars($tuKhoa ?? '') ?>">
        <button type="submit" class="btn-search">Tìm kiếm</button>
        <?php if (!empty($tuKhoa)): ?>
            <a href="dang_ky_cua_toi.php" class="btn-clear">✕ Xóa lọc</a>
        <?php endif; ?>
    </form>

    <!-- Hiển thị bảng -->
    <?php if (empty($danhSachDangKy)): ?>
        <div class="empty"><p>Không tìm thấy sự kiện nào.</p></div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tên sự kiện</th>
                    <th>Câu lạc bộ</th>
                    <th>Địa điểm</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachDangKy as $dk): ?>
                    <tr id="row-<?= $dk['dang_ky_id'] ?>">
                        <td><?= htmlspecialchars($dk['ten_su_kien']) ?></td>
                        <td><?= htmlspecialchars($dk['ten_cau_lac_bo']) ?></td>
                        <td><?= htmlspecialchars($dk['dia_diem'] ?? '') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($dk['thoi_gian_bat_dau'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($dk['thoi_gian_dang_ky'])) ?></td>
                        <td>
                            <?php 
                                $trangThaiClass = 'da-dang-ky';
                                $trangThaiText = 'Đã đăng ký';
                                if ($dk['trang_thai_dang_ky'] === 'da_huy') {
                                    $trangThaiClass = 'da-huy';
                                    $trangThaiText = 'Đã hủy';
                                }
                            ?>
                            <span class="trang-thai-chu <?= $trangThaiClass ?>"><?= $trangThaiText ?></span>
                        </td>
                        <td>
                            <?php if ($dk['trang_thai_dang_ky'] === 'da_dang_ky'): ?>
                                <button class="btn-huy" 
                                        data-id="<?= $dk['dang_ky_id'] ?>"
                                        data-ten="<?= htmlspecialchars($dk['ten_su_kien']) ?>">
                                    Hủy đăng ký
                                </button>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Modal xác nhận -->
    <div id="modalXacNhan" class="modal-overlay">
        <div class="modal-box">
            <h3>Xác nhận hủy đăng ký</h3>
            <p id="modalText">Bạn có chắc chắn muốn hủy?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="dongModal()">Không</button>
                <button class="modal-btn modal-btn-confirm" onclick="xacNhanHuy()">Hủy đăng ký</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
    let dangKyIdCanHuy = null;
    let buttonDangHuy = null;

    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');
        el.textContent = noiDung;
        el.className = 'message ' + loai;
        el.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { el.style.display = 'none'; }, 5000);
    }

    function moModal(dangKyId, tenSuKien, btn) {
        dangKyIdCanHuy = dangKyId;
        buttonDangHuy = btn;
        document.getElementById('modalText').innerHTML = 'Hủy đăng ký sự kiện: <strong>' + tenSuKien + '</strong>?';
        document.getElementById('modalXacNhan').classList.add('show');
    }

    function dongModal() {
        document.getElementById('modalXacNhan').classList.remove('show');
        dangKyIdCanHuy = null;
        buttonDangHuy = null;
    }

    function xacNhanHuy() {
        if (!dangKyIdCanHuy) return;
        const dangKyId = dangKyIdCanHuy;
        const btn = buttonDangHuy;
        const row = document.getElementById('row-' + dangKyId);
        
        document.getElementById('modalXacNhan').classList.remove('show');
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';

        // Gọi API Hủy
        fetch('../api/huy_dang_ky.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `dang_ky_id=${dangKyId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                hienThiThongBao(data.message, 'success');
                row.querySelector('td:nth-child(6)').innerHTML = '<span class="trang-thai-chu da-huy">Đã hủy</span>';
                row.querySelector('td:nth-child(7)').innerHTML = '<span>—</span>';
            } else {
                hienThiThongBao(data.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Hủy đăng ký';
            }
        })
        .catch(err => {
            hienThiThongBao('Lỗi kết nối server', 'error');
            btn.disabled = false;
            btn.textContent = 'Hủy đăng ký';
        });
    }

    document.querySelectorAll('.btn-huy').forEach(button => {
        button.addEventListener('click', function() {
            moModal(this.getAttribute('data-id'), this.getAttribute('data-ten'), this);
        });
    });
    </script>
</body>
</html>