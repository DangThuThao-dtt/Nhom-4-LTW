<?php
session_start();
require_once __DIR__ . '/../app/Core/ket_noi_csdl.php';

// Lấy danh sách sự kiện đang mở (cong_khai)
$sql = "
    SELECT 
        sk.id, sk.ten_su_kien, sk.dia_diem, sk.mo_ta,
        sk.thoi_gian_bat_dau, sk.thoi_gian_ket_thuc,
        sk.so_luong_toi_da, sk.trang_thai,
        clb.ten_cau_lac_bo,
        (SELECT COUNT(*) FROM dang_ky dk 
         WHERE dk.su_kien_id = sk.id 
         AND dk.trang_thai IN ('da_dang_ky', 'da_tham_gia')) AS so_luong_da_dang_ky
    FROM su_kien sk
    LEFT JOIN cau_lac_bo clb ON sk.cau_lac_bo_id = clb.id
    WHERE sk.trang_thai = 'cong_khai'
    ORDER BY sk.thoi_gian_bat_dau ASC
";
$stmt = $pdo->query($sql);
$danhSachSuKien = $stmt->fetchAll();

// Lấy danh sách sự kiện user đã đăng ký
$daDangKy = [];
if (isset($_SESSION['nguoi_dung']['id'])) {
    $stmt = $pdo->prepare("
        SELECT su_kien_id FROM dang_ky 
        WHERE nguoi_dung_id = ? 
        AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
    ");
    $stmt->execute([$_SESSION['nguoi_dung']['id']]);
    $daDangKy = array_column($stmt->fetchAll(), 'su_kien_id');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sự kiện</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn-dang-ky {
            background: #28a745; color: white; border: none; 
            padding: 8px 16px; cursor: pointer; border-radius: 4px;
            font-size: 14px;
        }
        .btn-dang-ky:hover:not(:disabled) { background: #218838; }
        .btn-dang-ky:disabled { background: #ccc; cursor: not-allowed; }
        .btn-dang-ky.da-dang-ky { background: #6c757d; }
        .message { 
            padding: 12px; margin: 10px 0; border-radius: 4px; 
            display: none; font-weight: bold;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge.con-cho { background: #d4edda; color: #155724; }
        .badge.het-cho { background: #f8d7da; color: #721c24; }
                /* Giữ số chỗ và nhãn nằm trên 1 dòng */
        td:nth-child(6) {
            white-space: nowrap;
            min-width: 120px;
        }
    </style>
</head>
<body>
    <h1> Danh sách sự kiện </h1>

    <div id="thongBao" class="message"></div>

    <?php if (empty($danhSachSuKien)): ?>
        <p>Hiện chưa có sự kiện nào đang mở.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tên sự kiện</th>
                    <th>CLB</th>
                    <th>Địa điểm</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Số chỗ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachSuKien as $sk): 
                    $conCho = $sk['so_luong_da_dang_ky'] < $sk['so_luong_toi_da'];
                    $chuaBatDau = date('Y-m-d H:i:s') < $sk['thoi_gian_bat_dau'];
                    $daDangKyRoi = in_array($sk['id'], $daDangKy);
                ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($sk['ten_su_kien']) ?></strong>
                            <?php if ($sk['mo_ta']): ?>
                                <br><small><?= htmlspecialchars(mb_substr($sk['mo_ta'], 0, 80)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($sk['ten_cau_lac_bo'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($sk['dia_diem'] ?? 'Chưa cập nhật') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sk['thoi_gian_bat_dau'])) ?></td>
                        <td><?= $sk['thoi_gian_ket_thuc'] ? date('d/m/Y H:i', strtotime($sk['thoi_gian_ket_thuc'])) : '—' ?></td>
                        <td>
                            <?= $sk['so_luong_da_dang_ky'] ?>/<?= $sk['so_luong_toi_da'] ?>
                            <?php if ($conCho): ?>
                                <span class="badge con-cho">Còn chỗ</span>
                            <?php else: ?>
                                <span class="badge het-cho">Hết chỗ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($daDangKyRoi): ?>
                                <button class="btn-dang-ky da-dang-ky" disabled>✓ Đã đăng ký</button>
                            <?php elseif (!$conCho): ?>
                                <button class="btn-dang-ky" disabled>Hết chỗ</button>
                            <?php elseif (!$chuaBatDau): ?>
                                <button class="btn-dang-ky" disabled>Đã bắt đầu</button>
                            <?php elseif (!isset($_SESSION['nguoi_dung']['id'])): ?>
                                <a href="dang_nhap.php"><button class="btn-dang-ky">Đăng nhập để ĐK</button></a>
                            <?php else: ?>
                                <button class="btn-dang-ky" data-id="<?= $sk['id'] ?>">Đăng ký</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <script>
    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');
        el.textContent = noiDung;
        el.className = 'message ' + loai;
        el.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { el.style.display = 'none'; }, 5000);
    }

    document.querySelectorAll('.btn-dang-ky[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            const suKienId = this.getAttribute('data-id');
            const btn = this;

            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            fetch('api/dang_ky.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'su_kien_id=' + suKienId
            })
            .then(res => res.json())
            .then(data => {
             if (data.status === 'success') {
    hienThiThongBao(data.message, 'success');
    btn.textContent = '✓ Đã đăng ký';
    btn.classList.add('da-dang-ky');

    const tdSoCho = btn.closest('tr').querySelector('td:nth-child(6)');
    if (tdSoCho) {
        // Lấy chính xác con số đầu tiên (bỏ qua chữ "Còn chỗ")
        const text = tdSoCho.textContent.trim();
        const parts = text.split('/');
        
        // Tách lấy số hiện tại và số tối đa
        const hienTai = parseInt(parts[0].trim()); 
        const toiDa = parseInt(parts[1].trim().split(' ')[0]); 
        
        const soMoi = hienTai + 1;
        
        const badge = soMoi >= toiDa 
            ? '<span class="badge het-cho">Hết chỗ</span>' 
            : '<span class="badge con-cho">Còn chỗ</span>';
            
        tdSoCho.innerHTML = soMoi + '/' + toiDa + ' ' + badge;
    }
}else {
                    hienThiThongBao(data.message, 'error');
                    btn.disabled = false;
                    btn.textContent = 'Đăng ký';
                }
            })
            .catch(err => {
                console.error(err);
                hienThiThongBao('Lỗi kết nối server', 'error');
                btn.disabled = false;
                btn.textContent = 'Đăng ký';
            });
        });
    });
    </script>
</body>
</html>