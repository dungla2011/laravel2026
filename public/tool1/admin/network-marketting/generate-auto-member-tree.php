<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/var/www/html/public/index.php';

if (! isCli()) {
    //    die("NOT CLI!");
}

$maxChildOne = 10;

$projid = 6868;
$del = getch(" (---có thể cần chạy 2 lần mới set parent ở dưới ---)\n DELETE OLD DATA? (y)");
if ($del == 'y') {
    \App\Models\User::where('email', 'like', '%trieuphu24h.com')->forceDelete();
    \App\Models\User::where('email', 'like', '%@gmail.comm')->forceDelete();
    \App\Models\User::where('email', 'like', '%@ymail.com')->forceDelete();
    \App\Models\NetworkMarketing::where('project_id', $projid)->forceDelete();
    getch('đã xóa ...');
}

$str = 'Khánh An Lê,Nam An Lê,Xuân An Lê,Trung Anh Lê,Yên Bằng Lê,Duy Bảo Lê,Hữu Bình Lê,Duy Cẩn Lê,Gia Cẩn Lê,Hữu Châu Lê,Ngọc Đại Lê,Hồng Ðăng Lê,Minh Ðạt Lê,Mạnh Ðình Lê,Hữu Ðịnh Lê,Thành Doanh Lê,Thụy Du Lê,Tài Ðức Lê,Mạnh Dũng Lê,Hoàng Khải Lê,Ngọc Khang Lê,Trọng Khánh Lê,Xuân Khoa Lê,Quang Hà Lê,Sơn Hà Lê,Quang Hải Lê,Tạ Hiền Lê,Minh Hiếu Lê,Trọng Hiếu Lê,Khánh Hoàn Lê,Sỹ Hoàng Lê,Chấn Hùng Lê,Nhật Hùng Lê,Quốc Hùng Lê,Trọng Hùng Lê,Thiên Hưng Lê,Quốc Huy Lê,Quang Lâm Lê,Hiếu Liêm Lê,Hoàng Linh Lê,Hồng Lĩnh Lê,Huy Lĩnh Lê,Công Lộc Lê,Tấn Lợi Lê,Ðức Long Lê,Hiểu Minh Lê,Khánh Minh Lê,Thanh Minh Lê,Hồ Nam Lê,Gia Nghi Lê,Hoàng Gia Lê,Khôi Nguyên Lê,Nam Nhật Lê,Nam Phi Lê,Thanh Phi Lê,Chấn Phong Lê,Chiêu Phong Lê,Hiếu Phong Lê,Ðình Phú Lê,Ðình Phúc Lê,Thế Phúc Lê,Lam Phương Lê,Long Quân Lê,Ngọc Quang Lê,Minh Quý Lê,Ðức Quyền Lê,Ngọc Quyết Lê,Quang Sáng Lê,Hùng Sơn Lê,Phước Sơn Lê,Nhật Tấn Lê,Nhất Tiến Lê,Thanh Toản Lê,Hữu Trác Lê,Ðức Trung Lê,Minh Trung Lê,Thế Trung Lê,Xuân Trường Lê,Nam Tú Lê,Thanh Tú Lê,Tuấn Tú Lê,Cảnh Tuấn Lê,Quang Tuấn Lê,Thanh Tuấn Lê,An Tường Lê,Quang Thái Lê,Ðắc Thành Lê,Duy Thành Lê,Thanh Thiên Lê,Ngọc Thiện Lê,Quốc Thiện Lê,Tâm Thiện Lê,Nhật Thịnh Lê,Quốc Thịnh Lê,Ðức Thọ Lê,Cát Uy Lê,Khánh Văn Lê,Vương Việt Lê,Hồng Vinh Lê,Thanh Vinh Lê,Thiên Ân Lê,Dương Anh Lê,Minh Anh Lê,Thuận Anh Lê,Hữu Bảo Lê,Ðức Bình Lê,Quang Bửu Lê,Gia Cảnh Lê,Trọng Chính Lê,Quốc Ðại Lê,Ðắc Di Lê,Thanh Ðoàn Lê,Thiện Ðức Lê,Lâm Dũng Lê,Tấn Dũng Lê,Thế Dũng Lê,Trung Dũng Lê,Khắc Duy Lê,Phúc Duy Lê,Hải Giang Lê,Duy Khang Lê,Nguyên Khang Lê,Huy Khánh Lê,Việt Khoa Lê,Ngọc Khương Lê,Gia Kiệt Lê,Minh Kiệt Lê,Trọng Hà Lê,Công Hải Lê,Quốc Hải Lê,Sơn Hải Lê,Thanh Hào Lê,Hoàng Hiệp Lê,Chí Hiếu Lê,Duy Hiếu Lê,Xuân Hiếu Lê,Quốc Hoài Lê,Hữu Hoàng Lê,Tùng Lâm Lê,Quang Lân Lê,Gia Lập Lê,Hữu Lễ Lê,Bá Lộc Lê,Hoàng Long Lê,Tuấn Long Lê,Công Luận Lê,Ðức Mạnh Lê,Thiên Mạnh Lê,An Nam Lê,Hùng Ngọc Lê,Tuấn Ngọc Lê,Ðông Nguyên Lê,Trung Nguyên Lê,Hồng Nhật Lê,Khắc Ninh Lê,Xuân Ninh Lê,Xuân Phúc Lê,Anh Quân Lê,Minh Quân Lê,Nhật Quang Lê,Thanh Quang Lê,Tùng Quang Lê,Anh Quốc Lê,Thiện Sinh Lê,Công Sơn Lê,Ðông Sơn Lê,Xuân Sơn Lê,Tuấn Tài Lê,Nhật Tiến Lê,Thuận Toàn Lê,Ðức Toản Lê,Công Tuấn Lê,Ðức Tuấn Lê,Khắc Tuấn Lê,Xuân Thái Lê,Ðức Thắng Lê,Quốc Thắng Lê,Công Thành Lê,Tuấn Thành Lê,Gia Thịnh Lê,Huy Thông Lê,Việt Thông Lê,Ðại Thống Lê,Trọng Việt Lê,Tiến Võ Lê,Khắc Vũ Lê,Quang Vũ Lê,Duy An Lê,Trường An Lê,Ðức Ân Lê,Gia Bạch Lê,Gia Bình Lê,Kiên Bình Lê,Kiến Bình Lê,Thế Bình Lê,Thành Châu Lê,Hữu Chiến Lê,Thành Công Lê,Mạnh Cương Lê,Ngọc Cường Lê,Minh Danh Lê,Thanh Ðạo Lê,Thành Ðạt Lê,Ðình Ðôn Lê,Quang Đông Lê,Nghĩa Dũng Lê,Nhật Dũng Lê,Công Giang Lê,Hòa Giang Lê,Hồng Giang Lê,Khánh Giang Lê,Minh Giang Lê,Anh Khải Lê,Phi Hải Lê,Thanh Hậu Lê,Minh Hoàng Lê,Hòa Hợp Lê,Minh Huy Lê,Thanh Liêm Lê,Ðình Lộc Lê,Thắng Lợi Lê,Thụy Miên Lê,Cao Minh Lê,Hoàng Minh Lê,Hoàng Mỹ Lê,Nhật Nam Lê,Trung Nghĩa Lê,Quang Ninh Lê,Khánh Phi Lê,Trường Phúc Lê,Nam Phương Lê,Hoàng Quân Lê,Nhật Quân Lê,Hồng Quý Lê,Ðình Sang Lê,Viết Sơn Lê,Tuấn Sỹ Lê,Khải Tâm Lê,Ðình Toàn Lê,Minh Toàn Lê,Hữu Trí Lê,Quang Trọng Lê,Lâm Trường Lê,Anh Tú Lê,Minh Tú Lê,Anh Tuấn Lê,Ðức Tuệ Lê,Bá Tùng Lê,Sơn Tùng Lê,Thế Tường Lê,Bảo Thạch Lê,Hữu Thắng Lê,Quyết Thắng Lê,Duy Thanh Lê,Khắc Thành Lê,Hữu Thiện Lê,Phước Thiện Lê,Ngọc Thọ Lê,Quang Vinh Lê,Quốc Vũ Lê,Bình An Lê,Thiên An Lê,Vĩnh Ân Lê,Quang Anh Lê,Tùng Anh Lê,Gia Cần Lê,Hữu Canh Lê,Bảo Chấn Lê,Tuấn Châu Lê,Trung Chính Lê,Phi Cường Lê,Thịnh Cường Lê,Bảo Ðịnh Lê,Hồng Đức Lê,Minh Ðức Lê,Quang Ðức Lê,Trung Ðức Lê,Hải Dương Lê,Thế Duyệt Lê,Ðức Khiêm Lê,Duy Khiêm Lê,Anh Khoa Lê,Bá Kỳ Lê,Minh Kỳ Lê,Phú Hải Lê,Tuấn Hải Lê,Ðại Hành Lê,Hòa Hiệp Lê,Trung Hiếu Lê,Minh Hòa Lê,Nghĩa Hòa Lê,Tất Hòa Lê,Gia Hoàng Lê,Tuấn Hoàng Lê,Nhật Hồng Lê,Ðình Hợp Lê,Gia Huy Lê,Nhật Huy Lê,Tuấn Linh Lê,Phi Long Lê,Quốc Minh Lê,Tường Nguyên Lê,Thành Nhân Lê,Thống Nhất Lê,Ðức Phú Lê,Kim Phú Lê,Gia Phúc Lê,Công Phụng Lê,Bá Phước Lê,Hữu Phước Lê,Minh Quang Lê,Ðức Sinh Lê,Phúc Sinh Lê,Trường Sơn Lê,Việt Sơn Lê,Hoài Tín Lê,Minh Triết Lê,Phương Triều Lê,Quốc Trụ Lê,Quốc Trung Lê,Thanh Trung Lê,Mạnh Trường Lê,Khải Tuấn Lê,Thanh Tùng Lê,Minh Thắng Lê,Mạnh Thiện Lê,Xuân Thiện Lê,Hồng Thịnh Lê,Kim Thông Lê,Thanh Thuận Lê,Quốc Việt Lê,Phước An Lê,Gia Anh Lê,Tuấn Anh Lê,Bảo Châu Lê,Phong Châu Lê,Mạnh Chiến Lê,Minh Chuyên Lê,Minh Ðan Lê,Vinh Diệu Lê,Thái Ðức Lê,Ðông Dương Lê,Thái Dương Lê,Anh Duy Lê,Ðức Duy Lê,Hoàng Khang Lê,Hữu Khang Lê,Hoàng Khôi Lê,Nhật Khương Lê,Vĩnh Hải Lê,Ðình Hảo Lê,Bảo Hoàng Lê,Phú Hùng Lê,Trí Hữu Lê,Thụy Long Lê,Ðình Luận Lê,Bình Minh Lê,Thái Minh Lê,Trí Minh Lê,Khánh Nam Lê,Hữu Nghĩa Lê,Trọng Nghĩa Lê,Ðình Nhân Lê,Minh Nhật Lê,Hoài Phong Lê,Quốc Phong Lê,Thuận Phong Lê,Thành Phương Lê,Thuận Phương Lê,Sơn Quân Lê,Đăng Quang Lê,Ðức Quảng Lê,Lương Quyền Lê,Thái San Lê,Tấn Sinh Lê,Thanh Sơn Lê,Vân Sơn Lê,Quang Tài Lê,Việt Tiến Lê,Vĩnh Toàn Lê,Quang Triệu Lê,Quang Thạch Lê,Hòa Thái Lê,Việt Thắng Lê,Thuận Thành Lê,Bá Thịnh Lê,Hùng Thịnh Lê,Cao Thọ Lê,Minh Thông Lê,Nam Thông Lê,Kiến Văn Lê,Huy Việt Lê,Nam Việt Lê,Trung Việt Lê,Công Vinh Lê,Hoàng Vương Lê,Hữu Vượng Lê,Ðức Bằng Lê,Khải Ca Lê,Xuân Cung Lê,Mạnh Cường Lê,Ðăng Ðạt Lê,Ðình Diệu Lê,Anh Ðức Lê,Hùng Dũng Lê,Ngọc Dũng Lê,Ðại Dương Lê,Việt Duy Lê,Quang Khải Lê,Phúc Khang Lê,Việt Khôi Lê,Trọng Kim Lê,Tiến Hiệp Lê,Hiệp Hòa Lê,Khánh Hoàng Lê,Hữu Hùng Lê,Phúc Hưng Lê,Quang Hữu Lê,Bảo Huỳnh Lê,Tường Lâm Lê,Nguyên Lộc Lê,Nhật Minh Lê,Tường Minh Lê,Văn Minh Lê,Xuân Nam Lê,Minh Nghĩa Lê,Hải Nguyên Lê,Trung Nhân Lê,Cao Phong Lê,Ðức Phong Lê,Uy Phong Lê,Chiêu Quân Lê,Hải Quân Lê,Ðức Quang Lê,Minh Quốc Lê,Việt Quyết Lê,Thái Sang Lê,Hồng Sơn Lê,Kim Sơn Lê,Ðức Tâm Lê,Duy Tâm Lê,Hữu Tâm Lê,Bảo Toàn Lê,Kiên Trung Lê,Mạnh Tường Lê,Trí Thắng Lê,Hoài Thanh Lê,Ngọc Thuận Lê,Triều Vĩ Lê,Anh Việt Lê,Quốc Vinh Lê,Thành Ân Lê,Quốc Anh Lê,Thế Anh Lê,Minh Chiến Lê,Ðức Chính Lê,Việt Chính Lê,Quảng Ðạt Lê,Phúc Ðiền Lê,Từ Ðông Lê,Lâm Ðông Lê,Trí Dũng Lê,Nhật Duy Lê,Trường Giang Lê,Huy Kha Lê,Ðức Khang Lê,Tấn Khang Lê,Gia Khiêm Lê,Ngọc Khôi Lê,Trọng Kiên Lê,Liên Kiệt Lê,Ðức Hải Lê,Khánh Hải Lê,Ngọc Hiển Lê,Huy Hoàng Lê,Khánh Hội Lê,Gia Huấn Lê,Minh Huấn Lê,Chính Hữu Lê,Công Lập Lê,Công Luật Lê,Ðăng Minh Lê,Hải Nam Lê,An Nguyên Lê,Bình Nguyên Lê,Thành Nguyên Lê,Thiện Nhân Lê,Quang Nhật Lê,Gia Phong Lê,Huy Phong Lê,Ðông Phương Lê,Việt Phương Lê,Hồng Quang Lê,Việt Quốc Lê,Danh Sơn Lê,Ngọc Sơn Lê,Cao Sỹ Lê,Cao Tiến Lê,Khắc Triệu Lê,Vương Triệu Lê,Huy Tuấn Lê,Minh Thái Lê,Mạnh Thắng Lê,Toàn Thắng Lê,Vạn Thắng Lê,Thiện Thanh Lê,Bá Thiện Lê,Minh Thiện Lê,Thành Thiện Lê,Hữu Thọ Lê,Hoàng Việt Lê,Hiệp Vũ Lê,Bảo An Lê,Tường Anh Lê,Ðức Bảo Lê,Phú Bình Lê,Chí Công Lê,Hải Ðăng Lê,Thành Danh Lê,Phi Ðiệp Lê,Ðình Dương Lê,Nam Dương Lê,Trọng Duy Lê,Việt Khải Lê,Minh Khôi Lê,Trung Kiên Lê,Ðông Hải Lê,Duy Hải Lê,Công Hậu Lê,Quốc Hiển Lê,Gia Hiệp Lê,Ðạt Hòa Lê,Ðức Hòa Lê,Quốc Hoàn Lê,Quang Hùng Lê,Trí Hùng Lê,Tuấn Hùng Lê,Gia Hưng Lê,Quang Hưng Lê,Bảo Lâm Lê,Thế Lâm Lê,Ðinh Lộc Lê,Minh Lý Lê,Ngọc Minh Lê,Quốc Mỹ Lê,Thanh Phong Lê,Sỹ Phú Lê,Thành Tín Lê,Kim Toàn Lê,Thiên Trí Lê,Quang Trung Lê,Tấn Trương Lê,Chiến Thắng Lê,Huy Thành Lê,Tấn Thành Lê,Ân Thiện Lê,Hiếu Thông Lê,Chính Thuận Lê,Trường Vũ Lê,Thành An Lê,Chí Anh Lê,Thiệu Bảo Lê,Tất Bình Lê,Trường Chinh Lê,Tuấn Dũng Lê,Tuấn Khanh Lê,Hiệp Hà Lê,Mạnh Hà Lê,Xuân Hãn Lê,Minh Hào Lê,Tất Hiếu Lê,Khải Hòa Lê,Minh Hưng Lê,Tùng Linh Lê,Bá Long Lê,Việt Long Lê,Công Lý Lê,Hồng Minh Lê,Hoài Nam Lê,Hào Nghiệp Lê,Duy Gia Lê,Phúc Nguyên Lê,Hạo Nhiên Lê,Chế Phương Lê,Thế Quyền Lê,Mạnh Quỳnh Lê,Hữu Tài Lê,Lương Tài Lê,Thiện Tâm Lê,Hữu Tân Lê,Minh Tân Lê,Công Tráng Lê,Minh Trí Lê,Hữu Từ Lê,Ðức Tường Lê,Minh Thạc Lê,Việt Thái Lê,Quốc Thành Lê,Phúc Thịnh Lê,Vũ Uy Lê,Hoài Việt Lê,Phú Ân Lê,Ðức Anh Lê,Vũ Anh Lê,Quốc Bình Lê,Thái Bình Lê,Gia Ðức Lê,Tuấn Ðức Lê,Chí Dũng Lê,An Khang Lê,Quốc Khánh Lê,Thiện Khiêm Lê,Hữu Khôi Lê,Chí Kiên Lê,Tuấn Kiệt Lê,Hoàng Hải Lê,Hiệp Hào Lê,Công Hoán Lê,Quốc Hoàng Lê,Phi Hùng Lê,Chấn Hưng Lê,Huy Lâm Lê,Hoàng Lâm Lê,Nam Lộc Lê,Thanh Long Lê,Thiện Luân Lê,Chí Nam Lê,Giang Nam Lê,Trường Nam Lê,Trường Nhân Lê,Tường Phát Lê,Ðông Phong Lê,Tân Phước Lê,Thiện Phước Lê,Ðức Siêu Lê,Tấn Tài Lê,Duy Tân Lê,Xuân Trung Lê,Quang Tú Lê,Triều Thành Lê,Duy Thông Lê,Vạn Thông Lê,Hữu Thống Lê,Quang Thuận Lê,Quốc Văn Lê, Duy An Lê,Tùng Châu Lê,Đình Chiến Lê,Hùng Cường Lê,Bình Ðạt Lê,Hữu Ðạt Lê,Bách Du Lê,Trí Hào Lê,Duy Hiền Lê,Vĩnh Hưng Lê,Hải Phong Lê,Nguyên Phong Lê,Huy Quang Lê,Giang Sơn Lê,Minh Triệu Lê,Ðắc Trọng Lê,Bảo Thái Lê,Giang Thiên Lê,Hồng Thụy Lê,Tân Bình Lê,Nguyên Giáp Lê,Gia Kiên Lê,Huy Hà Lê,Thanh Hải Lê,Quốc Hạnh Lê,Sơn Lâm Lê,Thành Lợi Lê,Tân Long Lê,Tuấn Minh Lê,Bảo Quốc Lê,Hải Sơn Lê,Quốc Tuấn Lê,Danh Văn Lê,Việt An Lê,Hải Bằng Lê,Tiểu Bảo Lê,Minh Cảnh Lê,Việt Cương Lê,Ngọc Ðoàn Lê,Thiện Dũng Lê,Ðức Khải Lê,Chí Khiêm Lê,Trường Kỳ Lê,Minh Hải Lê,Duy Hùng Lê,Tường Lĩnh Lê,Bảo Long Lê,Hữu Lương Lê,Thế Minh Lê,Phương Nam Lê,Việt Phong Lê,Gia Phước Lê,Sơn Quyền Lê,Trọng Tấn Lê,Trọng Trí Lê,Minh Vũ Lê,Chí Bảo Lê,Hưng Ðạo Lê,Viễn Ðông Lê,Thường Kiệt Lê,Việt Hùng Lê,Chiêu Minh Lê,Ðình Nguyên Lê,Minh Nhân Lê,Viễn Phương Lê,Công Sinh Lê,Minh Sơn Lê,Triệu Thái Lê,Trọng Vinh Lê,Hoàng Ân Lê,Huy Anh Lê,Công Hiếu Lê,Phi Hoàng Lê,Phúc Lâm Lê,Duy Luận Lê,Hữu Minh Lê,Xuân Minh Lê,Tấn Nam Lê,Việt Nhân Lê,Cao Sơn Lê,Ngọc Trụ Lê,Quốc Trường Lê,Hữu Tường Lê,Ngọc Thạch Lê,Bá Thành Lê,Minh Thuận Lê,Hoài Bắc Lê,An Cơ Lê,Duy Cường Lê,Quốc Hòa Lê,Hoàng Nam Lê,Thái Sơn Lê,Lâm Viên Lê,Thế Vinh Lê,Tường Vinh Lê,Thanh Vũ Lê,Anh Dũng Lê,Vương Gia Lê,Phú Hiệp Lê,Tường Lân Lê,Khởi Phong Lê,Chí Thành Lê,Cường Thịnh Lê,Anh Vũ Lê,Ðình Chương Lê,Đăng Khương Lê,Cao Kỳ Lê,Bảo Hiển Lê,Hữu Long Lê,Khắc Minh Lê,Thụ Nhân Lê,Hồng Phát Lê,Ðức Tài Lê,Khắc Trọng Lê,Chí Vịnh Lê,Nguyên Bảo Lê,Nguyên Ðan Lê,Thế Doanh Lê,Phước Lộc Lê,Quốc Mạnh Lê,Quang Minh Lê,Thiên Phú Lê,Chí Sơn Lê,Dũng Trí Lê,Vương Triều Lê,Ðăng Khoa Lê,Xuân Hòa Lê,Hoàng Lân Lê,Kim Long Lê,Việt Ngọc Lê,Ðình Quảng Lê,Nam Sơn Lê,Thái Tân Lê,Minh Tiến Lê,Ðình Trung Lê,Ðình Thiện Lê,Ðăng Khánh Lê,Xuân Kiên Lê,Phú Hưng Lê,Thế Phương Lê,Quốc Quân Lê,Xuân Vũ Lê,Công Bằng Lê,Xuân Bình Lê,Quang Dũng Lê,Anh Khôi Lê,Việt Huy Lê,Thiên Lương Lê,Ðại Ngọc Lê,Quang Nhân Lê,Thạch Tùng Lê,Quảng Thông Lê,Gia Uy Lê,Phúc Cường Lê,Minh Dân Lê,Hoàng Duệ Lê,Việt Dương Lê,Bảo Duy Lê,Nam Hải Lê,Thái Hòa Lê,Gia Hùng Lê,Thế Hùng Lê,Hải Long Lê,Thiện Gia Lê,Trọng Nhân Lê,Tuấn Trung Lê,Quang Thiên Lê,Vĩnh Thụy Lê,Tiến Ðức Lê,Bảo Giang Lê,Ngọc Hải Lê,Trường Long Lê,Ðức Phi Lê,Hùng Phong Lê,Bình Quân Lê,Phú Thịnh Lê,Khôi Vĩ Lê,Gia Bảo Lê,Đức Cao Lê,Ðức Cường Lê,Hữu Cường Lê,Minh Dũng Lê,Việt Khang Lê,Thành Khiêm Lê,Thăng Long Lê,Nhân Nguyên Lê,Ðông Quân Lê,Quang Triều Lê,Quang Thắng Lê,Thành Vinh Lê,Lâm Vũ Lê,Minh Ân Lê,Thiên Ðức Lê,Thái Duy Lê,Minh Khang Lê,Anh Minh Lê,Duy Minh Lê,Quốc Thông Lê,Hải Thụy Lê,Hữu Vĩnh Lê,Quốc Bảo Lê,Hữu Cương Lê,Quang Danh Lê,Kiến Ðức Lê,Thiện Giang Lê,Tiến Hoạt Lê,Ngọc Huy Lê,Quốc Phương Lê,Vinh Quốc Lê,Bảo Tín Lê,Ðình Thắng Lê,Quang Thịnh Lê,Hoàng Dũng Lê,Hiếu Dụng Lê,Hoàng Giang Lê,Vũ Minh Lê,Hữu Trung Lê,Huy Tường Lê,Khánh Duy Lê,Gia Hòa Lê,Phúc Hòa Lê,Ngọc Lân Lê,Hồng Việt Lê,Ðăng An Lê,Thế An Lê,Thế Dân Lê,Phước Nguyên Lê,Ðức Nhân Lê,Quang Trường Lê,Chí Thanh Lê,Lập Thành Lê';

$mm = explode(',', $str);

$randHo = ['Nguyễn', 'Trần', 'Phạm', 'Lê', 'Đinh', 'Triệu', 'Lý'];
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($randHo);
//echo "</pre>";

$cc = 0;
$mUserid = [];
foreach ($mm as $name) {
    $str = str_replace(' Lê', '', $name);
    $cc++;
    $name = $randHo[$cc % 7].' '.$str;
    $uname = \LadLib\Common\cstring2::convert_codau_khong_dau($name);
    $uname = str_replace(' ', '_', strtolower($uname));
    $mail = $uname.'@ymail.com';
    //    echo "<br/>\n$cc. $name / $mail";

    if (! $objUser = \App\Models\User::where('email', $mail)->first()) {
        $ret = \App\Models\User::insert(['email' => $mail, 'name' => $name, 'username' => $uname]);
        $idx = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();
        echo "<br/>\n Insert ok $idx / $mail";
    } else {
        $idx = $objUser->id;
        echo "<br/>\n Old UID = $objUser->id / $objUser->email";
    }
    $mUserid[] = $idx;
    \App\Models\User::setRoleUser($idx);
    \App\Models\NetworkMarketing::insertOrGetNetworkObj($idx, $projid);
}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mUserid);
//echo "</pre>";

//\App\Models\NetworkMarketing::insertOrGetNetworkObj()
//\App\Models\NetworkMarketing::insertUpdateUserLinkMarketing();

class clsTmpTree1
{
    public $id;

    public $child = [];
}

$all = [];
$tt = count($mUserid);

for ($i = 0; $i < $tt; $i++) {
    $x = new clsTmpTree1();
    $x->id = $i;
    $all[$i] = $x;
}

$pointer = 1;
for ($i = 0; $i < $tt; $i++) {
    $x = &$all[$i];
    if (count($x->child) < $maxChildOne) {
        for ($j = $pointer; $j < $pointer + $maxChildOne && $j < $tt; $j++) {
            $x->child[] = $j;
        }
    }
    $pointer += $maxChildOne;
}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($all);
//echo "</pre>";

foreach ($all as $id => &$obj) {
    $uid = $mUserid[$id];
    $obj->user_id = $uid;
    if ($obj->child) {
        if (! isset($obj->child_uid)) {
            $obj->child_uid = [];
        }
        foreach ($obj->child as $id1) {
            $obj->child_uid[] = $mUserid[$id1];
            echo "<br/>\n Set PID : ".$mUserid[$id1].' -> PID = '.$uid;
            \App\Models\NetworkMarketing::insertUpdateUserLinkMarketing($mUserid[$id1], $uid);
        }
    }
}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($all);
//echo "</pre>";
