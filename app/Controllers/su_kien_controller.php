<?php

require_once __DIR__ . '/../Models/su_kien.php';

class SuKienController
{
    private SuKien $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new SuKien($pdo);
    }
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
    // 4. Lấy danh sách sự kiện theo các điều kiện lọc
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
    public function trangChu(): void
    {
        $danhSachSuKien = $this->model->layDanhSach();
        $danhSachCauLacBo = $this->model->layDanhSachCauLacBo();

        require __DIR__ . '/../Views/thanh_vien/trang_chu.php';
    }
}