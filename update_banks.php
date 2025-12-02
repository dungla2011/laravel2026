<?php
$lines = file('config/banks.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$idMap = [];

foreach ($lines as $line) {
    if (preg_match('/^(.+?)\s-\s(\d{8})$/', trim($line), $matches)) {
        $name = trim($matches[1]);
        $id = $matches[2];
        if (!isset($idMap[$id])) {
            $idMap[$id] = $name . ' - ' . $id;  // Giữ nguyên format với ID
        }
    }
}

$phpContent = file_get_contents('config/banks.php');
$updated = 0;

// Regex để tìm từng bank entry và thêm bidv_name2
foreach ($idMap as $id => $fullName) {
    // Tìm pattern: 'bidv_name' => '...- 01307001'
    $pattern = "/'bidv_name'\\s*=>\\s*'([^']*?-\\s*" . preg_quote($id) . ")'/";
    
    if (preg_match($pattern, $phpContent, $matches)) {
        $oldBidvName = $matches[1];
        $newName = addslashes($fullName);
        
        // Replace 'bidv_name' => '...' với thêm 'bidv_name2' => '...'
        $replacement = "'bidv_name' => '$oldBidvName', 'bidv_name2' => '$newName'";
        $phpContent = preg_replace(
            $pattern,
            $replacement,
            $phpContent,
            1
        );
        $updated++;
    }
}

file_put_contents('config/banks.php', $phpContent);
echo "✓ Updated $updated bank entries with full name + ID\n";
