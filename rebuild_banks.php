<?php

// Step 1: Đọc banks.txt và tạo mapping
$txtLines = file('config/banks.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$idMap = [];

foreach ($txtLines as $line) {
    if (preg_match('/^(.+?)\s-\s(\d{8})$/', trim($line), $matches)) {
        $name = trim($matches[1]);
        $id = $matches[2];
        if (!isset($idMap[$id])) {
            $idMap[$id] = $line;  // Giữ nguyên toàn bộ dòng từ banks.txt
        }
    }
}

echo "Loaded " . count($idMap) . " bank entries from banks.txt\n";

// Step 2: Đọc banks.php và transform
$phpContent = file_get_contents('config/banks.php');

// Thêm array format với tất cả 3 trường
$newContent = preg_replace_callback(
    "/^(\\s*)'([A-Z_-]+)'\\s*=>\\s*'([^']*?-\\s*(\\d{8}))'/m",
    function($matches) use ($idMap) {
        $indent = $matches[1];
        $key = $matches[2];
        $value = $matches[3];
        $id = $matches[4];
        
        // Tách name từ value (bỏ ID ở cuối)
        $namePart = preg_replace('/\\s*-\\s*\\d{8}$/', '', $value);
        
        // Convert thành uppercase
        $upperValue = strtoupper(str_replace(['À', 'Á', 'Â', 'Ã', 'È', 'É', 'Ê', 'Ì', 'Í', 'Ò', 'Ó', 'Ô', 'Õ', 'Ù', 'Ú', 'Ý', 'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ằ', 'ắ', 'ẳ', 'ẵ', 'ặ', 'â', 'ầ', 'ấ', 'ẩ', 'ẫ', 'ậ', 'đ', 'è', 'é', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ề', 'ế', 'ểe', 'ễ', 'ệ', 'ì', 'í', 'ỉ', 'ĩ', 'ị', 'ò', 'ó', 'ỏ', 'õ', 'ọ', 'ô', 'ồ', 'ố', 'ổ', 'ỗ', 'ộ', 'ơ', 'ờ', 'ớ', 'ở', 'ỡ', 'ợ', 'ù', 'ú', 'ủ', 'ũ', 'ụ', 'ư', 'ừ', 'ứ', 'ử', 'ữ', 'ự', 'ỳ', 'ý', 'ỷ', 'ỹ', 'ỵ'], ['A', 'A', 'A', 'A', 'E', 'E', 'E', 'I', 'I', 'O', 'O', 'O', 'O', 'U', 'U', 'Y', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y'], $value));
        
        // Tạo public_name (chữ thường từ giá trị gốc)
        $publicName = $namePart . ' - ' . $key;
        
        // Tạo bidv_name (uppercase, bỏ dấu)
        $bidvName = $upperValue;
        
        // Tạo bidv_name2 (lấy từ banks.txt)
        $bidvName2 = isset($idMap[$id]) ? $idMap[$id] : $bidvName;
        
        return "{$indent}'{$key}' => ['public_name' => '{$publicName}', 'bidv_name' => '{$bidvName}', 'bidv_name2' => '{$bidvName2}']";
    },
    $phpContent
);

file_put_contents('config/banks.php', $newContent);
echo "✓ Updated banks.php with all 3 fields\n";
