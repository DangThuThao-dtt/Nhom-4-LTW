<?php

require_once __DIR__ . '/../Repositories/AdminRepository.php';

class AdminController
{
    private AdminRepository $adminRepository;

    public function __construct()
    {
        $this->adminRepository = new AdminRepository();
    }

    // =========================================
    // QUAN LY CAU LAC BO
    // =========================================

    public function danhSachCauLacBo(): void
    {
        $this->yeuCauAdmin();

        $cheDo = 'danh_sach';
        $danhSach = $this->adminRepository->danhSachCauLacBo();

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    public function themCauLacBo(): void
    {
        $this->yeuCauAdmin();

        $cheDo = 'them';
        $loi = '';
        $ten = '';
        $moTa = '';
        $nguoiQuanLyId = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
            $moTa = trim($_POST['mo_ta'] ?? '');
            $nguoiQuanLyId = trim($_POST['nguoi_quan_ly_id'] ?? '');

            if ($ten === '') {
                $loi = 'Tên câu lạc bộ không được để trống.';
            } elseif (
                $nguoiQuanLyId !== '' &&
                (!ctype_digit($nguoiQuanLyId) || (int) $nguoiQuanLyId <= 0)
            ) {
                $loi = 'Mã người quản lý không hợp lệ.';
            } else {
                $this->adminRepository->themCauLacBo(
                    $ten,
                    $moTa !== '' ? $moTa : null,
                    $nguoiQuanLyId !== '' ? (int) $nguoiQuanLyId : null
                );

                $this->chuyenHuong('quan-ly-cau-lac-bo');
                return;
            }
        }

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    public function suaCauLacBo(): void
    {
        $this->yeuCauAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            exit('ID câu lạc bộ không hợp lệ.');
        }

        $clb = $this->adminRepository->timCauLacBoTheoId($id);

        if (!$clb) {
            http_response_code(404);
            exit('Không tìm thấy câu lạc bộ.');
        }

        $cheDo = 'sua';
        $loi = '';
        $ten = $clb['ten_cau_lac_bo'] ?? '';
        $moTa = $clb['mo_ta'] ?? '';
        $nguoiQuanLyId = $clb['nguoi_quan_ly_id'] ?? '';

        require __DIR__ . '/../Views/admin/cau_lac_bo.php';
    }

    public function capNhatCauLacBo(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $ten = trim($_POST['ten_cau_lac_bo'] ?? '');
        $moTa = trim($_POST['mo_ta'] ?? '');
        $quanLy = trim($_POST['nguoi_quan_ly_id'] ?? '');

        if ($id <= 0 || $ten === '') {
            exit('Dữ liệu câu lạc bộ không hợp lệ.');
        }

        if (
            $quanLy !== '' &&
            (!ctype_digit($quanLy) || (int) $quanLy <= 0)
        ) {
            exit('Mã người quản lý không hợp lệ.');
        }

        $this->adminRepository->capNhatCauLacBo(
            $id,
            $ten,
            $moTa !== '' ? $moTa : null,
            $quanLy !== '' ? (int) $quanLy : null
        );

        $this->chuyenHuong('quan-ly-cau-lac-bo');
    }

    public function xoaCauLacBo(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->adminRepository->xoaCauLacBo($id);
        }

        $this->chuyenHuong('quan-ly-cau-lac-bo');
    }

    // =========================================
    // QUAN LY SU KIEN
    // =========================================

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
            'so_dong' => max(
                1,
                min(100, (int) ($_GET['so_dong'] ?? 10))
            ),
        ];

        $tongSoSuKien = $this->adminRepository
            ->demDanhSachSuKien($boLoc);

        $soDongMoiTrang = $boLoc['so_dong'];

        $tongSoTrang = max(
            1,
            (int) ceil($tongSoSuKien / $soDongMoiTrang)
        );

        $boLoc['trang'] = min($boLoc['trang'], $tongSoTrang);

        $danhSachSuKien = $this->adminRepository
            ->danhSachSuKien($boLoc);

        $danhSachCauLacBo = $this->adminRepository
            ->danhSachCauLacBo();

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

    $chiTietSuKien = $this->adminRepository
        ->chiTietSuKien($id);

    if (!$chiTietSuKien) {
        http_response_code(404);
        exit('Không tìm thấy sự kiện.');
    }

    // Truyền dữ liệu sang view dùng chung
    $suKien = $chiTietSuKien;

    require __DIR__ . '/../Views/ban_to_chuc/chi_tiet_su_kien.php';
}
    public function themSuKien(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $data = $this->layDuLieuSuKien();

    $nguoiTaoId = (int) (
        $_SESSION['nguoi_dung']['id']
        ?? $_SESSION['user']['id']
        ?? $_SESSION['user_id']
        ?? 0
    );

    if ($nguoiTaoId <= 0) {
        exit('Không tìm thấy ID người đăng nhập. Vui lòng đăng nhập lại.');
    }

    $data['nguoi_tao_id'] = $nguoiTaoId;

    // Tải ảnh lên nếu người dùng đã chọn ảnh
    $data['hinh_anh'] = $this->xuLyAnhSuKien(null);

    $this->kiemTraDuLieuSuKien($data);

    $this->adminRepository->themSuKien($data);

    $this->chuyenHuong('quan-ly-su-kien');
}
    public function capNhatSuKien(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('ID sự kiện không hợp lệ.');
    }

    $suKienCu = $this->adminRepository->timSuKienTheoId($id);

    if (!$suKienCu) {
        http_response_code(404);
        exit('Không tìm thấy sự kiện.');
    }

    $data = $this->layDuLieuSuKien();

    // Nếu không chọn ảnh mới thì giữ ảnh hiện tại
    $anhCu = $suKienCu['hinh_anh'] ?? null;
    $data['hinh_anh'] = $this->xuLyAnhSuKien($anhCu);

    $this->kiemTraDuLieuSuKien($data);

    $this->adminRepository->capNhatSuKien($id, $data);

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

        $this->adminRepository->capNhatTrangThaiSuKien(
            $id,
            $trangThai
        );

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

        $suKien = $this->adminRepository
            ->timSuKienTheoId($id);

        if (!$suKien) {
            exit('Khong tim thay su kien.');
        }

        if ($this->adminRepository->suKienCoDangKy($id)) {
            $this->adminRepository->capNhatTrangThaiSuKien(
                $id,
                'da_huy'
            );

            $this->chuyenHuong('quan-ly-su-kien');
            return;
        }

        $this->adminRepository->xoaSuKien($id);

        $this->chuyenHuong('quan-ly-su-kien');
    }

    // =========================================
    // THONG KE
    // =========================================

    public function hienThiThongKe(): void
    {
        $this->yeuCauAdmin();

        $tongSoSuKien = $this->adminRepository->tongSoSuKien();

        $thongKeTrangThai = $this->adminRepository
            ->demSuKienTheoTrangThai();

        $tongLuotDangKy = $this->adminRepository->tongLuotDangKy();

        $dangKyTheoSuKien = $this->adminRepository
            ->dangKyTheoSuKien();

        require __DIR__ . '/../Views/admin/thong_ke.php';
    }

    public function dashboard(): void
    {
        $this->yeuCauAdmin();

        $soYeuCauDatLaiMatKhau = $this->adminRepository
            ->demYeuCauDatLaiMatKhauChoXuLy();

        $tongSuKien = $this->adminRepository->tongSoSuKien();

        $tongBanToChuc = $this->adminRepository
            ->tongTaiKhoanBanToChuc();

        $thongKeTrangThai = $this->adminRepository
            ->demSuKienTheoTrangThai();

        $tongSuKienChoDuyet = 0;
        $tongSuKienDangDienRa = 0;

        foreach ($thongKeTrangThai as $item) {
            $trangThai = $item['trang_thai'] ?? '';

            $soLuong = (int) (
                $item['so_luong']
                ?? $item['tong']
                ?? $item['total']
                ?? 0
            );

            if ($trangThai === 'nhap') {
                $tongSuKienChoDuyet += $soLuong;
            }

            if ($trangThai === 'cong_khai') {
                $tongSuKienDangDienRa += $soLuong;
            }
        }

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // =========================================
    // QUAN LY TAI KHOAN THANH VIEN
    // =========================================

    public function danhSachTaiKhoanThanhVien(): void
    {
        $this->yeuCauAdmin();

        $danhSachTaiKhoanThanhVien = $this->adminRepository
            ->danhSachTaiKhoanThanhVien();

        $danhSachCauLacBo = [];
        $tuKhoa = '';
        $cauLacBoId = 0;

        $tongTaiKhoan = count($danhSachTaiKhoanThanhVien);
        $tongKetQua = count($danhSachTaiKhoanThanhVien);

        $trang = 1;
        $tongTrang = 1;

        require __DIR__ . '/../Views/admin/tai_khoan_thanh_vien.php';
    }

    // =========================================
    // QUAN LY TAI KHOAN BAN TO CHUC
    // =========================================

    public function danhSachTaiKhoan(): void
    {
        $this->yeuCauAdmin();

        $tuKhoa = trim($_GET['q'] ?? '');
        $cauLacBoId = max(
            0,
            (int) ($_GET['cau_lac_bo_id'] ?? 0)
        );

        $soDongMoiTrang = 10;
        $trang = max(1, (int) ($_GET['trang'] ?? 1));

        $tongTaiKhoan = $this->adminRepository
            ->tongTaiKhoanBanToChuc();

        $tongKetQua = $this->adminRepository
            ->demTaiKhoanBanToChuc($tuKhoa, $cauLacBoId);

        $tongTrang = max(
            1,
            (int) ceil($tongKetQua / $soDongMoiTrang)
        );

        $trang = min($trang, $tongTrang);

        $offset = ($trang - 1) * $soDongMoiTrang;

        $danhSachTaiKhoan = $this->adminRepository
            ->danhSachTaiKhoanBanToChuc(
                $tuKhoa,
                $cauLacBoId,
                $soDongMoiTrang,
                $offset
            );

        $danhSachCauLacBo = $this->adminRepository
            ->danhSachCauLacBo();

        $danhSachClbChuaCoBanToChuc = $this->adminRepository
            ->danhSachClbChuaCoBanToChuc();

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
            $this->adminRepository->taoTaiKhoanBanToChuc(
                $hoTen,
                $email,
                $matKhau,
                $cauLacBoId
            );
        } catch (Throwable $e) {
            $maThongBao = match ($e->getMessage()) {
                'Email đã được sử dụng.' => 'email_da_ton_tai',
                'Câu lạc bộ này đã có tài khoản quản lý.' =>
                    'clb_da_co_ban_to_chuc',
                'Câu lạc bộ không tồn tại.' => 'clb_khong_ton_tai',
                default => 'tao_tai_khoan_that_bai',
            };

            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                $maThongBao
            );

            exit;
        }

        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
            'tao_tai_khoan_thanh_cong'
        );

        exit;
    }

    public function tuDongTaoTaiKhoan(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        try {
            $clbIds = $_POST['clb_ids'] ?? [];

            if (!is_array($clbIds) || empty($clbIds)) {
                header(
                    'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                    'chua_chon_clb'
                );

                exit;
            }

            $danhSachMoi = $this->adminRepository
                ->tuDongTaoTaiKhoanBanToChuc($clbIds);

            $_SESSION['tai_khoan_vua_tao'] = $danhSachMoi;

            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'tu_dong_tao_thanh_cong'
            );

            exit;
        } catch (Throwable $e) {
            error_log(
                'Lỗi tự động tạo tài khoản: ' . $e->getMessage()
            );

            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'tu_dong_tao_that_bai'
            );

            exit;
        }
    }

    public function capNhatTaiKhoan(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);
        $hoTen = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $cauLacBoId = (int) ($_POST['cau_lac_bo_id'] ?? 0);

        $matKhauMoi = (string) ($_POST['mat_khau_moi'] ?? '');
        $xacNhanMatKhau = (string) (
            $_POST['xac_nhan_mat_khau'] ?? ''
        );

        if (
            $id <= 0 ||
            $hoTen === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            $cauLacBoId <= 0
        ) {
            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'cap_nhat_that_bai'
            );

            exit;
        }

        $doiMatKhau = (
            $matKhauMoi !== '' ||
            $xacNhanMatKhau !== ''
        );

        if ($doiMatKhau) {
            if (
                strlen($matKhauMoi) < 8 ||
                strlen($matKhauMoi) > 4096
            ) {
                header(
                    'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                    'cap_nhat_that_bai'
                );

                exit;
            }

            if (!hash_equals($matKhauMoi, $xacNhanMatKhau)) {
                header(
                    'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                    'mat_khau_moi_khong_khop'
                );

                exit;
            }
        }

        try {
            $this->adminRepository->capNhatThongTinTaiKhoan(
                $id,
                $hoTen,
                $email,
                $cauLacBoId
            );

            if ($doiMatKhau) {
                $this->adminRepository->datLaiMatKhauTaiKhoan(
                    $id,
                    $matKhauMoi
                );
            }
        } catch (Throwable $e) {
            die('LỖI THỰC TẾ: ' . $e->getMessage());
        }

        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
            'cap_nhat_tai_khoan_thanh_cong'
        );

        exit;
    }

    public function datLaiMatKhauTaiKhoan(): void
    {
        $this->yeuCauAdmin();
        $this->chiNhanPOST();

        $id = (int) ($_POST['id'] ?? 0);
        $matKhauMoi = (string) ($_POST['mat_khau_moi'] ?? '');
        $xacNhanMatKhau = (string) (
            $_POST['xac_nhan_mat_khau'] ?? ''
        );

        if (
            $id <= 0 ||
            strlen($matKhauMoi) < 8 ||
            strlen($matKhauMoi) > 4096
        ) {
            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'dat_lai_mat_khau_that_bai'
            );

            exit;
        }

        if (!hash_equals($matKhauMoi, $xacNhanMatKhau)) {
            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'mat_khau_moi_khong_khop'
            );

            exit;
        }

        $thanhCong = $this->adminRepository
            ->datLaiMatKhauTaiKhoan($id, $matKhauMoi);

        if (!$thanhCong) {
            header(
                'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
                'dat_lai_mat_khau_that_bai'
            );

            exit;
        }

        $adminId = (int) ($_SESSION['nguoi_dung']['id'] ?? 0);

        $this->adminRepository
            ->danhDauYeuCauDatLaiMatKhauDaXuLy(
                $id,
                $adminId
            );

        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=' .
            'da_cap_lai_tai_khoan'
        );

        exit;
    }
    public function xoaTaiKhoan(): void
{
    $this->yeuCauAdmin();
    $this->chiNhanPOST();

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=xoa_tai_khoan_that_bai'
        );
        exit;
    }

    try {
        $this->adminRepository->xoaTaiKhoan($id);
    } catch (Throwable $e) {
        error_log('Lỗi xóa tài khoản: ' . $e->getMessage());

        header(
            'Location: ?action=quan-ly-tai-khoan&thong_bao=xoa_tai_khoan_that_bai'
        );
        exit;
    }

    header(
        'Location: ?action=quan-ly-tai-khoan&thong_bao=xoa_tai_khoan_thanh_cong'
    );
    exit;
}

    public function danhSachYeuCauDatLaiMatKhau(): void
    {
        $this->yeuCauAdmin();

        $danhSachYeuCauDatLaiMatKhau = $this->adminRepository
            ->danhSachYeuCauDatLaiMatKhauChoXuLy();

        require __DIR__ .
            '/../Views/admin/yeu_cau_dat_lai_mat_khau.php';
    }

    // =========================================
    // HAM HO TRO
    // =========================================

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
        'cau_lac_bo_id' => (int) (
            $_POST['cau_lac_bo_id'] ?? 0
        ),

        'ten_su_kien' => trim(
            $_POST['ten_su_kien'] ?? ''
        ),

        'mo_ta' => trim(
            $_POST['mo_ta'] ?? ''
        ),

        'dia_diem' => trim(
            $_POST['dia_diem'] ?? ''
        ),

        'thoi_gian_bat_dau' => str_replace(
            'T',
            ' ',
            trim($_POST['thoi_gian_bat_dau'] ?? '')
        ),

        'thoi_gian_ket_thuc' => (
            trim($_POST['thoi_gian_ket_thuc'] ?? '') !== ''
                ? str_replace(
                    'T',
                    ' ',
                    trim($_POST['thoi_gian_ket_thuc'])
                )
                : null
        ),

        'han_dang_ky' => (
            trim($_POST['han_dang_ky'] ?? '') !== ''
                ? str_replace(
                    'T',
                    ' ',
                    trim($_POST['han_dang_ky'])
                )
                : null
        ),

        'so_luong_toi_da' => (int) (
            $_POST['so_luong_toi_da'] ?? 0
        ),

        'trang_thai' => $_POST['trang_thai'] ?? 'nhap',

        'hinh_anh' => null,
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
            !in_array(
                $data['trang_thai'],
                $trangThaiHopLe,
                true
            )
        ) {
            exit('Du lieu su kien khong hop le.');
        }

        if (
            $data['thoi_gian_ket_thuc'] !== '' &&
            $data['thoi_gian_ket_thuc'] <
                $data['thoi_gian_bat_dau']
        ) {
            exit(
                'Thoi gian ket thuc phai sau thoi gian bat dau.'
            );
        }
        if (
    !empty($data['han_dang_ky']) &&
    $data['han_dang_ky'] > $data['thoi_gian_bat_dau']
) {
    exit('Hạn đăng ký phải trước thời gian bắt đầu sự kiện.');
}
    }
    private function xuLyAnhSuKien(?string $anhCu): ?string
{
    // Không chọn ảnh mới: giữ ảnh cũ
    if (
        !isset($_FILES['hinh_anh']) ||
        $_FILES['hinh_anh']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return $anhCu;
    }

    $file = $_FILES['hinh_anh'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        exit('Có lỗi xảy ra khi tải ảnh lên.');
    }

    // Giới hạn ảnh 5 MB
    if ($file['size'] > 5 * 1024 * 1024) {
        exit('Ảnh không được vượt quá 5 MB.');
    }

    // Kiểm tra loại ảnh thực tế
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $loaiAnhHopLe = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    if (!isset($loaiAnhHopLe[$mime])) {
        exit('Chỉ chấp nhận ảnh JPG, PNG, WEBP hoặc GIF.');
    }

    // Thư mục lưu ảnh: public/assets/image/su-kien/
    $thuMucLuu = dirname(__DIR__, 2)
        . '/public/assets/image/su-kien/';

    if (
        !is_dir($thuMucLuu) &&
        !mkdir($thuMucLuu, 0755, true)
    ) {
        exit('Không thể tạo thư mục lưu ảnh.');
    }

    $tenAnh = bin2hex(random_bytes(16))
        . '.'
        . $loaiAnhHopLe[$mime];

    $duongDanLuu = $thuMucLuu . $tenAnh;

    if (!move_uploaded_file($file['tmp_name'], $duongDanLuu)) {
        exit('Không thể lưu ảnh vào thư mục.');
    }

    // Lưu đường dẫn tương đối vào SQL
    return 'assets/image/su-kien/' . $tenAnh;
}
}