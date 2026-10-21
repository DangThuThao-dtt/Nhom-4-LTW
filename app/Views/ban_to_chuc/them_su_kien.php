
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sự kiện</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        button, .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            background: #2457a7;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Thêm sự kiện</h1>



<form action="/Nhom-4-LTW/public/index.php?trang=luu_su_kien" method="POST">
    <input type="hidden" name="csrf_token"
       value="<?= htmlspecialchars(taoCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">


        <label for="ten_su_kien">Tên sự kiện</label>
        <input id="ten_su_kien" name="ten_su_kien"
               type="text" maxlength="200" required>

        <label for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta" rows="4"></textarea>

        <label for="dia_diem">Địa điểm</label>
        <input id="dia_diem" name="dia_diem" type="text" maxlength="255">

        <label for="thoi_gian_bat_dau">Thời gian bắt đầu</label>
        <input id="thoi_gian_bat_dau" name="thoi_gian_bat_dau"
               type="datetime-local" required>

        <label for="thoi_gian_ket_thuc">Thời gian kết thúc</label>
        <input id="thoi_gian_ket_thuc" name="thoi_gian_ket_thuc"
               type="datetime-local">

<label for="han_dang_ky">Hạn đăng ký</label>
<input id="han_dang_ky" name="han_dang_ky"
       type="datetime-local">


        <label for="so_luong_toi_da">Số lượng tối đa</label>
        <input id="so_luong_toi_da" name="so_luong_toi_da"
               type="number" min="1" value="100" required>

        <label for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai">
            <option value="nhap">Bản nháp</option>
            <option value="cong_khai">Công khai</option>
        </select>

        <button type="submit">Lưu sự kiện</button>
    </form>

    <a href="/Nhom-4-LTW/public/index.php?trang=danh_sach">
    Quay lại danh sách
</a>
</div>


</body>
</html>
