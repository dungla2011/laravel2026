<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use MongoDB\Client;

$domainX = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";


if (!isCli()) {
    die("Not cli!");
}

$tl = getch("...xoa TmpDownloadSession ?");
if ($tl == 'y')
    \App\Models\TmpDownloadSession::truncate();

$maxId = \App\Models\TmpDownloadSession::max('tmp_old_id');
$maxId1 = $maxId - 10;

$lastInsertCount = $maxId1;
for($loop = 1; $loop < 50; $loop++) {

    $nError = 0;
    try {
        // Kết nối đến MongoDB
        $client = new Client("mongodb://localhost:27017");

        // Chọn database và collection
        $database = $client->selectDatabase('log_4share'); // Thay thế bằng tên database của bạn
        $collection = $database->selectCollection('log_download3'); // Thay thế bằng tên collection của bạn

        // Đọc tất cả tài liệu từ collection
        $documents = $collection->find();
        //Tim voi

        // Hiển thị kết quả
        $done = $cc = 0;
        $timeStart = time();
        $total = $count = $collection->countDocuments();
        foreach ($documents as $doc) {
            try {

                $cc++;
                $speed = $done / (time() - $timeStart + 1);
                echo "\n $cc/$total.(loop = $loop)  $doc->_id / $speed / s, nError = $nError";

                if ($cc <= $lastInsertCount - 100) {
                    echo "\n Skip: $lastInsertCount";
                    continue;
                }

                $lastInsertCount = $cc;
//        "userfile": 1494251,
//    "userdownload": 0,
//    "time": "2019-11-21 15:59:43",
//    "timeUnix": 1574326783,
//    "fid": 5785005,
//    "size": 2304477,
//    "ip": "203.205.32.25",
//    "speed": 109227

                if (\App\Models\TmpDownloadSession::where("tmp_old_id", $doc->_id)->first()) {
                    echo "\n Da ton tai: $doc->_id";
                    $timeStart = time();
                    continue;
                }
                $done++;

                echo "\n Insert now:";

                $obj = new \App\Models\TmpDownloadSession();
                $obj->tmp_old_id = $doc->_id;
                $obj->user_id = $doc->userdownload;
                $obj->user_id_file = $doc->userfile;

                $obj->fid = $doc->fid;
                $obj->file_size = $doc->size;
                $obj->isDlink = $doc->isDlink ?? 0;
                $obj->done_bytes = $doc->size;
                if ($doc->isVip ?? '')
                    $obj->isVip = $doc->isVip;
                $obj->ip_address = $doc->ip;
                $obj->created_at = $doc->timeUnix;
                $obj->save();

                echo json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

            } catch (Exception $e) {
                $nError++;
                // Xử lý lỗi
                echo "\n\n Lỗi1: " . $e->getMessage();
            }
        }

        die("\n DONE....");

    } catch (Exception $e) {
        // Xử lý lỗi
        echo "Lỗi2: " . $e->getMessage();
    }
}

echo "<br/>\n nError = $nError";

