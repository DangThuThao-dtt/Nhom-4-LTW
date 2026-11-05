<?php

require_once __DIR__ . '/../Models/Admin/TaiKhoan.php';
require_once __DIR__ . '/../Models/Admin/CauLacBo.php';
require_once __DIR__ . '/../Models/Admin/QuanLySuKien.php';
require_once __DIR__ . '/../Models/Admin/ThongKe.php';
require_once __DIR__ . '/../Models/Admin/TaiKhoanThanhVien.php';
require_once __DIR__ . '/../Models/Admin/YeuCauDatLaiMatKhau.php';

class AdminRepository
{
    private CauLacBo $cauLacBo;
    private QuanLySuKien $suKien;
    private ThongKe $thongKe;
    private TaiKhoan $taiKhoan;
    private TaiKhoanThanhVien $taiKhoanThanhVien;
    private YeuCauDatLaiMatKhau $yeuCauDatLaiMatKhau;

    public function __construct()
    {
        $this->cauLacBo = new CauLacBo();
        $this->suKien = new QuanLySuKien();
        $this->thongKe = new ThongKe();
        $this->taiKhoan = new TaiKhoan();
        $this->taiKhoanThanhVien = new TaiKhoanThanhVien();
        $this->yeuCauDatLaiMatKhau = new YeuCauDatLaiMatKhau();
    }

    // =========================================
    // QUAN LY CAU LAC BO
    // =========================================

    public function danhSachCauLacBo(): array
    {
        return $this->cauLacBo->layDanhSach();
    }

    public function timCauLacBoTheoId(int $id): array|false
    {
        return $this->cauLacBo->timTheoId($id);
    }

    public function themCauLacBo(
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        return $this->cauLacBo->them(
            $ten,
            $moTa,
            $nguoiQuanLyId
        );
    }

    public function capNhatCauLacBo(
        int $id,
        string $ten,
        ?string $moTa,
        ?int $nguoiQuanLyId
    ): bool {
        return $this->cauLacBo->capNhat(
            $id,
            $ten,
            $moTa,
            $nguoiQuanLyId
        );
    }

    public function xoaCauLacBo(int $id): bool
    {
        return $this->cauLacBo->xoa($id);
    }

    // =========================================
    // QUAN LY SU KIEN
    // =========================================

    public function danhSachSuKien(array $boLoc = []): array
    {
        return $this->suKien->layDanhSach($boLoc);
    }

    public function demDanhSachSuKien(array $boLoc = []): int
    {
        return $this->suKien->demDanhSach($boLoc);
    }

    public function chiTietSuKien(int $id): array|false
    {
        return $this->suKien->layChiTiet($id);
    }

    public function timSuKienTheoId(int $id): array|false
    {
        return $this->suKien->timTheoId($id);
    }

    public function themSuKien(array $data): bool
    {
        return $this->suKien->them($data);
    }

    public function capNhatSuKien(int $id, array $data): bool
    {
        return $this->suKien->capNhat($id, $data);
    }

    public function capNhatTrangThaiSuKien(
        int $id,
        string $trangThai
    ): bool {
        return $this->suKien->capNhatTrangThai(
            $id,
            $trangThai
        );
    }

    public function xoaSuKien(int $id): bool
    {
        return $this->suKien->xoa($id);
    }

    public function suKienCoDangKy(int $id): bool
    {
        return $this->suKien->coDangKy($id);
    }

    // =========================================
    // THONG KE
    // =========================================

    public function tongSoSuKien(): int
    {
        return $this->thongKe->tongSoSuKien();
    }

    public function demSuKienTheoTrangThai(): array
    {
        return $this->thongKe->demTheoTrangThai();
    }

    public function tongLuotDangKy(): int
    {
        return $this->thongKe->tongLuotDangKy();
    }

    public function dangKyTheoSuKien(): array
    {
        return $this->thongKe->dangKyTheoSuKien();
    }

    // =========================================
    // QUAN LY TAI KHOAN BAN TO CHUC
    // =========================================

    public function danhSachTaiKhoanBanToChuc(
        string $tuKhoa = '',
        int $cauLacBoId = 0,
        int $limit = 10,
        int $offset = 0
    ): array {
        return $this->taiKhoan->danhSachBanToChuc(
            $tuKhoa,
            $cauLacBoId,
            $limit,
            $offset
        );
    }

    public function demTaiKhoanBanToChuc(
        string $tuKhoa = '',
        int $cauLacBoId = 0
    ): int {
        return $this->taiKhoan->demTaiKhoanBanToChuc(
            $tuKhoa,
            $cauLacBoId
        );
    }

    public function tongTaiKhoanBanToChuc(): int
    {
        return $this->taiKhoan->tongTaiKhoanBanToChuc();
    }

    public function taoTaiKhoanBanToChuc(
        string $hoTen,
        string $email,
        string $matKhau,
        int $cauLacBoId
    ): bool {
        return $this->taiKhoan->taoBanToChuc(
            $hoTen,
            $email,
            $matKhau,
            $cauLacBoId
        );
    }

    public function capNhatThongTinTaiKhoan(
        int $id,
        string $hoTen,
        string $email,
        int $cauLacBoId
    ): bool {
        return $this->taiKhoan->capNhatThongTin(
            $id,
            $hoTen,
            $email,
            $cauLacBoId
        );
    }

    public function danhSachClbChuaCoBanToChuc(): array
    {
        return $this->taiKhoan->danhSachClbChuaCoBanToChuc();
    }

    public function tuDongTaoTaiKhoanBanToChuc(
        array $clbIds
    ): array {
        return $this->taiKhoan->tuDongTaoTaiKhoanBanToChuc(
            $clbIds
        );
    }

    public function datLaiMatKhauTaiKhoan(
        int $id,
        string $matKhauMoi
    ): bool {
        return $this->taiKhoan->datLaiMatKhau(
            $id,
            $matKhauMoi
        );
    }
    public function xoaTaiKhoan(int $id): bool
{
    return $this->taiKhoan->xoa($id);
}

    // =========================================
    // QUAN LY TAI KHOAN THANH VIEN
    // =========================================

    public function danhSachTaiKhoanThanhVien(): array
    {
        return $this->taiKhoanThanhVien
            ->danhSachTaiKhoanThanhVien();
    }

    // =========================================
    // QUAN LY YEU CAU DAT LAI MAT KHAU
    // =========================================

    public function demYeuCauDatLaiMatKhauChoXuLy(): int
    {
        return $this->yeuCauDatLaiMatKhau
            ->demYeuCauChoXuLy();
    }

    public function danhSachYeuCauDatLaiMatKhauChoXuLy(): array
    {
        return $this->yeuCauDatLaiMatKhau
            ->danhSachYeuCauChoXuLy();
    }

    public function danhDauYeuCauDatLaiMatKhauDaXuLy(
        int $nguoiDungId,
        int $adminId
    ): bool {
        return $this->yeuCauDatLaiMatKhau
            ->danhDauDaXuLyTheoNguoiDung(
                $nguoiDungId,
                $adminId
            );
    }
}