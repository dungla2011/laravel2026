<?php

/*
 * Kiểm tra các file có location1 không khớp với disk trong đường dẫn
 * Input: list_check_del.txt - mỗi dòng là filepath như /mnt/sdb/6474000-6475000/6474504
 * Output: location_mismatch.txt - các dòng có location1 không khớp với disk
 */

$_SERVER['SERVER_NAME'] = "4share.vn";

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if (!isCli()) {
    echo "ERROR: NOT CLI!";
    return;
}

set_time_limit(0);

$inputFile = __DIR__."/list_check_del.txt";
$outputFile = __DIR__."/location_mismatch.txt";

if (!file_exists($inputFile)) {
    echo "ERROR: Input file not found: $inputFile\n";
    exit;
}

echo "\n╔═══════════════════════════════════════════════════════════════╗";
echo "\n║          FileCloud Location Mismatch Checker                 ║";
echo "\n╚═══════════════════════════════════════════════════════════════╝\n";

$totalLines = 0;
$foundInDb = 0;
$notFoundInDb = 0;
$locationMatch = 0; 
$locationMismatch = 0;
$mismatchLines = [];

// Đọc file input
$handle = fopen($inputFile, "r");
if (!$handle) {
    echo "ERROR: Cannot open input file\n";
    exit;
}

while (($line = fgets($handle)) !== false) {
    $totalLines++;
    $line = trim($line);
    
    if (empty($line)) {
        continue;
    }
    
    // Parse path: /mnt/sdb/6474000-6475000/6474504
    // Extract disk name and file ID
    if (preg_match('#/mnt/([^/]+)/[^/]+/(\d+)#', $line, $matches)) {
        $diskName = $matches[1];  // e.g., "sdb"
        $fileId = $matches[2];     // e.g., "6474504"
        
        // Tìm FileCloud record
        $fileCloud = \App\Models\FileCloud::find($fileId);
        
        if ($fileCloud) {
            $foundInDb++;
            
            // Kiểm tra location1 có chứa disk name không
            if (strpos($fileCloud->location1, $diskName) !== false) {
                $locationMatch++;
                echo "\r[$totalLines] Match: $fileId ($diskName) - location1: {$fileCloud->location1}";
            } else {
                $locationMismatch++;
                $mismatchLines[] = [
                    'line' => $line,
                    'fileId' => $fileId,
                    'diskInPath' => $diskName,
                    'location1' => $fileCloud->location1,
                    'server1' => $fileCloud->server1
                ];
                echo "\n[!] MISMATCH: ID=$fileId, Path=$diskName, Location1={$fileCloud->location1}, Server={$fileCloud->server1}";
            }
        } else {
            $notFoundInDb++;
            echo "\n[-] NOT FOUND in DB: ID=$fileId (disk=$diskName)";
        }
    } else {
        echo "\n[?] Invalid format: $line";
    }
    
    if ($totalLines % 100 == 0) {
        echo "\n--- Progress: $totalLines lines, Mismatch: $locationMismatch ---";
    }
}

fclose($handle);

// Ghi file output - chỉ lưu file path để có thể xóa bằng shell command
if (count($mismatchLines) > 0) {
    $output = fopen($outputFile, "w");
    if ($output) {
        foreach ($mismatchLines as $item) {
            fwrite($output, $item['line'] . "\n");
        }
        
        fclose($output);
        echo "\n\n✓ Output written to: $outputFile";
        echo "\n  (One file path per line - ready for shell delete command)";
    } else {
        echo "\n\nERROR: Cannot write output file";
    }
}

// Summary
echo "\n\n╔═══════════════════════════════════════════════════════════════╗";
echo "\n║                         SUMMARY                               ║";
echo "\n╠═══════════════════════════════════════════════════════════════╣";
printf("\n║  Total lines processed    : %-30s ║", number_format($totalLines));
printf("\n║  Found in database        : %-30s ║", number_format($foundInDb));
printf("\n║  Not found in database    : %-30s ║", number_format($notFoundInDb));
printf("\n║  Location match           : %-30s ║", number_format($locationMatch));
printf("\n║  Location MISMATCH        : %-30s ║", number_format($locationMismatch));
echo "\n╚═══════════════════════════════════════════════════════════════╝\n";

if ($locationMismatch > 0) {
    echo "\n⚠ WARNING: Found $locationMismatch files with location mismatch!";
    echo "\n📄 Check output file: $outputFile\n";
}

echo "\n✓ Done!\n\n";

?>
