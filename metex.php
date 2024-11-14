<?php

// Đường dẫn đến tệp chứa cookie
$cookieFile = 'metex.txt';

// Hàm yêu cầu nhập cookie từ màn hình và lưu vào tệp
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
        return null;
    }
}

// Hàm yêu cầu người dùng nhập cookie mới từ màn hình
function promptForCookie($cookieFile) {
    echo "Nhập cookie (đảm bảo rằng mỗi cookie cách nhau bằng dấu ';'):\n";
    $cookie = trim(fgets(STDIN));  // Đọc từ input người dùng
    // Lưu cookie vào tệp
    file_put_contents($cookieFile, $cookie);
    echo "Đã lưu cookie vào tệp $cookieFile.\n";
    return $cookie;
}

// Hàm kiểm tra phản hồi từ trang web để xác nhận cookie có hợp lệ không
function checkCookieValidity($cookie) {
    $url = 'https://meteex.biz/golden_ticket';  // URL của trang cần kiểm tra cookie

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
        curl_close($ch);
        return false;  // Trả về false nếu có lỗi trong quá trình gửi yêu cầu
    }

    curl_close($ch);

    // Kiểm tra trạng thái phản hồi của trang web
    // Ví dụ: kiểm tra xem trang có chứa lỗi về cookie không
    if (strpos($response, 'cookie expired') !== false || strpos($response, 'session expired') !== false) {
        return false;  // Nếu cookie hết hạn, trả về false
    }

    // Nếu không có lỗi về cookie, tức là cookie hợp lệ
    return true;
}

// Hàm yêu cầu và kiểm tra cookie
function requestCookie($cookieFile) {
    // Lấy cookie từ tệp hoặc yêu cầu nhập nếu chưa có
    $cookie = getCookieInput($cookieFile);

    // Nếu đã có cookie cũ, kiểm tra tính hợp lệ của nó
    if ($cookie && checkCookieValidity($cookie)) {
        echo "Cookie hợp lệ. Tiến hành kiểm tra số dư.\n";
        return $cookie;  // Trả về cookie hợp lệ
    }

    // Nếu cookie không hợp lệ hoặc chưa có, yêu cầu nhập lại
    echo "Cookie không hợp lệ hoặc chưa có. Vui lòng nhập lại cookie.\n";
    $cookie = promptForCookie($cookieFile);  // Yêu cầu người dùng nhập cookie mới
    return $cookie;
}

// Hàm chính
function main() {
    // Kiểm tra cookie và yêu cầu nhập nếu không hợp lệ
    $cookie = requestCookie('metex.txt');
    // Số dư có thể được kiểm tra sau khi cookie hợp lệ
    echo "Cookie hợp lệ: $cookie\n";
}

main();
?>
