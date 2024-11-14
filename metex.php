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
    $url = 'https://meteex.biz';  // Thay bằng URL bạn cần kiểm tra

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
    if (strpos($response, 'Số dư:') !== false) {
        preg_match('/Số dư:\s*([\d\.]+)/', $response, $matches);
        $balance = $matches[1] ?? null;

        if ($balance) {
            return $balance;  // Trả về số dư nếu tìm thấy
        }
    }

    return null;  // Nếu không tìm thấy số dư
}

// Hàm thực hiện bấm vào phần tử sau khi tìm
function clickTask($cookie) {
    $url = 'https://meteex.biz/work-serf?ctrll=ee332be535014f4f86c7ef8594d46aaeee332be535014f4f86c7ef8594d46aae';  // Thay bằng URL trang chứa nhiệm vụ

    // Khởi tạo cURL để lấy nội dung trang
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    // Tìm phần tử đầu tiên với ID "start-serf-" (số đằng sau có thể thay đổi)
    if (preg_match('/<div class="adv-line-cell-2" id="start-serf-(\d+)">/', $response, $matches)) {
        $taskId = $matches[1];  // Lấy ID nhiệm vụ
        echo "Đã tìm thấy nhiệm vụ với ID: $taskId\n";
        // In nội dung phần tử đã tìm thấy (nếu cần)
        echo "Nội dung phần tử tìm thấy: \n";
        echo $matches[0];  // In phần tử đầu tiên

        // Tiến hành tìm và thực hiện thao tác bấm vào nút (Giả định bấm bằng cURL hoặc bạn có thể thực hiện thao tác JS)
        if (preg_match('/onclick="funcjs\[\'go-serf\'\]\(\'(\d+)\',\'[a-f0-9]+\');/', $response, $matches)) {
            $taskId = $matches[1];
            echo "Bấm vào nhiệm vụ với ID: $taskId\n";
            // Bạn có thể thực hiện các bước bấm tiếp theo (sử dụng cURL hoặc thư viện hỗ trợ JS nếu cần)
            // Ví dụ bạn có thể thực hiện hành động bằng cURL để tiếp tục làm việc với trang sau khi bấm
        }
    } else {
        echo "Không tìm thấy nhiệm vụ nào để bấm.\n";
    }
}

// Hàm kiểm tra phần tử <a class="start-yes-serf">
function checkIfButtonAppears($cookie) {
    $url = 'https://meteex.biz';  // URL của trang chứa nhiệm vụ

    // Khởi tạo cURL để lấy nội dung trang sau khi bấm
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    // Kiểm tra nếu phần tử <a class="start-yes-serf"> xuất hiện
    if (strpos($response, 'class="start-yes-serf"') !== false) {
        echo "Đã tìm thấy phần tử <a class=\"start-yes-serf\">.\n";
    } else {
        echo "Không tìm thấy phần tử <a class=\"start-yes-serf\">.\n";
    }
}

// Chạy mã chính
function main() {
    $cookie = getCookieInput('metex.txt');  // Lấy cookie từ tệp hoặc yêu cầu nhập

    echo "Đang kiểm tra số dư...\n";
    $balance = checkBalance($cookie);  // Kiểm tra số dư với cookie hiện tại

    if ($balance) {
        echo "Số dư hiện tại: $balance\n";  // In ra số dư
        // Nếu số dư hợp lệ, tiếp tục thực hiện các hành động khác (bấm vào nhiệm vụ)
        clickTask($cookie);  // Tiến hành bấm vào nhiệm vụ
        checkIfButtonAppears($cookie);  // Kiểm tra xem phần tử đã xuất hiện chưa
    } else {
        // Nếu không có số dư hoặc cookie die, yêu cầu nhập lại cookie
        echo "Cookie không hợp lệ hoặc đã hết hạn. Vui lòng nhập lại cookie.\n";
        // Xóa tệp cookie cũ và yêu cầu nhập lại
        file_put_contents('metex.txt', '');  // Xóa cookie cũ trong tệp
        getCookieInput('metex.txt');  // Yêu cầu nhập lại cookie
    }
}

main();

?>
