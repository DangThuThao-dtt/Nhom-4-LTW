<?php
// app/Controllers/DangKyController.php

require_once __DIR__ . '/../Repositories/DangKyRepository.php';

class DangKyController {
    private $repo;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->repo = new DangKyRepository($pdo);
    }

    public function index($nguoiDungId, $tuKhoa) {
        $danhSachDangKy = $this->repo->getDanhSachDangKy($nguoiDungId, $tuKhoa);
        
        $thongBaoNhacNho = '';
        $loaiThongBao = ''; 

        if (!empty($danhSachDangKy)) {
            $now = time();
            $dsSapToi = [];
            $dsSapKetThuc = [];

            foreach ($danhSachDangKy as $dk) {
                if ($dk['trang_thai_dang_ky'] !== 'da_dang_ky') continue;

                $batDau = strtotime($dk['thoi_gian_bat_dau']);
                $ketThuc = strtotime($dk['thoi_gian_ket_thuc'] ?? $dk['thoi_gian_bat_dau']);
                
                if ($now < $batDau && ($batDau - $now) <= (3 * 86400)) {
                    $dsSapToi[] = $dk['ten_su_kien'];
                }
                if ($now >= $batDau && $now <= $ketThuc && ($ketThuc - $now) <= 86400) {
                    $dsSapKetThuc[] = $dk['ten_su_kien'];
                }
            }

            if (!empty($dsSapKetThuc)) {
                $tenCacSuKien = implode(', ', $dsSapKetThuc);
                $thongBaoNhacNho = "⏳ Sự kiện <strong>$tenCacSuKien</strong> sắp kết thúc trong 24 giờ tới!";
                $loaiThongBao = 'danger'; 
            } elseif (!empty($dsSapToi)) {
                $tenCacSuKien = implode(', ', $dsSapToi);
                $thongBaoNhacNho = "🔥 Sự kiện <strong>$tenCacSuKien</strong> sắp diễn ra trong 3 ngày tới!";
                $loaiThongBao = 'warning'; 
            } else {
                $thongBaoNhacNho = "✅ Bạn không có sự kiện nào sắp diễn ra trong thời gian tới.";
                $loaiThongBao = 'success'; 
            }
        }
        
        return [
            'danhSachDangKy' => $danhSachDangKy,
            'thongBaoNhacNho' => $thongBaoNhacNho,
            'loaiThongBao' => $loaiThongBao,
            'tuKhoa' => $tuKhoa
        ];
    }

    public function dangKy($nguoiDungId, $suKienId) {
        $suKien = $this->repo->getSuKienById($suKienId);
        if (!$suKien) return ['status' => 'error', 'message' => 'Sự kiện không tồn tại.'];

        $now = $this->pdo->query("SELECT NOW()")->fetchColumn();
        
        if ($suKien['trang_thai'] !== 'cong_khai') {
            return ['status' => 'error', 'message' => 'Sự kiện chưa mở hoặc đã đóng đăng ký.'];
        }

        if (!empty($suKien['han_dang_ky'])) {
            if ($now > $suKien['han_dang_ky']) {
                return ['status' => 'error', 'message' => 'Đã hết hạn đăng ký sự kiện này.'];
            }
        } else {
            $soGiayConLai = strtotime($suKien['thoi_gian_bat_dau']) - strtotime($now);
            if ($soGiayConLai < 86400) {
                return ['status' => 'error', 'message' => 'Đã quá hạn đăng ký (phải đăng ký trước 24 giờ so với thời gian bắt đầu).'];
            }
        }

        $soLuongHienTai = $this->repo->countDangKyBySuKien($suKienId);
        if ($soLuongHienTai >= $suKien['so_luong_toi_da']) {
            return ['status' => 'error', 'message' => 'Sự kiện đã hết chỗ.'];
        }

        if ($this->repo->checkDaDangKy($nguoiDungId, $suKienId)) {
            return ['status' => 'error', 'message' => 'Bạn đã đăng ký sự kiện này rồi.'];
        }

        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("SELECT id FROM dang_ky WHERE nguoi_dung_id = ? AND su_kien_id = ? AND trang_thai = 'da_huy'");
            $stmt->execute([$nguoiDungId, $suKienId]);
            $daHuy = $stmt->fetch();

            if ($daHuy) {
                $this->repo->restoreDangKy($daHuy['id']);
            } else {
                $this->repo->insertDangKy($nguoiDungId, $suKienId);
            }

            $this->pdo->commit();
            return [
                'status' => 'success',
                'message' => 'Đăng ký sự kiện thành công!',
                'so_luong_con_lai' => $suKien['so_luong_toi_da'] - $soLuongHienTai - 1
            ];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    public function huyDangKy($nguoiDungId, $dangKyId) {
        $dangKy = $this->repo->getDangKyById($dangKyId, $nguoiDungId);
        if (!$dangKy) return ['status' => 'error', 'message' => 'Không tìm thấy đăng ký hoặc bạn không có quyền hủy.'];

        if ($dangKy['trang_thai'] !== 'da_dang_ky') {
            return ['status' => 'error', 'message' => 'Đăng ký này không thể hủy (đã hủy hoặc đã tham gia).'];
        }

        $now = $this->pdo->query("SELECT NOW()")->fetchColumn();
        $soGiayConLai = strtotime($dangKy['thoi_gian_bat_dau']) - strtotime($now);

        if ($soGiayConLai < 86400) {
            return ['status' => 'error', 'message' => 'Đã quá hạn hủy đăng ký (phải hủy trước 24 giờ so với thời gian bắt đầu).'];
        }

        try {
            $this->pdo->beginTransaction();
            $this->repo->huyDangKy($dangKyId);
            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Hủy đăng ký thành công!'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }
}
?>