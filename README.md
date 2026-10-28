
<h1 align="center">HỆ THỐNG QUẢN LÝ VÀ BÁN HÀNG TRỰC TUYẾN </h1>

# Sinh viên thực hiện
- Đồng Thị Ánh - 23013883
- Bùi Thị Hồng Tươi - 23015124
- Nguyễn Minh Phương - 23015738


# Mô Tả Dự Án
Dự án Hệ thống quản lý và bán hàng trực tuyến được xây dựng nhằm mô phỏng một website thương mại điện tử hỗ trợ mua bán sản phẩm trên nền tảng web. Hệ thống giúp người dùng có thể xem, tìm kiếm và đặt mua sản phẩm một cách dễ dàng, đồng thời hỗ trợ quản lý đơn hàng và thông tin khách hàng.

Bên cạnh đó, hệ thống cung cấp giao diện quản trị giúp quản lý sản phẩm, đơn hàng, người dùng và thống kê doanh thu. Ngoài ra, dự án còn tích hợp Chatbot AI để hỗ trợ tư vấn và nâng cao trải nghiệm người dùng.

Dự án được thực hiện nhằm áp dụng kiến thức về lập trình web, cơ sở dữ liệu và phát triển hệ thống thực tế.


# Chức năng chính
1. Luồng Khách hàng

Khám phá & tương tác
- Người dùng mở app → tìm kiếm / lọc sản phẩm
- Xem chi tiết sản phẩm
- Thực hiện đánh giá, bình luận hoặc thêm vào wishlist
  
Giao dịch (Đặt hàng)
- Thêm sản phẩm vào giỏ hàng
- Vào giỏ → áp mã giảm giá
- Nhấn đặt hàng → nhập địa chỉ + chọn thanh toán
- Hệ thống kiểm tra → tạo đơn hàng
- Thông báo đặt hàng thành công
  
Cá nhân
- Người dùng cập nhật thông tin cá nhân
- Quản lý địa chỉ nhận hàng
- Xem lịch sử đơn hàng
  
Theo dõi đơn hàng
- Đơn hàng được tạo → trạng thái: Chờ xác nhận
- Cập nhật lần lượt: → Đã xác nhận → Đang đóng gói → Đang giao → Hoàn thành / Hủy
- Hệ thống gửi thông báo khi trạng thái thay đổi
- Hiển thị timeline cho người dùng

Chatbox AI
- Người dùng nhập câu hỏi
- Gửi yêu cầu đến hệ thống
- AI xử lý + truy xuất dữ liệu
- Trả kết quả về cho người dùng
- 
2. Luồng Quản trị (Admin)
Quản lý sản phẩm & kho
- Admin thêm / sửa / xóa sản phẩm
- Cập nhật số lượng tồn kho
  
Quản lý đơn hàng
- Admin nhận đơn hàng từ hệ thống
- Xác nhận đơn
- Cập nhật trạng thái: → Đóng gói → Giao hàng → Hoàn thành / Hủy
- Hệ thống tự động cập nhật cho khách hàng

Quản lý người dùng
- Xem danh sách người dùng
- Phân quyền (admin / nhân viên / khách)

Báo cáo & thống kê
- Admin chọn thời gian
- Hệ thống xử lý dữ liệu
- Hiển thị: doanh thu, số đơn, sản phẩm bán chạy

Quản lý Chatbox AI
- Theo dõi lịch sử chat
- Cập nhật nội dung phản hồi AI
- Upload hình ảnh, giá bán, mô tả sản phẩm

# Yêu cầu hệ thống
Ngôn ngữ & Framework
+ PHP >= 8.0
+ Laravel >= 10
+ Hệ điều hành phát triển: Windows 11
+ Visual Studio Code 
+ Git
+ XAMPP / Laragon
# Yêu cầu môi trường 
- PHP phù hợp với composer.json của dự án. 
- Composer 8.3 
- Node.js và npm. 
- MySQL. 
- Tài khoản GroqAI và GROQ_API_KEY nếu muốn dùng Chatbox AI.
