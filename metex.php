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

// Hàm kiểm tra số dư từ trang web
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

// Hàm lấy thông tin về các nhiệm vụ từ trang công việc
function getTaskData($cookie) {
    $url = 'https://meteex.biz/work-serf?ctrll=ee332be535014f4f86c7ef8594d46aaeee332be535014f4f86c7ef8594d46aae'; // URL của trang công việc

    // Khởi tạo cURL để lấy dữ liệu nhiệm vụ
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

    // Tìm kiếm các phần tử nhiệm vụ có id bắt đầu với 'serf-link-'
    preg_match_all('/<div id="serf-link-(\d+)"/', $response, $matches);
    if (empty($matches[1])) {
        echo "Không tìm thấy nhiệm vụ.\n";
        return false;
    }

    // Lấy id của nhiệm vụ đầu tiên
    $taskId = $matches[1][0];
    echo "Đã tìm thấy nhiệm vụ với ID: $taskId\n";

    // Tạo selector của nút bấm
    $startButtonSelector = "#start-serf-$taskId > div";
    echo "Tìm phần tử nút bấm: $startButtonSelector\n";

    // Kiểm tra xem phần tử nút bấm có xuất hiện không
    if (strpos($response, $startButtonSelector) !== false) {
        echo "Nút bấm đã xuất hiện. Tiến hành bấm...\n";
        return true;
    }

    echo "Không tìm thấy nút bấm.\n";
    return false;
}

// Hàm chính
function main() {
    // Kiểm tra cookie và yêu cầu nhập nếu không hợp lệ
    $cookie = requestCookie('metex.txt');
    // Kiểm tra số dư sau khi cookie hợp lệ
    echo "Đang kiểm tra số dư với cookie...\n";
    $balance = checkBalance($cookie);

    if ($balance) {
        echo "Số dư hiện tại: $balance\n";  // In ra số dư nếu có
    } else {
        echo "Không thể lấy số dư hoặc cookie không hợp lệ.\n";
        return;
    }

    // Truy cập trang công việc và kiểm tra nhiệm vụ
    if (getTaskData($cookie)) {
        echo "Nhiệm vụ sẵn sàng để thực hiện.\n";
    } else {
        echo "Không thể thực hiện nhiệm vụ.\n";
    }
}

main();

?>
