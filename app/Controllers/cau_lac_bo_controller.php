
<?php

require_once __DIR__ . '/../Models/CauLacBo.php';

class CauLacBoThanhVienController
{
    private CauLacBo $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new CauLacBo($pdo);
    }

    public function danhSach(): void
    {
        $tuKhoa = trim($_GET['tu_khoa'] ?? '');
        $tuKhoa = mb_substr($tuKhoa, 0, 100);

        $danhSachCauLacBo = $this->model->layDanhSach($tuKhoa);

        require __DIR__ . '/../Views/thanh_vien/danh_sach_clb.php';
    }
}
