# PHẦN 1: THIẾT KẾ WIREFRAME GIAO DIỆN KHÁCH/THÀNH VIÊN

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

## 4. Màn hình 1 — Danh sách sự kiện:Cho phép người dùng xem các sự kiện hiện có và tìm sự kiện phù hợp với nhu cầu.
### 4.1. Thành phần giao diện
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

### 4.2. Quy tắc hiển thị
- Dữ liệu sự kiện phải lấy từ cơ sở dữ liệu của hệ thống khi chức năng được tích hợp.
- Các sự kiện cần hiển thị thông tin cơ bản để người dùng dễ lựa chọn.
- Trạng thái và số chỗ còn lại phải phản ánh dữ liệu hiện có của sự kiện.
- Nếu không tìm thấy kết quả, hệ thống hiển thị thông báo phù hợp.
- Sự kiện hết chỗ hoặc đã đóng đăng ký phải được thể hiện rõ ràng.

## 5. Màn hình 2 — Chi tiết sự kiện: Cung cấp đầy đủ thông tin để người dùng tìm hiểu sự kiện trước khi quyết định tham gia.
### 5.1. Thành phần giao diện
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

### 5.2. Quy tắc hiển thị
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

# PHẦN 2: QUY TẮC KIỂM TRA DỮ LIỆU VÀ QUY TẮC NGHIỆP VỤ
## 2.1. Quy tắc kiểm tra dữ liệu (Validation)
- **Từ khóa tìm kiếm:** Có thể để trống; loại bỏ khoảng trắng ở đầu và cuối; giới hạn tối đa 100 ký tự.
- **Câu lạc bộ:** Người dùng có thể chọn một câu lạc bộ trong danh sách hoặc chọn “Tất cả” để xem sự kiện của mọi câu lạc bộ.
- **Ngày tổ chức:** Ngày được chọn phải hợp lệ. Nếu lọc theo khoảng ngày, ngày bắt đầu không được lớn hơn ngày kết thúc.
- **Trạng thái sự kiện:** Chỉ chấp nhận các trạng thái có trong hệ thống hoặc chọn “Tất cả”.
- **Mã sự kiện:** Phải là mã hợp lệ và tồn tại trong cơ sở dữ liệu. Nếu không tìm thấy, hệ thống hiển thị thông báo phù hợp.

## 2.2. Quy tắc nghiệp vụ
- **Hiển thị danh sách sự kiện:** Chỉ hiển thị các sự kiện phù hợp với từ khóa tìm kiếm và điều kiện lọc mà người dùng đã chọn.
- **Tìm kiếm sự kiện:** Không phân biệt khoảng trắng thừa ở đầu và cuối từ khóa. Nếu không có kết quả phù hợp, hệ thống thông báo “Không tìm thấy sự kiện phù hợp”.
- **Lọc sự kiện:** Khi chọn câu lạc bộ, ngày tổ chức hoặc trạng thái, danh sách phải được cập nhật theo điều kiện tương ứng. Có thể kết hợp nhiều điều kiện lọc.
- **Xem chi tiết sự kiện:** Khi người dùng chọn một sự kiện, hệ thống hiển thị thông tin chi tiết của đúng sự kiện đó. Nếu sự kiện không tồn tại, hệ thống thông báo và không hiển thị thông tin sai.
- **Trạng thái đăng ký:** Sự kiện đã đóng đăng ký hoặc đã hết hạn đăng ký không được tiếp tục đăng ký. Việc xử lý đăng ký cần thống nhất với thành viên phụ trách chức năng đăng ký sự kiện.
- **Số lượng chỗ còn lại:** Số chỗ còn lại được hiển thị dựa trên dữ liệu của hệ thống và không được nhỏ hơn 0.
- **An toàn dữ liệu:** Dữ liệu tìm kiếm và các tham số được gửi lên hệ thống phải được kiểm tra. Khi truy vấn cơ sở dữ liệu, sử dụng câu lệnh có tham số để hạn chế nguy cơ SQL Injection.
- 
# PHẦN 3: XÁC ĐỊNH ROUTE DỰ KIẾN
## 3.1. Danh sách route
- **Trang chủ / danh sách sự kiện:** `index.php` — Hiển thị danh sách sự kiện đang có trong hệ thống.
- **Tìm kiếm và lọc sự kiện:** `index.php?keyword=...&club_id=...&status=...` — Tìm kiếm theo từ khóa và lọc theo câu lạc bộ, trạng thái sự kiện.
- **Lọc theo ngày tổ chức:** `index.php?start_date=...&end_date=...` — Hiển thị các sự kiện trong khoảng thời gian được chọn.
- **Xem chi tiết sự kiện:** `event-detail.php?id=...` — Hiển thị thông tin chi tiết của sự kiện theo mã sự kiện.
- **Đăng ký tham gia sự kiện:** `event-register.php?id=...` — Chuyển đến chức năng đăng ký sự kiện, phối hợp với thành viên phụ trách nghiệp vụ đăng ký.

## 3.2. Quy tắc sử dụng route
- Các tham số tìm kiếm và lọc được truyền qua URL bằng phương thức GET.
- Mã sự kiện phải được kiểm tra trước khi truy vấn và hiển thị chi tiết.
- Nếu mã sự kiện không hợp lệ hoặc không tồn tại, hệ thống hiển thị thông báo phù hợp.
- Chức năng đăng ký phải kiểm tra trạng thái đăng ký và số chỗ còn lại trước khi tiếp nhận yêu cầu.
- Các route trên là đường dẫn dự kiến, cần thống nhất với cấu trúc thư mục và cách định tuyến hiện tại của dự án trước khi tích hợp.
