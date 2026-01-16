<?php

/*
 * Update 02.2025
 * Review OK: 10.3.2015
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



/////////////////////////////////

    set_time_limit(0);

    $date = date("Y-m-d-H-i-s", time());

    $nolog = 0;


//$mSv = \App\Models\CloudServer::where("status",1)->get();
// CloudServer : gồm cac hàng có 2 trường  [server , proxy],
    $mSvProxy = \App\Models\CloudServer::pluck('proxy_domain', 'domain')->toArray();


    /*
     *  Đọc danh sách file ID cần xóa từ file text
     *  Mỗi dòng là 1 FileCloud ID
     *
     */

    $fileIdListPath = __DIR__."/list_check_del.txt";

    if (!file_exists($fileIdListPath)) {
        ol1("ERROR: File not found: $fileIdListPath");
        exit;
    }

    // Đọc danh sách file ID từ file
    $fileIds = [];
    $handle = fopen($fileIdListPath, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $fileId = trim($line);
            if ($fileId && is_numeric($fileId)) {
                $fileIds[] = intval($fileId);
            }
        }
        fclose($handle);
    }

    $tt = count($fileIds);
    echo "\n N FileCloud IDs = $tt";
    ol1("Loaded $tt FileCloud IDs from $fileIdListPath");


    \App\Models\FileCloud::find($fid);


?>
