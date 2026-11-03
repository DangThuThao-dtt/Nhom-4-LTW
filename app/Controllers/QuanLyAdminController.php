
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Repositories/CauLacBoRepository.php';

class QuanLyAdminController
{
    // Đường dẫn gốc của dự án trên XAMPP
    private const DUONG_DAN_GOC = '/Nhom-4-LTW/public';

    // Trang Dashboard Admin
    public function dashboard(): void
    {
        $repository = new CauLacBoRepository($GLOBALS['pdo']);

        // Lấy số liệu thống kê từ Repository
        $thongKe = $repository->layThongKeDashboard();

        $tongCauLacBo = $thongKe['tongCauLacBo'];
        $tongTaiKhoan = $thongKe['tongTaiKhoan'];
        $tongSuKien = $thongKe['tongSuKien'];

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // Danh sách câu lạc bộ
    public function danhSachCauLacBo(): void
    {
        $repository = new CauLacBoRepository($GLOBALS['pdo']);

        $danhSach = $repository->layDanhSach();

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    // Thêm câu lạc bộ
    public function themCauLacBo(): void
    {
        $repository = new CauLacBoRepository($GLOBALS['pdo']);

        $loi = '';
        $ten = '';
        $moTa = '';
        $nguoiQuanLyId = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
            $moTa = trim($_POST['mo_ta'] ?? '');
            $nguoiQuanLyId = trim($_POST['nguoi_quan_ly_id'] ?? '');

            if ($ten === '') {
                $loi = 'Vui lòng nhập tên câu lạc bộ.';
            } elseif (
                $nguoiQuanLyId !== '' &&
                (!ctype_digit($nguoiQuanLyId) || (int) $nguoiQuanLyId < 1)
            ) {
                $loi = 'Mã người quản lý không hợp lệ.';
            } else {
                try {
                    $thanhCong = $repository->them(
                        $ten,
                        $moTa !== '' ? $moTa : null,
                        $nguoiQuanLyId !== ''
                            ? (int) $nguoiQuanLyId
                            : null
                    );

                    if ($thanhCong) {
                        header(
                            'Location: ' . self::DUONG_DAN_GOC
                            . '/?page=cau-lac-bo&them=thanh-cong'
                        );
                        exit;
                    }

                    $loi = 'Không thể thêm câu lạc bộ.';
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $loi = 'Không thể thêm câu lạc bộ. Hãy kiểm tra dữ liệu và mã người quản lý.';
                }
            }
        }

        require __DIR__ . '/../Views/admin/them_cau_lac_bo.php';
    }

    // Sửa thông tin câu lạc bộ
    public function suaCauLacBo(): void
    {
        $repository = new CauLacBoRepository($GLOBALS['pdo']);

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID câu lạc bộ không hợp lệ.');
        }

        // Lấy thông tin câu lạc bộ từ Repository
        $clb = $repository->layTheoId($id);

        if (!$clb) {
            http_response_code(404);
            exit('Không tìm thấy câu lạc bộ.');
        }

        $loi = '';
        $ten = $clb['ten_cau_lac_bo'];
        $moTa = $clb['mo_ta'] ?? '';
        $nguoiQuanLyId = $clb['nguoi_quan_ly_id'] ?? '';

        // Xử lý khi người dùng bấm Lưu thay đổi
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
            $moTa = trim($_POST['mo_ta'] ?? '');
            $nguoiQuanLyId = trim($_POST['nguoi_quan_ly_id'] ?? '');

            if ($ten === '') {
                $loi = 'Vui lòng nhập tên câu lạc bộ.';
            } elseif (
                $nguoiQuanLyId !== '' &&
                (!ctype_digit($nguoiQuanLyId) || (int) $nguoiQuanLyId < 1)
            ) {
                $loi = 'Mã người quản lý không hợp lệ.';
            } else {
                try {
                    $thanhCong = $repository->capNhat(
                        $id,
                        $ten,
                        $moTa !== '' ? $moTa : null,
                        $nguoiQuanLyId !== ''
                            ? (int) $nguoiQuanLyId
                            : null
                    );

                    if ($thanhCong) {
                        header(
                            'Location: ' . self::DUONG_DAN_GOC
                            . '/?page=cau-lac-bo&sua=thanh-cong'
                        );
                        exit;
                    }

                    $loi = 'Không thể cập nhật câu lạc bộ.';
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $loi = 'Không thể cập nhật. Hãy kiểm tra mã người quản lý và dữ liệu liên quan.';
                }
            }
        }

        require __DIR__ . '/../Views/admin/sua_cau_lac_bo.php';
    }

    // Xóa câu lạc bộ
    public function xoaCauLacBo(): void
    {
        // Chỉ chấp nhận yêu cầu POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        // Lấy ID câu lạc bộ từ form
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            header(
                'Location: ' . self::DUONG_DAN_GOC
                . '/?page=cau-lac-bo&xoa=loi-id'
            );
            exit;
        }

        $repository = new CauLacBoRepository($GLOBALS['pdo']);

        try {
            $thanhCong = $repository->xoa($id);

            if ($thanhCong) {
                header(
                    'Location: ' . self::DUONG_DAN_GOC
                    . '/?page=cau-lac-bo&xoa=thanh-cong'
                );
                exit;
            }

            header(
                'Location: ' . self::DUONG_DAN_GOC
                . '/?page=cau-lac-bo&xoa=that-bai'
            );
            exit;
        } catch (PDOException $e) {
            // Ghi chi tiết lỗi vào log
            error_log($e->getMessage());

            header(
                'Location: ' . self::DUONG_DAN_GOC
                . '/?page=cau-lac-bo&xoa=bi-rang-buoc'
            );
            exit;
        }
    }
}
