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
    // In toàn bộ phản hồi HTML từ trang
    echo "Nội dung phản hồi trang:\n";
    echo $response;
}

curl_close($ch);

// Kiểm tra xem số dư có xuất hiện trong phản hồi hay không
if (strpos($response, 'new-money-ballans') !== false) {
    echo "\nSố dư được tìm thấy trong phản hồi.\n";
} else {
    echo "\nKhông tìm thấy số dư trong phản hồi.\n";
}

?>
