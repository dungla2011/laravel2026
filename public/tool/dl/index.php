<?php
//Đoạn code chạy độc lập không dùng index.php
//Mẫu Như sau thì ko bị báo lỗi header, nếu ko thì bị báo lỗi header sent, không rõ tại sao...
use App\Models\FileUpload;
use LadLib\Common\fslib;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Kết thúc đoạn Init Framework Laravel
////////////////////////////////////////////////////////////////////

dl_file_v2();

function dl_file_v2()
{

    if (($fid = ($_GET['fid'] ?? '')) && ($uid = ($_GET['uid'] ?? '')) && ($tokenEnc = ($_GET['tokenEnc'] ?? ''))) {

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $tokenEnc)){
            http_response_code(500);
            die("Not valid infoE!");
        }

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $fid)){
            http_response_code(500);
            die("Not valid Fid!");
        }

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $uid)){
            http_response_code(500);
            die("Not valid Uid!");
        }

        $idOK = dfh1b($fid);
        if(!is_numeric($idOK))
            die("Not valid idl!");
//    echo "<br/>\n $idOK / $fid/ $uid/ $tokenEnc";
        $domainH = fslib::getDomainHome();
        $link = "https://$domainH/tool/gw/fs.php?cmd=check_file_dl_info&fid=$idOK&tokenEnc=$tokenEnc&uid=$uid";
        echo "\n $link";
//    die();
        //Kiểm tra xem tải có valid không:
        [$ret, $code] = fslib::curl_get_contents_timeout($link, 6);
        $objRet = json_decode($ret);
        if($objRet->code != 1){
//        ob_clean();
            if($objRet->code == -1112){
                header("Location: https://$domainH/f/$fid");
                die();
            }
            http_response_code(400);
            die("ErrorDl1 $objRet->code , $objRet->message, Domain:$domainH");
        }

        $location = fslib::getLocationInToken($tokenEnc);

        if($location == 'not_replicated_yet'){
            $clf = FileUpload::getCloudObj($idOK);
            $filePath = $clf->file_path;
        }
        else{
            $filePath = fslib::getFileFullPath($idOK, $location);

            echo "<br/>\n FPATH = $filePath";
            echo "<br/>\n$link ";
            echo "<br/>\n";
            echo "\n RET12 = $ret";
        }

        if(!file_exists($filePath)){
            http_response_code(502);
            die("<br>Not found file glx!");
        }

        echo "<br/>\n FileOK $filePath";

        fslib::dl_file_resumable3($filePath, $objRet->data->name, 1, $idOK, $tokenEnc, $uid);
    }

    if(isset($_GET['test_download_resume_func'])){
        $filePath = "/share/test.download.4s.zip";
        fslib::dl_file_resumable3($filePath, basename($filePath), 1, 1, 1, 1);
        die();
    }

    if(isset($_GET['test_download_session_counter'])){
        $filePath = "/share/test.download.4s.zip";
        fslib::dl_file_resumable3($filePath, basename($filePath), 1,1, 1, 1);
        die();
    }


    die("dl_file_v2 abc123");
}
