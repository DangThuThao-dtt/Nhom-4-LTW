<?php
session_start();

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';

if (!isset($_SESSION['nguoi_dung']['id'])) {
    exit('Ban can dang nhap de xem danh sach dang ky.');
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];

$sql = "
    SELECT
        dk.id AS dang_ky_id,
        dk.thoi_gian_dang_ky,
        dk.trang_thai AS trang_thai_dang_ky,
        sk.id AS su_kien_id,
        sk.ten_su_kien,
        sk.dia_diem,
        sk.thoi_gian_bat_dau,
        clb.ten_cau_lac_bo
    FROM dang_ky dk
    JOIN su_kien sk ON dk.su_kien_id = sk.id
    JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
    WHERE dk.nguoi_dung_id = :nguoi_dung_id
    ORDER BY dk.thoi_gian_dang_ky DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
$danhSachDangKy = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký của tôi</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; margin: 0; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }

        /* ============ NÚT HỦY ĐÃ ĐƯỢC BO TRÒN ============ */
        .btn-huy {
            background: #dc3545; color: white; border: none; 
            padding: 10px 24px; cursor: pointer; 
            border-radius: 50px; /* Bo tròn hoàn toàn */
            font-size: 14px; font-weight: bold;
            min-width: 120px; text-align: center;
            transition: all 0.3s ease;
        }
        .btn-huy:hover:not(:disabled) { 
            background: #c82333; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); 
        }
        .btn-huy:disabled { 
            background: #ccc; cursor: not-allowed; 
        }

        /* ============ TRẠNG THÁI CHỈ HIỆN CHỮ ============ */
        .trang-thai-chu { 
            font-weight: bold; 
            font-size: 14px;
        }
        .trang-thai-chu.da-dang-ky { color: #28a745; } /* Màu xanh lá */
        .trang-thai-chu.da-huy { color: #dc3545; }     /* Màu đỏ */
        .trang-thai-chu.da-tham-gia { color: #007bff; } /* Màu xanh dương */

        .message { 
            padding: 12px; margin: 10px 0; border-radius: 8px; 
            display: none; font-weight: bold;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .empty { text-align: center; padding: 40px; background: white; border-radius: 12px; color: #666; }

        /* ============ MODAL XÁC NHẬN ============ */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 12px;
            padding: 20px 24px;
            max-width: 340px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .modal-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .modal-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 18px;
            line-height: 1.4;
        }

        .modal-buttons {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        /* ============ NÚT TRONG MODAL ĐÃ BO TRÒN ============ */
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 50px; /* Bo tròn */
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-btn-cancel {
            background: #e0e0e0;
            color: #333;
        }
        .modal-btn-cancel:hover {
            background: #d0d0d0;
        }

        .modal-btn-confirm {
            background: #dc3545;
            color: white;
        }
        .modal-btn-confirm:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <h1> Danh sách đăng ký của tôi</h1>

    <div id="thongBao" class="message"></div>

    <?php if (empty($danhSachDangKy)): ?>
        <div class="empty">
            <p>Bạn chưa đăng ký sự kiện nào.</p>
            <a href="../danh_sach_su_kien.php">← Xem danh sách sự kiện</a>
        </div>
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
                                } elseif ($dk['trang_thai_dang_ky'] === 'da_tham_gia') {
                                    $trangThaiClass = 'da-tham-gia';
                                    $trangThaiText = 'Đã tham gia';
                                }
                            ?>
                            <!-- Chỉ hiển thị chữ, không có nền/viền -->
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

    <p style="margin-top: 20px;">
        <a href="../danh_sach_su_kien.php">← Quay lại danh sách sự kiện</a>
    </p>

    <!-- ============ MODAL XÁC NHẬN HỦY ============ -->
    <div id="modalXacNhan" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-title">Xác nhận hủy đăng ký</div>
            <div class="modal-text" id="modalText">
                Bạn có chắc chắn muốn hủy đăng ký sự kiện này không?
            </div>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="dongModal()">Không</button>
                <button class="modal-btn modal-btn-confirm" onclick="xacNhanHuy()">Hủy đăng ký</button>
            </div>
        </div>
    </div>

    <script>
    let dangKyIdCanHuy = null;
    let buttonDangHuy = null;

    // Hiển thị thông báo
    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');
        el.textContent = noiDung;
        el.className = 'message ' + loai;
        el.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { el.style.display = 'none'; }, 5000);
    }

    // Mở modal xác nhận
    function moModal(dangKyId, tenSuKien, btn) {
        dangKyIdCanHuy = dangKyId;
        buttonDangHuy = btn;
        
        document.getElementById('modalText').innerHTML = 
            'Bạn có chắc chắn muốn hủy đăng ký sự kiện<br><strong>"' + tenSuKien + '"</strong>?';
        
        document.getElementById('modalXacNhan').classList.add('show');
    }

    // Đóng modal
    function dongModal() {
        document.getElementById('modalXacNhan').classList.remove('show');
        dangKyIdCanHuy = null;
        buttonDangHuy = null;
    }

    // Xác nhận hủy
    function xacNhanHuy() {
        if (!dangKyIdCanHuy) return;

        const dangKyId = dangKyIdCanHuy;
        const btn = buttonDangHuy;
        const row = document.getElementById('row-' + dangKyId);

        // Đóng modal
        document.getElementById('modalXacNhan').classList.remove('show');

        // Đổi nút thành "Đang xử lý..."
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';

        // Gọi API
        fetch('../api/dang_ky.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=huy_dang_ky&dang_ky_id=${dangKyId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                hienThiThongBao(data.message, 'success');
                
                // Đổi trạng thái thành chữ "Đã hủy" (màu đỏ)
                const tdTrangThai = row.querySelector('td:nth-child(6)');
                tdTrangThai.innerHTML = '<span class="trang-thai-chu da-huy">Đã hủy</span>';
                
                // Đổi cột hành động
                const tdHanhDong = row.querySelector('td:nth-child(7)');
                tdHanhDong.innerHTML = '<span>—</span>';
            } else {
                hienThiThongBao(data.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Hủy đăng ký';
            }
        })
        .catch(err => {
            console.error(err);
            hienThiThongBao('Lỗi kết nối server', 'error');
            btn.disabled = false;
            btn.textContent = 'Hủy đăng ký';
        })
        .finally(() => {
            dangKyIdCanHuy = null;
            buttonDangHuy = null;
        });
    }

    // Gắn sự kiện click cho tất cả nút Hủy
    document.querySelectorAll('.btn-huy').forEach(button => {
        button.addEventListener('click', function() {
            const dangKyId = this.getAttribute('data-id');
            const tenSuKien = this.getAttribute('data-ten');
            moModal(dangKyId, tenSuKien, this);
        });
    });

    // Đóng modal khi click ra ngoài
    document.getElementById('modalXacNhan').addEventListener('click', function(e) {
        if (e.target === this) {
            dongModal();
        }
    });

    // Đóng modal khi bấm ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dongModal();
        }
    });
    </script>
</body>
</html>