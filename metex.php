<?php

// Đường dẫn đến tệp chứa cookie
$cookieFile = 'metex.txt';

// Hàm yêu cầu nhập cookie từ màn hình
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

// Hàm lấy số dư từ chuỗi HTML
function extractBalanceFromHTML($htmlContent) {
    // Tìm chuỗi bắt đầu của phần tử số dư
    $startTag = '<span class="new-up-osn" translate="no">';
    $endTag = '</span>';

    // Tìm vị trí bắt đầu và kết thúc của số dư trong HTML
    $startPos = strpos($htmlContent, $startTag);
    if ($startPos === false) {
        return null;  // Nếu không tìm thấy, trả về null
    }

    $startPos += strlen($startTag);  // Di chuyển đến vị trí bắt đầu số dư
    $endPos = strpos($htmlContent, $endTag, $startPos);  // Tìm vị trí kết thúc số dư
    if ($endPos === false) {
        return null;  // Nếu không tìm thấy vị trí kết thúc
    }

    // Cắt chuỗi số dư từ HTML
    $balance = substr($htmlContent, $startPos, $endPos - $startPos);
    return trim($balance);  // Trả về số dư đã cắt
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

    // Sử dụng hàm extractBalanceFromHTML để lấy số dư từ nội dung phản hồi
    return extractBalanceFromHTML($response);
}

// Hàm yêu cầu và kiểm tra cookie
function requestCookie($cookieFile) {
    $attempt = 0; // Biến đếm số lần nhập cookie

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
            $attempt++;

            if ($attempt >= 3) {
                echo "Lỗi: Đã thử 3 lần nhập cookie không hợp lệ. Chương trình kết thúc.\n";
                exit(1);  // Kết thúc chương trình nếu nhập sai 3 lần
            }
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
