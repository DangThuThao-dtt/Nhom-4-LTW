# CHỦ ĐỀ 6: HỆ THỐNG QUẢN LÝ SỰ KIỆN/CÂU LẠC BỘ SINH VIÊN

## 1\. Danh sách thành viên và phân công công việc

### 1\. Trương Mỹ Hoa Trang – Ban tổ chức

* Tạo, xem, sửa và xóa sự kiện thuộc phạm vi được phép.
* Quản lý đăng ký và số chỗ còn lại.
* Đóng đăng ký khi hết hạn.
* Xem danh sách người tham gia.
* Điểm danh người đã đăng ký hợp lệ.
* Thống kê người tham gia và lượt đăng ký.

### 2\. Trần Thị Bình Minh – Trang Khách/Thành viên

* Xem danh sách và chi tiết sự kiện.
* Lọc sự kiện theo ngày và câu lạc bộ.
* Hiển thị trạng thái sự kiện và số chỗ còn lại.
* Thiết kế giao diện và liên kết các trang sự kiện.

### 3\. Đặng Thu Thảo – Trang Khách/Thành viên

* Đăng ký sự kiện còn chỗ.
* Xem danh sách đăng ký của bản thân.
* Hủy đăng ký khi còn trong thời hạn cho phép.
* Kiểm tra đăng ký trùng, sự kiện hết hạn, đã đóng hoặc hết chỗ.
* Tích hợp Fetch API cho ít nhất một thao tác phù hợp.
* Hiển thị thông báo thành công hoặc lỗi.

### 4\. Nguyễn Phương Anh – Trang Admin

* Xem danh sách câu lạc bộ.
* Thêm, sửa và xóa câu lạc bộ.
* Xem danh sách tài khoản.
* Quản lý thông tin tài khoản và vai trò theo quyền Admin.
* Kiểm tra quyền truy cập các chức năng quản trị.

### 5\. Bùi Mai Phương – Trang Admin

* Xem toàn bộ sự kiện.
* Quản lý sự kiện trên phạm vi toàn hệ thống.
* Thống kê số lượt đăng ký theo sự kiện.
* Thiết kế giao diện Dashboard Admin.
* Kiểm thử các chức năng quản trị và phối hợp tích hợp hệ thống.

## 2. Thiết kế biểu mẫu và kiểm tra dữ liệu

### 2.1. Biểu mẫu

Form thêm sự kiện dành cho Ban tổ chức, gồm các trường:

- Tên sự kiện: bắt buộc, tối đa 200 ký tự.
- Mô tả: không bắt buộc.
- Địa điểm: không bắt buộc, tối đa 255 ký tự.
- Thời gian bắt đầu: bắt buộc, phải là ngày giờ hợp lệ.
- Thời gian kết thúc: không bắt buộc; nếu có phải sau thời gian bắt đầu.
- Hạn đăng ký: không bắt buộc; nếu có phải trước hoặc bằng thời gian bắt đầu.
- Số lượng tối đa: bắt buộc, là số nguyên lớn hơn hoặc bằng 1.
- Trạng thái: chỉ nhận `nhap` hoặc `cong_khai`.

### 2.2. Quy tắc xử lý và bảo mật

- Kiểm tra người dùng đã đăng nhập và có quyền phù hợp.
- Kiểm tra mã CSRF khi gửi biểu mẫu.
- Kiểm tra dữ liệu ở phía máy chủ, không chỉ dựa vào thuộc tính HTML.
- Sử dụng câu lệnh SQL có tham số để hạn chế SQL Injection.
- Từ chối dữ liệu không hợp lệ và không lưu sự kiện khi kiểm tra thất bại.

### 2.3. Route dự kiến

- `GET /Nhom-4-LTW/public/index.php?trang=them_su_kien`: hiển thị biểu mẫu thêm sự kiện.
- `POST /Nhom-4-LTW/public/index.php?trang=luu_su_kien`: kiểm tra dữ liệu và lưu sự kiện.

### 2.4. Phân công thực hiện

- Trương Mỹ Hoa Trang: chức năng Ban tổ chức, quản lý sự kiện và đăng ký tham gia.
- Trần Thị Bình Minh: giao diện khách và thành viên.
- Đặng Thu Thảo: đăng ký tham gia, quản lý đăng ký cá nhân và kiểm tra điều kiện đăng ký.
- Nguyễn Phương Anh: quản trị câu lạc bộ, tài khoản và phân quyền.
- Bùi Mai Phương: quản trị sự kiện, trang tổng quan, thống kê và kiểm thử.