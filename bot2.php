<?php
system("clear");

function clear() {
    system("clear");
}

// Kiểm tra cookie và nhập cookie nếu chưa có
$cookieFile = 'claimcoin.txt';
$cookie = '';

// Kiểm tra nếu tệp cookie đã tồn tại và có dữ liệu
if (file_exists($cookieFile) && filesize($cookieFile) > 0) {
    $cookie = trim(file_get_contents($cookieFile));
} else {
    echo "Nhập cookie của bạn: ";
    $cookie = trim(fgets(STDIN)); // Đọc cookie từ bàn phím và loại bỏ khoảng trắng

    // Kiểm tra nếu người dùng nhập cookie và ghi vào tệp nếu hợp lệ
    if (!empty($cookie)) {
        file_put_contents($cookieFile, $cookie . PHP_EOL);
    } else {
        echo "Không có cookie được nhập. Vui lòng nhập cookie hợp lệ.\n";
        exit;
    }
}

// Hàm để lấy và hiển thị số dư token
function getTokenBalance($cookie) {
    $url = 'https://claimcoin.in/dashboard';
    $response = fetchPage($url, $cookie);

    // Phân tích HTML và lấy số dư token
    $doc = new DOMDocument();
    @$doc->loadHTML($response);
    $xpath = new DOMXPath($doc);

    // Tìm phần tử chứa số dư token
    $tokenNode = $xpath->query('//div[@class="project-counter"]/h2')->item(0);
    if ($tokenNode) {
        $tokenBalance = trim($tokenNode->nodeValue);
        echo "Token của bạn là: " . $tokenBalance . "\n";
    } else {
        echo "Không tìm thấy số dư token.\n";
    }
}

// Hàm để thực hiện yêu cầu cURL
function fetchPage($url, $cookie) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Gửi cookie trong yêu cầu
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    return $response;
}

// Hàm để lấy CSRF token
function getCsrfToken($cookie, $url) {
    $response = fetchPage($url, $cookie);

    // Phân tích HTML và lấy CSRF token
    $doc = new DOMDocument();
    @$doc->loadHTML($response);
    $xpath = new DOMXPath($doc);

    // Tìm phần tử chứa CSRF token
    $csrfNode = $xpath->query('//input[contains(@name, "csrf_token_name")]')->item(0);  // Thay "csrf" bằng tên thực tế
    if ($csrfNode) {
        return $csrfNode->getAttribute('value');
    } else {
        echo "Không tìm thấy CSRF token.\n";
        return null;
    }
}

// Hàm để thực hiện nhiệm vụ
$buttonNodes = $xpath->query('//button[contains(@class, "btn btn-success btn-block")]');
if ($buttonNodes->length > 0) {
    foreach ($buttonNodes as $buttonNode) {
        $onclick = $buttonNode->getAttribute('onclick');
        preg_match("/location.href='(.*?)'/", $onclick, $urlMatches);
        if (isset($urlMatches[1])) {
            $taskUrl = $urlMatches[1];
            // Thực hiện nhiệm vụ
            echo "Đang thực hiện nhiệm vụ với URL: $taskUrl\n";
            $response = fetchPage($taskUrl, $cookie);
            $timeNode = $xpath->query('//*[@id="iframe"]/div/div[1]/div/div/div[2]/div/span[2]/i');
            if ($timeNode->length > 0) {
                $timeNode = $timeNode->item(0);
                $time = null;
                if ($timeNode) {
                    preg_match('/(\d+)\s+seconds/', $timeNode->nodeValue, $matches);
                    $time = isset($matches[1]) ? $matches[1] : 0;
                }

                if ($time) {
                    for ($t = $time; $t > 0; $t--) {
                        echo "Làm nhiệm vụ trong $t giây...\r";
                        sleep(1);
                    }

                    // Gửi yêu cầu xác nhận nhiệm vụ
                    $csrf_token = getCsrfToken($cookie, $taskUrl);
                    if ($csrf_token === null) {
                        echo "Không thể lấy CSRF token.\n";
                        return false;
                    }

                    $postData = ['csrf_token_name' => $csrf_token];
                    $sv = basename($taskUrl);
                    $urlPost = "https://claimcoin.in/ptc/verify/" . $sv;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urlPost);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        echo 'Error: ' . curl_error($ch);
                        exit;
                    }
                    curl_close($ch);

                    echo "Đã hoàn thành nhiệm vụ.\n";
                    getTokenBalance($cookie);
                    sleep(2); // Đợi một chút trước khi tải lại trang
                    return true;
                }
            } else {
                echo "Không tìm thấy thời gian.\n";
            }
        }
    }
} else {
    echo "Không có nhiệm vụ nào.\r";
    return false;
}


getTokenBalance($cookie);
echo"lam nv \n";
// Vòng lặp chính để thực hiện nhiệm vụ
do {
    // Thực hiện nhiệm vụ vòng 1
    if (!performTask($cookie)) {
        break; // Nếu không có nhiệm vụ, thoát khỏi vòng lặp
    }

    // Thực hiện nhiệm vụ vòng 2 nếu có
    if (!performTasks($cookie)) {
        break; // Nếu không có nhiệm vụ, thoát khỏi vòng lặp
    }

} while (true);

// Sau khi hết nhiệm vụ, chờ 30 phút (1800 giây) trước khi tìm lại nhiệm vụ
echo "Đã hoàn thành tất cả nhiệm vụ, chờ 30 phút trước khi thử lại.\n";
for ($delay = 1800; $delay > 0; $delay--) {
    echo "Chờ $delay giây...\r";
    sleep(1);
}
?>
