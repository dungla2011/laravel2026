<?php

// Đọc banks.txt và tạo mapping theo ID
$lines = file('config/banks.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$idMap = [];

foreach ($lines as $line) {
    // Tách ID từ cuối dòng (8 chữ số)
    if (preg_match('/^(.+?)\s-\s(\d{8})$/', trim($line), $matches)) {
        $name = trim($matches[1]);
        $id = $matches[2];
        if (!isset($idMap[$id])) {
            $idMap[$id] = $name;
        }
    }
}

echo "Total unique IDs in banks.txt: " . count($idMap) . "\n";

// Đọc banks.php và cập nhật
$phpContent = file_get_contents('config/banks.php');
$modified = false;

// Regex để tìm và cập nhật từng entry
// Match pattern: 'KEY' => ['public_name' => '...', 'bidv_name' => '...']
preg_match_all(
    '/\'([A-Z_-]+)\'\s*=>\s*\[\s*\'public_name\'\s*=>\s*\'([^\']*)\',\s*\'bidv_name\'\s*=>\s*\'([^\']*?)-\s*(\d{8})\'\s*\]/',
    $phpContent,
    $matches,
    PREG_OFFSET_CAPTURE
);

echo "Found " . count($matches[4]) . " entries in banks.php\n";

$replacements = [];
for ($i = 0; $i < count($matches[4]); $i++) {
    $key = $matches[1][$i][0];
    $publicName = $matches[2][$i][0];
    $bidvName = $matches[3][$i][0];
    $id = $matches[4][$i][0];
    $fullMatch = $matches[0][$i][0];
    
    if (isset($idMap[$id])) {
        $bidvName2 = $idMap[$id];
        
        // Tạo replacement mới
        $newEntry = "'$key' => ['public_name' => '$publicName', 'bidv_name' => '$bidvName - $id', 'bidv_name2' => '$bidvName2']";
        
        $replacements[] = [
            'old' => $fullMatch,
            'new' => $newEntry,
            'key' => $key,
            'id' => $id,
            'bidv_name2' => $bidvName2
        ];
        
        echo "✓ $key ($id) => Found: $bidvName2\n";
    } else {
        echo "✗ $key ($id) => NOT found in banks.txt\n";
    }
}

// Lưu replacements để dùng sau
file_put_contents('map_banks_replacements.json', json_encode($replacements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n" . count($replacements) . " replacements prepared\n";
echo "Replacements saved to map_banks_replacements.json\n";
