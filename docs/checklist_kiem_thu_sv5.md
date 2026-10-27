# CHECKLIST KIỂM THỬ – SV5

**Phạm vi:** Quản lý sự kiện, quản lý ban tổ chức và thống kê.

**Quy ước:** Chỉ điền kết quả thực tế sau khi chạy kiểm thử. Trường hợp chưa chạy phải để trống cột kết quả và trạng thái.

| STT | Chức năng | Trường hợp kiểm thử | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|---:|---|---|---|---|---|
| 1 | Sự kiện | Mở danh sách sự kiện | Hiển thị danh sách | | |
| 2 | Sự kiện | Xem chi tiết sự kiện | Hiển thị thông tin đúng | | |
| 3 | Sự kiện | Thêm sự kiện với dữ liệu hợp lệ | Lưu thành công | | |
| 4 | Sự kiện | Thêm sự kiện thiếu tên | Hiển thị thông báo phù hợp | | |
| 5 | Sự kiện | Thêm sự kiện thiếu thời gian | Hiển thị thông báo phù hợp | | |
| 6 | Sự kiện | Cập nhật sự kiện hợp lệ | Lưu thông tin mới | | |
| 7 | Sự kiện | Cập nhật mã sự kiện không tồn tại | Xử lý phù hợp, không cập nhật nhầm | | |
| 8 | Sự kiện | Thay đổi trạng thái sự kiện | Trạng thái được cập nhật | | |
| 9 | Sự kiện | Xóa sự kiện tồn tại | Xử lý xóa hoặc xác nhận xóa | | |
| 10 | Sự kiện | Xóa sự kiện không tồn tại | Hiển thị thông báo phù hợp | | |
| 11 | Sự kiện | Tìm kiếm sự kiện (nếu có) | Hiển thị kết quả phù hợp | | |
| 12 | Ban tổ chức | Mở danh sách ban tổ chức | Hiển thị danh sách | | |
| 13 | Ban tổ chức | Thêm thành viên hợp lệ | Lưu thông tin thành công | | |
| 14 | Ban tổ chức | Thêm thành viên thiếu thông tin bắt buộc | Hiển thị thông báo phù hợp | | |
| 15 | Ban tổ chức | Cập nhật thông tin thành viên | Lưu thông tin mới | | |
| 16 | Ban tổ chức | Tìm thành viên không tồn tại | Hiển thị thông báo phù hợp | | |
| 17 | Thống kê | Mở trang thống kê | Hiển thị trang thống kê | | |
| 18 | Thống kê | Đối chiếu tổng số sự kiện với dữ liệu nguồn | Số liệu khớp dữ liệu nguồn | | |
| 19 | Thống kê | Kiểm tra khi chưa có dữ liệu | Hiển thị trạng thái rỗng phù hợp | | |
| 20 | Tích hợp | Truy cập các trang qua route | Điều hướng đúng, không phát sinh lỗi PHP | | |
