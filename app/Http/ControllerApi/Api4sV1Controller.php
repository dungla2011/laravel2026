<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Components\U4sHelper;
use App\Http\Controllers\BaseController;
use App\Models\FolderFile;
use App\Models\TmpDownloadSession;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\User;
use App\Repositories\DownloadLogRepositoryInterface;
use LadLib\Common\UrlHelper1;


use Elasticsearch\ClientBuilder;


function outputLog($strlog) {
    global $remoteIP;
    $file = "/var/glx/weblog/api4s.log";
    if (file_exists($file) &&  filesize($file) > 100 * _MB)
        rename($file, $file . '.' . time());
    outputT($file, $remoteIP . " - " . $strlog);
}

function ol3($strlog) {
    outputLog($strlog);
}


class Api4sV1Controller extends BaseController
{

    function ApiV1(){

//        if($_REQUEST['abc123'] ?? ''){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($_POST);
//            echo "</pre>";
//            die();
//        }

        //Cho phép cross domain
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization");
        //Cho phép thêm token này ở header
        header("Access-Control-Allow-Headers: accesstoken01");

        try {
            $cmd = request('cmd');

            $obj = new clsApiToolCmd();

            $mf = get_class_methods($obj);

            foreach ($mf as $funcname) {
                if (substr($funcname, 0, 5) == '_api_') {
                    $obj->$funcname();
//            echo "<br/>\n $funcname";
                }
            }
        }
        catch (\Exception $exception) {
            $error = $exception->getMessage();
            rtErrorApi($error);
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r(request()->all());
//        echo "</pre>";
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($_REQUEST);
//        echo "</pre>";

    }
}

class clsApiToolCmd {

    var $cmd;

    var $file_id;

    var $username;
    var $email;

    var $password;

    var $file_name;
    var $file_name_new;

    var $file_size;

    var $transaction_id_filename_uploaded;

    var $folder_id;

    var $folder_name;
    var $folder_name_new;

    var $folder_id_move_to;

    var $limit;

    var $order_by;
    var $order_type;

    var $md5;
    var $crc32b;
    var $filename;
    var $filesize;
    var $download_count;


    var $page;

    /**
     * @var U4sHelper
     */
    var $userProfile;

    function __construct()
    {

        if(isDebugIp()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($_REQUEST);
//            echo "</pre>";
//            die();
        }

        //Load trước
        $this->loadFromArray($_REQUEST, null, 0, 1);

        $uid = 0;
        //Public user ko cần login với các cmd sau:
        if (isset($_GET['cmd']) &&
            (
                $_GET['cmd'] == 'list_file_in_folder_share'
                || $_GET['cmd'] == 'list_folder_in_folder_share'
                || $_GET['cmd'] == 'search_file_name'
            )
        ) {

        } else {
            if (isset($_GET['cmd']) && $_GET['cmd'] == 'get_token') {
            } else {
                $uid = getCurrentUserId();
                if (!$uid) {
                    $strRet = ("Please input valid access token1!");
                    ol3($strRet);
                    rtErrorApi($strRet);
                }
            }
        }

        if ($uid) {

             $mail = getCurrentUserEmail();


            $timeVip = "2030-01-01";
            if ($timeVip < time()) {
                $strRet = ("Tài khoản '$mail' hết hạn vip: " . nowyh($timeVip));
                ol3($strRet);
                rtErrorApi($strRet);
            }

            $obj = new U4sHelper($uid);

            //Sau đó load user profile cuối cùng
            $this->userProfile = $obj;
        }
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($this);
//        echo "</pre>";
//        die();


        $this->isValidate();

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($this);
//        echo "</pre>";

    }

    function loadFromArray($row, $prefix = "", $debug = 0, $trim = 0) {
//        $this->clearField();

//        if(!is_array($row) || count($row) == 0)
//            return null;
        $foundField = 0;
        if($row && is_array($row) && count($row))
            foreach ($row AS $key=>$value){
                if($prefix)
                    $key = str_replace($prefix, "", $key);

                if(property_exists(get_class($this), $key)){
                    $foundField = 1;
                    if($trim)
                        $this->$key = trim($value);
                    else
                        $this->$key = $value;
                }
            }
        if($foundField)
            return $this;
        return null;
    }

    function isValidate($option = null, $param = null)
    {
//        $db = MysqliDb::getInstance();

        \clsValidate::$arrLastError = array();

        if ($this->username)
            if (!\clsValidate::isUsername($this->username) && !\clsValidate::isEmail($this->username)) {
                \clsValidate::addErrorStr("Not valid username/email ($this->username)");
            }

        if ($this->password)
            if (!\clsValidate::isPassword($this->password)) {
                \clsValidate::addErrorStr("Not valid password ($this->password)");
            }

        if ($this->filesize)
            if (!is_numeric($this->filesize)) {
                \clsValidate::addErrorStr("Not valid filesize number ($this->filesize)");
            }

        if ($this->download_count)
            if (!is_numeric($this->download_count)) {
                \clsValidate::addErrorStr("Not valid download_count number ($this->download_count)");
            }

        if ($this->crc32b)
            if (!is_numeric($this->crc32b)) {
                \clsValidate::addErrorStr("Not valid crc32b number ($this->crc32b)");
            }

        if ($this->filename) {
            if (strlen($this->filename) > 255) {
                \clsValidate::addErrorStr("Not valid filename, max 255 byte ($this->filename) ");
            }
        }

        if ($this->md5) {
            if (strlen($this->md5) != 32) {
                \clsValidate::addErrorStr("Not valid md5 need 32 byte ($this->md5)");
            }
            if (!\clsValidate::isStringAbcAndNumber($this->md5)) {
                \clsValidate::addErrorStr("Not valid md5 string? ($this->md5)");
            }
        }


        if ($this->page)
            if (!is_numeric($this->page) || $this->page < 0) {
                \clsValidate::addErrorStr("Not valid page number ($this->page)");
            }

        if ($this->limit)
            if (!is_numeric($this->limit) || $this->limit < 0) {
                \clsValidate::addErrorStr("Not valid page number ($this->limit)");
            }

        if ($this->file_size && ($this->file_size < 0 || !is_numeric($this->file_size))) {
            \clsValidate::addErrorStr("Not valid filesize : $this->file_size");
        }

        if ($this->file_name && !\clsValidate::isFilename($this->file_name)) {
            \clsValidate::addErrorStr("Not valid filename : $this->file_name");
        }

        if ($this->folder_name && !\clsValidate::isFilename($this->folder_name)) {
            \clsValidate::addErrorStr("Not valid folder_name : $this->folder_name");
        }

        if ($this->file_name_new && !\clsValidate::isFilename($this->file_name_new)) {
            \clsValidate::addErrorStr("Not valid file_name_new : $this->file_name_new");
        }

        if ($this->transaction_id_filename_uploaded && !\clsValidate::isFilename($this->transaction_id_filename_uploaded)) {
            \clsValidate::addErrorStr("Not valid transaction_id_filename_uploaded : $this->transaction_id_filename_uploaded");
        }

        if ($this->folder_name_new && !\clsValidate::isFilename($this->folder_name_new)) {
            \clsValidate::addErrorStr("Not valid folder_name_new : $this->folder_name_new");
        }

        if ($this->file_id) {


            if (strstr($this->file_id, "4share.vn/f/")) {
                $this->file_id = CFileGLX::getIdEncodeFromLink($this->file_id, "4share.vn");

            }
            if(strstr($this->file_id, '/')){
                $this->file_id = explode("/", $this->file_id)[0];
            }

//            die($this->file_id);

//            echo "\n $this->file_id ";
            $this->file_id = urldecode($this->file_id);

//            die(" FID = $this->file_id  ");


            $id = dfh1b($this->file_id);
            if (!is_numeric($id)) {
                \clsValidate::addErrorStr("Not valid file_id: $this->file_id");
            }
        }

        if ($this->folder_id) {
            $id = dfh1b($this->folder_id);
            if (!is_numeric($id)) {
                $strRet = ("Not valid folder_id: $this->folder_id");
                ol3($strRet);
                rtErrorApi($strRet);
            }
        }

        if ($this->folder_id_move_to) {
            $id = dfh1b($this->folder_id_move_to);
            if (!is_numeric($id)) {
                $strRet = ("Not valid folder_id_move_to: $this->folder_id_move_to");
                ol3($strRet);
                rtErrorApi($strRet);
            }
        }

        if (count(\clsValidate::$arrLastError) > 0) {
            $strRet = ("Not valid :" . \clsValidate::errorToString());
            ol3($strRet);
            rtErrorApi($strRet);
        }
        return 1;
    }

    /**
     * @api {post} ?cmd=get_token Get Access token by Code
     * @apiVersion 1.0.1
     * @apiDescription User Access token, nhập username|email/password, lấy access token (hết hạn sau 30 ngày)
     * @apiName GetAccessToken
     * @apiGroup AccessToken
     *
     * @apiParam {String} username Tên đăng nhập hoặc email
     * @apiParam {String} password Mật khẩu
     *
     * @apiSuccess (- (Success)) {json}  ReturnString Trả lại Access Token
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": <AccessTokenString>,
     * }
     * @apiUse Error0
     *
     */
    function _api_get_token()
    {
        if ($this->cmd != 'get_token')
            return 0;


        if($this->email){
            $unameOrEmail = $this->email;
        }
        else{
            $unameOrEmail = $this->username;
        }

//        dumpdebug(serialize($_REQUEST));
        $obj = \checkAuthTool($unameOrEmail, $this->password);
        if($obj instanceof User);
        //$passwordEnc = sha1($password . $obj->id.'salt23873');
        $passwordEnc = sha1($this->password  . $obj->id);
        if ($obj->password != $passwordEnc) {
            $strRet = ("Not valid username/password (2) ");
            ol3($strRet);
            rtErrorApi($strRet);
        }
        if($tk = $obj->getJWTUserToken())
            rtOkApi($tk, "Token ok1!");
        return rtErrorApi("Not token valid!");
    }

    /**
     * @api {get} ?cmd=check_token Check Access token
     * @apiVersion 1.0.1
     * @apiDescription User Access token, lấy token tại địa chỉ web, sau khi đã đăng nhập: <a target='_blank' href='https://4share.vn/member'>https://4share.vn/member</a> (hết hạn sau 30 ngày)
     * @apiName AccessToken
     * @apiGroup AccessToken
     * @apiUse token1
     * @apiSuccess (- (Success)) {json}  ReturnString Trả lại access token
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "Token is valid, email: abc123@gmail.com",
     * }
     * @apiUse Error0
     *
     * @apiExample {php} Example usage in PHP:
     * $url = "https://api.4share.vn/api/v1/?cmd=check_token";
     * $opts = ["http" => ["header" => "accesstoken01: 7b2275696...."]];
     * $context = stream_context_create($opts);
     * $ret = file_get_contents($url, false, $context);
     * echo "Return String: " . $ret;
     * //Return object:
     * $retObj = json_decode($ret);
     * if($retObj->errorNumber)
     * echo "\n Có lỗi xảy ra: $retObj->payload";
     */
    function _api_check_token()
    {
        if ($this->cmd != 'check_token')
            return 0;

////
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($_SERVER);
//        echo "</pre>";
//        return;

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($this->userProfile);
//        echo "</pre>";
//        die();
        $validTo = ($this->userProfile->getVipExpiredDate());

        $uid = getCurrentUserId();
        $user = User::find($uid);
        if(!$user){
            $strRet = ("Not valid user?");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        if ($mail = $user->email) {
            rtOkApi("Token is valid, vip time: " . $validTo . " / $mail");
        } else{
            $strRet = ("Not valid token?");
            ol3($strRet);
            rtErrorApi($strRet);
        }
    }


    /**
     * @api {get} ?cmd=get_user_info Get user information
     * @apiVersion 1.0.1
     * @apiName GetUserInfomation
     * @apiDescription Get user information
     * Thông tin ngày hết hạn VIP, dung lượng đã dùng, dung lượng tải/ngày
     * @apiGroup AccessToken
     * @apiUse token1
     * @apiSuccess (- (Success)) {json}  ReturnString Trả lại Thông tin người dùng
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": {
     * "userid": "cd439805",
     * "register_date": "0000-00-00 00:00:00",
     * "email": "test1@4share.vn",
     * "vip_time": "2025-04-18 21:52:24",
     * "quota_limit_upload_byte": 32212254720000,
     * "quota_limit_download_daily_byte": 128849018880,
     * "downloaded_lastday_byte": 0
     * },
     * "payloadEx": null
     * }
     * @apiUse Error0
     *
     * @apiExample {php} Example usage in PHP:
     * $url = "https://api.4share.vn/api/v1/?cmd=get_user_info";
     * $opts = ["http" => ["header" => "accesstoken01: 7b2275696...."]];
     * $context = stream_context_create($opts);
     * $ret = file_get_contents($url, false, $context);
     * echo "Return String: " . $ret;
     * //Return object:
     * $retObj = json_decode($ret);
     * if($retObj->errorNumber)
     * echo "\n Có lỗi xảy ra: $retObj->payload";
     */
    function _api_get_user_info()
    {
        if ($this->cmd != 'get_user_info')
            return 0;
////
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($_SERVER);
//        echo "</pre>";
//        return;

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($this->userProfile);
//        echo "</pre>";
//        die();
        $validTo = ($this->userProfile->getVipExpiredDate());
        $downToday = $this->userProfile->getDownloadToday();

        $ret = ['userid' => qqgetRandFromId_($this->userProfile->user_id),
            'register_date' => ($this->userProfile->objUserCms->created_at),
            'email' => ($this->userProfile->objUserCms->email),
            'vip_time' => $validTo,
            'quota_limit_upload_byte' => $this->userProfile->objUserCloud->glx_bytes_in_avail,
            'quota_limit_download_daily_byte' => $this->userProfile->objUserCloud->quota_daily_download * _GB,
            'downloaded_lastday_byte' => $downToday,
        ];

        rtOkApi($ret);

    }

    /**
     * @1api {post} ?cmd=upload_done Finish Upload file
     * @1apiVersion 1.0.1
     * @1apiName UploadDone
     * @1apiGroup File and Folder
     * @1apiUse token1
     * @1apiParam {String} transaction_id_filename_uploaded Tên file đã upload thành công qua FTP, nên là tên duy nhất ở một thời điểm upload, xem như transaction_id upload. <br>Mục đích cần upload qua FTP để có thể Resume với file lớn, tránh bị lỗi giữa chừng <br> Có thể đặt transaction_id_filename_uploaded = file_name + timestamp
     * <br> Các file không hoàn thành sẽ bị xóa sau khoảng 24h
     * @1apiParam {String} file_name Tên file
     * @1apiParam {Int} file_size Kích thước file
     * @1apiParam {String} folder_id ID folder muốn upload lên, để trống sẽ là folder gốc
     *
     * @1apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: Link upload thành công
     * @1apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "https://4share.vn/f/2342348afud980",
     * }
     * @1apiUse Error0
     *
     *
     *
     * 2024: chua can lam, vi ko up qua FTP?
     */
    /*
    function _api_upload_done_DEL()
    {
        if ($this->cmd != 'upload_done')
            return 0;

        $basename = $this->file_name;
        $filename_on_server = $this->transaction_id_filename_uploaded;
        $folderID0 = $folderID = $this->folder_id;
        $filesize_org = $this->file_size;

        if ($folderID) {
            $folderID = eth1b($folderID);
            if (!is_numeric($folderID)) {
                loiLg("not valid folder id: $folderID0");
            }
        }

        $userid = getCurrentUserId();

        $this->transaction_id_filename_uploaded = addslashes($this->transaction_id_filename_uploaded);

        if (!$this->file_size) {
            loiLg("need input file_size!");
        }

        if ($ofile = FileUpload::where("user_id", $userid)->where('trans_id_upload','$this->transaction_id_filename_uploaded')->first()) {
            $link = "https://" . MAIN_DOMAIN . "/f/" .$ofile->getLink1();
            rtOkApi("$link", "file uploaded at: $ofile->created_at");
        }

        $objCloud = $this->userProfile->objUserCloud;

        $home_dir_ftp_user = $objCloud->glx_homedir;
        if (!file_exists($home_dir_ftp_user)) {
            mkdir($home_dir_ftp_user, 0755, 1);
        }
        if (!file_exists($home_dir_ftp_user))
            loiLg("Not found home dir: " . $objCloud->glx_homedir);

        if (!$filename_on_server) {
            loiLg("Need input transaction_id_filename_uploaded");
        }

        $filepath = "$home_dir_ftp_user/$filename_on_server";

        $folderStore = "$home_dir_ftp_user";
        if (!file_exists($folderStore))
            mkdir($folderStore);
        if (!file_exists($folderStore))
            loiLg("Can not create folder store?");

        ol3("------ Start upload_done $filepath , $userid , size = $filesize_org /" . ByteSize($filesize_org));
        ol3("FILE On server: $filepath , HomeDir: $home_dir_ftp_user, Debug ");

        if (!file_exists("$filepath") || !is_file($filepath)) {
            loiLg("Upload File not found: $filename_on_server");
        }

        if (!$filesize_org) {
            loiLg("Need input filesize_org");
        }

        if (filesize($filepath) != $filesize_org) {
            loiLg("not valid file size ($filename_on_server): $filesize_org != " . filesize($filepath));
        }

        $arrRep = ['?', '"', "'", ":", "<", ">", "~", "!", '$', '\\', '/', "\n", "\r"];


        if (!$this->file_name) {
            loiLg("need input file name!");
        }

        $ofile = new FileUpload();
        $ofile->user_id = $userid;

        $ofile->name = str_replace($arrRep, "_", $basename);

        $ofile->created_at = nowyh();
        $ofile->size = filesize($filepath);
        $ofile->trans_id_upload = $this->transaction_id_filename_uploaded;

        //$ofile->filepath = $filepath;
        if (!$folderID)
            $folderID = 0;
        $ofile->parent_id = $folderID;

        ol3("Insert DB now:");

        if ($id = $ofile->save()) {
            //$fileStore = $folderStore . "/$id";

            ol3("Insert DB OK? ");
            $fileStore = $folderStore . \ClassUtilGlx::gen_path_file($id);
            $folderPath = dirname($fileStore);

            if (!file_exists($folderPath))
                if (!mkdir($folderPath, 0755, 1)) {
                    loiLg("Can not create upload folder store: $folderPath");
                }

            if (!rename($filepath, $fileStore)) {
                //$ofile->deleteMe();
                loiLg("Can not move file to folder store (2)? $filepath -> $fileStore ");
            }

            if (!file_exists($fileStore)) {
                loiLg("Can not move file to folder store (3)? $filepath -> $fileStore ");
            }

            $ofile->filepath = $fileStore;
            $ofile->link1 = "ms".eth1b($id);
            ol3("Update now DB: ");
            if ($ofile->updateDbMe("id = $id")) {
                ol3("------ End upload_done Org   $filepath ------");
                ol3("------ End upload_done Store $fileStore ------");
                ob_clean();
                $link = "http://" . MAIN_DOMAIN . "/f/".$ofile->getLink1();
                rtOkApi("$link", "upload done!");
                return;
            } else {
                ol3("Update err DB? ");

                loiLg("*** Error: can not update db file info???");
            }
        }

        $strRet = ("not valid info?");
        ol3($strRet);
        rtErrorApi($strRet);
    }
*/

    /**
     * @api {post} ?cmd=list_file_user List file in folder of User
     * @apiVersion 1.0.1
     * @apiDescription List file in user folder
     *
     * @apiName List File in Folder of User
     * @apiGroup File and Folder
     * @apiUse token1
     * @apiParam {String} [folder_id] Id of Folder cần list ra, để trống nếu list toàn bộ file, =0 nếu file ở folder gốc
     * @apiParam {Int} [limit] giới hạn số file list/1 page, default:100, max: 1000
     * @apiParam {Int} [page] số trang: 0,1....
     * //     * @apiParam {Int} filesize  search file have this size (byte)
     * //     * @apiParam {String} filename search file have this name
     * @apiParam {String} [md5]   search file have this md5 string
     * @apiParam {String} [crc32b]   search file have this md5 string
     * @apiParam {String} [order_by]  sắp thứ tự các trường: created_at,filesize,download_count
     * @apiParam {String} [order_type]  sắp thứ tự tăng hay giảm: desc,asc
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json folder list}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "[{'name': 'Ten file1' , 'created_at': '2020-01-02 15:55:51', 'size': 123},
     * {'name': 'Ten file' , 'created_at': '2020-01-02 15:55:51', 'size': 65154,
     * 'delete_date_real': 'ngày xóa file nếu đã xóa',
     * 'download_count': 'số lượt tải'
     * }
     * ]"
     * }
     * @apiUse Error0
     */
    function _api_list_file_user()
    {


        if ($this->cmd != 'list_file_user')
            return 0;

        $fid = null;
        if ($this->folder_id === '')
            $this->folder_id = null;

        if (isset($this->folder_id)) {
            if (!is_numeric($this->folder_id)) {
                $fid = dfh1b($this->folder_id);
                if (!is_numeric($fid)) {
                    $strRet = ("Not valid folderid '$this->folder_id'?");
                    ol3($strRet);
                    rtErrorApi($strRet);
                }
            } else {
                $fid = $this->folder_id;
            }
        }



        $uid = getCurrentUserId();
        $file = new FileUpload();

        $limit = 100;
        if ($this->limit && is_numeric($this->limit))
            $limit = $this->limit;

        if ($limit > 1000)
            $limit = 1000;

        $offset = 0;
        $page = 0;
        if ($this->page && is_numeric($this->page)) {
            $page = $this->page;

        }
        $offset = $page * $limit;

        $padOffset = "";
//        if($this->page || $this->limit)
        $padOffset = "LIMIT $offset, $limit";

//        $db = MysqliDb::getInstance();

        $padSort = null;
        if ($this->order_by) {
            if ($this->order_by == 'created_at') {
                $padSort = " ORDER BY created_at";
            }
            if ($this->order_by == 'download_count') {
                $padSort = " ORDER BY count_down";
            }
            if ($this->order_by == 'filesize') {
                $padSort = " ORDER BY file_size";
            }
//            if($this->order_by == 'filename'){
//                $padSort = " ORDER BY name";
//            }
        }
        if ($padSort)
            if ($this->order_type) {
                if ($this->order_type == 'asc' || $this->order_type == 'desc') {
                    $padSort .= " $this->order_type ";
                }
            }

        if(!$this->order_type)
            $this->order_type = "asc";
        if(!$this->order_by)
            $this->order_by = "id";




        $mf = $file->where(["user_id"=> $uid])->orderBy($this->order_by, $this->order_type)->limit($limit, $offset);
        if (isset($fid))
            $mf->where("parent_id", $fid);
        $mf = $mf->get();

//        $mf->where(function ($query) {
//            $query->where("parent_id", 0)
//                ->orWhereNull("parent_id");
//        });

//
//        die(MysqliDb::getLastQuery1());

        $ret = [];
        foreach ($mf as $f1) {
            $ret[] = ['id' => $f1->getLink1(), 'name' => $f1->name,
                'file_size' => $f1->file_size,
                'type' => 'file',
                'created_at' => $f1->created_at,
//                'delete_date_real' => $fold->delete_date_real,
                'count_down' => $f1->count_down];
        }





        rtOkApi($ret);
    }


    //Lay ca file va folder
    function _api_list_file_and_folder_user()
    {
        if ($this->cmd != 'list_file_and_folder_user')
            return 0;

        //Neu empty, null thi luon lay  = 0, la goc
        //Khac voi truong hop khac, vi day la browser foolder cho Tool
        $fid = 0;
        if ($this->folder_id === '')
            $this->folder_id = 0;

        if (isset($this->folder_id)) {
            if (!is_numeric($this->folder_id)) {
                $fid = dfh1b($this->folder_id);
                if (!is_numeric($fid)) {
                    $strRet = ("Not valid folderid '$this->folder_id'?");
                    ol3($strRet);
                    rtErrorApi($strRet);
                }
            } else {
                $fid = $this->folder_id;
            }
        }

        if($fid){
            if(!$folderCurent = FolderFile::find($fid)){
                $strRet = ("Not valid folderid '$this->folder_id'?");
                ol3($strRet);
                rtErrorApi($strRet);
            }
        }

        $uid = getCurrentUserId();

        $ret = [];
        if($folderCurent ?? '')
        $ret[] = ['id' => $folderCurent->parent_id ? eth1b($folderCurent->parent_id) : 0 , 'name' => '...',
            'size' => 0,
            'type' => 'folder',
            'parent_id' => '-100',
            'created_at' => '',
            'count_download' => ''];

        $folder = new FolderFile();
        $mf = $folder->where(["user_id"=> $uid])->where("parent_id", $fid)->orderBy('name', 'asc');
        $mf = $mf->get();
        foreach ($mf as $f1) {
            if($f1 instanceof FolderFile);
            $ret[] = ['id' => $f1->getLink1(), 'name' => strip_tags($f1->name),
                'size' => 0,
                'type' => 'folder',
//                'parent_id' => $fid,
                'created_at' => nowyh(strtotime($f1->created_at)),
                'count_download' => ''];
        }


        $file = new FileUpload();
        $mf = $file->where(["user_id"=> $uid])->where("parent_id", $fid)->orderBy('name', 'asc');
        $mf = $mf->get();
        foreach ($mf as $f1) {
            if($f1 instanceof FileUpload);
            $ret[] = ['id' => $f1->getLink1(), 'name' => strip_tags($f1->name),
                'size' => $f1->file_size,
                'type' => 'file',
//                'parent_id' => $fid,
                'created_at' => nowyh(strtotime($f1->created_at)),
                'count_download' => $f1->count_download];
        }
        $strNamepath = '';

        if($folderCurent ?? '')
        if($folderCurent instanceof FolderFile)
        if($mm = $folderCurent->getListParentId(1,1)){
            //Lay ra name list, path string
            foreach ($mm as $f1) {
                $strNamepath =  $f1->name ."/" . $strNamepath;
            }
        }

        if($folderCurent ?? '') {
            rtOkApi($ret,  $strNamepath);
        }
        rtOkApi($ret, $strNamepath);
    }

    /**
     * @api {post} ?cmd=list_folder_in_folder_user List folder in folder of User
     * @apiVersion 1.0.1
     * @apiName List folder in folder of User
     * @apiGroup File and Folder
     * @apiUse token1
     * @apiParam {String} [folder_id] Id of Folder cần list ra, để trống nếu list toàn bộ folder, folder_id=0 nếu folder ở folder gốc
     * @apiParam {Int} [limit] giới hạn số folder list/1 page, default:100, max: 1000
     * @apiParam {Int} [page] số trang: 0,1....
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json folder list}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "[{'name': 'Ten folder1' , 'created_at': '2020-01-02 15:55:51'}, {'name': 'Ten folder2' , 'created_at': '2020-01-02 15:55:51'}]"
     * }
     * @apiUse Error0
     */
    function _api_list_folder_in_folder_user()
    {

        if ($this->cmd != 'list_folder_in_folder_user')
            return 0;

        $fid = null;
        if ($this->folder_id === '')
            $this->folder_id = null;

        if (isset($this->folder_id)) {
            if (!is_numeric($this->folder_id)) {
                $fid = dfh1b($this->folder_id);
                if (!is_numeric($fid)) {
                    $strRet = ("Not valid folderid '$this->folder_id'?");
                    ol3($strRet);
                    rtErrorApi($strRet);
                }
            } else {
                $fid = $this->folder_id;
            }
        }

        $uid = getCurrentUserId();
//        $file = new FileUpload();
        $file = new FolderFile();

        $limit = 100;
        if ($this->limit && is_numeric($this->limit))
            $limit = $this->limit;

        if ($limit > 500)
            $limit = 500;

        $offset = 0;
        $page = 0;
        if ($this->page && is_numeric($this->page)) {
            $page = $this->page;

        }
        $offset = $page * $limit;

        $padOffset = "";
//        if($this->page || $this->limit)
        $padOffset = "LIMIT $offset, $limit";

        $mf = $file->where("user_id", $uid)->orderBy('name', 'asc')->skip($offset)->take($limit);
        if (isset($fid))
            $mf =  $mf->where("parent_id", $fid);
        $mf = $mf->get();

//
//        die(MysqliDb::getLastQuery1());

        $ret = [];
        foreach ($mf as $fold) {
            $ret[] = ['id' => $fold->getLink1(),
                'type' => 'folder',
                'name' => $fold->name, 'created_at' => $fold->created_at];
        }
        rtOkApi($ret);
    }


    /**
     * @api {post} ?cmd=list_file_in_folder_share List file in shared folder
     * @apiVersion 1.0.1
     * @apiDescription List files in shared folder
     * @apiName List files in shared folder
     * @apiGroup File and Folder
     * @apiParam {String} [folder_id] Id of Folder cần list ra
     * @apiParam {Int} [limit] giới hạn số file list/1 page, default:100, max: 1000
     * @apiParam {Int} [page] số trang: 0,1....
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json folder list}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "[{'name': 'Ten file1' , 'created_at': '2020-01-02 15:55:51', 'size': 123},
     * {'name': 'Ten file' , 'created_at': '2020-01-02 15:55:51', 'size': 65154,
     * 'delete_date_real': 'ngày xóa file nếu đã xóa',
     * 'download_count': 'số lượt tải'
     * }
     * ]"
     * }
     * @apiUse Error0
     */
    function _api_list_file_in_folder_share()
    {


        $enableBothFolderAndFile = 1;

        if ($this->cmd != 'list_file_in_folder_share')
            return 0;

        $fid = null;
        if (!$this->folder_id){
            $strRet = ("Not valid folder_id");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        if ($this->folder_id) {
            $fid = dfh1b($this->folder_id);
            if (!is_numeric($fid)) {
                $strRet = ("Not valid folderid '$this->folder_id'?");
                ol3($strRet);
                rtErrorApi($strRet);
            }
        }


        $file = new FileUpload();

        $limit = 100;
        if ($this->limit && is_numeric($this->limit))
            $limit = $this->limit;

        if ($limit > 1000)
            $limit = 1000;

        $offset = 0;
        $page = 0;
        if ($this->page && is_numeric($this->page)) {
            $page = $this->page;
        }
        $offset = $page * $limit;


        /////////////Nếu enable cả folder thì:
        $mfold = null;

        $fold = new FolderFile();
        $totalFolderCount = $fold->where("parent_id", $fid)->count();
        if ($enableBothFolderAndFile)
            if ($totalFolderCount > 0) {
                $mRetPaginator = \clsPaginator::createArrayLimitOffset2TableToQueryPaginator($totalFolderCount, $limit, $page + 1);
                $limit1 = $mRetPaginator['limit1'];
                $offset1 = $mRetPaginator['offset1'];
                $limit2 = $mRetPaginator['limit2'];
                $offset2 = $mRetPaginator['offset2'];

                if (isset($_GET['debug1'])) {
                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    print_r($mRetPaginator);
                    echo "</pre>";
//                die("ABC = ");
                }
                if ($offset1 >= 0) {
                    $mfold = FolderFile::where("parent_id",$fid)->orderBy('name', 'asc')->skip($offset1)->take($limit1)->get();
                }

                $offset = $offset2;
                $limit = $limit2;

//            if(isset($_GET['debug1'])){
//
//               echo "<br/>\n OF1 = $offset, $limit";
//            }
            }


        $padOffset = "";
//        if($this->page || $this->limit)
        $padOffset = "LIMIT $offset, $limit";

//        $db = MysqliDb::getInstance();

        $padSort = "ORDER BY name";
        if ($this->order_by) {
            if ($this->order_by == 'created_at') {
                $padSort = " ORDER BY created_at";
            }
            if ($this->order_by == 'filesize') {
                $padSort = " ORDER BY size";
            }
//            if($this->order_by == 'filename'){
//                $padSort = " ORDER BY name";
//            }
        }
        if ($padSort)
            if ($this->order_type) {
                if ($this->order_type == 'asc' || $this->order_type == 'desc') {
                    $padSort .= " $this->order_type ";
                }
            }


        $totalFileThisPage = $mf = null;
        if ($offset != -1) {
            $mf = $file->where("parent_id",$fid)->orderBy('name','asc')->skip($offset)->take($limit)->get();

            if (isset($_GET['debug1'])) {
//                echo "<br/>\n OF11 =  $fid $padSort $padOffset ";
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($mf);
//                echo "</pre>";
//                die();
            }

            if ($mf)
                $totalFileThisPage = count($mf);
        }


        //        die(MysqliDb::getLastQuery1());
        $format_as_VietMediaF = 0;
        if (isset($_GET['format_as']) && ($_GET['format_as'] == 'vietmediaf1' || $_GET['format_as'] == 'vietmediaf2')) {
//            ob_clean();
//            if($mf)
//            foreach ($mf AS $filex){
//                if($_GET['format_as'] == 'vietmediaf1')
//                    echo "*$filex->name|https://4share.vn/f/$filex->link1<br/>";
//                else
//                    echo "*$filex->name|https://4share.vn/f/$filex->link1\r\n";
//            }
//            die();
            $format_as_VietMediaF = 1;
            ob_clean();
        }

        $ret = [];

        $ttFileThisPage = $ttFoldThisPage = 0;
        if ($mf)
            $ttFileThisPage = count($mf);
        if ($mfold)
            $ttFoldThisPage = count($mfold);
        if ($mfold && is_array($mfold) && count($mfold)) {
            foreach ($mfold as $filex) {

                $link1 = $filex->getLink1();
                if ($format_as_VietMediaF) {
                    echo "*@$filex->name|https://4share.vn/d/$link1\n";
                }

                $ret[] = ['id' => $link1, 'name' => $filex->name,
                    'link' => "https://4share.vn/d/$link1",
                    'type' => 'folder',
                    'size' => null,
                    'created_at' => $filex->created_at
                ];
            }
        }

        if ($mf)
            foreach ($mf as $filex) {

                $link1 = $filex->getLink1();
                if ($format_as_VietMediaF) {
                    echo "*$filex->name|https://4share.vn/f/$link1\n";
                }
                $ret[] = ['id' => $link1, 'name' => $filex->name,
                    'link' => "https://4share.vn/f/$link1",
                    'type' => 'file',
                    'size' => $filex->file_size,
                    'created_at' => $filex->created_at,
//                    'delete_date_real' => $filex->delete_date_real
                ];
            }

        if ($format_as_VietMediaF) {
            $page++;
            $nextUrl = UrlHelper1::setUrlParam(UrlHelper1::getFullUrl(), 'page', $page);
            $nextUrl = urlencode($nextUrl);
            if ((!$mf && !$mfold) || ($ttFileThisPage + $ttFoldThisPage) < $limit) {
                echo "";
            } else
                echo "*@[COLOR yellow]Nextpage[/COLOR]|$nextUrl";


            return;
        }
        if (isset($_GET['debug1'])) {
//            echo "<br/>\n OF1 = $offset, $limit";
//            die();
        }

        rtOkApi($ret, "List all file in this folder, Limit=$limit, Page=$page, ( totalFileThisPage = $totalFileThisPage, ttFoldThisPage = $ttFoldThisPage ), totalFolderCount=$totalFolderCount ");
    }

    /**
     * @api {post} ?cmd=list_folder_in_folder_share List folder in shared folder
     * @apiVersion 1.0.1
     * @apiName List folder in shared folder
     * @apiGroup File and Folder
     * @apiParam {String} [folder_id] Id of Folder cần list
     * @apiParam {Int} [limit] giới hạn số folder list/1 page, default:100, max: 1000
     * @apiParam {Int} [page] số trang: 0,1....
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json folder list}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "[{'name': 'Ten folder1' , 'created_at': '2020-01-02 15:55:51'}, {'name': 'Ten folder2' , 'created_at': '2020-01-02 15:55:51'}]"
     * }
     * @apiUse Error0
     */
    function _api_list_folder_in_folder_share()
    {

        if ($this->cmd != 'list_folder_in_folder_share')
            return 0;

        $fid = null;
        if ($this->folder_id === '')
            $this->folder_id = null;

        if (!isset($this->folder_id)){
            $strRet = ("Please enter folder_id");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        $fid = dfh1b($this->folder_id);
        if (!is_numeric($fid)) {
            $strRet = ("Not valid folderid '$this->folder_id'?");
            ol3($strRet);
            rtErrorApi($strRet);
        }

//        $file = new FileUpload();
        $file = new FolderFile();

        $limit = 100;
        if ($this->limit && is_numeric($this->limit))
            $limit = $this->limit;

        if ($limit > 500)
            $limit = 500;

        $offset = 0;
        $page = 0;
        if ($this->page && is_numeric($this->page)) {
            $page = $this->page;

        }
        $offset = $page * $limit;

        $padOffset = "";
//        if($this->page || $this->limit)
        $padOffset = "LIMIT $offset, $limit";

        $mf = $file->where("parent_id",$fid)->skip($offset)->take($limit)->get();

//        die(MysqliDb::getLastQuery1());

        $ret = [];
        foreach ($mf as $fold) {
            $link1 = $fold->getLink1();
            $ret[] = ['id' => $link1,
                'type' => 'folder',
                'link' => "https://4share.vn/d/$link1", 'name' => $fold->name, 'created_at' => $fold->created_at];
        }
        rtOkApi($ret, "List all folder in this folder, Limit=$limit, Page=$page ");
    }

    function _api_rename_folder()
    {
        if ($this->cmd != 'rename_folder')
            return 0;

    }

    function _api_rename_file()
    {
        if ($this->cmd != 'rename_file')
            return 0;

    }

    function _api_delete_file()
    {
        if ($this->cmd != 'delete_file')
            return 0;

    }

    function _api_delete_folder()
    {
        if ($this->cmd != 'delete_folder')
            return 0;

    }

    /**
     * @api {post} ?cmd=get_file_info Get file infomation
     * @apiVersion 1.0.1
     * @apiName Get File Info
     * @apiGroup File and Folder
     * @apiUse token1
     * @apiParam {String} file_id Id of File
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json file info, nếu là file của tài khoản}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "{'name': 'Ten file','size': 123 , 'created_at': '2020-01-02 15:55:51'}"
     * }
     * @apiUse Error0
     */
    function _api_get_file_info()
    {


        if(isDebugIp()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($_REQUEST);
//            echo "</pre>";
//            die("1111111 $this->cmd / $this->file_id");
        }
        if ($this->cmd != 'get_file_info')
            return 0;

        if (!$this->file_id) {
            $strRet = ("Need input file_id");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        $fid = dfh1b($this->file_id);


        $file = new FileUpload();
        if (!($file = $file->find($fid))) {
            $strRet = ("Not found file id: $this->file_id");
            ol3($strRet);
            rtErrorApi($strRet, null, -111);
        }


        $parentId = '';
        $parentName = '';
        if ($file->parent) {
            $fold = new FolderFile();
            $fold = $fold->find($file->parent_id);
            $parentId = $fold->getLink1();
            $parentName = $fold->name;
        }

        if($file instanceof FileUpload);

        $ret = ['id' => $file->getLink1(), 'name' => $file->name, 'size' => $file->file_size, 'created_at' => $file->created_at,
            'full_link' => "https://4share.vn/f/".$file->getLink1(),
            'parentId' => $parentId,
            'parentName' => $parentName,
            'delete_date' => $file->delete_date];

        if ($file->user_id == getCurrentUserId())
            $ret['download_count'] = $file->count_down;

        rtOkApi($ret);

//        rtOkApi(['id'=>$file->link1,'name'=>$file->name,
//            'size'=>$file->size, 'download_count'=> null, 'created_at'=>$file->created_at,
//            'full_link' => "https://4share.vn/f/$file->link1/$file->name",
//            'delete_date'=>$file->delete_date]);
    }

    /**
     * @api {get} ?cmd=search_file_name&exactly=1&search_string=... Search File by FileName
     * @apiDescription Search File by FileName
     * @apiVersion 1.0.1
     * @apiName Tìm Kiếm file
     * @apiGroup File and Folder
     * @apiParam {String} search_string Chuỗi cần tìm kiếm
     * @apiParam {String} ext Đuôi file (mkv, ts, zip, rar...), có thể thêm nhiều đuôi, cách nhau bởi dấu phẩy (,)
     * @apiParam {String} sort_by (sort_by=new hoặc sort_by=old)  : sắp theo thứ tự mới nhất, cũ nhất
     * @apiParam {Int} from_size Kích thước file tối thiểu (MegaByte)
     * @apiParam {Int} exactly 1: Tìm chính xác tên, 0: Tìm gần đúng (tìm kiếm fuzzy)
     * @apiParam {Int} page Trang hiện tại
     * @apiParam {Int} limit Giới hạn hiển thị số file/1 trang
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: link list info}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": {
     * "search_string": "avatar",
     * "found": 111,
     * "current_page": 1,
     * "limit": "2",
     * "total_page": 56,
     * "links": [
     * {
     * "name": "Avatar.rar",
     * "size": 778194599,
     * "delete_date": null,
     * "created_at": "2018-05-18 23:10:35",
     * "link": "https://4share.vn/f/390c0e0000080f01/Avatar.rar",
     * "file_id": "390c0e0000080f01"
     * },
     * {
     * "name": "Capturing Avatar.mkv",
     * "size": 2672956939,
     * "delete_date": null,
     * "created_at": "2013-07-09 11:22:57",
     * "link": "https://4share.vn/f/3a0c080a0e0c0b08/Capturing Avatar.mkv",
     * "file_id": "3a0c080a0e0c0b08"
     * }
     * ]
     * },
     * "payloadEx": null
     * }
     * @apiUse Error0
     */
    function _api_search_file_name()
    {
        if ($this->cmd != 'search_file_name')
            return 0;
        $params = request()->all();
        $searchString = '';
        if (isset($params['search_string'])) {
            $params['search_string'] = substr($params['search_string'], 0, 100);
            $searchString = $params['search_string'];
            $searchString = trim(urldecode($searchString));
        }
        $searchString = str_replace(['`', '!', '@', '^', ",", '.', ':', "_", "#", "-", "(", ")", "{", "}", "+", "*"], ' ', $searchString);

        $arrBlackWord = U4sHelper::getBlackWordList();
        if (in_array($searchString, $arrBlackWord)) {
            return null;
        }


        if (isset($params['limit']) && is_numeric($params['limit']))
            $limit = $params['limit'];
        else
            $limit = $params['limit'] = 20;

        $cPage = 1;
        if (isset($params['page']))
            $cPage = $params['page'];
        if(!$cPage || $cPage <= 0 || !is_numeric($cPage))
            $cPage = $params['page'] = 1;


        ///
        $obj = new FileUpload();

        $dbName = $obj->getElasticDbName();
        $prs = getParamForElastic($searchString, $params, $dbName);

        try{
            $response = searchElastic($prs, $dbName);
        } catch (\Throwable $e) { // For PHP 7
            $strRet = ($e->getMessage());
            rtErrorApi("ErrorEl2: " . $strRet);
            return;
        } catch (\Exception $exception) {
            $strRet = ($exception->getMessage());
            rtErrorApi("ErrorEl1: " . $strRet);
            return;
        }

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($response);
//echo "</pre>";
        $total = 0;
        $ret = [];
        if (!$response) {
            $strRet = ("Not found result");
            ol3($strRet);
            rtErrorApi($strRet);
        } else {
            if (isset($response['hits']['hits'])) {
                $total = $response['hits']['total']['value'];
            }
            foreach ($response['hits']['hits'] as $hit) {
                $name = $hit['_source']['name'];
                if (isset($hit['_source']['summary']))
                    $sum = $hit['_source']['summary'];
                //$cont = $hit['_source']['content'];
                $id = $hit['_id'];
                //echo "<br/>\n $id . $name  ";
                if($obj0 = FileUpload::find($id))
                {
                    $obj = $obj0->toArray();
                    $ret[] = (object) $obj;
                }
            }
        }

        $nPage = ceil($total / $limit);

        ////////////////////////////// Show ket qua //////////////////////////////
        $format_as_VietMediaF = 0;
        if (isset($_GET['format_as']) && ($_GET['format_as'] == 'vietmediaf1' || $_GET['format_as'] == 'vietmediaf2')){
            ob_clean();
            $format_as_VietMediaF = 1;
        }

        $mRet = [];
        $cc = 0;
        foreach ($ret as $obj) {
            $objDb = FileUpload::find($obj->id);
            $link = "https://4share.vn/f/" . $objDb->getLink1() . "";
            //echo "$link\n";
            $obj->link = $link;
            $link1 = $obj->file_id = $objDb->getLink1();

            if ($format_as_VietMediaF) {
                $size = ByteSize($obj->file_size);
                echo "*[COLOR yellow][$size][/COLOR]$obj->name|https://4share.vn/f/$link1\n";
            }

            resetKeepFieldObj($obj, ['name', 'file_size', 'created_at', 'link1']);
//            $obj->reset1(['name', 'size', 'created_at', 'delete_date', 'link', 'file_id']);
            $mRet[$cc] = $obj;


            $cc++;
        }

        if ($format_as_VietMediaF) {
            $ttFileThisPage = 0;
            if ($ret)
                $ttFileThisPage = count($ret);
            $nextUrl = UrlHelper1::setUrlParam(UrlHelper1::getFullUrl(), 'page', $cPage + 1);
            $nextUrl = urlencode($nextUrl);
            if ((!$ret) || ($ttFileThisPage) < $limit) {
                echo "";
            } else
                echo "*@[COLOR yellow]Trang tiếp[/COLOR]|$nextUrl";
            return;
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mRet);
//        echo "</pre>";
//        return;
//        ob_clean();
//        ol3(json_encode($mRet));
        rtOkApi(['search_string' => $searchString, 'found' => $total, 'current_page' => $cPage, 'limit' => $limit, 'total_page' => $nPage, 'links' => $mRet]);
    }

    /**
     * @api {post} ?cmd=get_download_link Get link to download file
     * @apiDescription Link download sẽ tồn tại thời gian 12h, một tải khoản tải link tải từ nhiều IP cùng 1 thời điểm sẽ bị khóa tài khoản
     * @apiVersion 1.0.1
     * @apiName Get Link To download File
     * @apiGroup File and Folder
     * @apiUse token1
     * @apiParam {String} file_id Id of File or Link of file
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: link to download}
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "<link to download>"
     * }
     * @apiUse Error0
     */
    function _api_get_download_link()
    {
        if ($this->cmd != 'get_download_link')
            return 0;

        $remoteIP = ClassNetwork::getRemoteAddress();

        if (!getCurrentUserId()) {
            $strRet = ("Need login Vip!");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        //Kiểm tra Quota user:
        $userid = getCurrentUserId();

        if (!$this->file_id) {
            $strRet = ("Need input file_id");
            ol3($strRet);
            rtErrorApi($strRet);
        }

        //Todo: kiem tra user con duoc phep download khong:
        $u4s = new U4sHelper($userid);

        $ret = TmpDownloadSession::getLinkDownload4s($this->file_id, $userid);

        $DLINK = $ret['dlink'];

//        ol3("download_link return, server dl = " . $DLINK);
//        ol3("DEBUG x = ". serialize($file));
        rtOkApi(['download_link' => $DLINK], "*** Check user valid...");
    }


    function _api_move_file()
    {
        if ($this->cmd != 'move_file')
            return 0;
    }

    function _api_move_folder()
    {
        if ($this->cmd != 'move_folder')
            return 0;


    }


    /**
     * @api {post} https://api.4share.vn/tool/4s/upload-free/ Upload File - Free user
     * @apiVersion 1.0.1
     * @apiDescription Upload free file, 1GB/1 file, 10 file/giờ/1 IP, file quá 5 ngày không có download sẽ xóa
     * @apiName Upload File - Free user
     * @apiGroup File and Folder
     * @apiParam {String} myfile File Data, kích thước tối đa 1GB, upload 10 file/giờ/ 1 IP
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json file info}
     * @apiSampleRequest off
     * @apiSuccessExample {json} Sample Success:
     *
     * {
     * "errorNumber": 0,
     * "payload": "{'filename': 'Ten file','id_cloud': 123}"
     * }
     * @apiUse Error0
     *
     * @apiExample {php} Example usage in PHP:
     * $fileUp = "d:/file1.jpg";
     * if (function_exists('curl_file_create')) { // php 5.5+
     * $cFile = curl_file_create($fileUp);
     * } else {
     * $cFile = '@' . realpath($fileUp);
     * }
     * $post = array('myfile'=> $cFile);
     * $ch = curl_init();
     * curl_setopt($ch, CURLOPT_URL,$url);
     * curl_setopt($ch, CURLOPT_POST,1);
     * curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     * $result=curl_exec ($ch);
     * curl_close ($ch);
     *
     * echo "\n Upload result: ";
     * print_r($result);
     */


    /**
     * @api {post} https://api.4share.vn/a_p_i/member-cloud/upload Upload File - VIP User
     * @apiVersion 1.0.1
     * @apiDescription Upload VIP, 100GB/1 file
     * @apiName Upload File - VIP User
     * @apiGroup File and Folder
     * @apiUse token1
     * @apiParam {String} myfile File Data, kích thước file tối đa 100GB
     * @apiParam {String} folder_id_link ID folder của user, nếu sai hoặc không có, sẽ upload vào thư mục gốc của user
     * @apiSuccess (- (Success)) {json}  ReturnJson errorNumber: =0: ; payload: {json file info}
     * @apiSampleRequest off
     * @apiSuccessExample {json} Sample Success:
     * {
     * "errorNumber": 0,
     * "payload": "{'filename': 'Ten file','id_cloud': 123}"
     * }
     * @apiUse Error0
     *
     * @apiExample {php} Example usage in PHP:
     * $fileUp = "d:/file1.jpg";
     * if (function_exists('curl_file_create')) { // php 5.5+
     * $cFile = curl_file_create($fileUp);
     * } else {
     * $cFile = '@' . realpath($fileUp);
     * }
     *
     * $url = "https://api.4share.vn/a_p_i/member-cloud/upload";
     *
     * $post = ['myfile'=> $cFile, 'folder_id_link'=> 0 ];
     * $ch = curl_init();
     * curl_setopt($ch, CURLOPT_URL,$url);
     * curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: currentUser=<User API Token>"));
     * curl_setopt($ch, CURLOPT_POST,1);
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     * curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
     * $result=curl_exec ($ch);
     * curl_close ($ch);
     * echo "\n Upload result: ";
     * print_r($result);
     */


    /**
     * @api limit Limit API Speed
     * @apiVersion 1.0.1
     * @apiSampleRequest off
     * @apiName Limit speed
     * @apiDescription API giới hạn tốc độ: 1 request/1 giây, 60 request/1 phút
     * @apiGroup Limit, Quota
     */

}

class ClassApi {

    //errorNumber
    //payload
    public static $isLogined = false;
    public static $currentUser = null;
    public static $currentGidUsingApi = null; //GID đang được sử dụng, user có thể thuộc nhiều GID, list nằm trong user->gid_extra

    public static function returnApi($errorNumber, $data, $dataEx = null){
        //return json_encode(array("errorNumber"=>$errorNumber, "data" => $data));
        return json_encode(array("errorNumber"=>$errorNumber, "payload" => $data, 'payloadEx'=>$dataEx), JSON_PRETTY_PRINT);
    }

    public static function returnApiOkAndDie($data , $dataEx = null, $returnArray = 0){
        //return json_encode(array("errorNumber"=>$errorNumber, "data" => $data));
        $ret = array("errorNumber"=>0, "payload" => $data, 'payloadEx'=>$dataEx);
        if($returnArray)
            return $ret;
        @ob_clean();
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        die(json_encode($ret, JSON_PRETTY_PRINT));
    }

    public static function returnApiErrorAndDie($data, $dataEx = null, $errorNum = -100, $errorCode = 200, $returnArray = 0){

//        if(ClassRoute::$isWeb)
//            $data = $data."\n<br/><br/><i style='font-size: smaller; font-style: italic'>[ $fullAct ]</i>";
//        else
//            $data = $data."\n[$fullAct]";

        //return json_encode(array("errorNumber"=>$errorNumber, "data" => $data));
        @ob_clean();
        header("HTTP/1.1 $errorCode OK");
        header('Content-Type: application/json');
        die(json_encode(array("errorNumber"=>$errorNum, "payload" => $data, 'payloadEx'=>"[...]\n" . $dataEx), JSON_PRETTY_PRINT));
    }

    public static function returnApiDie($errorNumber, $data, $dataEx = null){
        //return json_encode(array("errorNumber"=>$errorNumber, "data" => $data));
        @ob_clean();
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        die(json_encode(array("errorNumber"=>$errorNumber, "payload" => $data, 'payloadEx'=>$dataEx), JSON_PRETTY_PRINT));
    }

    public static function returnApiErrorAuthAndDie($data = "Lỗi xác thực", $dataEx = null){
        //return json_encode(array("errorNumber"=>$errorNumber, "data" => $data));
//        $fullAct = ClassRoute::getFullAction();
//        if(ClassRoute::$isWeb)
//            $data = $data."\n<br/><br/><i style='font-size: smaller; font-style: italic'>[ $fullAct ]</i>";
//        else
//            $data = $data."\n[$fullAct]";
        @ob_clean();
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        die(json_encode(array("errorNumber"=>-1, "payload" => $data, 'payloadEx'=>"[...]\n" . $dataEx), JSON_PRETTY_PRINT));
    }

    public static function isLogined(){
        return ClassApi::$isLogined;
    }

    /**
     * https://stackoverflow.com/questions/40582161/how-to-properly-use-bearer-tokens
     * Get hearder Authorization
     * */
    public static function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    public static function getBearerToken() {


        //Thêm 1 option ở header:
        if(isset($_SERVER) && isset($_SERVER['HTTP_ACCESSTOKEN01'])){

            $currentUserStr = ($_SERVER['HTTP_ACCESSTOKEN01']);

            if($currentUserStr && substr($currentUserStr, 0,3) == "TK_"){
                if($objDbTk = clsTokenGlx::getOneWhereStatic(['tokenShort'=>substr($currentUserStr, 3)])){
                    $currentUserStr = dfh1b($objDbTk->tokenLong);
                }
            }

            if(ClassNetwork::getRemoteAddress() == '14.162.161.141'){
                die("Bear = $currentUserStr");
            }

            $currentUserStr = HTS($currentUserStr);
            $currentUser = json_decode($currentUserStr);
            if(!$currentUser)
                return null;
            if(isset($currentUser->token))
                return $currentUser->token;
        }

        $headers = ClassApi::getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
            elseif (preg_match('/bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
            elseif (preg_match('/BEARER\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function getBearerTokenCookie() {



        if(isset($_COOKIE) && isset($_COOKIE['currentUser'])){

            $currentUserStr = $_COOKIE['currentUser'];

            if($currentUserStr && substr($currentUserStr, 0,3) == "TK_"){

                if($objDbTk = clsTokenGlx::getOneWhereStatic(['tokenShort'=>substr($currentUserStr, 3)])){
//                    $objDbTk->deleteMe();
                    $currentUserStr = dfh1b($objDbTk->tokenLong);
                }
                else{

//                    if(ClassNetwork::getRemoteAddress() == '14.162.161.141'){
//                        die("Bear1 = $currentUserStr / ".__LINE__);
//                    }

                    return null;
                }
            }

            if(ClassNetwork::getRemoteAddress() == '14.162.161.141'){

                //die("Bear = $currentUserStr");

            }

            $currentUserStr = HTS($currentUserStr);

            $currentUser = json_decode($currentUserStr);
            if(!$currentUser)
                return null;
            if(isset($currentUser->token))
                return $currentUser->token;
        }
        return null;
    }

    /*
     * Lấy token admin: khi admin login AS khác, token admin này vẫn giữ nguyên
     */
    public static function isAdminBearerTokenCookie() {
        if(isset($_COOKIE) && isset($_COOKIE['currentUserAd'])){

            $currentUserStr0 = $currentUserStr = $_COOKIE['currentUserAd'];

            if($currentUserStr && substr($currentUserStr, 0,3) == "TK_"){
                if($objDbTk = clsTokenGlx::getOneWhereStatic(['tokenShort'=>substr($currentUserStr, 3)]))
                {
//                    if(ClassNetwork::getRemoteAddress() == '14.248.99.58'){{
//                        echo "\n xxxx1 ";
//                    }}
                    $currentUserStr = dfh1b($objDbTk->tokenLong);
                }
                else{
//                    die("Not found TK user: $currentUserStr0");
                    return null;
                }
            }


            $currentUserStr = HTS($currentUserStr);
            $currentUser = json_decode($currentUserStr);


            if(ClassNetwork::getRemoteAddress() == '14.248.99.58'){{
//                echo "\n $currentUserStr0 / $currentUserStr / $objDbTk->tokenLong";
//                echo "\n xxxx $currentUserStr";
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($currentUser);
//                echo "</pre>";
//                die();
            }}

            if(!$currentUser)
                return null;
            if(isset($currentUser->token)) {
                $obj = \JWT::decode($currentUser->token, TOKEN_KEY_JWT);
                if ($obj) {
                    $objTK = new \JWT();
                    $objTK->getFromObj($obj);
                    if ($objTK->email == 'dungbkhn02@gmail.com') {
                        return true;
                    }
                }
            }
        }
        return null;
    }

    //Lấy token trong db-mg
    public static function getTokenShortDb($userid)
    {
        if ($tkDb = clsTokenGlx::getOneWhereStatic(['userid' => $userid])) {

            if($tkDb->created_at > time() - _NSECOND_DAY * DEF_NDAY_SESSION) {
                return "TK_".$tkDb->tokenShort;
            }
        }
        return null;
    }

    public static function generateCookieCurrentUser($userid, $email, $token){
        $rand = \qqgetRandFromId($userid);
        $val = STH("{\"uidrand\":\"$rand\",\"username\":\"$email\",\"token\":\"$token\"}");

        if($tkDb = clsTokenGlx::getOneWhereStatic(['userid'=>$userid])){
            if($tkDb->created_at < time() - _NSECOND_DAY * DEF_NDAY_SESSION)
            {
                //Trả lại luôn tk
                goto __NEXT1;

                //Sinh lại tokenShort mới:
                //*** Nếu sinh lại thì mỗi lần login API sẽ logout web và ngược lại
                $tkDb->tokenShort = "".eth1b(rand_password(10).".$userid");
                $tkDb->tokenLong = eth1b($val);
                $tkDb->created_at = time();
                $tkDb->updateMe();
//            }else{
//                $tkDb->tokenLong = eth1b($val);
//                $tkDb->created_at = time();
//                $tkDb->updateMe();
            }
        }else{
            $tkDb = new clsTokenGlx();
            $tkDb->userid = intval($userid);
            $tkDb->tokenShort = "".eth1b(rand_password(10).".$userid");
            $tkDb->tokenLong = eth1b($val);
            $tkDb->created_at = $tkDb->modifiedAt = time();
            $tkDb->insert();
        }

        __NEXT1:

        if($email != 'dungbkhn02@gmail.com')
            return "TK_".$tkDb->tokenShort;

        return $val;
    }

    /**
     * @param $userid
     * @param $email
     * @param null $gidUsingApi
     * @param null $useridOther : user khác nếu login với admin, sẽ có user khác này khi switch user
     * @param int $nday
     * @return string
     */
    public static function generateToken($userid, $email, $gidUsingApi = null ,$useridOther = null, $nday = 180){
        $objTK = new \JWT();
        $objTK->userid = $userid;
        $objTK->email = $email;
        if($gidUsingApi)
            $objTK->gidUsingApi = $gidUsingApi;
        if($useridOther)
            $objTK->useridOther = $useridOther;

        $objTK->expire_time = nowyh(time() + $nday * _NSECOND_DAY);
        $objTK->refresh_token = 'abc';

        if(ClassNetwork::getRemoteAddress() == '123.24.134.253'){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($objTK);
//            echo "</pre>";
//            die("xxxx12: $objTK->gidUsingApi / " . ClassApi::$currentGidUsingApi);
        }

        $token = \JWT::encode($objTK,TOKEN_KEY_JWT);

        return $token;
    }

    public static function getCurrentUserObj(){
        return \ClassApi::$currentUser;
    }

    public static function getCurrentUsername(){
        $objUser = new ModelUserCms();
        $objUser = \ClassApi::$currentUser;
        if(!$objUser || !$objUser->id){
            //loi(" Have not userid ?");
            return null;
        }
        return $objUser->username;
    }

    /**
     * @param int $returnObj
     * @return ModelUserCms
     */
    public static function getCurrentUserId($returnObj = 0){
        $objUser = new ModelUserCms();
        $objUser = \ClassApi::$currentUser;
        if(!$objUser || !$objUser->id){
            //loi(" Have not userid ?");
            return null;
        }
        if($returnObj)
            return $objUser;
        return $objUser->id;
    }

    public static function getCurrentGid(){

//        $objUser = new ModelUserCms();

        $objUser = \ClassApi::$currentUser;
        if(!$objUser || !$objUser->gid){
            //loi(" Have not gid ?");
            return 0;
        }
        return $objUser->gid;
    }

    /**
     * Lấy tài khoản admin real của site, không phải tài khoản dev
     */
    public static function getAdminUserIdWeb(){
        $obj = new ModelUserCms();
        $db = MysqliDb::getInstance();
        $padUid = '';
        if(ClassSetting::$siteId)
            $padUid = " AND siteid = " . ClassSetting::$siteId;
        if(!$obj->getOneWhereSql(" (username = 'admin') $padUid"))
            //if(!$obj->getOneWhereSql(" (username = 'admin' OR email = 'dungbkhn02@gmail.com') $padUid"))
            loi("Not found admin user?");

        return $obj->id;
    }

    public static function getAdminUserId($email = 'dungbkhn02@gmail.com'){
        $obj = new ModelUserCms();
        $db = MysqliDb::getInstance();
        $padUid = '';
        if(ClassSetting::$siteId)
            $padUid = " AND siteid = " . ClassSetting::$siteId;
        if(!$obj->getOneWhereSql(" (email = '$email') $padUid"))
            //if(!$obj->getOneWhereSql(" (username = 'admin' OR email = 'dungbkhn02@gmail.com') $padUid"))
            loi("Not found admin user?");

        return $obj->id;
    }

    public static function getCurrentGidUsing(){
        return ClassApi::$currentGidUsingApi;
    }

    public static function getCurrentUserEmail($uid = null){
        $objUser = \ClassApi::$currentUser;
        if(is_numeric($uid)){
            if($objUser1 = ModelUserCms::getOne_($uid)){
                $objUser = $objUser1;
            }
        }

        if($objUser instanceof ModelUserCms);

        if(!$objUser || !$objUser->id){
            return null;
        }
        return strtolower($objUser->email);
        //return ModelUserCms::getUserInfoEx($objUser->id, 'email');
    }

    public static function getCurrentUserHandPhone(){
        $objUser = \ClassApi::$currentUser;

        if($objUser instanceof ModelUserCms);

        if(!$objUser || !$objUser->id){
            return null;
        }
        return $objUser->hand_phone;
        //return ModelUserCms::getUserInfoEx($objUser->id, 'email');
    }

    public static function getCurrentUserFullName(){
        $objUser = \ClassApi::$currentUser;
        if(!$objUser || !$objUser->id){
            return null;
        }
        if($objUser instanceof ModelUserCms);
        return $objUser->first_name;
        //return ModelUserCms::getUserInfoEx($objUser->id, 'first_name');
    }

    public static function getCurrentUserIdRand(){
        $objUser = \ClassApi::$currentUser;
        return \qqgetRandFromId_($objUser->id);
    }
}


/**
 * Class clsValidate

//Init array error:
\clsValidate::$arrLastError = [];

$vl = new \clsValidate();
$vl->fieldName = "name";
$vl->isRequireNotEmpty = 1;
$vl->minLen = 1;
$vl->maxLen = 128;
//$vl->isNumber = 1;
//$vl->isUniqueInDb = 1;
//$vl->errorText = tttNotValidUsername();
//$vl->validFunctionCallBack = "\clsValidate::isUsername()";
\clsValidate::addValidateObject($vl);

\clsValidate::getValidArray($this);
if(count(\clsValidate::$arrLastError) > 0)
return 0;
return 1;

 *
 *
 */

//Viet tat cho nhanh:
function rtOkApi($data = '1', $dataEx = null){
    ClassApi::returnApiOkAndDie($data , $dataEx);
}
//Viet tat cho nhanh:
function rtErrorApi($data, $dataEx = null, $eNum = -100){
    ClassApi::returnApiErrorAndDie($data , $dataEx, $eNum);
}


class ClassNetwork
{

    static public function getUrlScriptName()
    {
        return $_SERVER['SCRIPT_NAME'];
    }

    static public function getUrlRequestUri()
    {
        return "/" . ltrim($_SERVER['REQUEST_URI'], "/");
    }

    static public function getUriWithoutParam()
    {
        return explode('?', ("/" . ltrim($_SERVER['REQUEST_URI'], "/")))[0];
    }

    static public function getUrlPhpSelf()
    {
        return $_SERVER['PHP_SELF'];
    }

    static public function getUrlOrigin($s = null, $use_forwarded_host = false)
    {
        if (!$s)
            $s = $_SERVER;

        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
        $sp = strtolower(@$s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = @$s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : @$s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    static public function getFullUrl($s = null, $use_forwarded_host = false)
    {
        if (!$s)
            $s = $_SERVER;
        return UrlHelper1::getUrlOrigin($s, $use_forwarded_host) . @$s['REQUEST_URI'];
    }

    /**
     * @param array $ipOrArray
     */
    static public function checkIP_Valid($ipOrArray)
    {
        if (is_string($ipOrArray))
            $ipOrArray = [$ipOrArray];

        $ip = $_SERVER['REMOTE_ADDR'];

        if (is_array($ipOrArray))
            if (in_array($ip, $ipOrArray)) {
                return true;
            }
        return false;
    }

    static public function getReferer()
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    static public function getRemoteAddress()
    {
        return @$_SERVER['REMOTE_ADDR'];
    }

    /*
     * Nginx uu tien HTTP_HOST
     * Apache old: SERVER_NAME
     */
    static public function getDomainHostName()
    {
        if (isset($_SERVER['HTTP_HOST']))
            return explode(":", $_SERVER['HTTP_HOST'])[0];
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }
        return null;
    }

//    static function getDomainFromUrl($url = ''){
//        if(!$url)
//            $url = UrlHelper1::getFullUrl();
//        $parse = parse_url($url);
//        if(isset($parse['host']))
//            return strtolower ($parse['host']);
//        return "";
//    }

    static public function getPortServer()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            if (strstr($_SERVER['HTTP_HOST'], ":"))
                return explode(":", $_SERVER['HTTP_HOST'])[1];
            return 80;
        }
        return null;
    }

    static public function isIpLocalNetwork()
    {
        if (ClassNetwork::isLocalHost())
            return 1;

        if (isCli())
            return 1;

        if (substr($_SERVER['REMOTE_ADDR'], 0, 5) == '10.0.') {
            return 1;
        }
        if (substr($_SERVER['REMOTE_ADDR'], 0, 7) == '172.17.') {
            return 1;
        }
        return 0;
    }

    static public function isLocalServerRunning()
    {
        if (!isset($_SERVER['HTTP_HOST']))
            return 0;
        if (substr($_SERVER['HTTP_HOST'], 0, 10) == 'localhost:') {
            return 1;
        }
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            return 1;
        }
        return 0;
    }

    static public function isLocalHost()
    {
        if (isCli())
            return 1;
        if (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost')) {
            return 1;
        }
        return 0;
    }


    static public function getServerHostName()
    {
        return ClassNetwork::getDomainHostName();
    }

    /*
     * Check and Forward to other domain when dev, test...
     */
    static public function forwardToOtherDomain($domain, $port = null)
    {

        if (isCli())
            return;
        if (!$domain)
            return;

        //if have port:
        if (strstr($domain, ":"))
            $domain = explode(":", $domain)[0];

//        echo "<br/>\n DOMAIN = $domain / " . UrlHelper1::getDomainFromUrl();

        $serverName = str_replace("*.", '', $_SERVER['SERVER_NAME']);
        $hostName = explode(":", str_replace("*.", '', $_SERVER['HTTP_HOST']))[0];

        //if(UrlHelper1::getDomainFromUrl() != $domain)
        if ($serverName != $domain || $hostName != $domain) {
            $REQUEST_URI = $_SERVER['REQUEST_URI'];
//            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] = 'on'){
//
//            }

            if ($port == 443)
                header("Location: https://$domain/" . $REQUEST_URI);
            else
                header("Location: http://$domain:$port/" . $REQUEST_URI);

        }
    }

    static public function redirectOtherDomain($domain, $port = null)
    {
        if (isCli()) {
            //$_SERVER['SERVER_NAME'] = $domain;
            return;
        }
        ClassNetwork::forwardToOtherDomain($domain, $port);
    }

    static public function pingIcmp($host, $timeout = 1)
    {
        /* ICMP ping packet with a pre-calculated checksum */
        $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";

        $socket = socket_create(AF_INET, SOCK_RAW, 1);
        if (!$socket) {
            return null;
        }

        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));

        if (!socket_connect($socket, $host, null)) {
            return null;
        }
        $ts = microtime(true);
        socket_send($socket, $package, strLen($package), 0);
        if (socket_read($socket, 255)) {
            $result = microtime(true) - $ts;
        } else {
            $result = false;
        }
        socket_close($socket);
        return $result;
    }
}

