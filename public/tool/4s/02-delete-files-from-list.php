<?php

/*
 * Xóa các file được liệt kê trong file location_mismatch.txt
 * Mỗi dòng là một đường dẫn file cần xóa
 */

if (!function_exists('isCli')) {
    function isCli() {
        return php_sapi_name() === 'cli';
    }
}

if (!isCli()) {
    echo "ERROR: This script must be run from CLI!";
    exit;
}

set_time_limit(0);

$inputFile = __DIR__."/location_mismatch.txt";
$logFile = "/share/delete_log_" . date("Y-m-d_H-i-s") . ".txt";

if (!file_exists($inputFile)) {
    echo "ERROR: Input file not found: $inputFile\n";
    exit;
}

echo "\n╔═══════════════════════════════════════════════════════════════╗";
echo "\n║              File Deletion from List                          ║";
echo "\n╚═══════════════════════════════════════════════════════════════╝\n";

$totalLines = 0;
$deleteSuccess = 0;
$deleteError = 0;
$notFound = 0;
$skipEmpty = 0;

$logHandle = fopen($logFile, "w");
fwrite($logHandle, "File Deletion Log - " . date("Y-m-d H:i:s") . "\n");
fwrite($logHandle, "Input file: $inputFile\n\n");

// Đọc file input
$handle = fopen($inputFile, "r");
if (!$handle) {
    echo "ERROR: Cannot open input file\n";
    exit;
}

while (($line = fgets($handle)) !== false) {
    $totalLines++;
    $filePath = trim($line);

    if (empty($filePath)) {
        $skipEmpty++;
        continue;
    }

    // Bỏ qua dòng comment
    if (substr($filePath, 0, 1) === '#') {
        $skipEmpty++;
        continue;
    }

    // Kiểm tra file có tồn tại không
    {

        // Thử xóa file
        if(0)
        if (unlink($filePath)) {
            $deleteSuccess++;
            echo "\r[$totalLines] DELETED: $filePath";
            fwrite($logHandle, "SUCCESS: $filePath\n");
        } else {
            $deleteError++;
            echo "\n[!] ERROR deleting: $filePath";
            fwrite($logHandle, "ERROR: $filePath (Permission denied or other error)\n");
        }
    }

    // Progress report mỗi 100 files
//    if ($totalLines % 100 == 0)
    {
        echo "\n--- Progress: $totalLines lines, Deleted: $deleteSuccess, Errors: $deleteError, Not Found: $notFound ---";
    }
}

fclose($handle);
fclose($logHandle);

// Summary
echo "\n\n╔═══════════════════════════════════════════════════════════════╗";
echo "\n║                         SUMMARY                               ║";
echo "\n╠═══════════════════════════════════════════════════════════════╣";
printf("\n║  Total lines processed    : %-30s ║", number_format($totalLines));
printf("\n║  Skipped (empty/comment)  : %-30s ║", number_format($skipEmpty));
printf("\n║  Successfully deleted     : %-30s ║", number_format($deleteSuccess));
printf("\n║  Not found                : %-30s ║", number_format($notFound));
printf("\n║  Delete errors            : %-30s ║", number_format($deleteError));
echo "\n╚═══════════════════════════════════════════════════════════════╝\n";

echo "\n📄 Log file: $logFile\n";

if ($deleteSuccess > 0) {
    echo "\n✓ Successfully deleted $deleteSuccess files!\n";
}

if ($deleteError > 0) {
    echo "\n⚠ WARNING: $deleteError files could not be deleted (check permissions)!\n";
}

echo "\n✓ Done!\n\n";

?>
