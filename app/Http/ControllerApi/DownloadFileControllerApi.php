<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Http\Controllers\BaseController;
use App\Models\CloudServer;
use App\Models\MyDocument;
use App\Models\TmpDownloadSession;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\User;
use App\Repositories\DownloadLogRepositoryInterface;
use Base\ModelCloudFile;
use Illuminate\Support\Facades\Http;


class DownloadFileControllerApi extends BaseController {
    function apiDownload1k() {

//        die("StopNow");

        if($fid = ($_GET['get_file_info'] ?? '')){
            $id = dfh1b($fid);
//    echo "<br/>\n FID = $id";
            if(is_numeric($id)){
                if($obj =FileUpload::find($id))
                {
                    $sv = CloudServer::getProxyDomainServer($obj->server1);
                    $obj->serverProxy = $sv;

                    if($obj instanceof ModelCloudFile);
//                    $ret = $obj->getIdLink(1);
                    $clid = $obj->cloud_id;
                    if(!$clid){
                        $clid = $obj->id;
                    }
                    $ret = FileCloud::find($clid);
                    if(($ret)){
//                $obj = $ret;
                        $obj->server1 = $ret->server1;
                        $sv = CloudServer::getProxyDomainServer($obj->server1);
                        $obj->serverProxy = $sv;
                        $obj->location1 = $ret->location1;
                        $obj->idlink = $ret->link1;
                    }

//                    $us = User::find($obj->user_id);
//                    dump($us);
                    $mail = User::find($obj->user_id)?->email;

//                    die($obj->user_id . '/' . $mail);
//                    $mail = User ($obj->userid, 'email');
                    $obj->email = $mail;
                    unset($obj->log);
                    die(json_encode($obj));
                }
            }
            die(("Error:NotvalidInfo"));
        }
    }
    function getLinkDownloadDoc() {

        if(!getCurrentUserId()){
            return response()->json([
                'error' =>  'need_login',
                'message' => "Bạn cần đăng nhập để tải file!"
            ]);
        }

        try {
            $request = request();
            // Validate request
            $request->validate([
                'recaptcha_token' => 'required|string'
            ]);

            $recaptchaToken = $request->input('recaptcha_token');
            $secretKey = trim(env('RECAPTCHA_SECRET_KEY_2025'));


            // Log để debug
            \Log::info('reCAPTCHA verification attempt', [
                'token' => $recaptchaToken,
                'secret' => substr($secretKey, 0, 5) . '...' // Log một phần của secret key để debug
            ]);

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaToken,
                'remoteip' => $request->ip() // Thêm IP người dùng
            ]);

            $recaptchaData = $response->json();

            if ($response->successful() &&
                isset($recaptchaData['success']) &&
                $recaptchaData['success'] === true) {
                //doc_id
                $docID = $request->doc_id;
                $linkDl = null;
                if($docx = MyDocument::find($docID)){
                    if($docx->file_list) {
                        $file = \App\Models\FileUpload::find($docx->file_list);
                        if ($file instanceof \App\Models\FileUpload) ;
                        if ($docx instanceof \App\Models\MyDocument) ;

                        if ($file) {
                            $linkDl = $file->getCloudLinkEnc(0);
//                            $imgThumb = $obj->getThumbSmall(800) ?? '/images/no-image.png';
                        }
                    }
                }

                // Không cần kiểm tra action và score quá nghiêm ngặt trong giai đoạn test
                return response()->json([
                    'success' => true,
                    'download_link' => $linkDl
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification failed: ' . json_encode($recaptchaData)
            ], 400);

        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function getLinkDownload()
    {
        $uid = getCurrentUserId();
        if(!$uid){
            return rtJsonApiError("Bạn cần đăng nhập để tải file!");
        }

        $ide = request('ide');
        if(!$ide){
            return rtJsonApiError("Không có mã file!");
        }

        $mm = TmpDownloadSession::getLinkDownload4s($ide, $uid);

        return rtJsonApiDone(['dlink'=>$mm['dlink'], 'sid' => $mm['sid'], 'done_bytes'=>$mm['done_bytes']] ,'Link tải file',1);

    }

}
