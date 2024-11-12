<?php
// Đường dẫn đến file bot.php trên GitHub
$githubUrl = 'https://raw.githubusercontent.com/Dangthang0034/golike/main/claimcoin';

// Tên file mà chúng ta sẽ lưu nội dung tải về
$filename = 'claimcoin';

// Tải nội dung từ GitHub về
echo "Đang tải tool .....\n";
$fileContent = file_get_contents($githubUrl);

// Kiểm tra xem nội dung có được tải về hay không
if ($fileContent !== false) {
    // Lưu nội dung vào file bot.php
    file_put_contents($filename, $fileContent);

    // Chạy file bot.php (nếu file tải về thành công)
    echo "Vào tool \n";
    include($filename); // Thực thi file PHP vừa tải về
	if (file_exists($filename)) {
        unlink($filename); // Xóa file
        echo "Đã xóa file tool.\n";
    }
} else {
    echo "Lỗi: Không thể tải tool \n";
}
?>
