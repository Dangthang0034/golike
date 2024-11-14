<?php

// Đường dẫn đến tệp chứa cookie
$cookieFile = 'metex.txt';

// Hàm yêu cầu nhập cookie
function getCookieInput($cookieFile) {
    // Kiểm tra nếu tệp cookie tồn tại
    if (file_exists($cookieFile)) {
        // Đọc cookie từ tệp
        $cookie = file_get_contents($cookieFile);
        echo "Đã tìm thấy cookie cũ: \n$cookie\n";
        return $cookie;
    } else {
        // Nếu tệp không tồn tại, tạo tệp mới
        echo "Tệp metex.txt chưa tồn tại, tạo mới...\n";
        touch($cookieFile); // Tạo tệp trống nếu chưa tồn tại
        echo "Nhập cookie (đảm bảo rằng mỗi cookie cách nhau bằng dấu ';'):\n";
        $cookie = trim(fgets(STDIN));  // Đọc từ input người dùng
        // Lưu cookie vào tệp
        file_put_contents($cookieFile, $cookie);
        echo "Đã lưu cookie vào tệp $cookieFile.\n";
        return $cookie;
    }
}

// Hàm kiểm tra số dư từ trang web
function checkBalance($cookie) {
    $url = 'https://meteex.biz/golden_ticket';  // URL của trang chứa số dư

    // Khởi tạo cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Dùng cookie
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

    // Gửi yêu cầu và nhận nội dung phản hồi
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    // Kiểm tra nếu số dư xuất hiện trong phản hồi
    if (preg_match('/<span id="new-money-ballans">.*?<span class="new-up-osn"[^>]*>([\d\.]+)<\/span>/', $response, $matches)) {
        $balance = $matches[1] ?? null;
        if ($balance) {
            return $balance;  // Trả về số dư nếu tìm thấy
        }
    }

    return null;  // Nếu không tìm thấy số dư
}

// Hàm yêu cầu và kiểm tra cookie
function requestCookie($cookieFile) {
    while (true) {
        $cookie = getCookieInput($cookieFile);  // Lấy cookie từ tệp hoặc yêu cầu nhập

        echo "Đang kiểm tra số dư với cookie...\n";
        $balance = checkBalance($cookie);  // Kiểm tra số dư với cookie hiện tại

        if ($balance) {
            echo "Số dư hiện tại: $balance\n";  // In ra số dư
            return $cookie;  // Nếu số dư hợp lệ, trả về cookie và thoát khỏi vòng lặp
        } else {
            echo "Cookie không hợp lệ hoặc đã hết hạn. Vui lòng nhập lại cookie.\n";
            // Xóa tệp cookie cũ và yêu cầu nhập lại
            file_put_contents($cookieFile, '');  // Xóa cookie cũ trong tệp
        }
    }
}

// Chạy mã chính
function main() {
    $cookie = requestCookie('metex.txt');  // Lấy cookie từ tệp hoặc yêu cầu nhập

    // Lặp lại cho đến khi cookie hợp lệ và số dư được kiểm tra thành công
    echo "Quá trình hoàn tất. Số dư đã được kiểm tra và cookie hợp lệ.\n";
}

main();

?>
