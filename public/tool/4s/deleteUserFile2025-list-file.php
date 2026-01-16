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


function outputLog($str)
{
    global $date, $nolog, $allowDelete;
    $padReal = "";
    if ($allowDelete)
        $padReal = "_real_delete";
    if (!file_exists("/var/glx/weblog/delete_user_data/"))
        mkdir("/var/glx/weblog/delete_user_data/");
    if ($nolog == 0)
        outputT("/var/glx/weblog/delete_user_data/$date$padReal.log", $str);
    echo("\n$str");
}

function outputError($str)
{
    global $date;
    if (!file_exists("/var/glx/weblog/delete_user_data/"))
        mkdir("/var/glx/weblog/delete_user_data/");
    outputT("/var/glx/weblog/delete_user_data/$date.error.log", $str);
    echo("\n$str");
}

function ol1($str)
{
    outputLog($str);
}

function outFNF($fid)
{
    $date = nowy();
    $file_not_found_list = "/var/glx/weblog/delete_user_data_$date.log";
    output($file_not_found_list, $fid);
}

function deleteRealFile($fc, $fid, $sv)
{
    $locationFull = \App\Components\U4sHelper::getFileFullPath($fid,$fc->location1);
//                        getch("... Before del: $locationFull");
    $retHTTP = SocketSendCMD("deletefile $locationFull", "$sv", 16868);

    ol1(" --- RETURN FROM STORAGE SERVER ($sv Socket): \n    $retHTTP\n");

//                        getch("...after del ");
    if (strstr($retHTTP, "File found, DELETE OK:") != FALSE) {
        ol1(" ===> DELETE OK $locationFull");

        $deleteDateOld = $fc->delete_date_real;
        $fc->delete_date_real = nowyh();
        $fc->addLog("Delete Real Done $deleteDateOld-> $fc->delete_date_real ");
        $fc->save();
        return 1;
    } else {
        if (strstr($retHTTP, "ERROR : File found, DELETE NOT OK:") != FALSE) {
            ol1(" Can not delete file? ");
        } else if (strstr($retHTTP, "ERROR: delelete file not found:") != FALSE) {
            ol1(" ===> DELETE ERROR: File not found $locationFull");
        } else if (strstr($retHTTP, "Location OK, File not found, so may be deleted before:") != FALSE) {

        } else {
            ol1("***STOP: ERROR: NOT FOUND VALID-RETURN FROM REPLICATE SERVER---> STOP");
            //return;
        }
    }
    return 0;
}


echo(" Start ......");

/*
  Xóa các file đã đánh dấu xóa trước đây 1 time

 * Các điều kiện:
 *  + Chú ý TK Funny, 2 tháng mới xóa file!
 *  + Các file không phải là save file
 *  + File mới đánh dấu xóa trong X tuần, thì sẽ ko xóa
 *  - User thường:
 *  + (File đánh xóa bởi user Hoặc user hết hạn ) và (file không có lượt donwload trong 1-2 tuần gần nhất, hoặc file có lượt download < N)
 *  + User chưa hết hạn thì không xóa file
 *  - Uploader:
 *  + File đánh dấu xóa bởi uploader và (file không có lượt donwload trong 1-2 tuần gần nhất, hoặc file có lượt download < N)
 *  + File đánh dấu xóa bởi System và (file không có lượt donwload trong 1-2 tuần gần nhất, hoặc file có lượt download < N)
 *  + Uploader chưa hết hạn nhưng file bị xóa bởi system thì vẫn xóa file
 *  - Một số user đặc biệt thì không bị xóa file (trường keepdataforever = 1)
 *  + Chưa xét trường hợp có link save
 *
 */
$allowDelete = 1;
$deleteFuture = 0;

try {

//Time out for file_get_content:
    $timeout = 36000; //36000 = 10h
    $old = ini_set('default_socket_timeout', $timeout);

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

    $fileIdListPath = "/share/sv18-2026/not_found_may_be_delete.txt";

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

    $cc = 0;
    $foundCloud = 0;
    $stillNotDelete = 0;
    $aliveInDisk = 0;
    $aliveInDiskSize = 0;
    $nErrorCheckFileAlive = 0;
    $nFileUploadTrungCloudId = 0;
    $deleteRealError = $deleteRealDone = 0;
    $notFoundCloud = 0;
    $ttSize = 0;

    foreach ($fileIds as $cloudId) {
        $cc++;
        ol1("\n $cc/$tt. CloudID: $cloudId, FoundCloud: $foundCloud, NotFound: $notFoundCloud");
        ol1(" nErrorCheckFileAlive = $nErrorCheckFileAlive");
        ol1(" aliveInDisk = $aliveInDisk,  aliveInDiskSize = ". ByteSize($aliveInDiskSize));
        ol1(" nFileUploadTrungCloudId = $nFileUploadTrungCloudId");
        ol1(" deleteRealDone = $deleteRealDone, deleteRealError = $deleteRealError");

        // Kiểm tra xem có FileUpload nào đang dùng cloud_id này không
        if(\App\Models\FileUpload::where('cloud_id', $cloudId)->first()){
            $nFileUploadTrungCloudId++;
            ol1(" SKIP: FileUpload still using this cloud_id");
            continue;
        }

        // Tìm FileCloud record
        if($fCloud = \App\Models\FileCloud::where('id', $cloudId)->first()){
            $foundCloud++;
            ol1(" FOUND CLOUD: $fCloud->id, Server: $fCloud->server1, $fCloud->location1 (AliveDB = $stillNotDelete, AliveInDisk = $aliveInDisk / $foundCloud), $fCloud->name");
            echo "\n TSIZE = ". ByteSize($ttSize);

            if($fCloud->location1 != 'sa5'){
                echo "\n NOT SA5";
                continue;
            }

            $ttSize+= $fCloud->size;

//            if(!$fCloud->delete_date_real)
            {
                $stillNotDelete++;

                if($allowDelete)
                if($mSvProxy[$fCloud->server1] ?? '')
                try{
                    if($ret = \App\Components\U4sHelper::checkDownloadAble_($cloudId, $mSvProxy[$fCloud->server1], $fCloud->location1, -1)){
                        $aliveInDisk++;
                        $aliveInDiskSize+=$fCloud->size;
//                        getch("...");


                        if(deleteRealFile($fCloud, $cloudId, $mSvProxy[$fCloud->server1])){
                            $deleteRealDone++;
                        }
                        else
                            $deleteRealError++;
//                        getch("...1");
                    }
                }
                catch (Exception $e){
                    $nErrorCheckFileAlive++;
                    ol1("Exception: ". $e->getMessage());
                }
            }
        } else {
            $notFoundCloud++;
            ol1(" NOT FOUND: FileCloud ID $cloudId does not exist in database");
        }

    }

    ol1("\n\n=== SUMMARY ===");
    ol1("Total IDs processed: $tt");
    ol1("Found in database: $foundCloud");
    ol1("Not found in database: $notFoundCloud");
    ol1("Still in use by FileUpload: $nFileUploadTrungCloudId");
    ol1("Alive in disk: $aliveInDisk (" . ByteSize($aliveInDiskSize) . ")");
    ol1("Delete successful: $deleteRealDone");
    ol1("Delete errors: $deleteRealError");
    ol1("Check errors: $nErrorCheckFileAlive");


} catch (Exception $e) {
    ol1('Exception:'. $e->getMessage() . ' / ' . $e->getTraceAsString());
}
?>
