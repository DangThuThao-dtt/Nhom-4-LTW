
<?php
require_once __DIR__ . '/../Models/TaiKhoan.php';
require_once __DIR__ . '/../Models/CauLacBo.php';
require_once __DIR__ . '/../Models/QuanLySuKien.php';
require_once __DIR__ . '/../Models/ThongKe.php';

class AdminController
{
    private CauLacBo $cauLacBo;
    private QuanLySuKien $suKien;
    private ThongKe $thongKe;
    private TaiKhoan $taiKhoan;

    public function __construct()
    {
        $this->cauLacBo = new CauLacBo();
        $this->suKien = new QuanLySuKien();
        $this->thongKe = new ThongKe();
        $this->taiKhoan = new TaiKhoan();
    }

    // =========================
    // QUAN LY CAU LAC BO
    // =========================

    public function danhSachCauLacBo(): void
    {
        $danhSachCauLacBo = $this->cauLacBo->layDanhSach();
        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    public function themCauLacBo(): void
    {
        $this->chiNhanPOST();

        $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
        $moTa = trim($_POST['mo_ta'] ?? '');
        $quanLy = $_POST['nguoi_quan_ly_id'] ?? '';

        if ($ten === '') {
            exit('Ten cau lac bo khong duoc de trong.');
        }

        $this->cauLacBo->them(
            $ten,
            $moTa !== '' ? $moTa : null,
            $quanLy !== '' ? (int) $quanLy : null
        );

        $this->chuyenHuong('quan-ly-cau-lac-bo');
    }

    public function capNhatCauLacBo(): void
    {
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);
        $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
        $moTa = trim($_POST['mo_ta'] ?? '');
        $quanLy = $_POST['nguoi_quan_ly_id'] ?? '';

        if ($id <= 0 || $ten === '') {
            exit('Du lieu cau lac bo khong hop le.');
        }

        $this->cauLacBo->capNhat(
            $id,
            $ten,
            $moTa !== '' ? $moTa : null,
            $quanLy !== '' ? (int) $quanLy : null
        );

        $this->chuyenHuong('quan-ly-cau-lac-bo');
    }

    public function xoaCauLacBo(): void
    {
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->cauLacBo->xoa($id);
        }

        $this->chuyenHuong('quan-ly-cau-lac-bo');
    }

    // =========================
    // QUAN LY SU KIEN
    // =========================

    
public function danhSachSuKien(): void
{
    $this->yeuCauAdmin();

    $boLoc = [
        'tu_khoa' => trim($_GET['tu_khoa'] ?? ''),
        'cau_lac_bo_id' => (int) ($_GET['cau_lac_bo_id'] ?? 0),
        'trang_thai' => $_GET['trang_thai'] ?? '',
        'tu_ngay' => $_GET['tu_ngay'] ?? '',
        'den_ngay' => $_GET['den_ngay'] ?? '',
        'sap_xep' => $_GET['sap_xep'] ?? 'thoi_gian',
        'huong' => $_GET['huong'] ?? 'ASC',
        'trang' => max(1, (int) ($_GET['trang'] ?? 1)),
        'so_dong' => max(1, min(
            100,
            (int) ($_GET['so_dong'] ?? 10)
        )),
    ];

    $tongSoSuKien = $this->suKien->demDanhSach($boLoc);
    $soDongMoiTrang = $boLoc['so_dong'];
    $tongSoTrang = max(
        1,
        (int) ceil($tongSoSuKien / $soDongMoiTrang)
    );

    $boLoc['trang'] = min($boLoc['trang'], $tongSoTrang);

    $danhSachSuKien = $this->suKien->layDanhSach($boLoc);
    $danhSachCauLacBo = $this->cauLacBo->layDanhSach();

    require __DIR__ . '/../Views/admin/su_kien.php';
}

public function xemChiTietSuKien(): void
{
    $this->yeuCauAdmin();

    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        exit('ID sự kiện không hợp lệ.');
    }

    $chiTietSuKien = $this->suKien->layChiTiet($id);

    if (!$chiTietSuKien) {
        http_response_code(404);
        exit('Không tìm thấy sự kiện.');
    }

    require __DIR__ . '/../Views/admin/chi_tiet_su_kien.php';
}

    public function themSuKien(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $data = $this->layDuLieuSuKien();

        // Lay ID nguoi dang nhap tu session.
        $nguoiTaoId = (int) (
            $_SESSION['nguoi_dung']['id']
            ?? $_SESSION['user']['id']
            ?? $_SESSION['user_id']
            ?? 0
        );

        if ($nguoiTaoId <= 0) {
            exit(
                'Khong tim thay ID nguoi dang nhap. ' .
                'Hay dang nhap lai va kiem tra session tai khoan.'
            );
        }

        $data['nguoi_tao_id'] = $nguoiTaoId;

        $this->kiemTraDuLieuSuKien($data);
        $this->suKien->them($data);

        $this->chuyenHuong('quan-ly-su-kien');
    }

    public function capNhatSuKien(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->layDuLieuSuKien();

        if ($id <= 0) {
            exit('ID su kien khong hop le.');
        }

        $this->kiemTraDuLieuSuKien($data);
        $this->suKien->capNhat($id, $data);

        $this->chuyenHuong('quan-ly-su-kien');
    }

    public function doiTrangThaiSuKien(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? '';

        if ($id <= 0) {
            exit('ID su kien khong hop le.');
        }

        $this->suKien->capNhatTrangThai($id, $trangThai);

        $this->chuyenHuong('quan-ly-su-kien');
    }

    public function xoaSuKien(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->suKien->timTheoId($id);

    if (!$suKien) {
        exit('Khong tim thay su kien.');
    }

    $sql = "
        SELECT COUNT(*)
        FROM dang_ky
        WHERE su_kien_id = :id
    ";

    // Dùng kết nối PDO đã có trong model thông qua hàm mới bên dưới.
    if ($this->suKien->coDangKy($id)) {
        $this->suKien->capNhatTrangThai($id, 'da_huy');
        $this->chuyenHuong('quan-ly-su-kien');
    }

    $this->suKien->xoa($id);
    $this->chuyenHuong('quan-ly-su-kien');
}

    // =========================
    // THONG KE
    // =========================

    public function hienThiThongKe(): void
    {
        $this->yeuCauAdmin();
        $tongSoSuKien = $this->thongKe->tongSoSuKien();
        $thongKeTrangThai = $this->thongKe->demTheoTrangThai();
        $tongLuotDangKy = $this->thongKe->tongLuotDangKy();
        $dangKyTheoSuKien = $this->thongKe->dangKyTheoSuKien();

        require __DIR__ . '/../Views/admin/thong_ke.php';
    }

    // =========================
    // HAM HO TRO
    // =========================

    private function chiNhanPOST(): void
    {
        $this->yeuCauAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Phuong thuc khong duoc ho tro.');
        }
    }

    private function chuyenHuong(string $action): void
    {
        $this->yeuCauAdmin();
        header('Location: ?action=' . urlencode($action));
        exit;
    }

    private function layDuLieuSuKien(): array
    {
        $this->yeuCauAdmin();
        return [
            'cau_lac_bo_id' => (int) ($_POST['cau_lac_bo_id'] ?? 0),
            'ten_su_kien' => trim($_POST['ten_su_kien'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'dia_diem' => trim($_POST['dia_diem'] ?? ''),
            'thoi_gian_bat_dau' => trim(
                $_POST['thoi_gian_bat_dau'] ?? ''
            ),
            'thoi_gian_ket_thuc' => trim(
                $_POST['thoi_gian_ket_thuc'] ?? ''
            ),
            'so_luong_toi_da' => (int) (
                $_POST['so_luong_toi_da'] ?? 0
            ),
            'trang_thai' => $_POST['trang_thai'] ?? 'nhap',
        ];
    }

    
    private function yeuCauAdmin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (
        empty($_SESSION['nguoi_dung']['id']) ||
        ($_SESSION['nguoi_dung']['vai_tro'] ?? '') !== 'admin'
    ) {
        http_response_code(403);
        exit('Bạn không có quyền thực hiện chức năng này.');
    }
}
private function kiemTraDuLieuSuKien(array $data): void
    {
        $this->yeuCauAdmin();
        $trangThaiHopLe = [
            'nhap',
            'cong_khai',
            'da_huy',
            'ket_thuc',
        ];

        if (
            $data['cau_lac_bo_id'] <= 0 ||
            $data['ten_su_kien'] === '' ||
            $data['thoi_gian_bat_dau'] === '' ||
            $data['so_luong_toi_da'] <= 0 ||
            !in_array($data['trang_thai'], $trangThaiHopLe, true)
        ) {
            exit('Du lieu su kien khong hop le.');
        }

        if (
            $data['thoi_gian_ket_thuc'] !== '' &&
            $data['thoi_gian_ket_thuc'] < $data['thoi_gian_bat_dau']
        ) {
            exit('Thoi gian ket thuc phai sau thoi gian bat dau.');
        }
    }
public function danhSachTaiKhoan(): void
{
    $this->yeuCauAdmin();

    $tuKhoa = trim($_GET['q'] ?? '');
    $cauLacBoId = max(0, (int) ($_GET['cau_lac_bo_id'] ?? 0));

    $soDongMoiTrang = 10;
    $trang = max(1, (int) ($_GET['trang'] ?? 1));

    $tongTaiKhoan = $this->taiKhoan->tongTaiKhoanBanToChuc();
    $tongKetQua = $this->taiKhoan->demTaiKhoanBanToChuc(
        $tuKhoa,
        $cauLacBoId
    );

    $tongTrang = max(1, (int) ceil($tongKetQua / $soDongMoiTrang));
    $trang = min($trang, $tongTrang);
    $offset = ($trang - 1) * $soDongMoiTrang;

    $danhSachTaiKhoan = $this->taiKhoan->danhSachBanToChuc(
        $tuKhoa,
        $cauLacBoId,
        $soDongMoiTrang,
        $offset
    );

    $danhSachCauLacBo = $this->cauLacBo->layDanhSach();

    require __DIR__ . '/../Views/admin/tai_khoan.php';
}
public function themTaiKhoan(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $hoTen = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $matKhau = $_POST['mat_khau'] ?? '';
    $cauLacBoId = (int) ($_POST['cau_lac_bo_id'] ?? 0);

    if (
        $hoTen === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($matKhau) < 8 ||
        $cauLacBoId <= 0
    ) {
        exit(
            'Du lieu khong hop le. Mat khau phai co it nhat 8 ky tu.'
        );
    }

    try {
        $this->taiKhoan->taoBanToChuc(
            $hoTen,
            $email,
            $matKhau,
            $cauLacBoId
        );
    } catch (Throwable $e) {
        $maThongBao = match ($e->getMessage()) {
            'Email đã được sử dụng.' => 'email_da_ton_tai',
            'Câu lạc bộ này đã có tài khoản quản lý.' => 'clb_da_co_ban_to_chuc',
            'Câu lạc bộ không tồn tại.' => 'clb_khong_ton_tai',
            default => 'tao_tai_khoan_that_bai',
        };
        header('Location: ?action=quan-ly-tai-khoan&thong_bao=' . $maThongBao);
        exit;
    }

    header('Location: ?action=quan-ly-tai-khoan&thong_bao=tao_tai_khoan_thanh_cong');
    exit;
}

public function capNhatTaiKhoan(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $id = (int) ($_POST['id'] ?? 0);
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cauLacBoId = (int) ($_POST['cau_lac_bo_id'] ?? 0);
    $matKhauCu = (string) ($_POST['mat_khau_cu'] ?? '');
    $matKhauMoi = (string) ($_POST['mat_khau_moi'] ?? '');
    $xacNhanMatKhau = (string) ($_POST['xac_nhan_mat_khau'] ?? '');

    if (
        $id <= 0 ||
        $hoTen === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $cauLacBoId <= 0
    ) {
        header('Location: ?action=quan-ly-tai-khoan&thong_bao=cap_nhat_that_bai');
        exit;
    }

    // Chỉ yêu cầu mật khẩu hiện tại nếu Admin muốn đổi mật khẩu tài khoản.
    if ($matKhauMoi !== '' || $xacNhanMatKhau !== '' || $matKhauCu !== '') {
        if ($matKhauCu === '' || !$this->taiKhoan->kiemTraMatKhauHienTai($id, $matKhauCu)) {
            header('Location: ?action=quan-ly-tai-khoan&thong_bao=mat_khau_cu_khong_dung');
            exit;
        }

        if ($matKhauMoi === '' || strlen($matKhauMoi) < 8 || strlen($matKhauMoi) > 4096) {
            header('Location: ?action=quan-ly-tai-khoan&thong_bao=cap_nhat_that_bai');
            exit;
        }

        if (!hash_equals($matKhauMoi, $xacNhanMatKhau)) {
            header('Location: ?action=quan-ly-tai-khoan&thong_bao=mat_khau_moi_khong_khop');
            exit;
        }
    }

    try {
        $this->taiKhoan->capNhatThongTin($id, $hoTen, $email, $cauLacBoId);

        if ($matKhauMoi !== '') {
            $this->taiKhoan->datLaiMatKhau($id, $matKhauMoi);
        }
    } catch (PDOException $e) {
        header('Location: ?action=quan-ly-tai-khoan&thong_bao=email_da_ton_tai');
        exit;
    } catch (RuntimeException $e) {
        $maThongBao = $e->getMessage() === 'Câu lạc bộ đã có ban tổ chức phụ trách.'
            ? 'clb_da_co_ban_to_chuc'
            : 'cap_nhat_that_bai';
        header('Location: ?action=quan-ly-tai-khoan&thong_bao=' . $maThongBao);
        exit;
    }

    header('Location: ?action=quan-ly-tai-khoan&thong_bao=cap_nhat_tai_khoan_thanh_cong');
    exit;
}

public function datLaiMatKhauTaiKhoan(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $id = (int) ($_POST['id'] ?? 0);
    $matKhauMoi = $_POST['mat_khau_moi'] ?? '';

    if (
        $id <= 0 ||
        strlen($matKhauMoi) < 8 ||
        strlen($matKhauMoi) > 4096
    ) {
        exit(
            'ID tài khoản không hợp lệ hoặc mật khẩu mới phải có từ 8 ký tự trở lên.'
        );
    }

    $thanhCong = $this->taiKhoan->datLaiMatKhau($id, $matKhauMoi);

    if (!$thanhCong) {
        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=dat_lai_mat_khau_that_bai'
        );
        exit;
    }

    header(
        'Location: ?action=quan-ly-tai-khoan&thong_bao=dat_lai_mat_khau_thanh_cong'
    );
    exit;
}}