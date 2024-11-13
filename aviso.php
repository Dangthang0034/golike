<?php

// Hàm glike để gửi yêu cầu GET với các tiêu đề tùy chỉnh
function glike($host, $tsm) {
    // Khởi tạo cURL
    $mr = curl_init();
    
    // Cấu hình các tùy chọn cURL
    curl_setopt_array($mr, array(
        CURLOPT_PORT => "443",  // Cổng HTTPS
        CURLOPT_URL => "$host",  // URL đích
        CURLOPT_RETURNTRANSFER => true,  // Trả về kết quả dưới dạng chuỗi
        CURLOPT_SSL_VERIFYPEER => false,  // Tắt xác minh SSL (thường dùng cho các môi trường không xác thực SSL)
        CURLOPT_TIMEOUT => 30,  // Thời gian chờ (30 giây)
        CURLOPT_CUSTOMREQUEST => "GET",  // Phương thức GET
        CURLOPT_HTTPHEADER => $tsm  // Tiêu đề HTTP tùy chỉnh
    ));
    
    // Thực thi yêu cầu cURL và lấy phản hồi
    $mr2 = curl_exec($mr);
    
    // Đóng kết nối cURL
    curl_close($mr);
    
    // Trả về kết quả phản hồi
    return $mr2;
}

// Ví dụ sử dụng hàm glike
$host = "https://aviso.bz/work-serf";  // URL cần truy cập
$tsm = array(  // Tiêu đề HTTP tùy chỉnh (có thể thay đổi theo yêu cầu)
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
);

// Gọi hàm glike và lấy kết quả
$response = glike($host, $tsm);

// Kiểm tra và hiển thị kết quả
if ($response === false) {
    echo "Lỗi khi gửi yêu cầu.\n";
} else {
    echo "Kết quả từ máy chủ: \n";
    echo $response;  // In ra kết quả nhận được
}
?>
