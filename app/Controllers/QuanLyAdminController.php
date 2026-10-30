
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Models/CauLacBo.php';

class QuanLyAdminController
{
    // Đường dẫn gốc của dự án trên XAMPP
    private const DUONG_DAN_GOC = '/Nhom-4-LTW/public';

    // Trang Dashboard Admin
    public function dashboard(): void
    {
        $pdo = $GLOBALS['pdo'];

        $stmt = $pdo->query("SELECT COUNT(*) FROM cau_lac_bo");
        $tongCauLacBo = (int) $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM nguoi_dung");
        $tongTaiKhoan = (int) $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM su_kien");
        $tongSuKien = (int) $stmt->fetchColumn();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // Danh sách câu lạc bộ
    public function danhSachCauLacBo(): void
    {
        $model = new CauLacBo($GLOBALS['pdo']);
        $danhSach = $model->layDanhSach();

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    // Thêm câu lạc bộ
    public function themCauLacBo(): void
    {
        $model = new CauLacBo($GLOBALS['pdo']);

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
                    $thanhCong = $model->them(
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
        $pdo = $GLOBALS['pdo'];
        $model = new CauLacBo($pdo);

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID câu lạc bộ không hợp lệ.');
        }

        // Lấy thông tin câu lạc bộ cần sửa
        $stmt = $pdo->prepare(
            "SELECT id, ten_cau_lac_bo, mo_ta, nguoi_quan_ly_id
             FROM cau_lac_bo
             WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $clb = $stmt->fetch();

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
                    $thanhCong = $model->capNhat(
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

        $model = new CauLacBo($GLOBALS['pdo']);

        try {
            $thanhCong = $model->xoa($id);

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
            // Ghi chi tiết lỗi vào log, không hiển thị lỗi SQL cho người dùng
            error_log($e->getMessage());

            header(
                'Location: ' . self::DUONG_DAN_GOC
                . '/?page=cau-lac-bo&xoa=bi-rang-buoc'
            );
            exit;
        }
    }
}
