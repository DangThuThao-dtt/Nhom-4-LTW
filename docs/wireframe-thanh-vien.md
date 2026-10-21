# THIẾT KẾ WIREFRAME GIAO DIỆN KHÁCH/THÀNH VIÊN

## 1. Mục tiêu
Thiết kế wireframe cho chức năng khám phá sự kiện trong hệ thống quản lý sự kiện/câu lạc bộ sinh viên.
Giao diện giúp khách hoặc thành viên xem danh sách sự kiện, tìm kiếm theo tên, lọc theo câu lạc bộ, ngày tổ chức và trạng thái, đồng thời xem thông tin chi tiết của từng sự kiện.

## 2. Phạm vi thực hiện
Phần giao diện do thành viên phụ trách gồm hai màn hình chính:
- Màn hình danh sách và khám phá sự kiện.
- Màn hình chi tiết sự kiện.

## 3. Công cụ thiết kế
- Công cụ: Figma.
- Loại thiết kế: Wireframe giao diện web.
- Link thiết kế: https://www.figma.com/design/qmhpkTAKk4V1wn8RcFlxtm

## 4. Màn hình 1 — Danh sách sự kiện
### 4.1. Mục đích
Cho phép người dùng xem các sự kiện hiện có và tìm sự kiện phù hợp với nhu cầu.

### 4.2. Thành phần giao diện
Thanh điều hướng: Hiển thị tên hệ thống và các mục điều hướng chính 
Ô tìm kiếm: Nhập tên hoặc từ khóa liên quan đến sự kiện 
Bộ lọc câu lạc bộ: Lọc sự kiện theo câu lạc bộ tổ chức 
Bộ lọc ngày tổ chức: Chọn ngày hoặc khoảng ngày cần tìm 
Bộ lọc trạng thái: Lọc sự kiện theo trạng thái 
Nút xóa bộ lọc: Đưa các điều kiện lọc về trạng thái mặc định 
Danh sách sự kiện: Hiển thị các thẻ sự kiện 
Thẻ sự kiện: Hiển thị tên, câu lạc bộ, thời gian, địa điểm, số chỗ và trạng thái 
Nút xem chi tiết: Chuyển đến màn hình chi tiết sự kiện 
Thông báo kết quả: Thông báo khi không tìm thấy sự kiện phù hợp 

### 4.3. Quy tắc hiển thị
- Dữ liệu sự kiện phải lấy từ cơ sở dữ liệu của hệ thống khi chức năng được tích hợp.
- Các sự kiện cần hiển thị thông tin cơ bản để người dùng dễ lựa chọn.
- Trạng thái và số chỗ còn lại phải phản ánh dữ liệu hiện có của sự kiện.
- Nếu không tìm thấy kết quả, hệ thống hiển thị thông báo phù hợp.
- Sự kiện hết chỗ hoặc đã đóng đăng ký phải được thể hiện rõ ràng.

## 5. Màn hình 2 — Chi tiết sự kiện
### 5.1. Mục đích
Cung cấp đầy đủ thông tin để người dùng tìm hiểu sự kiện trước khi quyết định tham gia.

### 5.2. Thành phần giao diện
Nút quay lại: Quay lại danh sách sự kiện 
Tên sự kiện: Hiển thị tên sự kiện 
Hình ảnh: Hiển thị hình ảnh đại diện nếu có 
Thông tin câu lạc bộ: Hiển thị đơn vị tổ chức 
Mô tả: Giới thiệu nội dung và hoạt động của sự kiện 
Thời gian: Hiển thị thời gian bắt đầu và kết thúc 
Địa điểm: Hiển thị nơi tổ chức 
Hạn đăng ký: Hiển thị thời hạn đăng ký nếu có 
Số chỗ còn lại: Hiển thị số lượng chỗ còn có thể tham gia 
Trạng thái sự kiện: Cho biết sự kiện đang mở đăng ký, hết chỗ hoặc đã đóng 
Nút đăng ký tham gia: Chuyển sang luồng đăng ký của hệ thống khi được phép 

### 5.3. Quy tắc hiển thị
- Thông tin chi tiết phải tương ứng với sự kiện người dùng đã chọn.
- Không hiển thị nút đăng ký như một hành động khả dụng khi sự kiện đã đóng đăng ký hoặc hết chỗ.
- Nếu thiếu thông tin tùy chọn như hình ảnh, giao diện cần có cách hiển thị thay thế phù hợp.
- Nút đăng ký được kết nối với chức năng đăng ký do thành viên phụ trách nghiệp vụ triển khai.

## 6. Luồng thao tác người dùng
1. Người dùng mở trang danh sách sự kiện.
2. Người dùng nhập từ khóa hoặc chọn các điều kiện lọc.
3. Hệ thống hiển thị danh sách sự kiện phù hợp.
4. Người dùng nhấn **Xem chi tiết** tại một sự kiện.
5. Hệ thống hiển thị thông tin chi tiết của sự kiện đã chọn.
6. Nếu sự kiện còn cho phép đăng ký, người dùng có thể chuyển sang luồng đăng ký của hệ thống.
7. Người dùng có thể quay lại danh sách để tiếp tục tìm kiếm.
