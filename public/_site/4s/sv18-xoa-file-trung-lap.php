<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);



$file = '/share/sv18-2026/all_file_size_sv18-012026.txt';
$logNotFoundInFileUpload = "/share/sv18-2026/not_found_may_be_delete.txt";

echo "Đang xử lý file: $file\n";

if (!file_exists($file)) {
    die("❌ File không tồn tại: $file\n");
}

$totalFiles = 0;
$totalSize = 0;
$files = [];

$nNotFoundDb = 0;

$nNotFoundSize  = 0;
$handle = fopen($file, 'r');
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (empty($line)) continue;

        // Tách theo dấu |
        $parts = explode('|', $line);
        if (count($parts) != 2) continue;

        $filepath = $parts[0];
        $size = intval($parts[1]);

        // Lấy filename từ path (phần cuối cùng)
        $filename = basename($filepath);

        // Lưu vào mảng
        $files[$filename] = $size;

        $totalFiles++;
        $totalSize += $size;

        if(is_numeric($filename)) {
//            if($objFile = \App\Models\FileCloud::find($filename))
            if($objFile = \App\Models\FileUpload::where("cloud_id", $filename)->first())
            {

            }
            else{


                //Append line to file:
                file_put_contents($logNotFoundInFileUpload, $filename . "\n", FILE_APPEND | LOCK_EX);
                $nNotFoundDb++;
                $nNotFoundSize+=$size;
            }
        }
        echo "\n $filename , nFile = $totalFiles . Size = ".formatBytes($totalSize);
        echo "\n NotFound = $nNotFoundDb . SizeNotFound = ".formatBytes($nNotFoundSize);

    }
    fclose($handle);
}


// Hàm format bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}
