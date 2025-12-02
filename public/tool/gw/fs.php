<?php

ob_start();

use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\TmpDownloadSession;

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";

$fid = request('fid');
$tokenEnc = request('tokenEnc');
$uid = $uidE = request('uid');
if(!is_numeric($uidE))
    $uid = qqgetIdFromRand_($uidE);
$done_bytes = request('done_bytes');
$startTime = request('startTime');
$endTime = request('endTime');

function jsonReturnError($mess = '', $data = '')
{
    jsonReturn(-1, $mess, $data);
}
function jsonReturn($code, $message = '', $data = '')
{
    echo json_encode([
        'code' => $code,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}


function update_byte_downloaded($fid, $tokenEnc, $uidE, $uid, $done_bytes, $ip, $startTime,$endTime){

//    die("$fid, $tokenEnc, UIDE= $uidE, UID= $uid, $done_bytes");

    if($fid && $tokenEnc && $uidE && $done_bytes){

        //Tìm cái cuối để cộng vào:
        $dlss = TmpDownloadSession::where("user_id", $uid)->where('fid', $fid)->where('token', $tokenEnc)
//            ->whereRaw('done_bytes < file_size')
            ->latest()
            ->first();
        if($dlss){

            if(!$dlss->user_id_file){
                if($file = FileUpload::find($fid)){
                    $dlss->user_id_file = $file->user_id;
                    $dlss->save();
                }
            }

            if(!$dlss->done_bytes){
                $dlss->done_bytes = 0;
                $dlss->time_begin_update_byte = nowyh();
            }
            $dlss->done_bytes += $done_bytes;
            if(!$dlss->time_begin_update_byte)
                $dlss->time_begin_update_byte = nowyh();

            $dlss->time_end_update_byte = nowyh();

            if(!str_contains($dlss->ip_download_list, $ip)){
                $dlss->ip_download_list .= "$ip,";
//                $dlss->ip_download_list = trim($dlss->ip_download_list, ',');
            }

            if($dlss->done_bytes >= $dlss->file_size * 0.8 ){

                //Update cache
                if(!Cache::get("dlss-$dlss->id")){
                    Cache::put("dlss-$dlss->id", $dlss, 60*60*24);
                    if($file = FileUpload::find($fid)){
                        $file->lastdate_down = nowy();
                        if(!$file->count_download)
                            $file->count_download = 1;
                        else
                            $file->count_download++;
                        $file->addLog("Update count_download $file->count_download / $file->lastdate_down");
                        $file->save();
                    }
                }




            }

            //qua 10s thi moi log:, vì khi đó mới check các IP tải cùng 1 lúc được
            if($endTime - $startTime > 20)
                if(strlen($dlss->logs) < 3900)
                    $dlss->logs.="$ip,$startTime,$endTime|";


            $dlss->save();
            jsonReturn(1, "$dlss->id, Found and + $done_bytes");
        }
        else{
            jsonReturn(-1, "Not found Session info2: $fid / $tokenEnc / $uidE");
        }
    }
    else{
        jsonReturn(-2, "Not valid tokenEnc");

    }
}

function checkSessionDownloadValid($fid, $tokenEnc, $uidE, $uid) {
    if($fid && $tokenEnc && $uidE && $uid) {
        $fileUpload = FileUpload::find($fid);
        if(!$fileUpload){
            http_response_code(505);
            jsonReturnError("Not Found fid!");
        }
//        $fileCL = FileUpload::getCloudObj($fid);

        //Nếu thấy 1 cái cuối cùng chưa full, thì trả lại hợp lệ, được tải tiếp
        if ($dlss = TmpDownloadSession::getLastSessionNotFull($fid, $uid, $tokenEnc)){
            return [$fileUpload, $dlss];
        }
        else
        {
            //Nếu mọi cái đều full, thì redirect
            jsonReturn(-1112, "Not found Session info1: $fid / $tokenEnc / $uidE / Please re-download: <a href='https://4share.vn/f/$fileUpload->link1'>https://4share.vn/f/$fileUpload->link1</a>");
        }
    }
    jsonReturnError("Not valid info");
}


function check_file_dl_info($fid, $tokenEnc, $uidE, $uid)
{
    [$fileCl, $dlss] = checkSessionDownloadValid($fid, $tokenEnc, $uidE, $uid);
    ob_start();
    ob_clean();
    jsonReturn(1, '' , ['size' => $fileCl->file_size, 'name' => $fileCl->name]);
}


//////////////////////////////////////////////////
if(request('cmd') == 'check_file_dl_info'){
    check_file_dl_info($fid, $tokenEnc, $uidE, $uid);
}

if(request('cmd') == 'update_byte_downloaded'){
    $done_bytes = request('sizeDone');
    $ip = request('ip');
    update_byte_downloaded($fid, $tokenEnc, $uidE, $uid, $done_bytes, $ip, $startTime, $endTime);
}
