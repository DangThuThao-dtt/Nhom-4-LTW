<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../Core/xac_thuc.php';
require_once __DIR__ . '/../Repositories/SuKienRepository.php';

class SuKienController
{
    private SuKienRepository $suKienRepository;

    public function __construct()
    {
        global $pdo;

        $this->suKienRepository = new SuKienRepository($pdo);
    }

    /**
     * Lay danh sach su kien theo quyen nguoi dung.
     */
    public function danhSach()
{
    yeuCauDangNhap();

    $nguoiDung = nguoiDungHienTai();

    $suKienList = $this->suKienRepository
        ->danhSachTheoVaiTro($nguoiDung);

    // Lay ten cau lac bo cua nguoi dang dang nhap
    $tenCauLacBo = 'Câu lạc bộ';

    if (($nguoiDung['vai_tro'] ?? '') === 'ban_to_chuc') {
        $idNguoiDung = (int) $nguoiDung['id'];

        $cauLacBoId = $this->suKienRepository
            ->layCauLacBoTheoNguoiQuanLy($idNguoiDung);

        if ($cauLacBoId !== null) {
            $tenCauLacBo = $this->suKienRepository
                ->tenCauLacBo((int) $cauLacBoId);
        }
    }

    // Dem so thong bao chua doc
$soThongBaoChuaDoc = 0;

if (!empty($nguoiDung['id'])) {
    global $pdo;

    $stmtDemThongBao = $pdo->prepare("
        SELECT COUNT(*)
        FROM thong_bao
        WHERE nguoi_dung_id = :nguoi_dung_id
          AND da_doc = 0
    ");

    $stmtDemThongBao->execute([
        'nguoi_dung_id' => (int) $nguoiDung['id']
    ]);

    $soThongBaoChuaDoc = (int) $stmtDemThongBao->fetchColumn();
}

require __DIR__ . '/../Views/ban_to_chuc/danh_sach_su_kien.php';
}
    /**
     * Chuyen chuoi ngay gio tu input datetime-local thanh DateTime.
     */
    private function docNgayGio(string $giaTri): ?DateTime
    {
        $ngayGio = DateTime::createFromFormat(
            '!Y-m-d\TH:i',
            $giaTri
        );

        $loi = DateTime::getLastErrors();

        if (
            $ngayGio === false ||
            (
                $loi !== false &&
                (
                    $loi['warning_count'] > 0 ||
                    $loi['error_count'] > 0
                )
            )
        ) {
            return null;
        }

        return $ngayGio;
    }

    /**
     * Lay su kien neu nguoi dung co quyen quan ly.
     */
    private function laySuKienDuocPhep(int $id): ?array
    {
        $nguoiDung = nguoiDungHienTai();

        return $this->suKienRepository
            ->laySuKienDuocPhep($id, $nguoiDung);
    }

/**
 * Them su kien moi.
 */
public function them()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $nguoiDung = nguoiDungHienTai();

    if (!$nguoiDung) {
        yeuCauDangNhap();
        return;
    }

    $nguoiTaoId = (int) $nguoiDung['id'];

    $tenSuKien = trim($_POST['ten_su_kien'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');
    $diaDiem = trim($_POST['dia_diem'] ?? '');
    $batDau = trim($_POST['thoi_gian_bat_dau'] ?? '');
    $ketThuc = trim($_POST['thoi_gian_ket_thuc'] ?? '');
    $hanDangKy = trim($_POST['han_dang_ky'] ?? '');

    $soLuong = filter_var(
        $_POST['so_luong_toi_da'] ?? null,
        FILTER_VALIDATE_INT
    );

    $trangThai = $_POST['trang_thai'] ?? 'nhap';
    // Xu ly anh su kien.
$hinhAnh = null;

if (
    isset($_FILES['hinh_anh']) &&
    $_FILES['hinh_anh']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $fileAnh = $_FILES['hinh_anh'];

    // Kiem tra loi upload.
    if ($fileAnh['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit('Tai anh len that bai.');
    }

    // Gioi han dung luong 3 MB.
    if ($fileAnh['size'] > 3 * 1024 * 1024) {
        http_response_code(400);
        exit('Anh khong duoc vuot qua 3 MB.');
    }

    // Kiem tra MIME thuc te cua file.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileAnh['tmp_name']);

    $danhSachMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($danhSachMime[$mimeType])) {
        http_response_code(400);
        exit('Chi chap nhan anh JPG, PNG hoac WEBP.');
    }

    // Tao ten file ngau nhien, khong dung ten file nguoi dung gui.
    $tenFile = bin2hex(random_bytes(16))
        . '.'
        . $danhSachMime[$mimeType];

    // Thu muc luu anh trong public.
    $thuMucAnh = dirname(__DIR__, 2)
        . '/public/assets/image/su_kien';

    if (
        !is_dir($thuMucAnh) &&
        !mkdir($thuMucAnh, 0755, true)
    ) {
        http_response_code(500);
        exit('Khong tao duoc thu muc luu anh.');
    }

    $duongDanFile = $thuMucAnh . '/' . $tenFile;

    if (!move_uploaded_file($fileAnh['tmp_name'], $duongDanFile)) {
        http_response_code(500);
        exit('Khong the luu anh su kien.');
    }

    // Duong dan luu vao database.
    $hinhAnh = '/Nhom-4-LTW/public/assets/image/su_kien/'
        . $tenFile;
}

    if (
        $tenSuKien === '' ||
        $soLuong === false ||
        $soLuong === null ||
        $soLuong < 1 ||
        !in_array($trangThai, ['nhap', 'cong_khai'], true)
    ) {
        http_response_code(400);
        exit('Du lieu khong hop le. Vui long kiem tra lai.');
    }

    // Kiem tra thoi gian bat dau.
    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

    // Kiem tra thoi gian ket thuc.
    $ketThucNgay = null;

    if ($ketThuc !== '') {
        $ketThucNgay = $this->docNgayGio($ketThuc);

        if (
            $ketThucNgay === null ||
            $ketThucNgay <= $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Thoi gian ket thuc phai sau thoi gian bat dau.'
            );
        }
    }

    // Kiem tra han dang ky.
    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Han dang ky phai truoc hoac bang thoi gian bat dau.'
            );
        }
    }

    // Xac dinh cau lac bo.
    if ($nguoiDung['vai_tro'] === 'admin') {
        $cauLacBoId = filter_var(
            $_POST['cau_lac_bo_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $cauLacBoId === false ||
            $cauLacBoId === null ||
            $cauLacBoId < 1
        ) {
            http_response_code(400);
            exit('Vui long chon cau lac bo.');
        }

        if (
            !$this->suKienRepository
                ->cauLacBoTonTai((int) $cauLacBoId)
        ) {
            http_response_code(400);
            exit('Cau lac bo khong ton tai.');
        }
    } else {
        $cauLacBoId = $this->suKienRepository
            ->layCauLacBoTheoNguoiQuanLy($nguoiTaoId);

        if ($cauLacBoId === null) {
            http_response_code(403);
            exit('Ban chua duoc gan quan ly cau lac bo.');
        }
    }

    // Chuan bi du lieu gui sang Repository.
    $duLieu = [
    'cau_lac_bo_id' => (int) $cauLacBoId,
    'ten_su_kien' => $tenSuKien,
    'mo_ta' => $moTa !== '' ? $moTa : null,
    'dia_diem' => $diaDiem !== '' ? $diaDiem : null,
    'bat_dau' => $batDauNgay->format('Y-m-d H:i:s'),
    'ket_thuc' => $ketThucNgay !== null
        ? $ketThucNgay->format('Y-m-d H:i:s')
        : null,
    'han_dang_ky' => $hanDangKyNgay !== null
        ? $hanDangKyNgay->format('Y-m-d H:i:s')
        : null,
    'so_luong' => (int) $soLuong,
    'trang_thai' => $trangThai,
    'nguoi_tao_id' => $nguoiTaoId,
    'hinh_anh' => $hinhAnh
];

    $this->suKienRepository->them($duLieu);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb'
    );
    exit;
}
    /**
     * Hien thi bieu mau sua su kien.
     */

public function sua()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        http_response_code(403);
        exit('Khong the sua su kien da huy hoac da ket thuc.');
    }

    $tenCauLacBo = $this->suKienRepository
        ->tenCauLacBo((int) $suKien['cau_lac_bo_id']);

    require __DIR__ . '/../Views/ban_to_chuc/sua_su_kien.php';
}

    public function capNhat()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        http_response_code(403);
        exit('Khong the cap nhat su kien da huy hoac da ket thuc.');
    }

    $tenSuKien = trim($_POST['ten_su_kien'] ?? '');
    $moTa = trim($_POST['mo_ta'] ?? '');
    $diaDiem = trim($_POST['dia_diem'] ?? '');
    $batDau = trim($_POST['thoi_gian_bat_dau'] ?? '');
    $ketThuc = trim($_POST['thoi_gian_ket_thuc'] ?? '');
    $hanDangKy = trim($_POST['han_dang_ky'] ?? '');

    $soLuong = filter_var(
        $_POST['so_luong_toi_da'] ?? null,
        FILTER_VALIDATE_INT
    );

    $trangThai = $_POST['trang_thai'] ?? '';

    if (
        $tenSuKien === '' ||
        $soLuong === false ||
        $soLuong === null ||
        $soLuong < 1 ||
        !in_array($trangThai, ['nhap', 'cong_khai'], true)
    ) {
        http_response_code(400);
        exit('Du lieu cap nhat khong hop le.');
    }

    $batDauNgay = $this->docNgayGio($batDau);

    if ($batDauNgay === null) {
        http_response_code(400);
        exit('Thoi gian bat dau khong hop le.');
    }

    $ketThucNgay = null;

    if ($ketThuc !== '') {
        $ketThucNgay = $this->docNgayGio($ketThuc);

        if (
            $ketThucNgay === null ||
            $ketThucNgay <= $batDauNgay
        ) {
            http_response_code(400);
            exit('Thoi gian ket thuc phai sau thoi gian bat dau.');
        }
    }

    $hanDangKyNgay = null;

    if ($hanDangKy !== '') {
        $hanDangKyNgay = $this->docNgayGio($hanDangKy);

        if (
            $hanDangKyNgay === null ||
            $hanDangKyNgay > $batDauNgay
        ) {
            http_response_code(400);
            exit(
                'Han dang ky phai truoc hoac bang thoi gian bat dau.'
            );
        }
    }
    // Giữ ảnh hiện tại nếu không chọn ảnh mới.
$hinhAnh = $suKien['hinh_anh'] ?? null;

if (
    isset($_FILES['hinh_anh']) &&
    $_FILES['hinh_anh']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $fileAnh = $_FILES['hinh_anh'];

    if ($fileAnh['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit('Tai anh len that bai.');
    }

    // Giới hạn 3 MB.
    if ($fileAnh['size'] > 3 * 1024 * 1024) {
        http_response_code(400);
        exit('Anh khong duoc vuot qua 3 MB.');
    }

    // Kiểm tra loại ảnh.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileAnh['tmp_name']);

    $danhSachMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($danhSachMime[$mimeType])) {
        http_response_code(400);
        exit('Chi chap nhan anh JPG, PNG hoac WEBP.');
    }

    $tenFile = bin2hex(random_bytes(16))
        . '.'
        . $danhSachMime[$mimeType];

    $thuMucAnh = dirname(__DIR__, 2)
        . '/public/assets/image/su_kien';

    if (
        !is_dir($thuMucAnh) &&
        !mkdir($thuMucAnh, 0755, true)
    ) {
        http_response_code(500);
        exit('Khong tao duoc thu muc luu anh.');
    }

    $duongDanFile = $thuMucAnh . '/' . $tenFile;

    if (!move_uploaded_file($fileAnh['tmp_name'], $duongDanFile)) {
        http_response_code(500);
        exit('Khong the luu anh su kien.');
    }

    $hinhAnh = '/Nhom-4-LTW/public/assets/image/su_kien/'
        . $tenFile;
}

    $duLieu = [
        'ten_su_kien' => $tenSuKien,
        'mo_ta' => $moTa !== '' ? $moTa : null,
        'dia_diem' => $diaDiem !== '' ? $diaDiem : null,
        'bat_dau' => $batDauNgay->format('Y-m-d H:i:s'),
        'ket_thuc' => $ketThucNgay !== null
            ? $ketThucNgay->format('Y-m-d H:i:s')
            : null,
        'han_dang_ky' => $hanDangKyNgay !== null
            ? $hanDangKyNgay->format('Y-m-d H:i:s')
            : null,
        'so_luong' => (int) $soLuong,
        'trang_thai' => $trangThai,
        'hinh_anh' => $hinhAnh
    ];

    $this->suKienRepository->capNhat((int) $id, $duLieu);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb'
    );
    exit;
}

    /**
     * Xoa su kien.
     */

public function xoa()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if ($this->suKienRepository->demDangKy((int) $id) > 0) {
        http_response_code(409);
        exit(
            'Su kien da co nguoi dang ky. Vui long huy su kien thay vi xoa.'
        );
    }

    $this->suKienRepository->xoa((int) $id);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb'
    );
    exit;
}

    /**
     * Huy su kien.
     */

/**
 * Huy su kien.
 */
public function huy()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    if (
        in_array(
            $suKien['trang_thai'],
            ['da_huy', 'ket_thuc'],
            true
        )
    ) {
        header(
            'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb'
        );
        exit;
    }

    $this->suKienRepository->huy((int) $id);

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_su_kien_clb'
    );
    exit;
}


/**
 * Hien thi danh sach su kien hoac danh sach nguoi dang ky.
 */
public function danhSachDangKy()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $nguoiDung = nguoiDungHienTai();
    global $pdo;

$tenCauLacBo = 'Câu lạc bộ';

if (!empty($nguoiDung['cau_lac_bo_id'])) {
    $stmtCauLacBo = $pdo->prepare("
        SELECT ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE id = :cau_lac_bo_id
        LIMIT 1
    ");

    $stmtCauLacBo->execute([
        'cau_lac_bo_id' => (int) $nguoiDung['cau_lac_bo_id']
    ]);

    $tenCauLacBo = $stmtCauLacBo->fetchColumn()
        ?: 'Câu lạc bộ';
}
    // Dem so thong bao chua doc
global $pdo;

$soThongBaoChuaDoc = 0;

$stmtDemThongBao = $pdo->prepare("
    SELECT COUNT(*)
    FROM thong_bao
    WHERE nguoi_dung_id = :nguoi_dung_id
      AND da_doc = 0
");

$stmtDemThongBao->execute([
    'nguoi_dung_id' => (int) $nguoiDung['id']
]);

$soThongBaoChuaDoc = (int) $stmtDemThongBao->fetchColumn();

    // Chua chon su kien: hien thi danh sach su kien.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === null || $id === false) {
        $id = 0;
    }

    if ($id < 1) {
        $suKienList = $this->suKienRepository
            ->danhSachTheoVaiTro($nguoiDung);

        require __DIR__ . '/../Views/ban_to_chuc/danh_sach_dang_ky.php';
        return;
    }

    // Da chon su kien: hien thi danh sach nguoi dang ky.
    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    $dangKyList = $this->suKienRepository
        ->danhSachDangKy((int) $id);

    $thongKe = $this->suKienRepository
        ->thongKeDangKy((int) $id);

    require __DIR__ . '/../Views/ban_to_chuc/danh_sach_dang_ky.php';
}

/**
 * Diem danh nguoi da dang ky su kien.
 */
public function diemDanh()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phuong thuc khong hop le.');
    }

    kiemTraCsrfToken();

    $dangKyId = filter_input(
        INPUT_POST,
        'dang_ky_id',
        FILTER_VALIDATE_INT
    );

    $suKienId = filter_input(
        INPUT_POST,
        'su_kien_id',
        FILTER_VALIDATE_INT
    );

    if (
        !$dangKyId || $dangKyId < 1 ||
        !$suKienId || $suKienId < 1
    ) {
        http_response_code(400);
        exit('ID dang ky hoac su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $suKienId);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    $thanhCong = $this->suKienRepository->diemDanh(
        (int) $dangKyId,
        (int) $suKienId
    );

    if (!$thanhCong) {
        http_response_code(404);
        exit('Khong tim thay dang ky hop le de diem danh.');
    }

    header(
        'Location: /Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id='
        . (int) $suKienId
    );
    exit;
}



/**
 * Hien thi chi tiet su kien cho Ban to chuc va Admin.
 */
public function chiTiet()
{
    yeuCauVaiTro(['ban_to_chuc', 'admin']);

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit('ID su kien khong hop le.');
    }

    $suKien = $this->laySuKienDuocPhep((int) $id);

    if (!$suKien) {
        http_response_code(404);
        exit('Khong tim thay su kien hoac ban khong co quyen.');
    }

    // Lay ten cau lac bo cua su kien.
    $tenCauLacBo = $this->suKienRepository
        ->tenCauLacBo((int) $suKien['cau_lac_bo_id']);

    // Dua ten cau lac bo vao du lieu su kien de view hien thi.
    $suKien['ten_cau_lac_bo'] = $tenCauLacBo;

    // Lay thong ke dang ky.
    $thongKe = $this->suKienRepository
        ->thongKeDangKy((int) $id);

    // Truyen du lieu sang view.
    require __DIR__ . '/../Views/ban_to_chuc/chi_tiet_su_kien.php';
}
public function thongKe()
{
    yeuCauVaiTro(['ban_to_chuc']);

    $nguoiDung = nguoiDungHienTai();

    $idNguoiDung = (int) $nguoiDung['id'];

    $thongKeList = $this->suKienRepository
        ->thongKeTheoNguoiTao($idNguoiDung);

    $tongSuKien = count($thongKeList);

    $tongNguoiDangKy = 0;
    $tongNguoiDaThamGia = 0;

    foreach ($thongKeList as $suKien) {
        $tongNguoiDangKy += (int) $suKien['so_nguoi_dang_ky'];
        $tongNguoiDaThamGia += (int) $suKien['so_nguoi_da_tham_gia'];
    }

        global $pdo;

    $tenCauLacBo = 'Câu lạc bộ';

    $stmtTenClb = $pdo->prepare("
        SELECT ten_cau_lac_bo
        FROM cau_lac_bo
        WHERE nguoi_quan_ly_id = :nguoi_dung_id
        LIMIT 1
    ");

    $stmtTenClb->execute([
        'nguoi_dung_id' => $idNguoiDung
    ]);

    $tenCauLacBo = $stmtTenClb->fetchColumn()
        ?: 'Câu lạc bộ';

    $stmtDemThongBao = $pdo->prepare("
        SELECT COUNT(*)
        FROM thong_bao
        WHERE nguoi_dung_id = :nguoi_dung_id
          AND da_doc = 0
    ");

    $stmtDemThongBao->execute([
        'nguoi_dung_id' => $idNguoiDung
    ]);

    $soThongBaoChuaDoc = (int) $stmtDemThongBao->fetchColumn();

    require __DIR__ . '/../Views/ban_to_chuc/thong_ke.php';
}
}