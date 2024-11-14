<?php

// Đường dẫn của tệp cookie
$cookieFile = 'metex.txt';

// Hàm để lấy cookie từ tệp hoặc yêu cầu người dùng nhập nếu chưa có
function getCookieInput($cookieFile) {
    if (file_exists($cookieFile)) {
        // Đọc cookie từ file nếu có
        $cookie = file_get_contents($cookieFile);
        echo "Đã tìm thấy cookie cũ. Kiểm tra cookie...\n";
    } else {
        // Nếu không có cookie trong file, yêu cầu nhập từ người dùng
        echo "Nhập cookie (đảm bảo rằng mỗi cookie cách nhau bằng dấu ';'):\n";
        $cookie = trim(fgets(STDIN));
    }

    // Kiểm tra tính hợp lệ của cookie bằng cách gửi yêu cầu HTTP và kiểm tra trạng thái phản hồi
    $isValid = checkCookieValidity($cookie);
    if (!$isValid) {
        echo "Cookie không hợp lệ hoặc đã hết hạn. Vui lòng nhập lại cookie.\n";
        return getCookieInput($cookieFile); // Yêu cầu nhập lại nếu không hợp lệ
    }

    // Nếu hợp lệ, ghi lại cookie vào tệp để sử dụng lần sau
    file_put_contents($cookieFile, $cookie);
    return $cookie;
}

// Hàm để kiểm tra tính hợp lệ của cookie
function checkCookieValidity($cookie) {
    $url = 'https://meteex.biz/golden_ticket';  // URL kiểm tra số dư
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Đặt cookie vào yêu cầu
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0"); // Giả lập trình duyệt
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Theo dõi các redirect nếu có
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // Thực thi cURL và nhận phản hồi
    $response = curl_exec($ch);

    // Kiểm tra cURL
    if ($response === false) {
        echo "Lỗi cURL: " . curl_error($ch) . "\n";
        curl_close($ch);
        return false;
    }

    // Kiểm tra phản hồi và xác định cookie hợp lệ hay không
    if (strpos($response, 'Số dư') !== false) {
        echo "Cookie hợp lệ!\n";
        curl_close($ch);
        return true;  // Cookie hợp lệ nếu thấy số dư
    }

    echo "Cookie không hợp lệ hoặc đã hết hạn.\n";
    curl_close($ch);
    return false;  // Cookie không hợp lệ nếu không tìm thấy số dư
}

// Hàm để lấy số dư từ trang
function getBalance($cookie) {
    $url = 'https://meteex.biz/golden_ticket';  // Trang để lấy số dư
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Đặt cookie vào yêu cầu
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // Thực thi cURL và nhận phản hồi
    $response = curl_exec($ch);
    if ($response === false) {
        echo "Lỗi cURL: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;  // Lỗi nếu không nhận được phản hồi
    }

    // Tìm số dư trong phản hồi
    preg_match('/<span class="new-up-osn"[^>]*>([\d\.]+)<\/span>/', $response, $matches);
    curl_close($ch);

    if (isset($matches[1])) {
        return $matches[1];  // Trả về số dư tìm được
    } else {
        echo "Không thể tìm thấy số dư.\n";
        return null;  // Nếu không tìm thấy số dư
    }
}

// Chương trình chính
function main() {
    $cookieFile = 'metex.txt';
    
    // Lấy cookie hợp lệ từ file hoặc yêu cầu nhập từ người dùng
    $cookie = getCookieInput($cookieFile);

    // Kiểm tra số dư sau khi đảm bảo cookie hợp lệ
    $balance = getBalance($cookie);
    
    if ($balance !== null) {
        echo "Số dư hiện tại là: $balance\n";
    }
}

// Chạy chương trình chính
main();
?>
