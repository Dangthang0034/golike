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
    
    // Tạo đối tượng DOMDocument để phân tích cú pháp HTML
    $dom = new DOMDocument();
    @$dom->loadHTML($response); // Tải HTML vào DOM (sử dụng @ để bỏ qua các lỗi thông báo về HTML không hợp lệ)

    // Tìm tất cả các phần tử có id "new-money-ballans"
    $xpath = new DOMXPath($dom);
    $balanceElement = $xpath->query('//*[@id="new-money-ballans"]/span[@class="new-up-osn"]');
    
    // Kiểm tra nếu tìm thấy phần tử và lấy giá trị
    if ($balanceElement->length > 0) {
        $balance = $balanceElement->item(0)->nodeValue;
        echo "Số dư hiện tại: $balance\n";  // In số dư
    } else {
        echo "Không tìm thấy số dư trong phản hồi.\n";
    }
}

// Đóng cURL
curl_close($ch);
?>
