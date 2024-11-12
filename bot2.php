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
function performTask($cookie) {
    $url = 'https://claimcoin.in/ptc';
    $response = fetchPage($url, $cookie);
    sleep(2);
    // Phân tích HTML và lấy thông tin nhiệm vụ
    $doc = new DOMDocument();
    @$doc->loadHTML($response);
    $xpath = new DOMXPath($doc);

    // Tìm phần tử chứa thông tin nhiệm vụ
    $buttonNode = $xpath->query('//button[contains(@class, "btn-success")]')->item(0);
    if ($buttonNode) {
        $urll = $buttonNode->getAttribute('onclick');
        preg_match("/location.href='(.*?)'/", $urll, $urlMatches);
        $urll = isset($urlMatches[1]) ? $urlMatches[1] : null;

        // Thực hiện nhiệm vụ
        $response = fetchPage($urll, $cookie);
            // Lấy CSRF token tại đây
        $csrf_token = getCsrfToken($cookie, $urll); // Lấy CSRF token từ URL nhiệm vụ
        if ($csrf_token === null) {
            return false; // Không thể thực hiện nhiệm vụ nếu không có CSRF token
            }
        // Chờ đợi và làm nhiệm vụ
        $timeNode = $xpath->query('//span[@class="badge span-danger text-danger"]')->item(0);
        
        // Đảm bảo $time có giá trị mặc định nếu không tìm thấy
        $time = null;
        if ($timeNode) {
            preg_match('/(\d+)\s+seconds/', $timeNode->nodeValue, $matches);
            $time = isset($matches[1]) ? $matches[1] : 0; // Nếu không tìm thấy, gán $time = 0
        }

        if ($urll && $time) {
            for ($t = $time; $t > 0; $t--) {
                echo "Làm nhiệm vụ trong $t giây...\r";
                sleep(1);
            }



            // Gửi yêu cầu xác nhận nhiệm vụ với CSRF token
            $postData = ['csrf_token_name' => $csrf_token];  // Tên CSRF token và giá trị cần thay đổi theo thực tế
            $sv = basename($urll);
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

            echo "Đã hoàn thành nhiệm vụ.\r";
            // Kiểm tra lại số dư token sau khi hoàn thành nhiệm vụ
            getTokenBalance($cookie);

            // Làm mới trang sau khi hoàn thành nhiệm vụ
           
            sleep(2); // Đợi một chút trước khi tải lại trang

            return true; // Nhiệm vụ đã hoàn thành
        } else {
            echo "Không tìm thấy link làm nhiệm vụ \r";
            return false; // Không có nhiệm vụ
        }
    } 
}

// Hàm thực hiện vòng 2 các nhiệm vụ
function performTasks($cookie) {
    $url = 'https://claimcoin.in/ptc';
    $response = fetchPage($url, $cookie);
    sleep(2);

    // Phân tích HTML và lấy thông tin nhiệm vụ
    $doc = new DOMDocument();
    @$doc->loadHTML($response);
    $xpath = new DOMXPath($doc);

    // Tìm tất cả các button có class 'btn btn-success btn-block'
    $buttonNodes = $xpath->query('//button[contains(@class, "btn btn-success btn-block")]');

    if ($buttonNodes->length > 0) {
        // Lặp qua các button để lấy link
        foreach ($buttonNodes as $buttonNode) {
            $onclick = $buttonNode->getAttribute('onclick');
            preg_match("/location.href='(.*?)'/", $onclick, $urlMatches);

            if (isset($urlMatches[1])) {
                $taskUrl = $urlMatches[1];

                // Thực hiện nhiệm vụ
                echo "Đang thực hiện nhiệm vụ với URL: $taskUrl\n";

                // Gửi yêu cầu đến URL nhiệm vụ
                $response = fetchPage($taskUrl, $cookie);
                
                // Chờ đợi và làm nhiệm vụ
                $timeNode = $xpath->query('//*[@id="iframe"]/div/div[1]/div/div/div[2]/div/span[2]/i');

if ($timeNode->length > 0) {
    $timeNode = $timeNode->item(0);
    // Tiếp tục xử lý với $timeNode
} else {
    echo "Không tìm thấy thời gian.\n";
    $timeNode = null;
}


                if ($taskUrl && $time) {
                    for ($t = $time; $t > 0; $t--) {
                        echo "Làm nhiệm vụ trong $t giây...\r";
                        sleep(1);
                    }

                    // Gửi yêu cầu xác nhận nhiệm vụ
                    $csrf_token = getCsrfToken($cookie, $taskUrl); // Lấy CSRF token từ URL nhiệm vụ
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
                    // Kiểm tra lại số dư token sau khi hoàn thành nhiệm vụ
                    getTokenBalance($cookie);

                    // Làm mới trang sau khi hoàn thành nhiệm vụ
                    sleep(2); // Đợi một chút trước khi tải lại trang
                    return true; // Nhiệm vụ đã hoàn thành
                } else {
                    echo "Không tìm thấy link làm nhiệm vụ \n";
                    return false; // Không có nhiệm vụ
                }
            }
        }
    } else {
        echo "Không có nhiệm vụ nào.\r";
        return false; // Không có nhiệm vụ
    }
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
