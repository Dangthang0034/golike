<?php
// Hàm gửi yêu cầu GET và lấy HTML của trang
function get_html($url, $cookies) {
    $ch = curl_init();
    
    // Cấu hình cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bỏ qua SSL nếu cần thiết
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);  // Dùng cookies
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    
    curl_close($ch);
    return $response;
}

// Hàm tìm số dư trong HTML
function find_balance($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);  // Sử dụng @ để bỏ qua lỗi không mong muốn trong DOMDocument
    
    $xpath = new DOMXPath($dom);
    
    // Tìm phần tử có chứa số dư (ví dụ, class "balance" trong thẻ <span>)
    $balance_elements = $xpath->query("//span[contains(@class, 'balance')]");
    
    if ($balance_elements->length > 0) {
        $balance = $balance_elements->item(0)->nodeValue;
        echo "Số dư tài khoản là: " . trim($balance) . "\n";
    } else {
        echo "Không tìm thấy thông tin số dư!\n";
    }
}

// Hàm tìm phần tử trước khi bấm nút
function find_task_elements_before_click($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);  // Sử dụng @ để bỏ qua lỗi không mong muốn trong DOMDocument
    
    $xpath = new DOMXPath($dom);
    
    // Tìm tất cả các div có id bắt đầu với "start-serf-" và có class "adv-line-cell-2"
    $tasks = $xpath->query("//div[starts-with(@id, 'start-serf-') and contains(@class, 'adv-line-cell-2')]");
    
    if ($tasks->length > 0) {
        echo "Tìm thấy " . $tasks->length . " nhiệm vụ trước khi bấm nút!\n";
        
        // Lấy phần tử đầu tiên
        $first_task = $tasks->item(0);
        echo "Phần tử đầu tiên trước khi bấm nút:\n";
        echo $dom->saveHTML($first_task);
    } else {
        echo "Không tìm thấy nhiệm vụ trước khi bấm nút.\n";
    }
}

// Hàm giả lập hành động bấm nút và lấy HTML sau khi bấm
function perform_click_action($spr_value) {
    $url = "https://meteex.biz/run/serfview?spr=" . $spr_value;
    
    // Cookies cần thiết
    $cookies = "PHPSESSID=bdndcimfv7aes8kuaiat0msqc6; _ym_uid=1731570111370052875; _ym_d=1731570111; _ym_isad=1; menu_ref=b75de28c243f02ea2e9532cfb8c21dcb";
    
    // Lấy HTML sau khi bấm nút
    return get_html($url, $cookies);
}

// Hàm tìm phần tử sau khi bấm nút
function find_task_elements_after_click($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    
    $xpath = new DOMXPath($dom);
    
    // Tìm tất cả các div có id bắt đầu với "start-serf-" và có class "adv-line-cell-2"
    $tasks = $xpath->query("//div[starts-with(@id, 'start-serf-') and contains(@class, 'adv-line-cell-2')]");
    
    if ($tasks->length > 0) {
        echo "Tìm thấy " . $tasks->length . " nhiệm vụ sau khi bấm nút!\n";
        
        // Lấy phần tử đầu tiên
        $first_task = $tasks->item(0);
        echo "Phần tử đầu tiên sau khi bấm nút:\n";
        echo $dom->saveHTML($first_task);
    } else {
        echo "Không tìm thấy nhiệm vụ sau khi bấm nút.\n";
    }
}

// Hàm chính để chạy chương trình
function main() {
    // URL ban đầu trước khi bấm
    $initial_url = "https://meteex.biz/work-serf?ctrll=ee332be535014f4f86c7ef8594d46aaeee332be535014f4f86c7ef8594d46aae";
    
    // Cookies (cần cung cấp cookies hợp lệ từ trang)
    $cookies = "PHPSESSID=bdndcimfv7aes8kuaiat0msqc6; _ym_uid=1731570111370052875; _ym_d=1731570111; _ym_isad=1; menu_ref=b75de28c243f02ea2e9532cfb8c21dcb";
    
    // Lấy HTML ban đầu
    $initial_html = get_html($initial_url, $cookies);
    
    if ($initial_html) {
        // Trước khi bấm, tìm phần tử đầu tiên và số dư
        find_balance($initial_html);
        find_task_elements_before_click($initial_html);
        
        // ID nhiệm vụ và tham số cần thiết
        $spr_value = "f0ec5e62e880af3c29905eb68aca04b1";  // Tham số SPR lấy từ URL bạn đã cung cấp
        
        // Giả lập bấm nút
        $html_content = perform_click_action($spr_value);
        
        if ($html_content) {
            // Sau khi bấm nút, tìm các phần tử trong HTML trả về
            find_task_elements_after_click($html_content);
        }
    }
}

// Chạy hàm chính
main();
?>
