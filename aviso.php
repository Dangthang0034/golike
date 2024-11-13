<?php

// Thông tin đăng nhập
$username = "dangthang003@gmail.com";
$password = "ThangBich199@#";

// URL đăng nhập và URL của trang kiểm tra số dư
$url_login = "https://aviso.bz/login";
$url_work_serf = "https://aviso.bz/work-serf";

// Cấu hình cURL
$ch = curl_init();

// Thiết lập các tùy chọn cURL cho việc đăng nhập
curl_setopt($ch, CURLOPT_URL, $url_login);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'username' => $username,
    'password' => $password
]);

// Lấy kết quả đăng nhập (có thể kiểm tra các lỗi ở đây)
$response = curl_exec($ch);

// Kiểm tra kết quả đăng nhập
if ($response === false) {
    echo "Lỗi khi đăng nhập: " . curl_error($ch);
    exit();
}

// Sau khi đăng nhập, gửi yêu cầu GET để lấy thông tin số dư
curl_setopt($ch, CURLOPT_URL, $url_work_serf);
curl_setopt($ch, CURLOPT_POST, false);  // Đổi lại thành GET
$response = curl_exec($ch);

// Kiểm tra nếu có lỗi khi lấy trang
if ($response === false) {
    echo "Lỗi khi lấy trang: " . curl_error($ch);
    exit();
}

// Sử dụng thư viện Simple HTML DOM để phân tích HTML và lấy số dư
require_once('vendor/autoload.php');  // Đảm bảo bạn đã cài đặt Simple HTML DOM

// Tạo đối tượng DOM
$dom = new simple_html_dom();
$dom->load($response);

// Tìm phần tử chứa số dư
$balance_element = $dom->find('#new-money-ballans', 0);

// Kiểm tra và in ra số dư
if ($balance_element) {
    echo "Số dư hiện tại của bạn: " . $balance_element->plaintext . "\n";
} else {
    echo "Không tìm thấy số dư trên trang.\n";
}

// Đóng cURL
curl_close($ch);
?>
