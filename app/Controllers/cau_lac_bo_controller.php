<?php

require_once __DIR__ . '/../Models/thanh_vien/CauLacBoThanhVien.php';

class CauLacBoThanhVienController
{
    private CauLacBoThanhVien $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new CauLacBoThanhVien($pdo);
    }

    // ==========================================
    // HIỂN THỊ DANH SÁCH CÂU LẠC BỘ
    // ==========================================
    public function danhSach(): void
    {
        // Nhận từ khóa tìm kiếm
        $tuKhoa = trim($_GET['tu_khoa'] ?? '');
        $tuKhoa = mb_substr($tuKhoa, 0, 100);

        // Lấy danh sách câu lạc bộ
        $danhSachCauLacBo = $this->model->layDanhSach($tuKhoa);

        // Lấy thông tin người dùng hiện tại
        $nguoiDung = $_SESSION['nguoi_dung'] ?? null;

        // Danh sách CLB người dùng đã tham gia
        $clbDaThamGia = [];

        if ($nguoiDung && !empty($nguoiDung['id'])) {
            $clbDaThamGia = $this->model->layCLBDaThamGia(
                (int) $nguoiDung['id']
            );
        }

        // Hiển thị giao diện
        require __DIR__ . '/../Views/thanh_vien/danh_sach_clb.php';
    }

    // ==========================================
    // XỬ LÝ THAM GIA CÂU LẠC BỘ
    // ==========================================
    public function thamGia(): void
    {
        // Chỉ chấp nhận phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=cau-lac-bo'
            );
            exit;
        }

        $nguoiDung = $_SESSION['nguoi_dung'] ?? null;

        // Kiểm tra đăng nhập
        if (!$nguoiDung || empty($nguoiDung['id'])) {
            header('Location: /Nhom-4-LTW/public/dang_nhap.php');
            exit;
        }

        // Chỉ thành viên mới được tham gia CLB
        if (($nguoiDung['vai_tro'] ?? '') !== 'thanh_vien') {
            $_SESSION['thong_bao_clb'] =
                'Chỉ tài khoản thành viên mới có thể tham gia câu lạc bộ.';

            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=cau-lac-bo'
            );
            exit;
        }

        // Nhận ID câu lạc bộ
        $cauLacBoId = filter_input(
            INPUT_POST,
            'cau_lac_bo_id',
            FILTER_VALIDATE_INT
        );

        if (!$cauLacBoId || $cauLacBoId <= 0) {
            $_SESSION['thong_bao_clb'] = 'Câu lạc bộ không hợp lệ.';

            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=cau-lac-bo'
            );
            exit;
        }

        $nguoiDungId = (int) $nguoiDung['id'];

        // Kiểm tra đã tham gia hay chưa
        if ($this->model->daThamGia($cauLacBoId, $nguoiDungId)) {
            $_SESSION['thong_bao_clb'] =
                'Bạn đã tham gia câu lạc bộ này rồi.';
        } else {
            try {
                $this->model->thamGia($cauLacBoId, $nguoiDungId);

                $_SESSION['thong_bao_clb'] =
                    'Tham gia câu lạc bộ thành công!';
            } catch (PDOException $e) {
                $_SESSION['thong_bao_clb'] =
                    'Không thể tham gia câu lạc bộ. Vui lòng thử lại.';
            }
        }

        header(
            'Location: /Nhom-4-LTW/public/index.php?trang=cau-lac-bo'
        );
        exit;
    }

    // ==========================================
    // XỬ LÝ THÀNH VIÊN RỜI CÂU LẠC BỘ
    // ==========================================
    public function roiCLB(): void
    {
        // Chỉ chấp nhận phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi'
            );
            exit;
        }

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['nguoi_dung']['id'])) {
            header('Location: /Nhom-4-LTW/public/dang_nhap.php');
            exit;
        }

        // Chỉ tài khoản thành viên được rời CLB
        if (
            ($_SESSION['nguoi_dung']['vai_tro'] ?? '')
            !== 'thanh_vien'
        ) {
            $_SESSION['thong_bao_clb'] =
                'Bạn không có quyền thực hiện chức năng này.';

            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi'
            );
            exit;
        }

        $nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];

        // Nhận ID câu lạc bộ
        $cauLacBoId = filter_input(
            INPUT_POST,
            'cau_lac_bo_id',
            FILTER_VALIDATE_INT
        );

        if (!$cauLacBoId || $cauLacBoId <= 0) {
            $_SESSION['thong_bao_clb'] =
                'Câu lạc bộ không hợp lệ.';

            header(
                'Location: /Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi'
            );
            exit;
        }

        // Thực hiện rời câu lạc bộ
        $thanhCong = $this->model->roiCLB(
            (int) $cauLacBoId,
            $nguoiDungId
        );

        $_SESSION['thong_bao_clb'] = $thanhCong
            ? 'Bạn đã rời câu lạc bộ thành công.'
            : 'Bạn chưa tham gia câu lạc bộ này.';

        header(
            'Location: /Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi'
        );
        exit;
    }
}