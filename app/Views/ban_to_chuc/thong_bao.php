
<?php
require_once __DIR__ . '/../../Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../Core/xac_thuc.php';
class ThongBao
{
    public static function taoNeuChuaCo(
        PDO $pdo,
        int $nguoiDungId,
        ?int $suKienId,
        string $loai,
        string $tieuDe,
        string $noiDung
    ): void {
        $sql = "
            SELECT id
            FROM thong_bao
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id <=> :su_kien_id
              AND loai = :loai
              AND tieu_de = :tieu_de
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'loai' => $loai,
            'tieu_de' => $tieuDe
        ]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return;
        }

        $sql = "
            INSERT INTO thong_bao (
                nguoi_dung_id,
                su_kien_id,
                tieu_de,
                noi_dung,
                loai,
                da_doc,
                ngay_tao
            ) VALUES (
                :nguoi_dung_id,
                :su_kien_id,
                :tieu_de,
                :noi_dung,
                :loai,
                0,
                NOW()
            )
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
            'loai' => $loai
        ]);
    }
}

yeuCauVaiTro(['ban_to_chuc', 'admin']);

$nguoiDung = nguoiDungHienTai();

// Lấy danh sách thông báo của người dùng hiện tại
$sql = "
    SELECT
        tb.id,
        tb.su_kien_id,
        tb.tieu_de,
        tb.noi_dung,
        tb.loai,
        tb.da_doc,
        tb.ngay_tao
    FROM thong_bao tb
    WHERE tb.nguoi_dung_id = :nguoi_dung_id
    ORDER BY tb.ngay_tao DESC, tb.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'nguoi_dung_id' => (int) $nguoiDung['id']
]);

$danhSachThongBao = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm số thông báo chưa đọc
$soChuaDoc = 0;

foreach ($danhSachThongBao as $tb) {
    if (!(int) $tb['da_doc']) {
        $soChuaDoc++;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Thông báo</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #14532d;
            --primary-dark: #0f3d25;
            --primary-light: #e8f3ec;
            --text: #1f2937;
            --muted: #64748b;
            --border: #e2e8f0;
            --surface: #ffffff;
            --background: #f4f7f5;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--background);
            color: var(--text);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 36px 24px;
        }

        /* Tiêu đề trang */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0 0 10px;
            color: var(--primary-dark);
            font-size: 30px;
            font-weight: 750;
            letter-spacing: -0.5px;
        }

        .summary {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
        }

        /* Nút quay lại */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 9px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s, transform .2s;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Thẻ thông báo */
        .notice {
            position: relative;
            background: var(--surface);
            padding: 22px 24px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            border-left: 5px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
            transition: box-shadow .2s, transform .2s;
        }

        .notice:hover {
            box-shadow: 0 7px 20px rgba(15, 23, 42, .08);
            transform: translateY(-2px);
        }

        .notice.unread {
            background: #f7fbf8;
            border-left-color: var(--primary);
        }

        .notice.warning {
            border-left-color: #d97706;
        }

        .notice.danger {
            border-left-color: #dc2626;
        }

        /* Tiêu đề thông báo */
        .notice h3 {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 0 12px;
            color: #1e293b;
            font-size: 17px;
            font-weight: 700;
        }

        /* Nhãn chưa đọc */
        .unread-label {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 20px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        /* Nội dung */
        .notice p {
            margin: 0 0 14px;
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        /* Thời gian */
        .meta {
            color: var(--muted);
            font-size: 13px;
        }

        /* Link xem sự kiện */
        .notice a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 650;
            text-decoration: none;
        }

        .notice a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Chưa có thông báo */
        .empty {
            padding: 52px 24px;
            text-align: center;
            background: var(--surface);
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            color: var(--muted);
            font-size: 15px;
        }

        /* Hiển thị trên điện thoại */
        @media (max-width: 600px) {
            .container {
                padding: 24px 16px;
            }

            .header h1 {
                font-size: 25px;
            }

            .notice {
                padding: 18px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <header class="header">
        <div>
            <h1>🔔 Thông báo</h1>

            <p class="summary">
                Bạn có
                <strong><?= $soChuaDoc ?></strong>
                thông báo chưa đọc.
            </p>
        </div>

        <a
            class="btn"
            href="/Nhom-4-LTW/public/index.php?trang=ban_to_chuc"
        >
            ← Quay lại Dashboard
        </a>
    </header>

    <?php if (empty($danhSachThongBao)): ?>

        <div class="empty">
            Chưa có thông báo nào.
        </div>

    <?php else: ?>

        <?php foreach ($danhSachThongBao as $tb): ?>

            <?php
            $loai = in_array(
                $tb['loai'],
                ['warning', 'danger'],
                true
            ) ? $tb['loai'] : '';

            $tieuDe = htmlspecialchars(
                $tb['tieu_de'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            );

            $noiDung = htmlspecialchars(
                $tb['noi_dung'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            );

            $ngayTao = htmlspecialchars(
                $tb['ngay_tao'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            );

            $chuaDoc = !(int) $tb['da_doc'];
            ?>

            <article class="notice <?= $loai ?> <?= $chuaDoc ? 'unread' : '' ?>">

                <h3>
                    <?= $tieuDe ?>

                    <?php if ($chuaDoc): ?>
                        <span class="unread-label">
                            CHƯA ĐỌC
                        </span>
                    <?php endif; ?>
                </h3>

                <p>
                    <?= nl2br($noiDung) ?>
                </p>

                <div class="meta">
                    <?= $ngayTao ?>
                </div>

                <?php if (!empty($tb['su_kien_id'])): ?>

                    <p style="margin-top: 14px;">
                        <a
                            href="/Nhom-4-LTW/public/index.php?trang=chi_tiet_su_kien&id=<?= (int) $tb['su_kien_id'] ?>"
                        >
                            Xem sự kiện →
                        </a>
                    </p>

                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
</body>
</html>
