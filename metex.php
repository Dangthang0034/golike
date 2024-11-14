<?php

// URL của trang cần truy cập
$url = 'https://meteex.biz/golden_ticket';

// Cookie (đọc từ file hoặc đã có sẵn)
$cookie = 'cookie_của_bạn';  // Đảm bảo rằng bạn đã có cookie hợp lệ

// Khởi tạo cURL để gửi yêu cầu
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0"); // Giả lập trình duyệt
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Theo dõi các redirect nếu có

// Thêm kiểm tra lỗi SSL nếu trang dùng HTTPS và gặp lỗi SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Thực thi yêu cầu và nhận phản hồi
$response = curl_exec($ch);

// Kiểm tra lỗi cURL
if($response === false) {
    echo "Lỗi cURL: " . curl_error($ch) . "\n";  // In lỗi nếu có
} else {
    // Nếu phản hồi thành công, tiếp tục xử lý HTML
    echo "Đã tải trang thành công.\n";
    
    // Dùng preg_match để tìm số dư
    // Biểu thức chính quy để tìm số dư
    preg_match('/<span class="new-up-osn"[^>]*>([\d\.]+)<\/span>/', $response, $matches);

    // Kiểm tra nếu số dư được tìm thấy
    if (isset($matches[1])) {
        $balance = $matches[1];  // Lấy số dư từ kết quả của preg_match
        echo "Số dư hiện tại: $balance\n";  // In số dư
    } else {
        echo "Không tìm thấy số dư trong phản hồi.\n";
    }
}

// Đóng cURL
curl_close($ch);
?>
