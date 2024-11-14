<?php

// Đường dẫn đến file cookie (metex.txt)
$cookieFile = 'metex.txt';

// Hàm yêu cầu nhập cookie từ người dùng hoặc đọc từ file
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
    return $cookie;
}

// Hàm kiểm tra trạng thái cookie
function checkCookieStatus($cookie) {
    // Địa chỉ trang kiểm tra cookie
    $url = 'https://meteex.biz/golden_ticket';

    // Khởi tạo cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie); // Gửi cookie cho yêu cầu
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $response = curl_exec($ch);
    curl_close($ch);

    // Kiểm tra phản hồi xem có thông tin về số dư hay không
    if (strpos($response, 'new-money-ballans') !== false) {
        return true; // Cookie hợp lệ
    } else {
        return false; // Cookie không hợp lệ
    }
}

// Hàm lấy số dư từ phản hồi trang
function getBalanceFromResponse($response) {
    // Tìm số dư trong phản hồi
    preg_match('/<span id="new-money-ballans">.*?<span class="new-up-osn" translate="no">([\d\.]+)<\/span>/', $response, $matches);
    if (!empty($matches[1])) {
        return $matches[1]; // Số dư
    }
    return false;
}

// Hàm kiểm tra và bấm nút
function checkAndClickTask($cookie) {
    // Địa chỉ trang nhiệm vụ
    $url = 'https://meteex.biz/work-serf?ctrll=ee332be535014f4f86c7ef8594d46aaeee332be535014f4f86c7ef8594d46aae';
    
    // Gửi yêu cầu GET để lấy dữ liệu nhiệm vụ
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie); // Gửi cookie
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $response = curl_exec($ch);
    curl_close($ch);

    // Kiểm tra số dư trước khi bấm nút
    echo "Đang kiểm tra số dư...\n";
    $balance = getBalanceFromResponse($response);
    if ($balance) {
        echo "Số dư hiện tại: $balance\n"; // In ra số dư
    } else {
        echo "Không tìm thấy số dư trong phản hồi.\n";
    }

    // Tìm kiếm các nhiệm vụ (dùng regex để tìm phần tử có id bắt đầu bằng "serf-link-")
    preg_match('/<div id="serf-link-(\d+)"/', $response, $matches);
    
    if (!empty($matches[1])) {
        $taskId = $matches[1];  // ID của nhiệm vụ đầu tiên

        echo "Tìm thấy nhiệm vụ đầu tiên với ID: $taskId\n";

        // Mô phỏng bấm nút
        $buttonSelector = "#start-serf-$taskId > div";
        echo "Tìm thấy phần tử nút bấm: $buttonSelector\n";

        // URL để bấm nút (dùng title trong <div> để mở trang nhiệm vụ)
        preg_match('/title="(.*?)"/', $response, $urlMatches);
        $taskUrl = $urlMatches[1];

        // Mở link để mô phỏng hành động "bấm" nút
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $taskUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
        $response = curl_exec($ch);
        curl_close($ch);

        // Kiểm tra xem có phần tử "Bắt đầu xem" xuất hiện không
        if (strpos($response, 'start-yes-serf') !== false) {
            echo "Nút đã được bấm và phần tử 'Bắt đầu xem' đã xuất hiện.\n";
        } else {
            echo "Không tìm thấy phần tử 'Bắt đầu xem' sau khi bấm nút.\n";
        }

        // In phản hồi trang để xem có gì lạ
        echo "Phản hồi trang sau khi bấm nút:\n";
        echo $response;
    } else {
        echo "Không tìm thấy nhiệm vụ nào.\n";
    }
}

// Hàm chính để kiểm tra và thực hiện
function main($cookieFile) {
    // Vòng lặp kiểm tra cookie và xử lý nếu không hợp lệ
    while (true) {
        $cookie = getCookieInput($cookieFile);  // Lấy cookie từ file hoặc nhập từ người dùng

        // Kiểm tra tính hợp lệ của cookie
        if (checkCookieStatus($cookie)) {
            echo "Cookie hợp lệ.\n";
            break; // Thoát vòng lặp khi cookie hợp lệ
        } else {
            echo "Cookie không hợp lệ. Vui lòng nhập lại cookie.\n";
        }
    }

    // Kiểm tra và bấm nút vào nhiệm vụ
    checkAndClickTask($cookie);
}

// Bắt đầu chương trình
main('metex.txt');

?>
