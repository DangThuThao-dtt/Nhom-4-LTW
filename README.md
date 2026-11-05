# CHỦ ĐỀ 6: HỆ THỐNG QUẢN LÝ SỰ KIỆN/CÂU LẠC BỘ SINH VIÊN

## 1. Giới thiệu dự án

### 1.1. Mục tiêu

Xây dựng hệ thống quản lý sự kiện và câu lạc bộ sinh viên, hỗ trợ tổ chức, quản lý và tham gia các hoạt động trong trường.

Hệ thống được phát triển bằng PHP, MySQL và mô hình MVC, phân chia chức năng theo ba nhóm người dùng:

- **Khách/Thành viên:** Xem danh sách, tìm kiếm, lọc và xem chi tiết sự kiện; đăng ký tham gia; xem và hủy đăng ký của bản thân theo điều kiện cho phép.
- **Ban tổ chức:** Quản lý sự kiện thuộc phạm vi được phân công; xem danh sách người đăng ký; điểm danh và theo dõi tình hình tham gia.
- **Admin:** Quản lý câu lạc bộ, tài khoản và sự kiện toàn hệ thống; theo dõi các số liệu thống kê.

### 1.2. Công nghệ sử dụng

| Công nghệ | Mục đích |
|---|---|
| PHP | Xử lý nghiệp vụ phía máy chủ |
| MySQL/MariaDB | Lưu trữ dữ liệu |
| PDO | Kết nối và truy vấn cơ sở dữ liệu |
| HTML, CSS | Xây dựng giao diện |
| JavaScript | Xử lý tương tác phía trình duyệt |
| Fetch API | Gửi yêu cầu đăng ký/hủy đăng ký mà không tải lại trang |
| XAMPP | Môi trường chạy Apache và MySQL |
| Visual Studio Code | Soạn thảo mã nguồn |
| Git, GitHub | Quản lý phiên bản và tích hợp nhóm |

## 2. Danh sách thành viên và phân công công việc

| Thành viên | Nhiệm vụ | Nhánh Git | Minh chứng |
|---|---|---|---|
| Trương Mỹ Hoa | Dashboard Ban tổ chức; CRUD sự kiện; quản lý trạng thái; xem người đăng ký; điểm danh; thống kê sự kiện thuộc phạm vi Ban tổ chức | `feature/organizer-events` | Controller, View, Repository, truy vấn thống kê, kiểm thử phân quyền, commit |
| Trần Thị Bình Minh | Danh sách và chi tiết sự kiện; tìm kiếm, lọc theo ngày và câu lạc bộ | `feature/event-discovery` | View danh sách/chi tiết, Controller, CSS/JS, kiểm thử tìm kiếm và lọc |
| Đặng Thu Thảo | Đăng ký, hủy đăng ký, trang Đăng ký của tôi, tích hợp Fetch API | `feature/event-registration` | Controller, View, endpoint JSON, JavaScript Fetch API, kiểm thử đăng ký |
| Nguyễn Phương Anh | Dashboard quản lý câu lạc bộ/tài khoản; quản lý câu lạc bộ, tài khoản và vai trò | `feature/admin-clubs-users` | Controller, View, truy vấn PDO, kiểm thử phân quyền và dữ liệu |
| Bùi Mai Phương | Quản lý sự kiện toàn hệ thống, tài khoản Ban tổ chức; thống kê dành cho Admin; tổng hợp README, checklist kiểm thử và slide | `feature/admin-events-stats` | Controller, View, truy vấn thống kê, README, checklist, slide, commit |

## 3. Chức năng và quy tắc nghiệp vụ

### 3.1. Chức năng Ban tổ chức

- Xem Dashboard Ban tổ chức.
- Xem danh sách sự kiện thuộc phạm vi quản lý.
- Thêm sự kiện mới.
- Cập nhật thông tin sự kiện.
- Quản lý trạng thái sự kiện.
- Xem chi tiết sự kiện.
- Xem danh sách người đăng ký.
- Điểm danh người tham gia.
- Theo dõi số lượng đăng ký và các số liệu liên quan.

### 3.2. Chức năng Khách/Thành viên

- Xem danh sách sự kiện công khai.
- Tìm kiếm sự kiện theo từ khóa.
- Lọc sự kiện theo câu lạc bộ và ngày tổ chức.
- Xem chi tiết sự kiện.
- Đăng ký tham gia sự kiện còn chỗ và còn thời hạn.
- Xem danh sách đăng ký cá nhân.
- Hủy đăng ký khi đáp ứng điều kiện nghiệp vụ.

### 3.3. Chức năng Admin

- Xem Dashboard Admin.
- Quản lý câu lạc bộ.
- Xem danh sách tài khoản.
- Quản lý sự kiện toàn hệ thống.
- Xem chi tiết sự kiện.
- Theo dõi số lượng sự kiện và lượt đăng ký.
- Quản lý tài khoản Ban tổ chức trong phạm vi chức năng được triển khai.

## 4. Thiết kế biểu mẫu và kiểm tra dữ liệu

### 4.1. Biểu mẫu thêm/sửa sự kiện

**Thành viên phụ trách:** Trương Mỹ Hoa.

| Trường dữ liệu | Quy tắc kiểm tra |
|---|---|
| Tên sự kiện | Bắt buộc nhập, tối đa 200 ký tự |
| Mô tả | Không bắt buộc |
| Địa điểm | Không bắt buộc, tối đa 255 ký tự |
| Thời gian bắt đầu | Bắt buộc, phải là ngày giờ hợp lệ |
| Thời gian kết thúc | Không bắt buộc; nếu có, phải sau thời gian bắt đầu |
| Hạn đăng ký | Không bắt buộc; nếu có, phải trước hoặc bằng thời gian bắt đầu |
| Số lượng tối đa | Bắt buộc, là số nguyên lớn hơn hoặc bằng 1 |
| Trạng thái | Chỉ nhận các giá trị `nhap` hoặc `cong_khai` |

### 4.2. Biểu mẫu danh sách sự kiện

**Thành viên phụ trách:** Trần Thị Bình Minh.

| Trường dữ liệu | Quy tắc kiểm tra |
|---|---|
| Từ khóa tìm kiếm | Có thể để trống; loại bỏ khoảng trắng đầu/cuối |
| Câu lạc bộ | Chọn câu lạc bộ trong danh sách hoặc chọn tất cả |
| Ngày tổ chức | Phải là ngày hợp lệ |
| Khoảng ngày | Ngày bắt đầu không được lớn hơn ngày kết thúc |
| Trạng thái | Chỉ chấp nhận trạng thái hợp lệ của hệ thống |
| Mã sự kiện | Phải hợp lệ và tồn tại khi xem chi tiết |

Nếu không có kết quả phù hợp, giao diện hiển thị thông báo không tìm thấy sự kiện.

### 4.3. Biểu mẫu đăng ký và hủy đăng ký

**Thành viên phụ trách:** Đặng Thu Thảo.

| Trường dữ liệu | Ý nghĩa và quy tắc kiểm tra |
|---|---|
| `su_kien_id` | ID sự kiện được chọn; phải hợp lệ và tồn tại |
| `dang_ky_id` | ID lượt đăng ký cần hủy; phải hợp lệ và tồn tại |
| `nguoi_dung_id` | Lấy từ Session phía máy chủ; không tin tưởng ID do trình duyệt gửi lên |
| `action` | Xác định thao tác đăng ký hoặc hủy đăng ký theo quy ước API |

#### Điều kiện đăng ký

- Sự kiện phải tồn tại và có trạng thái `cong_khai`.
- Sự kiện phải còn thời hạn đăng ký.
- Số lượng đăng ký hợp lệ phải nhỏ hơn `so_luong_toi_da`.
- Không cho phép đăng ký trùng khi người dùng đã có lượt đăng ký chưa bị hủy.
- Nếu lượt đăng ký trước đó có trạng thái `da_huy`, việc đăng ký lại được xử lý theo quy tắc nghiệp vụ.
- Nếu không đáp ứng điều kiện, hệ thống từ chối yêu cầu và trả về thông báo phù hợp.

#### Điều kiện hủy đăng ký

- Chỉ cho phép hủy lượt đăng ký có trạng thái `da_dang_ky`.
- Người dùng chỉ được hủy lượt đăng ký thuộc về chính mình.
- Yêu cầu hủy phải được thực hiện trước thời gian bắt đầu sự kiện ít nhất 24 giờ (86.400 giây).
- Không cho phép hủy nếu lượt đăng ký không tồn tại, không thuộc người dùng hiện tại hoặc không đáp ứng điều kiện thời gian.
- Sau khi hủy thành công, trạng thái lượt đăng ký được cập nhật thành `da_huy`.

#### Kiểm tra sức chứa và dữ liệu

- Đối chiếu `nguoi_dung_id` và `su_kien_id` để kiểm tra đăng ký trùng.
- Đếm các lượt đăng ký hợp lệ để kiểm tra số chỗ còn lại.
- Đối chiếu thời điểm hiện tại với thời gian bắt đầu và hạn đăng ký.
- Kiểm tra quyền sở hữu lượt đăng ký trước khi hủy.
- Xác thực ID và tham số nhận từ yêu cầu trước khi truy vấn cơ sở dữ liệu.
- Chức năng đăng ký được báo cáo đã sử dụng Transaction và `FOR UPDATE` để hạn chế Race Condition; cần đối chiếu lại mã nguồn tích hợp trước khi xác nhận trên toàn hệ thống.

### 4.4. Quy tắc quản lý câu lạc bộ và tài khoản

**Thành viên phụ trách:** Nguyễn Phương Anh.

- Kiểm tra tên câu lạc bộ không được để trống.
- Kiểm tra câu lạc bộ có tồn tại trước khi cập nhật hoặc xóa.
- Kiểm tra mã người quản lý nếu nghiệp vụ yêu cầu.
- Sử dụng câu lệnh SQL có tham số.
- Thực hiện thao tác xóa bằng phương thức POST.
- Xử lý trường hợp không thể xóa do ràng buộc khóa ngoại.
- Kiểm tra tài khoản và vai trò theo cấu trúc dữ liệu thực tế.
- Chỉ cho phép tài khoản có quyền Admin truy cập chức năng quản trị.
- Kiểm tra quyền ở phía máy chủ, không chỉ ẩn nút trên giao diện.

### 4.5. Quy tắc quản lý sự kiện toàn hệ thống và thống kê

**Thành viên phụ trách:** Bùi Mai Phương.

- Chỉ tài khoản có quyền Admin mới được truy cập chức năng quản trị.
- Kiểm tra sự kiện có tồn tại trước khi xem chi tiết, cập nhật trạng thái hoặc xóa.
- Kiểm tra dữ liệu đầu vào ở phía máy chủ.
- Sử dụng câu lệnh SQL có tham số.
- Tính số lượng sự kiện từ dữ liệu thực tế trong cơ sở dữ liệu.
- Tính lượt đăng ký theo quan hệ giữa sự kiện và bảng đăng ký.
- Tránh đếm trùng bản ghi khi tổng hợp số liệu.
- Hiển thị số 0 hoặc thông báo phù hợp khi không có dữ liệu.
- Không sử dụng số liệu minh họa để trình bày như dữ liệu thật.

### 4.6. Quy tắc bảo mật chung

- Kiểm tra người dùng đã đăng nhập và có quyền phù hợp trước khi thực hiện chức năng.
- Kiểm tra mã CSRF đối với các yêu cầu thay đổi dữ liệu nếu được triển khai.
- Kiểm tra dữ liệu ở phía máy chủ, không chỉ dựa vào HTML validation.
- Sử dụng câu lệnh SQL có tham số để hạn chế SQL Injection.
- Kiểm tra quyền sở hữu dữ liệu trước khi chỉnh sửa, xóa hoặc hủy.
- Không cho phép người dùng thực hiện chức năng vượt quá quyền được cấp.
- Hiển thị thông báo phù hợp khi thao tác thành công hoặc thất bại.
- Không để lộ thông tin nhạy cảm hoặc lỗi nội bộ của hệ thống.

## 5. Route/API của hệ thống

Các route dưới đây được tổng hợp từ thông tin mã nguồn và kết quả kiểm thử do nhóm cung cấp. Do dự án có nhiều cách điều hướng khác nhau (`trang`, `action`, `page` và các file PHP trực tiếp), cần đối chiếu với `public/index.php`, Controller và API tương ứng trước khi sử dụng làm danh sách route chính thức.

### 5.1. Route Ban tổ chức – Trương Mỹ Hoa

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/Nhom-4-LTW/public/index.php?trang=them_su_kien` | Hiển thị form thêm sự kiện | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=luu_su_kien` | Lưu sự kiện mới | Đạt |
| GET | `/Nhom-4-LTW/public/index.php?trang=sua_su_kien&id={id}` | Hiển thị form sửa sự kiện | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=luu_sua_su_kien` | Cập nhật sự kiện | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=xoa_su_kien` | Xóa sự kiện | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=huy_su_kien` | Hủy sự kiện | Đạt |
| GET | `/Nhom-4-LTW/public/index.php?trang=chi_tiet_su_kien&id={id}` | Xem chi tiết sự kiện | Đạt |
| GET | `/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id={id}` | Xem danh sách người đăng ký | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=diem_danh` | Điểm danh người tham gia | Đạt |

`{id}` là mã sự kiện cần xem hoặc chỉnh sửa.

### 5.2. Route Khách/Thành viên – Trần Thị Bình Minh

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/Nhom-4-LTW/public/index.php?trang=su-kien` | Hiển thị danh sách sự kiện | Đạt |
| GET | `/Nhom-4-LTW/public/index.php?trang=chi-tiet&id=1` | Xem chi tiết sự kiện theo ID | Đạt |
| GET | `/Nhom-4-LTW/public/index.php?trang=su-kien&tu_khoa=...&cau_lac_bo_id=...` | Tìm kiếm và lọc sự kiện | Đạt |

### 5.3. Route đăng ký – Đặng Thu Thảo

| Phương thức | Route/API | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| POST | `/Nhom-4-LTW/public/api/dang_ky.php` | Đăng ký sự kiện qua Fetch API | Đạt |
| POST | `/Nhom-4-LTW/public/api/dang_ky.php` | Hủy đăng ký qua Fetch API | Đạt |
| GET | `/Nhom-4-LTW/public/thanh_vien/dang_ky_cua_toi.php` | Xem danh sách đăng ký cá nhân | Đạt |
| POST | `/Nhom-4-LTW/public/api/dang_ky.php` | Trả về JSON cho kết quả thành công hoặc lỗi | Đạt |

### 5.4. Route Admin – Nguyễn Phương Anh

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/?page=dashboard` | Dashboard Admin | Đạt |
| GET | `/?page=cau-lac-bo` | Danh sách câu lạc bộ | Đạt |
| GET/POST | `/?page=them-cau-lac-bo` | Hiển thị form và thêm câu lạc bộ | Đạt |
| GET/POST | `/?page=sua-cau-lac-bo&id={id}` | Sửa câu lạc bộ | Đạt |
| POST | `/?page=xoa-cau-lac-bo` | Xóa câu lạc bộ | Xóa thành công đã kiểm tra |
| GET | `/?page=tai-khoan` | Danh sách tài khoản | Đạt |

### 5.5. Route Admin – Bùi Mai Phương

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `public/index.php?action=quan-ly-su-kien` | Danh sách sự kiện toàn hệ thống |  Đạt |
| GET/POST | `public/index.php?action=them-su-kien` | Thêm sự kiện |  Đạt |
| GET/POST | `public/index.php?action=cap-nhat-su-kien` | Cập nhật sự kiện |  Đạt |
| GET/POST | `public/index.php?action=doi-trang-thai-su-kien` | Thay đổi trạng thái |  Đạt |
| GET/POST | `public/index.php?action=xoa-su-kien` | Xóa sự kiện |  Đạt |
| GET | `public/index.php?action=chi-tiet-su-kien` | Xem chi tiết sự kiện |  Đạt |
| GET | `public/index.php?action=thong-ke` | Xem thống kê |  Đạt |

> **Lưu ý:** Danh sách route là tài liệu tổng hợp. Chỉ ghi nhận route đã triển khai khi có xử lý tương ứng trong mã nguồn và đã kiểm tra trên môi trường chạy thực tế.

## 6. Kết quả kiểm thử

### 6.1. Kiểm thử form thêm/sửa sự kiện

**Thành viên phụ trách:** Trương Mỹ Hoa.

| STT | Trường hợp kiểm thử | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 1 | Mở form thêm sự kiện | Form hiển thị đầy đủ các trường | Đạt |
| 2 | Nhập dữ liệu hợp lệ và lưu | Sự kiện được lưu thành công | Đạt |
| 3 | Bỏ trống tên sự kiện | Từ chối dữ liệu không hợp lệ | Đạt |
| 4 | Nhập số lượng tối đa bằng 0 | Từ chối dữ liệu không hợp lệ | Đạt |
| 5 | Thời gian kết thúc trước thời gian bắt đầu | Từ chối dữ liệu không hợp lệ | Đạt |
| 6 | Mở form sửa sự kiện | Dữ liệu hiện tại được hiển thị | Đạt |
| 7 | Sửa thông tin hợp lệ | Dữ liệu được cập nhật | Đạt |
| 8 | Truy cập bằng tài khoản không đủ quyền | Từ chối truy cập | Đạt |

### 6.2. Checklist kiểm thử toàn hệ thống

| STT | Thành viên | Chức năng kiểm thử | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|---|
| 1 | SV1 | Mở form thêm sự kiện | Form hiển thị đúng | Đạt |
| 2 | SV1 | Lưu sự kiện hợp lệ | Sự kiện được lưu | Đạt |
| 3 | SV1 | Bỏ trống tên sự kiện | Từ chối dữ liệu | Đạt |
| 4 | SV1 | Nhập sức chứa bằng 0 | Từ chối dữ liệu | Đạt |
| 5 | SV1 | Thời gian kết thúc không hợp lệ | Từ chối dữ liệu | Đạt |
| 6 | SV1 | Sửa sự kiện hợp lệ | Dữ liệu được cập nhật | Đạt |
| 7 | SV1 | Kiểm tra quyền truy cập | Từ chối truy cập trái phép | Đạt |
| 8 | SV2 | Kiểm tra cú pháp PHP | Không có lỗi cú pháp | Đạt |
| 9 | SV2 | Xem danh sách sự kiện trên XAMPP | Hiển thị đúng dữ liệu CSDL | Đạt |
| 10 | SV2 | Xem chi tiết với ID hợp lệ/không hợp lệ | Hiển thị đúng sự kiện hoặc thông báo phù hợp | Đạt |
| 11 | SV2 | Tìm kiếm và lọc sự kiện | Kết quả đáp ứng điều kiện lọc | Đạt |
| 12 | SV3 | Đăng ký sự kiện còn chỗ | Đăng ký thành công | Đạt |
| 13 | SV3 | Đăng ký trùng sự kiện | Từ chối đăng ký trùng | Đạt |
| 14 | SV3 | Hủy đăng ký hợp lệ | Đăng ký được hủy | Đạt |
| 15 | SV3 | Đăng ký khi hết chỗ/đã đóng | Từ chối đăng ký | Đạt |
| 16 | SV4 | Quản lý câu lạc bộ | Thao tác đúng quyền | Đạt |
| 17 | SV4 | Quản lý tài khoản | Dữ liệu được xử lý đúng | Đạt |
| 18 | SV4 | Tài khoản thường truy cập chức năng Admin | Từ chối truy cập | Đạt |
| 19 | SV5 | Xem Dashboard Admin | Hiển thị dữ liệu tổng quan | Đạt |
| 20 | SV5 | Xem danh sách sự kiện toàn hệ thống | Danh sách khớp CSDL | Đạt |
| 21 | SV5 | Thêm và cập nhật sự kiện | Dữ liệu được lưu chính xác | Đạt |
| 22 | SV5 | Thay đổi trạng thái sự kiện | Trạng thái thay đổi đúng | Đạt |
| 23 | SV5 | Xóa sự kiện | Hệ thống xử lý đúng | Đạt |
| 24 | SV5 | Thống kê số lượng sự kiện | Số liệu chính xác | Đạt |
| 25 | SV5 | Thống kê lượt đăng ký | Số liệu chính xác, không đếm trùng | Đạt |
| 26 | SV5 | Kiểm tra quyền truy cập Admin | Tài khoản không đủ quyền bị từ chối | Đạt |

### 6.3. Minh chứng kiểm thử

Một số ảnh minh chứng đã được cung cấp: `docs/minh_chung/`

## 7. Kiến trúc và tích hợp Git

### 7.1. Kiến trúc MVC

Dự án tổ chức mã nguồn theo mô hình Model – View – Controller, kết hợp Repository để tách biệt thao tác dữ liệu.

- **Model/Repository:** Thực hiện truy vấn và thao tác dữ liệu với cơ sở dữ liệu.
- **Controller:** Tiếp nhận yêu cầu, xử lý nghiệp vụ và gọi Repository.
- **View:** Hiển thị dữ liệu và giao diện cho người dùng.
- **Public/API:** Tiếp nhận yêu cầu từ trình duyệt và trả về dữ liệu hoặc giao diện.

Mục tiêu của việc phân tách là giúp mã nguồn rõ ràng, dễ bảo trì và thuận tiện cho việc tích hợp các chức năng do nhiều thành viên phát triển.

### 7.2. Trương Mỹ Hoa – Ban tổ chức

- Nhánh Git: `feature/organizer-events`
- Repository: `app/Repositories/SuKienRepository.php`
- Controller: `app/Controllers/SuKienController.php`
- View: `app/Views/ban_to_chuc/`
- Commit trước refactor: `a2cc4fe` – `Hoan thien form them su kien va validation`
- Minh chứng: `docs/minh_chung/minh_chung_nguoi_1_my_hoa/`

**Nội dung refactor:** Chuyển các câu lệnh SQL và thao tác dữ liệu sự kiện từ Controller sang Repository; Controller gọi Repository để xử lý nghiệp vụ; View đảm nhiệm hiển thị giao diện.

### 7.3. Trần Thị Bình Minh

- Nhánh Git: `feature/event-discovery`
- Commit: `0ad329d`
- Pull Request: Chưa cập nhật.
- Wireframe Figma: [Xem thiết kế Figma](https://www.figma.com/design/qmhpkTAKk4V1wn8RcFlxtm)
- Kết quả kiểm tra cú pháp: Các file PHP trong gói đã vượt qua kiểm tra `php -l`.

**Nội dung thực hiện:**

- Xây dựng giao diện danh sách và chi tiết sự kiện dành cho thành viên.
- Hỗ trợ tìm kiếm và lọc sự kiện.
- Tích hợp chức năng thông qua route trong `public/index.php`.
- Refactor chức năng Đăng ký của tôi theo mô hình Repository – Controller – View.
- Tạo `DangKyRepository.php` để tập trung truy vấn và thao tác dữ liệu đăng ký.
- Cập nhật Controller để tiếp nhận yêu cầu, gọi Repository và chuyển dữ liệu sang View.
- Tách giao diện tại `app/Views/thanh_vien/dang_ky_cua_toi.php`.
- Xóa Model cũ `app/Models/dang_ky.php` sau khi xác nhận không còn được sử dụng.

### 7.4. Đặng Thu Thảo

- Nhánh Git: `feature/event-registration`
- Commit: `988f534`
- Minh chứng: `docs/minh_chung_nguoi_3/`

**Nội dung thực hiện:**

- Xây dựng chức năng đăng ký và hủy đăng ký sự kiện.
- Xây dựng trang Đăng ký của tôi.
- Tích hợp Fetch API để gửi yêu cầu mà không tải lại trang.
- Trả về JSON để giao diện hiển thị thông báo thành công hoặc lỗi.
- Refactor sang mô hình Repository – Controller – View.
- Chuyển SQL vào `DangKyRepository.php`.
- Xử lý nghiệp vụ trong `DangKyController.php`.
- Tách giao diện đăng ký cá nhân thành View riêng.
- Kiểm thử đăng ký thành công, đăng ký trùng, hết chỗ, hủy đăng ký và điều kiện hủy trước 24 giờ.
- Áp dụng Transaction và `FOR UPDATE` để hạn chế Race Condition theo kết quả kiểm thử được cung cấp.

### 7.5. Nguyễn Phương Anh

- Nhánh Git: `feature/admin-clubs-users`

**Các file liên quan:**

- Dashboard Admin: `app/Views/admin/dashboard.php`
- Quản lý câu lạc bộ: `app/Views/admin/cau_lac_bo.php`
- Quản lý tài khoản: `app/Views/admin/tai_khoan.php`
- Form thêm câu lạc bộ: `app/Views/admin/them_cau_lac_bo.php`
- Form sửa câu lạc bộ: `app/Views/admin/sua_cau_lac_bo.php`
- Controller: `app/Controllers/QuanLyAdminController.php`
- Controller tài khoản: `app/Controllers/QuanLyTaiKhoanController.php`
- Model câu lạc bộ: `app/Models/CauLacBo.php`
- Repository câu lạc bộ: `app/Repositories/CauLacBoRepository.php`

**Kết quả:** Đã xây dựng các màn hình quản lý câu lạc bộ và danh sách tài khoản; đã kiểm tra thao tác xóa câu lạc bộ thành công. Việc kết nối Repository với Controller và kiểm thử lại CRUD sau refactor cần được xác nhận thêm.

### 7.6. Bùi Mai Phương

- Nhánh Git: `feature/admin-events-stats`

**Các file liên quan:**

- Controller: `app/Controllers/AdminController.php`
- View quản lý sự kiện: `app/Views/admin/su_kien.php`
- View chi tiết sự kiện: `app/Views/admin/chi_tiet_su_kien.php`
- View thống kê: `app/Views/admin/thong_ke.php`
- View quản lý tài khoản: `app/Views/admin/tai_khoan.php`
- Repository tài khoản: `app/Repositories/TaiKhoanRepository.php`
- Checklist: `docs/checklist_kiem_thu_sv5.md`
- Danh sách route: `docs/routes_sv5.md`

**Nội dung thực hiện:**

- Xây dựng các giao diện quản trị sự kiện, chi tiết sự kiện và thống kê.
- Xây dựng chức năng quản lý tài khoản Ban tổ chức.
- Tách truy vấn và thao tác dữ liệu tài khoản sang `TaiKhoanRepository`.
- Tích hợp Repository với Controller thông qua PDO.
- Tổ chức tài liệu route và checklist kiểm thử thuộc phạm vi phụ trách.

## 8. Hướng dẫn cài đặt và chạy dự án

### 8.1. Công cụ cần chuẩn bị

- Windows.
- XAMPP.
- PHP.
- MySQL/MariaDB.
- Visual Studio Code.
- Git.
- Trình duyệt web.
- phpMyAdmin.

Kiểm tra phiên bản PHP bằng lệnh:

```bash
php -v
```

Có thể kiểm tra phiên bản MySQL/MariaDB trong phpMyAdmin hoặc bằng câu lệnh:

```sql
SELECT VERSION();
```

### 8.2. Lấy mã nguồn

Clone repository:

```bash
git clone https://github.com/DangThuThao-dtt/Nhom-4-LTW.git
```

Đặt thư mục dự án `Nhom-4-LTW` trong thư mục `htdocs` của XAMPP.

Đường dẫn thư mục dự án:

```text
C:\xampp\htdocs\Nhom-4-LTW
```

Mở thư mục dự án bằng Visual Studio Code.

### 8.3. Khởi động XAMPP

1. Mở XAMPP Control Panel.
2. Khởi động Apache.
3. Khởi động MySQL.
4. Mở trình duyệt và truy cập phpMyAdmin:

   [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)

### 8.4. Tạo cơ sở dữ liệu

1. Mở phpMyAdmin.
2. Tạo cơ sở dữ liệu có tên `quan_ly_su_kien`.
3. Chọn cơ sở dữ liệu vừa tạo.
4. Mở tab **Import**.
5. Nhập file SQL trong thư mục `database` của dự án.
6. Nếu có `schema.sql`, nhập file này trước để tạo cấu trúc bảng.
7. Sau đó nhập `seed.sql` để thêm dữ liệu mẫu.

Cơ sở dữ liệu của dự án sử dụng các bảng chính:

- `nguoi_dung`
- `cau_lac_bo`
- `su_kien`
- `dang_ky`
- `thanh_vien_cau_lac_bo`
- `thong_bao`
- `yeu_cau_dat_lai_mat_khau` 

### 8.5. Cấu hình kết nối cơ sở dữ liệu

Mở file:

```text
app/Core/ket_noi_csdl.php
```

Kiểm tra các thông tin kết nối:

| Thông tin | Giá trị |
|---|---|
| Host | `localhost` |
| Database | `quan_ly_su_kien` |
| Username | `root` |
| Password | Để trống nếu sử dụng cấu hình XAMPP mặc định |
| Charset | `utf8mb4` |

### 8.6. Truy cập dự án

Nếu thư mục dự án nằm tại `C:\xampp\htdocs\Nhom-4-LTW`, truy cập:

[http://localhost/Nhom-4-LTW/](http://localhost/Nhom-4-LTW/)

Trang ứng dụng chính:

[http://localhost/Nhom-4-LTW/public/](http://localhost/Nhom-4-LTW/public/)

### 8.7. Tài khoản kiểm thử

| Vai trò | Email kiểm thử | 
|---|---|---|
| Ban tổ chức | admin |  
| Thành viên | 260506hoa@gmail.com |  
| Admin | clb9_61f4314b02@nhom4.local |  

Thông tin tài khoản kiểm thử cần được bổ sung sau khi xác nhận dữ liệu trong `seed.sql` hoặc cơ sở dữ liệu đang sử dụng. Không đưa mật khẩu cá nhân hoặc thông tin nhạy cảm vào repository.

## 9. Tổng kết và tình trạng hoàn thành

### 9.1. Kết quả theo thành viên

**SV1 – Trương Mỹ Hoa**

- Hoàn thiện CRUD sự kiện thuộc phạm vi Ban tổ chức.
- Refactor thao tác cơ sở dữ liệu sang `SuKienRepository.php`.
- Sử dụng `SuKienController.php` để xử lý yêu cầu.
- Xây dựng giao diện View và kiểm tra dữ liệu đầu vào.
- Đã kiểm thử các trường hợp thêm, sửa, validation và phân quyền theo kết quả đã ghi tại Mục 6.

**SV2 – Trần Thị Bình Minh**

- Xây dựng giao diện danh sách và chi tiết sự kiện dành cho thành viên.
- Xây dựng chức năng tìm kiếm và lọc sự kiện.
- Refactor chức năng Đăng ký của tôi theo mô hình Repository – Controller – View.
- Tập trung truy vấn dữ liệu trong Repository, xử lý yêu cầu trong Controller và hiển thị giao diện trong View.
- Đã kiểm tra cú pháp PHP; các kiểm thử tích hợp danh sách, chi tiết và lọc sự kiện cần được xác nhận trên XAMPP.

**SV3 – Đặng Thu Thảo**

- Hoàn thiện chức năng đăng ký, hủy đăng ký và trang Đăng ký của tôi.
- Tích hợp Fetch API.
- Trả về JSON để giao diện hiển thị thông báo.
- Refactor sang mô hình Repository – Controller – View.
- Kiểm thử đăng ký thành công, đăng ký trùng, hết chỗ, hủy đăng ký và điều kiện thời gian hủy theo kết quả đã cung cấp.

**SV4 – Nguyễn Phương Anh**

- Xây dựng Dashboard Admin, danh sách câu lạc bộ, form thêm/sửa câu lạc bộ và danh sách tài khoản.
- Đã kiểm tra thao tác xóa câu lạc bộ thành công.
- Tạo Repository câu lạc bộ để tách truy vấn SQL khỏi Controller.
- Việc kết nối Repository, kiểm thử CRUD sau refactor, cập nhật tài khoản/vai trò và xác minh phân quyền cần được hoàn thiện hoặc xác nhận thêm.

**SV5 – Bùi Mai Phương**

- Phát triển các chức năng quản trị Admin thuộc phạm vi được phân công.
- Xây dựng giao diện quản lý sự kiện, chi tiết sự kiện và thống kê.
- Xây dựng chức năng quản lý tài khoản Ban tổ chức.
- Tạo `TaiKhoanRepository.php` để tách thao tác dữ liệu tài khoản khỏi Controller.
- Tổ chức tài liệu route và checklist kiểm thử.
- Các chức năng và kết quả kiểm thử Admin cần được xác nhận bằng kiểm thử thực tế trước khi ghi nhận hoàn thành.

### 9.2. Minh chứng nộp bài

- **Repository GitHub:** [DangThuThao-dtt/Nhom-4-LTW](https://github.com/DangThuThao-dtt/Nhom-4-LTW)
- **Ảnh minh chứng:** `docs/minh_chung/`.
- **Slide thuyết trình:** Nhom-4-LTW.pptx
- **Wireframe:** [Figma – Thiết kế giao diện](https://www.figma.com/design/qmhpkTAKk4V1wn8RcFlxtm)
- **Người tổng hợp README:** Trương Mỹ Hoa.


## 10. Kết luận

Dự án **Hệ thống quản lý sự kiện/câu lạc bộ sinh viên** được xây dựng nhằm hỗ trợ quản lý sự kiện và hoạt động câu lạc bộ trong môi trường sinh viên.

Thông qua quá trình thực hiện, nhóm vận dụng PHP, MySQL, PDO, mô hình MVC, Repository, Fetch API và Git để phát triển các chức năng quản lý sự kiện, đăng ký tham gia, quản lý câu lạc bộ, tài khoản và thống kê.

Việc phân chia nhiệm vụ theo thành viên giúp nhóm phát triển các chức năng độc lập, đồng thời thực hiện tích hợp mã nguồn và kiểm thử theo phạm vi được phân công.

README này tổng hợp mục tiêu, chức năng, quy tắc nghiệp vụ, route, kết quả kiểm thử, kiến trúc mã nguồn và hướng dẫn cài đặt dự án.
