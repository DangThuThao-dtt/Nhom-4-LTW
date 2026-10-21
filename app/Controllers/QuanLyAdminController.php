
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Models/CauLacBo.php';

class QuanLyAdminController
{
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

    // Danh sach cau lac bo
    public function danhSachCauLacBo(): void
    {
        $model = new CauLacBo($GLOBALS['pdo']);
        $danhSach = $model->layDanhSach();

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    // Them cau lac bo
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
                        header('Location: /?page=cau-lac-bo&them=thanh-cong');
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

    // Sua thong tin cau lac bo
    public function suaCauLacBo(): void
    {
        $pdo = $GLOBALS['pdo'];
        $model = new CauLacBo($pdo);

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID câu lạc bộ không hợp lệ.');
        }

        // Lay thong tin cau lac bo can sua
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

        // Xu ly khi nguoi dung bam Luu thay doi
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
                        header('Location: /?page=cau-lac-bo&sua=thanh-cong');
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

    // Xoa cau lac bo
    public function xoaCauLacBo(): void
    {
        // Chi chap nhan yeu cau POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        // Lay ID cau lac bo tu form
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            header('Location: /?page=cau-lac-bo&xoa=loi-id');
            exit;
        }

        $model = new CauLacBo($GLOBALS['pdo']);

        try {
            $thanhCong = $model->xoa($id);

            if ($thanhCong) {
                header('Location: /?page=cau-lac-bo&xoa=thanh-cong');
                exit;
            }

            header('Location: /?page=cau-lac-bo&xoa=that-bai');
            exit;
        } catch (PDOException $e) {
            // Ghi chi tiet loi vao log, khong hien loi SQL cho nguoi dung
            error_log($e->getMessage());

            header('Location: /?page=cau-lac-bo&xoa=bi-rang-buoc');
            exit;
        }
    }
}
