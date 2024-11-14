<?php
include('simple_html_dom.php');  // Nếu bạn đã tải thư viện simple_html_dom.php

// URL của trang cần truy cập
$url = 'https://meteex.biz/golden_ticket';

// Cookie
$cookie = 'cookie_của_bạn';

// Khởi tạo cURL để gửi yêu cầu
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$response = curl_exec($ch);
curl_close($ch);

// Tạo đối tượng DOM từ phản hồi
$html = str_get_html($response);

// Tìm phần tử chứa số dư
$balanceElement = $html->find('span#new-money-ballans .new-up-osn', 0);

if ($balanceElement) {
    // Lấy số dư chính
    $balance = $balanceElement->plaintext;

    // Lấy phần thập phân
    $decimalPart = $balanceElement->find('.format-price-lite', 0)->plaintext;

    // Kết hợp cả phần chính và phần thập phân
    $fullBalance = $balance . $decimalPart;

    echo "Số dư hiện tại: " . $fullBalance . " RUB\n";
} else {
    echo "Không tìm thấy số dư trong phản hồi.\n";
}

?>
