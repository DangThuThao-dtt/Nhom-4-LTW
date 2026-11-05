<?php

require_once __DIR__ . '/../Models/thanh_vien/su_kien.php';

class SuKienThanhVienController
{
    private SuKien $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new SuKien($pdo);
    }

    // ==========================================
    // DANH SÁCH SỰ KIỆN
    // ==========================================
    public function danhSach(): void
    {
        // 1. Nhận từ khóa tìm kiếm
        $tuKhoa = trim($_GET['tu_khoa'] ?? '');

        // 2. Nhận ID câu lạc bộ
        $cauLacBoId = filter_var(
            $_GET['cau_lac_bo_id'] ?? '0',
            FILTER_VALIDATE_INT
        );

        if ($cauLacBoId === false || $cauLacBoId < 0) {
            $cauLacBoId = 0;
        }

        // 3. Nhận ngày bắt đầu và ngày kết thúc
        $tuNgay = trim($_GET['tu_ngay'] ?? '');
        $denNgay = trim($_GET['den_ngay'] ?? '');

        // Kiểm tra định dạng ngày YYYY-MM-DD
        $ngayHopLe = static function (string $ngay): bool {
            if ($ngay === '') {
                return true;
            }

            $date = DateTime::createFromFormat('!Y-m-d', $ngay);

            return $date !== false
                && $date->format('Y-m-d') === $ngay;
        };

        if (!$ngayHopLe($tuNgay)) {
            $tuNgay = '';
        }

        if (!$ngayHopLe($denNgay)) {
            $denNgay = '';
        }

        // Nếu ngày bắt đầu lớn hơn ngày kết thúc,
        // bỏ điều kiện ngày để tránh kết quả không hợp lý
        if (
            $tuNgay !== ''
            && $denNgay !== ''
            && $tuNgay > $denNgay
        ) {
            $tuNgay = '';
            $denNgay = '';
        }

        // 4. Lấy danh sách sự kiện theo điều kiện lọc
        $danhSachSuKien = $this->model->layDanhSach(
            $tuKhoa,
            $cauLacBoId,
            $tuNgay,
            $denNgay
        );

        // 5. Lấy danh sách câu lạc bộ cho ô lọc
        $danhSachCauLacBo = $this->model->layDanhSachCauLacBo();

        // 6. Hiển thị giao diện
        require __DIR__ . '/../Views/thanh_vien/danh_sach_su_kien.php';
    }

    // ==========================================
    // CHI TIẾT SỰ KIỆN
    // ==========================================
    public function chiTiet(): void
    {
        $id = filter_var(
            $_GET['id'] ?? '',
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id < 1) {
            http_response_code(404);
            echo 'Không tìm thấy sự kiện.';
            return;
        }

        $suKien = $this->model->layChiTiet($id);

        if ($suKien === null) {
            http_response_code(404);
            echo 'Sự kiện không tồn tại hoặc không còn công khai.';
            return;
        }

        require __DIR__ . '/../Views/thanh_vien/chi_tiet_su_kien.php';
    }

    // ==========================================
    // TRANG CHỦ
    // ==========================================
    public function trangChu(): void
    {
        // Nhận từ khóa tìm kiếm từ Trang chủ
        $tuKhoa = trim($_GET['tu_khoa'] ?? '');

        // Giới hạn từ khóa tối đa 100 ký tự
        $tuKhoa = mb_substr($tuKhoa, 0, 100);

        // Lấy danh sách sự kiện theo từ khóa
        $danhSachSuKien = $this->model->layDanhSach(
            $tuKhoa
        );

        // Lấy danh sách câu lạc bộ
        $danhSachCauLacBo = $this->model->layDanhSachCauLacBo();

        // Hiển thị Trang chủ
        require __DIR__ . '/../Views/thanh_vien/trang_chu.php';
    }
}