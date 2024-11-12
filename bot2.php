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
