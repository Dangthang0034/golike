
<?php
system("clear");
error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$mlen = "999999999999999";

function clear() {
    system("clear");
}

$tg = date("G:i:s", time());
$re = "\033[1;31m";
$gr = "\033[1;32m";
$y = "\033[1;33m";
$bl = "\033[1;34m";
$res = "\033[1;35m";
$nau = "\033[1;36m";
$trang = "\033[1;37m";

$cookieFile = 'cookie.txt';
if (file_exists($cookieFile) && filesize($cookieFile) > 0) {
    $cookie = trim(file_get_contents($cookieFile));
} else {
    echo "Nhập cookie của bạn: ";
    $cookie = trim(fgets(STDIN)); // Đọc dữ liệu từ stdin và loại bỏ khoảng trắng

    // Ghi cookie vào file cookie.txt nếu người dùng nhập mới
    if (!empty($cookie)) {
        file_put_contents($cookieFile, $cookie . PHP_EOL);
    } else {
        echo "Không có cookie được nhập. Vui lòng nhập cookie hợp lệ.\n";
        exit;
    }
}


// Ghi cookie vào file cookie.txt
file_put_contents('cookie.txt', $cookie . PHP_EOL, FILE_APPEND);
clear();

$stt = 1;
while(true){if($stt>50){break;}
 // Bước 1: Truy cập trang web để lấy các giá trị cần thiết
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/doge");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
$response = curl_exec($ch);

// Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
if (curl_errno($ch)) {
    echo 'Lỗi: ' . curl_error($ch) . "\n";
    exit;
}

// Kiểm tra nếu phản hồi chỉ ra rằng cookie sai
if (strpos($response, 'Invalid cookie') !== false || empty($response)) {
    echo "Cookie không hợp lệ. Vui lòng nhập lại cookie.\n";
    file_put_contents('cookie.txt', ''); // Xóa dữ liệu cũ trong file
    echo "Nhập cookie mới của bạn: ";
    $cookie = trim(fgets(STDIN));
    file_put_contents('cookie.txt', $cookie . PHP_EOL);
    echo "Cookie mới đã được ghi vào file cookie.txt.\n";
    continue;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/doge");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
$response = curl_exec($ch);

// Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
if (curl_errno($ch)) {
    echo 'Lỗi: ' . curl_error($ch) . "\n";
    exit;
}
// Đóng cURL
curl_close($ch);
clear();
// Bước 2: Phân tích HTML để lấy các giá trị cần thiết từ form
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Để bỏ qua các cảnh báo nếu có lỗi trong HTML
$dom->loadHTML($response);
libxml_clear_errors();

$csrf_token_name = '';
$token = '';
$wallet = '';

$inputs = $dom->getElementsByTagName('input');
foreach ($inputs as $input) {
    if ($input->getAttribute('name') == 'csrf_token_name') {
        $csrf_token_name = $input->getAttribute('value');
    }
    if ($input->getAttribute('name') == 'token') {
        $token = $input->getAttribute('value');
    }
    if ($input->getAttribute('name') == 'wallet') {
        $wallet = $input->getAttribute('value');
    }
}

if (empty($csrf_token_name) || empty($token) || empty($wallet)) {
    echo "Không thể lấy các giá trị cần thiết từ trang. Chuyển sang mã tiếp theo.\n";
    break; // Dừng vòng lặp hiện tại và chuyển sang `doge` tiếp theo
}

// Bước 3: Gửi yêu cầu POST
$ch = curl_init();
$postData = [
    'csrf_token_name' => $csrf_token_name,
    'token' => $token,
    'wallet' => $wallet
];

curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/verify/doge");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Lỗi: ' . curl_error($ch) . "\n";
    exit;
}

curl_close($ch);
// Đóng cURL
curl_close($ch);
curl_setopt($ch, CURLOPT_URL, 'https://claim.naijafav.top/faucet/currency/doge');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Sử dụng cookie từ người dùng nhập
$response = curl_exec($ch);

// Phân tích phản hồi để tìm thông báo
preg_match("/html: '(.*?)'/", $response, $matches);
if (isset($matches[1])) {
    echo "$gr $stt |$bl $tg |$y" . $matches[1] . "$trang\n";
    $stt++;
} else {
    echo "Không tìm thấy thông báo.\r"; break;
}

// Thời gian chờ giữa các lần gửi
for ($time = 11; $time > 0; $time--) {
    echo "Nhận thêm tiền sau $time giây \r";
    sleep(1);
}
}

$stt = 0;
while(true){
    if($stt > 100){
        break;
    }

    // Bước 1: Truy cập trang web để lấy các giá trị cần thiết cho TRX
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/trx");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }

    // Kiểm tra nếu phản hồi chỉ ra rằng cookie sai
    if (strpos($response, 'Invalid cookie') !== false || empty($response)) {
        echo "Cookie không hợp lệ. Vui lòng nhập lại cookie.\n";
        file_put_contents('cookie.txt', ''); // Xóa dữ liệu cũ trong file
        echo "Nhập cookie mới của bạn: ";
        $cookie = trim(fgets(STDIN));
        file_put_contents('cookie.txt', $cookie . PHP_EOL);
        echo "Cookie mới đã được ghi vào file cookie.txt.\n";
        continue;
    }

    // Tiếp tục với TRX
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/trx");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }
    // Đóng cURL
    curl_close($ch);

    // Bước 2: Phân tích HTML để lấy các giá trị cần thiết từ form
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Để bỏ qua các cảnh báo nếu có lỗi trong HTML
    $dom->loadHTML($response);
    libxml_clear_errors();

    $csrf_token_name = '';
    $token = '';
    $wallet = '';

    $inputs = $dom->getElementsByTagName('input');
    foreach ($inputs as $input) {
        if ($input->getAttribute('name') == 'csrf_token_name') {
            $csrf_token_name = $input->getAttribute('value');
        }
        if ($input->getAttribute('name') == 'token') {
            $token = $input->getAttribute('value');
        }
        if ($input->getAttribute('name') == 'wallet') {
            $wallet = $input->getAttribute('value');
        }
    }

    if (empty($csrf_token_name) || empty($token) || empty($wallet)) {
        echo "Không thể lấy các giá trị cần thiết từ trang. Chuyển sang mã tiếp theo.\n";
        break; // Dừng vòng lặp hiện tại và chuyển sang mã tiếp theo
    }

    // Bước 3: Gửi yêu cầu POST cho TRX
    $ch = curl_init();
    $postData = [
        'csrf_token_name' => $csrf_token_name,
        'token' => $token,
        'wallet' => $wallet
    ];

    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/verify/trx");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }

    curl_close($ch);

    // Đóng cURL
    curl_close($ch);
    curl_setopt($ch, CURLOPT_URL, 'https://claim.naijafav.top/faucet/currency/trx');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Sử dụng cookie từ người dùng nhập
    $response = curl_exec($ch);

    // Phân tích phản hồi để tìm thông báo
    preg_match("/html: '(.*?)'/", $response, $matches);
    if (isset($matches[1])) {
        echo "$gr $stt |$bl $tg |$y" . $matches[1] . "$trang\n";
        $stt++;
    } else {
        echo "Không tìm thấy thông báo.\r"; break;
    }

    // Thời gian chờ giữa các lần gửi
    for ($time = 11; $time > 0; $time--) {
        echo "Nhận thêm tiền sau $time giây \r";
        sleep(1);
    }

    // Bước 4: Chuyển sang BNB sau khi TRX hoàn tất
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/bnb");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }

    // Tiếp tục với BNB
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/currency/bnb");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL hoặc lỗi liên quan đến cookie
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }
    // Đóng cURL
    curl_close($ch);

    // Bước 5: Phân tích HTML để lấy các giá trị cần thiết từ form BNB
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Để bỏ qua các cảnh báo nếu có lỗi trong HTML
    $dom->loadHTML($response);
    libxml_clear_errors();

    $csrf_token_name_bnb = '';
    $token_bnb = '';
    $wallet_bnb = '';

    $inputs = $dom->getElementsByTagName('input');
    foreach ($inputs as $input) {
        if ($input->getAttribute('name') == 'csrf_token_name') {
            $csrf_token_name_bnb = $input->getAttribute('value');
        }
        if ($input->getAttribute('name') == 'token') {
            $token_bnb = $input->getAttribute('value');
        }
        if ($input->getAttribute('name') == 'wallet') {
            $wallet_bnb = $input->getAttribute('value');
        }
    }

    if (empty($csrf_token_name_bnb) || empty($token_bnb) || empty($wallet_bnb)) {
        echo "Không thể lấy các giá trị cần thiết từ trang BNB. Chuyển sang mã tiếp theo.\n";
        break; // Dừng vòng lặp hiện tại và chuyển sang mã tiếp theo
    }

    // Bước 6: Gửi yêu cầu POST cho BNB
    $ch = curl_init();
    $postData_bnb = [
        'csrf_token_name' => $csrf_token_name_bnb,
        'token' => $token_bnb,
        'wallet' => $wallet_bnb
    ];

    curl_setopt($ch, CURLOPT_URL, "https://claim.naijafav.top/faucet/verify/bnb");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData_bnb));
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Lỗi: ' . curl_error($ch) . "\n";
        exit;
    }

    curl_close($ch);

    // Đóng cURL
    curl_close($ch);
    curl_setopt($ch, CURLOPT_URL, 'https://claim.naijafav.top/faucet/currency/bnb');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);  // Sử dụng cookie từ người dùng nhập
    $response = curl_exec($ch);

    // Phân tích phản hồi để tìm thông báo BNB
    preg_match("/html: '(.*?)'/", $response, $matches);
    if (isset($matches[1])) {
        echo "$gr $stt |$bl $tg |$y" . $matches[1] . "$trang\n";
        $stt++;
    } else {
        echo "Không tìm thấy thông báo.\r"; break;
    }

    // Thời gian chờ giữa các lần gửi
    for ($time = 11; $time > 0; $time--) {
        echo "Nhận thêm tiền sau $time giây \r";
        sleep(1);
    }
}


echo "Đã làm xong hết nhiệm vụ.\n";
?>
