<?php

namespace App\Http\Controllers;
function str_replace_first($search, $replace, $subject)
{
    $search = '/' . preg_quote($search, '/') . '/';
    return preg_replace($search, $replace, $subject, 1);
}

use App\Models\OrderItem;
use App\Models\DownloadLog;
use App\Models\FileRefer;
use App\Models\FileUpload;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use LadLib\Common\fslib;

function getUserToFile($em, $fileR)
{
    if ($usNew = User::where('email', $em)->first())
        return $usNew->id;

    $usNew = new User();
    $usNew->email = $em;
    $usNew->username = str_replace(["@", '.', '-'], "_", $em);
    $usNew->password = bcrypt(microtime(1));
    $usNew->save();


    $sid = getSiteIDByDomain();
    $tokenUs = eth1b($sid . '-uid.' . $usNew->id . '-' . microtime() . '-' . rand());
//    $usNew->token_user = $tokenUs;
    $usNew->setRoleUserIfRoleNull();
    $usNew->save();
    return $usNew->id;
}

class DownloadController extends Controller
{

    /**
     * Lấy link download 4s2, từ ID 4s
     * @param $idEncode
     * @return string|void
     * @throws \Exception
     */
    static function getLinkDownload4S2($idEncode, $uid, $ipRemote, $getFileInfoOnly = 0)
    {

        $idOK = dfh1b($idEncode);

        if (!is_numeric($idOK)) {
            loi("Not number ID?");
        }

        $url = 'https://4share.vn/apiDownload1k?get_file_info=' . $idEncode;
        if (!$uid) {
//            $ct = file_get_content_cache($url, null, 10);
            $ct = file_get_contents($url);
            if (!$objFile4S = json_decode($ct))
                loi('not valid fileinfo decode?');
            if ($objFile4S instanceof \Base\ModelCloudFile) ;
            return [
                '',
                $objFile4S->name,
                $objFile4S->size,
                null
            ];
        }

        //Tìm có trong DB chưa:
        $foundFileCache = 0;
        $oldCache = 0;
        if ($fileR = \App\Models\FileRefer::where(['remote_id' => $idOK, 'site' => '4s'])->first()) {
            //Patch old not uid
            if (!$fileR->user_id) {
                $ct = file_get_content_cache($url, null, 4, 1);
                if (!$objFile4S = json_decode($ct))
                    loi('not valid fileinfo decode?');
                $em = $objFile4S->email;

                $uidf = getUserToFile($em, $fileR);

                $fileR->refer_obj = $ct;
                $fileR->user_id = $uidf;
                $fileR->save();
            }


            if (!$fileR->param1) {
                $ct = file_get_content_cache($url, null, 4, 1);
                if (!$objFile4S = json_decode($ct))
                    loi('not valid fileinfo decode1?');
                $fileR->site = '4s';
                $fileR->param1 = $objFile4S->server1;
                if (isset($objFile4S->serverProxy))
                    $fileR->param1 = $objFile4S->serverProxy;
                $fileR->param2 = $objFile4S->location1;
                $fileR->save();
            }


            $foundFileCache = 1;
            if ($fileR->updated_at < nowyh(time() - 3600)) {
                $oldCache = 1;
            }

            $uidf = $fileR->user_id;
//                echo "<br/>\n $oldCache fileR->updated_at = $fileR->updated_at";
        }


        //Nếu tạo quá 10 phút thì cần query lại update location, file name...,
        // nếu file bị di chuyển đi chỗ khác...
        if (!$foundFileCache || $oldCache) {
            //Chưa có thì lấy info bên 4s để insert db

//                echo "<br/>\n URL = $url";

            $ct = file_get_contents_timeout($url, 4);
            if (!$ct) {
                loi("Error call URL get dl info !");
            }

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r(json_decode($ct));
//                echo "</pre>";

            if (!$objFile4S = json_decode($ct)) {
                loi('not valid fileinfo?');
            } else {
                if ($objFile4S instanceof \Base\ModelCloudFile) ;

                if ($oldCache) {
                } else
                    $fileR = new \App\Models\FileRefer();

                $em = $objFile4S->email;

                $uidf = getUserToFile($em, $fileR);

                $fileR->filesize = $objFile4S->file_size;
                $fileR->name = $objFile4S->name;
                $fileR->remote_id = $idOK;
                $fileR->user_id = $uidf;
                $fileR->refer_obj = $ct;

                $fileR->site = '4s';
                $fileR->param1 = $objFile4S->server1;
                if (isset($objFile4S->serverProxy))
                    $fileR->param1 = $objFile4S->serverProxy;
                $fileR->param2 = $objFile4S->location1;
                $fileR->save();
                $foundFileCache = 1;
            }
        }
        if (!$foundFileCache) {
            loi("File khong hợp lệ?");
        }

        //SaveSID vào DownloadLog
//            $uid = getCurrentUserId();
        $mtime = microtime(1);

//            echo "<br/>\n Tạo SID Download... ";

        //Nếu request quá 12h trước, thì tạo mới:
        $limitTime = nowyh(time() - 3600 * 12);
//            $limitTime = nowyh(time() - 10);

        $sidE = null;
        //Tìm file cuối cùng trong log của user
        //Nếu tải 0 thì trả link tải ok
        if (!$getFileInfoOnly)
            if ($dlObj = DownloadLog::where(["user_id" => $uid, 'file_id' => $idOK])
                ->where('count_dl', "<", 2)
                ->where('created_at', '>', $limitTime)->latest('id')->first()) {
                $sidE = $dlObj->sid_encode;
//                die(" Link cũ $sidE");

//            die("\n IDE1 = $dlObj->id ");

            } else {
                //nếu không thì save link mới
                $sidE = eth1b("$uid-$mtime");

//            die("IDE = $sidE ");
                $dlObj = new DownloadLog();
                $dlObj->sid_download = $mtime;
                $dlObj->user_id = $uid;

//            if(isset($uidf))
                $dlObj->user_id_file = $uidf;

                $dlObj->sid_encode = $sidE;
                $dlObj->file_id = $idOK;
                $dlObj->file_refer_id = $fileR->id;
                $dlObj->price_k = $fileR->price_k;
                $dlObj->addLog("add pre download");
                $dlObj->file_id_enc = $idEncode;
                $dlObj->filename = $fileR->name;
                $dlObj->size = $fileR->filesize;
                $dlObj->ip_request = $ipRemote;
                $dlObj->save();
//                echo "<br/>\n Save new DL Log  New";

//                die(" Link mới");
//                die("IDE2 = $sidE ");
//                echo "<br/>\n Save DL Log (Before)";
            }
//            echo "<br/>\n $uid / $mtime / $sidE";
//            $sidAndUid = eth1b("$uid-$sid");

        $objFile4S = json_decode($fileR->refer_obj);

        $linkDl = "#";
        $svx = $fileR->param1;

//        if(!strstr($svx, "a."))
//            $svx = str_replace_first(".", "a.", $svx);

        if ($sidE)
            $linkDl = "https://$svx" . "/6/?fid=$idEncode&sidE=$sidE&fidl=$objFile4S->idlink";

        return [
            $linkDl,
            $fileR->name,
            $fileR->filesize,
            $fileR
        ];

    }

    function replicateFile4s()
    {
        try {
            set_time_limit(3600 * 10);

            function outputLog($str)
            {
                //echo "$str";
                $date = nowy();
                if (!file_exists("/var/glx/weblog/replicate/"))
                    mkdir("/var/glx/weblog/replicate/");
                outputT("/var/glx/weblog/replicate/$date.outfile.log", $str);
            }


            function ol1($str)
            {
                outputLog($str);
            }

            ol1("Start ReplicateFile");

            if (isset($_GET['fid'])) {
                $fid = $_GET['fid'];
                ol1("FID = $fid");
                if (!is_numeric($fid)) {
                    ol1(" not valid number fid? $fid");
                    return;
                }
                $obj = \App\Models\FileCloud::find($fid);
                if (!$obj) {
                    ol1("NOT FOUND File db: $fid? ");
                    return;
                }
                $filepath = $obj->file_path;
                $basename = $obj->name;
                $file = $filepath;
                ol1("Filepath = $filepath, FILE = " . $file);
                //return;
                //ob_clean();
                if (!file_exists($file)) {
                    ol1("ERROR-FILE-NOT-FOUND");
                    return;
                }
                //ol1("Begin readfile, File = $file");
                ob_clean();
                set_time_limit(0);
                $fp = fopen($file, 'rb');
                if (!$fp) {
                    echo "ERROR-Can not openfile: '$file'?";
                    return;
                }
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/octet-stream'); // . getmimetype($file));
                header('Content-Disposition: attachment; filename=' . $basename);
                header('Content-Length: ' . filesize($file));

                $time = time();
                $obj->replicating_time = time();
                $obj->addLog("Update replicate 0");
                $obj->save();

                $cc = 0;
                while (!@feof($fp)) {
                    print(@fread($fp, 1024 * 640));
                    flush();
                    ob_flush();

                    //quá 3 phút se update 1 lần, để cho phép replicate multi thread
                    if(time() - $time > 120){
                        $cc++;
                        $obj->replicating_time = time();
                        $obj->addLog("Update replicating_time $cc");
                        $obj->save();
                        $time = time();
                    }
                }
                //readfile($file);
                fclose($fp);
                ol1("Complete, File = $file");
            }
        } catch (\Exception $e) {
            echo 'Exception:', $e->getMessage();
        }
    }

    public function download_one_file($fid, $name = null)
    {


        //        return;
        return $this->getViewLayout(null, ['fid' => $fid, 'name' => $name]);
    }

    public function download_check_file()
    {
        $check_done = request('check_done');
        $check_done = json_decode($check_done);
        $fid = dfh1b($check_done->fid ?? '');
        $sidE = ($check_done->sidE ?? '');
        if (!$fid || !$sidE)
            loi("Not valid check file dl");
        $m1 = [$uid, $sid] = $this->resolveSid($sidE);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($m1);
//        echo "</pre>";

        ob_clean();
        if ($dlObj = DownloadLog::where(["user_id" => $uid, 'file_id' => $fid, 'sid_download' => $sid])->latest('id')->first()) {

            die(json_encode(["count_dl" => $dlObj->count_dl, 'uid' => $uid, 'last_time' => nowyh(strtotime($dlObj->created_at))]));
        }
    }

    public function resolveSid($sidE)
    {

        $decode = dfh1b($sidE);

        echo "\n<br>resolveSid = $sidE / $decode";

        list($uid, $sidE) = explode("-", $decode);

        echo "<br/>\n";
        echo "\n $uid, $sidE ";


        return [$uid, $sidE];
    }

    public function download_done_file()
    {
        $sidE = request('sidE');
        $ipDone = request('ip');
        $fidE = request('fid');
        $fid = dfh1b($fidE);

        $sidEDecode = dfh1b($sidE);
//        if(!strstr($sidDecode, '-')){
//            loi("Not valid SID DECODE ");
//        }

        list($uid, $sid) = explode("-", $sidEDecode);

        $sid = json_decode($sid);
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($sid);
        echo "</pre>";

        echo "<br/>\n x1 = $sidEDecode / SID = $sid / $ipDone / UID - $uid / FID = $fid";
//        die();


        if ($dlObj = DownloadLog::where(["user_id" => $uid, 'file_id' => $fid, 'sid_download' => $sid])->latest('id')->first()) {

            echo "\n <br> Old, count ++ ";

            if (!$dlObj->ip_download_done)
                $dlObj->ip_download_done = $ipDone;
            $dlObj->time_download_done = nowyh();
            $dlObj->count_dl++;
            $dlObj->addLog("Download Done! $ipDone ");
            $dlObj->save();

            //Tăng countdl cho file refer
            if ($fr = FileRefer::find($dlObj->file_refer_id)) {
                $fr->cound_dl++;
                $fr->save();
            }
        } else {
            echo "<br/>\n NOT FOUND to insert ...";
        }
//            echo "\n  <br> New, save now";
//            $dlObj = new DownloadLog();
//            $dlObj->user_id = $uid;
//            $dlObj->ip_download_done = $ipDone;
//            $dlObj->sid_download = $sid;
//            $dlObj->time_download_done  = nowyh();
//            $dlObj->count_dl = 1;
//            $dlObj->addLog("Download Done! $ipDone ");
//            $dlObj->save();
//        }

        return " <br>DONESID = $sid";
    }


}
