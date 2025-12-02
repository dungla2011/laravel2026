<?php

defined("RUN_AUTO_ALL") || define("RUN_AUTO_ALL", 1);

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';


if(!isCli()){
    die("Not cli!!!");
}


$startTime = microtime(true);

// ========== EXECUTE PHP FILES WITH REAL-TIME OUTPUT ==========

function executePhpFileWithProgress($filename, $stepNumber, $totalSteps) {
    echo "\n🚀 BƯỚC {$stepNumber}/{$totalSteps}: {$filename}\n";
    echo "=" . str_repeat("=", 60) . "\n";

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

    // Kiểm tra file có tồn tại
    if (!file_exists($filePath)) {
        echo "❌ File không tồn tại: {$filePath}\n";
        return false;
    }

    // Tạo command
    $phpPath = PHP_BINARY; // Lấy đường dẫn PHP hiện tại
    $command = "\"{$phpPath}\" \"{$filePath}\"";

    echo "🔄 Đang chạy: {$command}\n";
    echo "-" . str_repeat("-", 60) . "\n";

    // Thực thi với real-time output
    $startTime = microtime(true);

    // Method 1: Sử dụng passthru (hiển thị output trực tiếp)
    passthru($command, $returnCode);

    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 2);

    echo "\n" . str_repeat("-", 60) . "\n";

    if ($returnCode === 0) {
        echo "✅ Hoàn thành bước {$stepNumber} trong {$executionTime}s\n";
        return true;
    } else {
        echo "❌ Lỗi bước {$stepNumber} (Exit code: {$returnCode}) trong {$executionTime}s\n";
        return false;
    }
}

// Method 2: Sử dụng proc_open để control output tốt hơn
function executePhpFileAdvanced($filename, $stepNumber, $totalSteps) {
    echo "\n🚀 BƯỚC {$stepNumber}/{$totalSteps}: {$filename}\n";
    echo "=" . str_repeat("=", 60) . "\n";

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

    if (!file_exists($filePath)) {
        echo "❌ File không tồn tại: {$filePath}\n";
        return false;
    }

    $phpPath = PHP_BINARY;
    $command = "\"{$phpPath}\" \"{$filePath}\"";

    echo "🔄 Đang chạy: {$command}\n";
    echo "-" . str_repeat("-", 60) . "\n";

    $startTime = microtime(true);

    // Tạo process với pipes
    $descriptors = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    $process = proc_open($command, $descriptors, $pipes);

    if (!is_resource($process)) {
        echo "❌ Không thể tạo process\n";
        return false;
    }

    // Đóng stdin
    fclose($pipes[0]);

    // Đọc output real-time
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    while (true) {
        $stdout = fgets($pipes[1]);
        $stderr = fgets($pipes[2]);

        if ($stdout !== false) {
            echo $stdout;
        }

        if ($stderr !== false) {
            echo "🔴 " . $stderr;
        }

        // Kiểm tra process còn chạy không
        $status = proc_get_status($process);
        if (!$status['running']) {
            // Đọc output còn lại
            while (!feof($pipes[1])) {
                $line = fgets($pipes[1]);
                if ($line !== false) echo $line;
            }
            while (!feof($pipes[2])) {
                $line = fgets($pipes[2]);
                if ($line !== false) echo "🔴 " . $line;
            }
            break;
        }

        usleep(10000); // 10ms delay
    }

    // Đóng pipes và process
    fclose($pipes[1]);
    fclose($pipes[2]);
    $returnCode = proc_close($process);

    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 2);

    echo "\n" . str_repeat("-", 60) . "\n";

    if ($returnCode === 0) {
        echo "✅ Hoàn thành bước {$stepNumber} trong {$executionTime}s\n";
        return true;
    } else {
        echo "❌ Lỗi bước {$stepNumber} (Exit code: {$returnCode}) trong {$executionTime}s\n";
        return false;
    }
}

// ========== DANH SÁCH CÁC FILE CẦN CHẠY ==========

$migrationFiles = [
    "00-copy-db.php",
    "021-drop-constrain.php",
    "022.mytree-change-all-int-to-bigint.php",
    "033.mytree-add-old-id-some-field-all-table.php",
    "036.change-userid-to-snowflake.php",
    "038-change-userid-all-table.php",
    "041.change-field-to-snowflake-any-table.php",
    "042.change-file-cloud-by-file-uploads.php",
    "043-update-cloud-id-of-file-uploads.php",
    "051-timTruongDungFileId-De-DoiSang-newId-all-table.php",
    "052-convert-old-tree_nodes_xy.php"
];

//Kiem tra xem các script có tồn tại không nếu ko thi die báo lỗi luôn
foreach ($migrationFiles as $filename) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $filename;
    if (!file_exists($filePath)) {
        die("❌ File không tồn tại: {$filePath}\n");
    }
}

// ========== MAIN EXECUTION ==========

echo "🚀 MIGRATION PIPELINE - CHẠY TẤT CẢ BƯỚC\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "📋 Tổng cộng: " . count($migrationFiles) . " bước\n";
echo "⏰ Bắt đầu lúc: " . date('Y-m-d H:i:s') . "\n";

$totalSteps = count($migrationFiles);
$successCount = 0;
$errorCount = 0;
$errorFiles = [];

$pipelineStartTime = microtime(true);

foreach ($migrationFiles as $index => $filename) {
    $stepNumber = $index + 1;

    // Sử dụng method 1 (đơn giản hơn)
    $success = executePhpFileWithProgress($filename, $stepNumber, $totalSteps);

    // Hoặc sử dụng method 2 (advanced)
    // $success = executePhpFileAdvanced($filename, $stepNumber, $totalSteps);

    if ($success) {
        $successCount++;
    } else {
        $errorCount++;
        $errorFiles[] = $filename;

        // Hỏi có muốn tiếp tục không
        echo "\n⚠️  Có lỗi xảy ra. Bạn có muốn tiếp tục? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim(strtolower($line)) !== 'y') {
            echo "❌ Pipeline bị dừng bởi người dùng\n";
            break;
        }
    }

    // Delay giữa các bước
    if ($stepNumber < $totalSteps) {
        echo "\n⏳ Chờ 2 giây trước bước tiếp theo...\n";
        sleep(2);
    }
}

$pipelineEndTime = microtime(true);
$totalExecutionTime = round($pipelineEndTime - $pipelineStartTime, 2);

// ========== KẾT QUẢ TỔNG KẾT ==========

echo "\n🎯 KẾT QUẢ MIGRATION PIPELINE\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "✅ Thành công: {$successCount}/{$totalSteps} bước\n";
echo "❌ Lỗi: {$errorCount}/{$totalSteps} bước\n";
echo "⏱️  Tổng thời gian: {$totalExecutionTime}s\n";
echo "🕐 Hoàn thành lúc: " . date('Y-m-d H:i:s') . "\n";

if (!empty($errorFiles)) {
    echo "\n🔴 CÁC FILE BỊ LỖI:\n";
    foreach ($errorFiles as $file) {
        echo "   - {$file}\n";
    }
}

if ($errorCount === 0) {
    echo "\n🎉 TẤT CẢ MIGRATION ĐÃ HOÀN THÀNH THÀNH CÔNG!\n";
} else {
    echo "\n⚠️  MIGRATION HOÀN THÀNH NHƯNG CÓ LỖI. KIỂM TRA LẠI!\n";
}

echo "\n✅ Pipeline hoàn thành\n";


echo "\n DTIME = " . round(microtime(true) - $startTime, 2) . "s\n";

?>
