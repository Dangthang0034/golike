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
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$response = curl_exec($ch);
curl_close($ch);

// In toàn bộ phản hồi HTML từ trang
echo "Nội dung phản hồi trang:\n";
echo $response;

// Kiểm tra xem số dư có xuất hiện trong phản hồi hay không
if (strpos($response, 'new-money-ballans') !== false) {
    echo "\nSố dư được tìm thấy trong phản hồi.\n";
} else {
    echo "\nKhông tìm thấy số dư trong phản hồi.\n";
}

?>
