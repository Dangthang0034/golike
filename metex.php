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

// Hàm kiểm tra số dư
function checkBalance($cookie) {
    $url = 'https://meteex.biz/golden_ticket';  // URL của trang cần kiểm tra số dư

    // Khởi tạo cURL để kiểm tra số dư
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
        return false;  // Nếu có lỗi, trả về false
    }

    curl_close($ch);

    // Tìm số dư trong phản hồi của trang (dùng chuỗi để tìm số dư)
    if (preg_match('/<span id="new-money-ballans">.*?<span class="new-up-osn" translate="no">(.*?)<\/span>.*?<span style="margin-left: 5px;font-size: 14px;">/', $response, $matches)) {
        return $matches[1];  // Trả về số dư tìm được
    }

    return false;  // Nếu không tìm thấy số dư, trả về false
}

// Hàm lấy dữ liệu nhiệm vụ và bấm nút
function getTaskData($cookie) {
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

    // Tìm kiếm các nhiệm vụ
    preg_match_all('/<div id="serf-link-(\d+)"/', $response, $matches);
    
    if (!empty($matches[1])) {
        echo "Tìm thấy các nhiệm vụ: \n";
        foreach ($matches[1] as $taskId) {
            echo "Nhiệm vụ ID: $taskId\n";
            // Mô phỏng bấm nút
            $buttonSelector = "#start-serf-$taskId > div";
            echo "Tìm thấy phần tử nút bấm: $buttonSelector\n";

            // Xây dựng URL thực hiện bấm (thực tế chỉ cần URL trong `title` để gửi yêu cầu)
            $taskUrl = "https://rassomaha2.aqulas.me";  // URL trong `title` của thẻ <div>
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $taskUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
            $response = curl_exec($ch);
            curl_close($ch);

            // Kiểm tra xem nút "Bắt đầu xem" có xuất hiện không
            if (strpos($response, 'start-yes-serf') !== false) {
                echo "Nút đã được bấm và phần tử đã xuất hiện.\n";
            } else {
                echo "Không tìm thấy phần tử sau khi bấm nút.\n";
            }
        }
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

    // Kiểm tra số dư và cập nhật cookie nếu hợp lệ
    $balance = checkBalance($cookie);
    if ($balance) {
        echo "Số dư hiện tại: $balance\n";  // In ra số dư
        file_put_contents($cookieFile, $cookie);  // Ghi đè cookie vào file
        echo "Đã cập nhật cookie trong tệp $cookieFile.\n";
    } else {
        echo "Không thể lấy số dư. Thử lại sau.\n";
    }

    // Truy cập và xử lý nhiệm vụ
    getTaskData($cookie);
}

// Bắt đầu chương trình
main('metex.txt');

?>
