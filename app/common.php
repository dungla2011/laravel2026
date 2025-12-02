<?php

//////////////////////////// Đang sửa

use App\Models\EventFaceInfo;
use App\Models\FileUpload;
use App\Models\User;
use LadLib\Common\UrlHelper1;
use function App\Http\ControllerApi\ol3;
use function App\Http\ControllerApi\rtErrorApi;

define('DEF_PREFIX_SEARCH_URL_PARAM_GLX', 'seby_');
define('DEF_PREFIX_SORTBY_URL_PARAM_GLX', 'soby_');
define('DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX', 'seoby_');
define("DEF_MONITOR_DEFAULT_FREE_QUOTA" , 10);
define('DEF_QUOTA_USER_CLOUD_INIT_FILE', 1000);
define('DEF_QUOTA_USER_CLOUD_INIT_SIZE_GB', 1);
defined('DEF_DIR_CACHE_MOMO') || define("DEF_DIR_CACHE_MOMO", '/mnt/glx/cache/7200/momo_trans');

defined('GLX_LOG_FOLDER') || define("GLX_LOG_FOLDER", "/var/glx");
defined('EMEGENCY_ERROR_FILE') || define("EMEGENCY_ERROR_FILE", GLX_LOG_FOLDER."/weblog/error_emegency.log");
defined('GLOBAL_ERROR_FILE') || define("GLOBAL_ERROR_FILE", GLX_LOG_FOLDER."/weblog/error_global.log");
defined('GLOBAL_ACCESS_FILE') || define("GLOBAL_ACCESS_FILE", GLX_LOG_FOLDER."/weblog/access_global.log");
defined('GLOBAL_DEBUG_FILE') || define("GLOBAL_DEBUG_FILE", GLX_LOG_FOLDER."/weblog/debug.log");


defined('_MB') || define('_MB', 1048576);
defined('_GB') || define('_GB', 1073741824);
defined('_NSECOND_DAY') || define('_NSECOND_DAY', 86400);

defined('DEF_FILE_JSON_LANG_PREFIX') || define('DEF_FILE_JSON_LANG_PREFIX', "");

defined('DEF_GLX_LOG_FILE_EVENT') || define('DEF_GLX_LOG_FILE_EVENT', '/var/glx/weblog/event_mng.log');

class ladDebug
{
    public static $enable = 1;

    public static $strTime = [];

    public static $startTime1;

    public static $startTime2;

    public static function addTime($file, $line, $count = 0)
    {
        if (! static::$enable) {
            return null;
        }
        $file = basename($file);
        static::$strTime["$file($line)$count"] = microtime(1);
    }

    public static function dumpDebugTime()
    {
        //return;
        if (! static::$enable) {
            return null;
        }

        $ret = static::toStringTime();
//        dumpdebug(' TimeDebug: - '.$ret." \nREQUEST: ".serialize($_REQUEST), '/var/glx/weblog/timer_access.log');

        return $ret;
    }

    public static function toStringTime()
    {
        //return null;
        $dtime = 0;
        $tmp = '';
        if (count(static::$strTime)) {
            $cc = 0;
            $t0 = 0;
            $x = 0;
            $dt1 = 0;
            foreach (static::$strTime as $key => $value) {
                $tmp .= "\n|$key->$value";
                if ($cc == 0) {
                    $t0 = $value;
                } else {
                    $dt1 = number_format($value - $x, 3);
                }
                $cc++;

                $tmp .= "(DT = $dt1) ";

                $x = $value;
            }
            $dtime = number_format($x - $t0, 3);
        }

        return " $tmp \n DTIME = $dtime";
    }
}


//Bad request:
function loiApi400($str, $code = -1)
{
    //    return response()->json(
    //        ['code'=>$code, $str]
    //        , 400);
    ob_clean();
    http_response_code(400);
    exit(json_encode(['errorCode' => $code, 'dataRet' => $str]));
}

function _rtJsonApiDone($dataRet, $mess = null, $code = 1, $payloadEx = null)
{
    return json_encode(['code' => $code, 'payload' => $dataRet, 'payloadEx' => $payloadEx, 'message' => $mess]);
}

function _rtJsonApiError($mess, $httpStatus = 400, $code = -1, $payloadEx = null)
{
    return json_encode(['code' => $code, 'payload' => ''.$mess, 'message' => $mess, 'payloadEx' => $payloadEx]);
}

function rtJsonApiDone($dataRet, $mess = null, $code = 1, $payloadEx = null)
{
    return response()->json(['code' => $code, 'payload' => $dataRet, 'payloadEx' => $payloadEx, 'message' => $mess], 200, [], JSON_PRETTY_PRINT);

}

function rtJsonApiError($mess, $httpStatus = 400, $code = -1, $payloadEx = null)
{

    $domain = @UrlHelper1::getDomainHostName();
    $logFile = "/var/glx/weblog/all_$domain.log";
    if($GLOBALS['_log_file'] ?? '') {
        $logFile = $GLOBALS['_log_file'];
    }
    $mess1 = $mess . " | " . ( $GLOBALS['_error_file'] ?? '');
    $mess1 = nowyh() . "#". @$_SERVER["REMOTE_ADDR"] . "#" . $mess1 . " | " ;
    @file_put_contents($logFile, $mess1, FILE_APPEND);

    return response()->json(['code' => $code, 'payload' => $mess, 'message' => $mess, 'payloadEx' => $payloadEx], $httpStatus);
}



function __XR($string, $key)
{
    $len = strlen($string);
    $ret = "";
    for ($i = 0, $j = 0; $i < $len; $i++, $j++) {
        if ($j >= strlen($key))
            $j = 0;
        //$string{$i} = $key{$j}^$string{$i};
        $ret .= substr($key, $j, 1) ^ substr($string, $i, 1);
    }

    return $ret;
//	return $string;
}


//////////////////////////////////////
function __rnd($_len)
{
    $len = $_len - 1;
    $base = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789~!#%^&*()_+-=?:"{}`,.;';
    $max = strlen($base) - 1;
    $activatecode = '';
//    mt_srand((double)microtime() * 1000000);
    while (strlen($activatecode) < $len + 1)
        $activatecode .= $base[mt_rand(0, $max)];
    return $activatecode;
}

function _timeMs() {
    return round(microtime(true) * 1000);
}

function amstore_xmlobj2array($obj, $level = 0)
{

    $items = array();

    if (!is_object($obj))
        return $items;

    $child = (array)$obj;

    if (sizeof($child) > 1) {
        foreach ($child as $aa => $bb) {
            if (is_array($bb)) {
                foreach ($bb as $ee => $ff) {
                    if (!is_object($ff)) {
                        $items[$aa][$ee] = $ff;
                    } else
                        if (get_class($ff) == 'SimpleXMLElement') {
                            $items[$aa][$ee] = amstore_xmlobj2array($ff, $level + 1);
                        }
                }
            } else
                if (!is_object($bb)) {
                    $items[$aa] = $bb;
                } else
                    if (get_class($bb) == 'SimpleXMLElement') {
                        $items[$aa] = amstore_xmlobj2array($bb, $level + 1);
                    }
        }
    } else
        if (sizeof($child) > 0) {
            foreach ($child as $aa => $bb) {
                if (!is_array($bb) && !is_object($bb)) {
                    $items[$aa] = $bb;
                } else
                    if (is_object($bb)) {
                        $items[$aa] = amstore_xmlobj2array($bb, $level + 1);
                    } else {
                        foreach ($bb as $cc => $dd) {
                            if (!is_object($dd)) {
                                $items[$obj->getName()][$cc] = $dd;
                            } else
                                if (get_class($dd) == 'SimpleXMLElement') {
                                    $items[$obj->getName()][$cc] = amstore_xmlobj2array($dd, $level + 1);
                                }
                        }
                    }
            }
        }

    return $items;
}
function SocketSendCMD($cmd, $host = '127.0.0.1', $port = GLX_PORT_FOR_FILE_SERVICE, $timeout = 15) {
//    $host="127.0.0.1" ;

//    $port = GLX_PORT_FOR_FILE_SERVICE;

    $sk = @fsockopen($host, $port, $errnum, $errstr, $timeout);

    $reply = "";

    //dumpdebug("SocketSendCMD: CMD = $cmd");

    if (!is_resource($sk)) {
        return "ERROR OpenSocket: '$host:$port'";
        //echo  "\nERROR SOCKET? \n";
    } else {
        //echo  "\n OK  SOCKET? \n";
        @fputs($sk, "cmd#$cmd");

        $reply = "";

        $count = 0;

        if ($timeout > 0)
            stream_set_timeout($sk, $timeout);

        while (!@feof($sk)) {
            if ($timeout > 0)
                stream_set_timeout($sk, $timeout);

            $reply.= @fgets($sk, 1024);
            $count++;
            if ($timeout > 0 && $count > 3)
                break;
        }

        /* while (!feof($sk))
          {
          $reply.= fgets ($sk, 1024);
          } */

        //echo  "\n Reply = $reply \n";
    }
    @fclose($sk);
    return $reply;
}

///////// CRYPTOR ///////////////
function __enc($data)
{
    $key = __rnd(2);
    $str_encode = __XR($data, $key);
    $str_result = $key . $str_encode;
    return $str_result;
}

function __dec($data)
{
    $key = substr($data, 0, 2);
    $str_decode = __XR($data, $key);
    $str_result = substr($str_decode, 2);
    return $str_result;
}

function e_t_h($data)
{
    return STH(__enc($data));
}

function d_f_h($hex_data)
{
    return __dec(HTS($hex_data));
}

function setLogFile($filename = null)
{
    $sid = $GLOBALS['GLX_SITE_ID'] ?? '';
    if($filename)
    if ($filename[0] == '/') {
        return $GLOBALS['GLX_LOG_FILE'] = $filename;
    }
    if (file_exists('/var/glx/weblog/')) {
        return $GLOBALS['GLX_LOG_FILE'] = "/var/glx/weblog/all_$sid.log";
    }
}

function ol0($str, $hideEcho = 0)
{
    $time = date('Y-m-d H:i:s');
    if (! $hideEcho) {
        echo "\n$time. $str";
    }
    $GLX_LOG_FILE = null;
    if (!($GLX_LOG_FILE = $GLOBALS['GLX_LOG_FILE'] ?? ''))
        //        if(file_exists($GLX_LOG_FILE) && filesize($GLX_LOG_FILE) > 10000000)
        //            movefile($GLX_LOG_FILE, $GLX_LOG_FILE . '.'. time());
        $GLX_LOG_FILE = setLogFile();

    if($GLX_LOG_FILE && file_exists($GLX_LOG_FILE))
        outputT($GLX_LOG_FILE, $str);
    return;

    loi('Not found GLX_LOG_FILE');
}

function ol00($str)
{
    ol0($str, 1);

}

function loi($string)
{
    if (app()->environment('production')) {
        //Nếu có buffer thì mới clean
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Production: chỉ log, không throw
//        echo "$string";;
        die($string);
    }


    throw new Exception($string);
}

function loi2($string)
{
    throw new Exception($string);
}

function echored($string)
{
    echo "<span style='color: red;'>$string</span>";
}

function echoblue($string)
{
    echo "<span style='color: red;'>$string</span>";
}

function echo_color($string, $color)
{
    echo "<span style='color: $color;'>$string</span>";
}

function getch($info = '')
{
    echo "\n $info";
    $handle = fopen('php://stdin', 'r');

    return trim(fgets($handle));
}

function nowh($time = '')
{
    if (!$time) {
        return $datetime = date('H:i:s');
    } else {
        return $datetime = date('H:i:s', $time);
    }
}

function nowyh($time = '')
{
    if (!$time) {
        return $datetime = date('Y-m-d H:i:s');
    } else {
        return $datetime = date('Y-m-d H:i:s', $time);
    }
}

function nowyh_vn($time = '')
{
    if (empty($time)) {
        return $datetime = date('H:i:s d-m-Y');
    } else {
        return $datetime = date('H:i:s d-m-Y', $time);
    }
}

function nowyh_vn_date_pre($time = '')
{
    if (empty($time)) {
        return $datetime = date('d-m-Y H:i:s ');
    } else {
        return $datetime = date('d-m-Y H:i:s ', $time);
    }
}


function nowy_vn($time = '')
{
    if (empty($time)) {
        return $datetime = date('d-m-Y');
    } else {
        return $datetime = date('d-m-Y', $time);
    }
}

function nowy_vn2_null($time = '')
{
    if (empty($time)) {
        return null;
    } else {
        return $datetime = date('d/m/Y', $time);
    }
}

function nowy_vn2($time = '')
{
    if (empty($time)) {
        return $datetime = date('d/m/Y');
    } else {
        return $datetime = date('d/m/Y', $time);
    }
}

function nowy($time = '')
{
    if (empty($time)) {
        return $datetime = date('Y-m-d');
    } else {
        return $datetime = date('Y-m-d', $time);
    }
}

function joinSl1()
{

    $mm = [
        0 => '--- xxx',
        //        1=>'Hà Nội ',
        //        2=>'HCM',
        //        3=>'Huế',
        //        4=>'Đà nẵng',
        //        5=>'Phú Quốc',
    ];

    return $mm;

}

//GetUserEmail from userid
function DEL__joinUserEmailUserId($objData, $value)
{
    if ($value && $obj = \App\Models\User::where('id', '=', $value)->first()) {
        $obj = json_decode($obj);

        //return 'abc';
        return [$obj->id => $obj->email];
    }

    return null;
}

function DELL_joinTags($objData, $value)
{

    if (! $objData) {
        if ($value) {
            if ($tag = \App\Models\TagDemo::where('id', $value)->first()) {
                return [$tag->id => $tag->name];
            }
        }

        return null;
    }

    if ($objData instanceof \App\Models\DemoTbl);
    if ($objData instanceof stdClass) {
        $objData = \App\Models\DemoTbl::where('id', $objData->id)->first();
    }

    //Chuyển sang dùng Relation Laravel, BelongToMany
    //Không dùng chuỗi id cách nhau bởi dấy phẩy nữa, chuỗi đó chỉ có tác dụng ở Update lên server
    $mTag = $objData->joinTags->toArray();
    $ret = [];
    if ($mTag) {
        foreach ($mTag as $tag) {
            $ret[$tag['id']] = $tag['name'];
        }
    }

    return $ret;

    //    if(!$value)
    //        return null;

    //    if($value && $mm = \App\Models\TagDemo::whereIn('id', explode(',', $value))->get()){
    //
    //        $sql = \Illuminate\Support\Facades\DB::getQueryLog();
    //
    //        $mm1 = $mm->toArray();
    //
    //        $ret = [];
    //        foreach ($mm1 AS $obj){
    //            $ret[$obj['id']] = $obj['name'];
    //        }
    //        return $ret;
    //    }

}

function getGidCurrentCookie()
{
    if($user = User::getUserByTokenAccess())
        return $user->getRoleIdUser(1);

    if (isset($_COOKIE['_tglx863516839'])) {
        $user = User::where('token_user', $_COOKIE['_tglx863516839'])->first();
        if ($user) {
            return $user->getRoleIdUser(1);
        }
    }

    return null;
}

//Có thể trả về một mảng, đó là Role List
function getGidCurrent_($returnArray = 0)
{
    $user = auth()->user();
    if ($user instanceof \App\Models\User);
    if ($user) {
        return $user->getRoleIdUser($returnArray);
    }

    return null;
}


function  isAdminCookie() {
    if($_COOKIE['admin_glx' ] ?? ''){
        return 1;
    }
    return 0;
}

function isAdminACP_()
{
    $user = auth()->user();
    if ($user instanceof \App\Models\User);
    if ($user && $user->is_admin) {
        return $user->id;
    }

    return null;
}

function isAdminMngGroup()
{
    $mm = getGidCurrent_(1);
    if (in_array(1, $mm)) {
        return 1;
    }
    if (in_array(2, $mm)) {
        return 1;
    }

    return 0;
}

function getUserIdCurrentInCookie($getUser = 0) {


    if($user = User::getUserByTokenAccess()){
        if ($getUser) {
            return $user;
        }

        return $user->id;
    }

    $tk = $_COOKIE['_tglx863516839'] ?? '';
    if(!$tk){
        //Phien ban cu 4s:
        $tk = request()->header("accesstoken01");
    }

    if ($tk) {
        $user = User::where('token_user', $tk)->first();
        if ($user) {
            if ($getUser) {
                return $user;
            }

            return $user->id;
        }
    }

    return null;
}

/**
 * @param $getObj
 * @return User|\Illuminate\Contracts\Auth\Authenticatable|int|mixed|string|null
 */
function getCurrentUserId($getObj = 0)
{
    if ($id = \Illuminate\Support\Facades\Auth::id()) {
        if ($getObj) {
            return \Illuminate\Support\Facades\Auth::user();
        }

        return $id;
    }

    return getUserIdCurrentInCookie($getObj);
}

/**
 * @param  int  $getObj
 * @return User|\Illuminate\Contracts\Auth\Authenticatable|int|string|null
 */
function getUserIdCurrent_($getObj = 0)
{
    if ($getObj) {
        return \Illuminate\Support\Facades\Auth::user();
    }

    return \Illuminate\Support\Facades\Auth::id();
}

function getUserIdByEmail_($email)
{
    if ($us = User::where('email', $email)->first()) {
        return $us->id;
    }

    return null;
}

function setDomainHostNameGlx($hname)
{
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $hname;
}

function getDatabaseName_()
{
    $databaseName = \Illuminate\Support\Facades\Config::get('database.connections.'.\Illuminate\Support\Facades\Config::get('database.default'));

    return $databaseName['database'];
}

function isLocalHost() {

    if($_SERVER['REMOTE_ADDR'] == 'localhost'){
        return 1;
    }
    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
        return 1;
    }
}

function  isDebugPcCli()
{
    if(gethostname() == 'DESKTOP-VFQHFQS' )
        return 1;
}

function isIPDebug()
{
    return isDebugIp();
}

function isDebugIp()
{
    $ctx = stream_context_create(['http' => ['timeout' => 1]]);

    $ipx = 'none_ip';
    if (isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
        $ipx = $_SERVER['REMOTE_ADDR'];
    }
    if($ipx == '127.0.0.1'){
        return $ipx;
    }
    if($ipx == gethostbyname('4s3.myftp.org')){
        return $ipx;
    }
    if($ipx == gethostbyname('4s6.myftp.org')){
        return $ipx;
    }

//    if(str_starts_with($ipx, '103.163.21'))
//        return $ipx;

    if (file_exists('/var/glx/weblog/myip_ok.txt')) {
        $ret = file_get_contents('/var/glx/weblog/myip_ok.txt');
        if (strstr($ret, $ipx) !== false) {
            return $ipx;
        }
    }

    //    $check = file_get_contents('http://galaxycloud.vn/tool/4s/c_ip.php', false, $ctx);
    //    if(strstr($check, @$_SERVER["REMOTE_ADDR"]) !== false)
    //    {
    //        return 1;
    //    }
    //    if(@$_SERVER['REMOTE_ADDR'] == '222.254.10.203')
    //        return 1;
    return 0;
}

function getRemoteIP()
{
    return @$_SERVER['REMOTE_ADDR'];
}

function isDebugViewable()
{
    if(isSupperAdmin_())
        return 1;
//    $mail = getCurrentUserEmail();
//    if (env('AUTO_SET_DEV_ADMIN_EMAIL')) {
//        if (in_array($mail, explode(',', env('AUTO_SET_DEV_ADMIN_EMAIL')))) {
//            return 1;
//        }
//        if (in_array($mail, explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS')))) {
//            return 1;
//        }
//    }
    return 0;
}

function isDevEmail()
{
    if (in_array(getCurrentUserEmail(), explode(',', env('AUTO_SET_DEV_ADMIN_EMAIL')))) {
        return 1;
    }

    return 0;
}

function isEmailIsAutoSetAdmin($email)
{
    //Chỉ 1 email được autoset admin
    if (env('AUTO_SET_ADMIN_EMAIL')) {
        if (in_array($email, explode(',', env('AUTO_SET_ADMIN_EMAIL')))) {
            return 1;
        }
    }

    return 0;
}

function dump500($str, $n = 500)
{
    dump(substr($str, 0, $n));
}

function getUserEmailCurrent_()
{
    if (! $user = auth()->user()) {
        return null;
    }

    return $user->email;
}

function getCurrentUserEmail($uid = null)
{
    if($uid){
        if ($user = User::find($uid)) {
            return $user->email;
        }
    }

    if (! $user = auth()->user()) {
        return null;
    }

    return $user->email ?? '';
}

function getCurrentUserEmailCookie()
{
    if($user = User::getUserByTokenAccess()){
        return $user->email;
    }

    if($user = auth()->user())
        return $user->email;
    if (isset($_COOKIE['_tglx863516839'])) {
        $user = User::where('token_user', $_COOKIE['_tglx863516839'])->first();
        if ($user) {
            return $user->email;
        }
    }
    return null;
}

function number_formatvn0($num, $decimal = 0)
{
    return number_format($num, $decimal, ',', '.');
}

//function getCurrentModuleLrv(){
//    \App\Components\Helper1::getModuleCurrentName(request());
//}

function getCurrentActionMethod()
{
    return \Illuminate\Support\Facades\Route::getCurrentRoute()->getActionMethod();
}

function getCurrentController1()
{
    $currentUri = Illuminate\Support\Facades\Route::current()->uri();

// Loại bỏ tiền tố /admin hoặc /member
    if (strpos($currentUri, 'admin') === 0) {
        $routeAfterPrefix = substr($currentUri, strlen('admin/'));
    } elseif (strpos($currentUri, 'member') === 0) {
        $routeAfterPrefix = substr($currentUri, strlen('member/'));
    } else {
        $routeAfterPrefix = $currentUri;
    }

// Lấy phần đầu tiên sau tiền tố
    return explode('/', $routeAfterPrefix)[0];

}

class clsDebugHelper
{
    public static $lastQuery = null;
}

function isCli()
{
    //echo "<br>-". $_SERVER['REMOTE_ADDR'];
    if (php_sapi_name() == 'cli') {
        return true;
    } else {
        return false;
    }
}

function sss($time)
{
    echo("\n --- Sleep $time --- ".date('Y-m-d H:i:s'));
    sleep($time);
}

function isWindow1()
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 1;
    }

return 0;
}

function bl($str, $str1 = '', $marginTop = 0)
{
    $str = \App\Support\HTMLPurifierSupport::clean($str);
    $str1 = \App\Support\HTMLPurifierSupport::clean($str1);
    //    $str = strip_tags($str);
    baoloi1($str, $str1, $marginTop);
}

//Return trang chủ
function blrt($str, $txt = 'Trở lại')
{
    $str = \App\Support\HTMLPurifierSupport::clean($str);
    $str = strip_tags($str);
    baoloi1($str, " <a href='/'>$txt</a> ");
}

function bl3($err, $detail = '')
{
    $detail = \App\Support\HTMLPurifierSupport::clean($detail);
    $err = \App\Support\HTMLPurifierSupport::clean($err);
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'><div style='text-align: center'><div style='max-width: 400px; border:0px solid #ccc ; margin: 6px auto; padding: 5px 10px; font-family: Calibri; color: red; display: inline-block; text-align: left;background-color:AntiqueWhite '>
        $err";
    if (! empty($detail)) {
        echo "<div style='padding: 6px; font-family: Calibri; color: darkgreen; text-align: left '>$detail</div>";
    }
    echo '</div></div>';
}

function tb3($info, $detail = '', $center = 0)
{
    $info = \App\Support\HTMLPurifierSupport::clean($info);
    $detail = \App\Support\HTMLPurifierSupport::clean($detail);

    echo "<meta name='viewport' content='width=device-width, initial-scale=1'><div style='text-align: center'><div style='max-width: 400px; border:0px solid DarkGreen ; margin: 6px auto; padding: 5px 10px; font-family: Calibri; color: darkgreen; display: inline-block; text-align: left;background-color:AntiqueWhite '><b>$info </b>";
    if (! empty($detail)) {
        echo "<div style='padding: 3px; font-family: Calibri; color: darkgreen; text-align: left '>$detail</div>";
    }
    echo '</div></div>';
}

function tb($str, $str1 = '')
{
    thongbao1($str, $str1);
}

function baoloi1($err, $detail = '', $marginTop = 0)
{
    //    $err = strip_tags($err);
    //    $detail = strip_tags($detail);

    echo "<div style='text-align: center; margin-top: $marginTop".''."px'><div style='max-width: 800px;
border:0px solid #ccc ; margin: 6px auto; padding: 10px 20px;
border-radius: 10px;
font-family: Calibri; color: brown; display: inline-block; text-align: left;background-color:AntiqueWhite '>
        $err";
    if (! empty($detail)) {
        echo "<div style='padding: 6px; font-family: Calibri; color: darkgreen; text-align: left '>$detail</div>";
    }
    echo '</div></div>';
}

function thongbao1($info, $detail = '', $marginTop = 0)
{
    $info = strip_tags($info, '<br>,<p>,<b>');
    echo "<div style='text-align: center; margin-top: $marginTop".''."px'>
<div
style='border-radius: 10px; max-width: 800px; border:0px solid DarkGreen ; margin: 6px auto;
 padding: 10px 15px; font-family: Calibri;
color: royalblue; display: inline-block; text-align: left;background-color:AntiqueWhite '>$info ";
    if (! empty($detail)) {
        echo "<div style='padding: 5px; font-family: Calibri; color: darkgreen; text-align: left '>$detail</div>";
    }
    echo '</div></div>';
}

///////// CRYPTOR ///////////////
function STH__($string)
{
    $strHex = '';
    $strlen = strlen($string);
    for ($i = 0; $i < $strlen; $i++) {
        if (ord($string[$i]) < 16) {
            $strHex .= '0'.dechex(ord($string[$i]));
        } else {
            $strHex .= dechex(ord($string[$i]));
        }
    }

    return $strHex;
}

function HTS__($string)
{
    $str = '';
    for ($i = 0; $i < strlen($string); $i += 2) {
        $str .= chr(hexdec(substr($string, $i, 2)));
    }

    return $str;
}

function XR1_($string, $key)
{
    $len = strlen($string);
    $ret = '';
    for ($i = 0,$j = 0; $i < $len; $i++,$j++) {
        if ($j >= strlen($key)) {
            $j = 0;
        }
        //$string[$i] = $key{$j}^$string[$i];
        $ret .= substr($key, $j, 1) ^ substr($string, $i, 1);
    }

    return $ret;
    //	return $string;
}

function enc1b($data, $key = 0)
{
    if (! $key) {
        $key = rand() % 9 + 1;
    } else {
        $key = $key % 10;
    }

    $str_encode = XR1_($data, $key);
    $str_result = $key.$str_encode;

    return $str_result;
}

function dec1b($data)
{
    $key = substr($data, 0, 1);
    $str_decode = XR1_($data, $key);
    $str_result = substr($str_decode, 1);

    return $str_result;
}

function e_t_h_1b($data, $preFix = null, $key = null)
{
    return $preFix.STH__(enc1b($data, $key));
}

function d_f_h_1b($hex_data)
{

    if (! isset($hex_data) || ! $hex_data) {
        return null;
    }

    if ($hex_data[0] == 'M') {
        if ($hex_data[1] == 'S' || $hex_data[1] == 'G') {
            $hex_data = substr($hex_data, 2);
        }
    } elseif ($hex_data[0] == 'm') {
        if ($hex_data[1] == 's' || $hex_data[1] == 'g') {
            $hex_data = substr($hex_data, 2);
        }
    }

    return dec1b(HTS__($hex_data));
}

function dfh1b($fid)
{
    return d_f_h_1b($fid);
}

function eth1b($fid, $preFix = "ms", $key = null)
{
    return e_t_h_1b($fid, $preFix, $key);
}

function qqgetIdFromRand_($rand)
{
    return \App\Components\ClassRandId2::getIdFromRand($rand);
}

function qqgetRandFromId_($id)
{
    return \App\Components\ClassRandId2::getRandFromId($id);
}

function isSupperAdmin__()
{
    return User::isSupperAdmin();
}

function isSupperAdminDoing()
{
    if($user = User::getUserByTokenAccess()){
        if (in_array($user->email, explode(',', env('AUTO_SET_DEV_ADMIN_EMAIL')))) {
            return $user->id;
        }
    }

    if (isset($_COOKIE) && isset($_COOKIE['_tglx863516839'])) {
        $tk = $_COOKIE['_tglx863516839'];
        if ($user = User::where('token_user', $tk)->first()) {
            if (in_array($user->email, explode(',', env('AUTO_SET_DEV_ADMIN_EMAIL')))) {
                return $user->id;
            }
        }
    }

    return null;
}

function isSupperAdminDevCookie()
{
    return User::isSupperAdminDevCookie();
}

function isSupperAdmin_()
{

    return User::isSupperAdmin();
}

function isAdminLrv_()
{
    return User::isAdminLrv_();
}

function output($filename, $string, $createFolder = 0)
{
    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }

    $file = @fopen($filename, 'a');
    if (! $file) {
        return;
    }
    @fwrite($file, $string."\r\n");
    @fclose($file);
}

function outputW($filename, $string, $createFolder = 0)
{
    if (! $filename) {
        return;
    }
    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }
    $file = fopen($filename, 'w');
    if (! $file) {
        return;
    }
    fwrite($file, $string);
    fclose($file);
}

function outputT($filename, $strlog, $createFolder = 0)
{

    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }

    $datetime = date('Y-m-d H:i:s');
    output($filename, $datetime.'#'.$strlog);
}

function outputFile($filename, $strlog, $createFolder = 0)
{
    outputT($filename, $strlog, $createFolder = 0);
}

//$arrFull = array();
//DirListFullToArray($dirPath,$arrFull);
function DirListFullToArray($dir, &$arrFull)
{
    $arr = ListDirSortABC($dir);
    for ($i = 0; $i < count($arr); $i++) {
        $arrFull[] = $dir.'/'.$arr[$i];
        if (is_dir($dir.'/'.$arr[$i])) {
            DirListFullToArray($dir.'/'.$arr[$i], $arrFull);
            //inl($countBook.". OK BOOK TO ZIP: ".$dir);
        }
    }
}

function ListDir($dirpath, $isFileOnly = 0)
{

    if (! is_dir($dirpath)) {
        return '';
    }

    if ($dirpath[strlen($dirpath) - 1] != '/') {
        $dirpath .= '/';
    }

    //inr($dirpath);

    $d = dir($dirpath); //need sort this a->z

    if (! $d) {
        return '';
    }

    $count = 0;

    $dirsort = [];

    while (false !== ($entry = $d->read())) {

        if ($entry[0] != '.') {
            //		echo chr(10).$count."-".$entry;
            $dirsort[$count] = $entry;
            $count++;
        }
    }

    return $dirsort;
}

//$arrFull = array();
//DirListFullToArray($dirPath,$arrFull);
function ListDirFullToArray($dir, &$arrFull)
{
    $arr = ListDirSortABC($dir);
    for ($i = 0; $i < count($arr); $i++) {
        $arrFull[] = $dir.'/'.$arr[$i];
        if (is_dir($dir.'/'.$arr[$i])) {
            DirListFullToArray($dir.'/'.$arr[$i], $arrFull);
            //inl($countBook.". OK BOOK TO ZIP: ".$dir);
        }
    }
}

function ListDirSortABC($dirpath, $ignoreCaseSen = 0, $des = 0)
{

    if (! is_dir($dirpath)) {
        return null;
    }

    if ($dirpath[strlen($dirpath) - 1] != '/') {
        $dirpath .= '/';
    }

    //inr($dirpath);

    $d = dir($dirpath); //need sort this a->z

    if (! $d) {
        return null;
    }

    $count = 0;

    $dirsort = [];

    while (false !== ($entry = $d->read())) {

        if ($entry[0] != '.') {
            //echo chr(10).$count."-".$entry;
            $dirsort[$count] = $entry;
            $count++;
        }
    }

    if ($count) {
        if ($des == 1) {
            array_multisort(array_map('strtolower', $dirsort), SORT_DESC, SORT_STRING, $dirsort);
        } else {
            array_multisort(array_map('strtolower', $dirsort), SORT_ASC, SORT_STRING, $dirsort);
        }
    }

    return $dirsort;
}

/*
  Resolve limit 32K file in a folder!
  ----
  12345678901 => 012/345/678/901/12345678901
  12345678901 => 0000/0123/4567/8901/12345678901
  12345678901 => 12345/67890/12345678901
  12345678901 => 12345678/12345678901

  abc => 0000/0000/0000/abc

 */

function gen_path_from_number($id, $nPart = 3, $lenPart = 3)
{
    $len = strlen($id);

    //if ($len > $nPart * $lenPart)
    //  loi("Error: gen_path_from_number, over max number $id has len = $len>" . ($nPart * $lenPart));

    $pad0 = $nPart * $lenPart - $len;

    $fullstr = $id;
    for ($i = 0; $i < $pad0; $i++) {
        $fullstr = "0$fullstr";
    }

    $ret = '';
    for ($i = 0; $i < $nPart - 1; $i++) {
        $ret .= substr($fullstr, $i * $lenPart, $lenPart).'/';
    }

    return "$ret$id";
}

function gen_path_from_number_not_file($id, $nPart = 3, $lenPart = 3)
{
    $len = strlen($id);

    //if ($len > $nPart * $lenPart)
    //  loi("Error: gen_path_from_number, over max number $id has len = $len>" . ($nPart * $lenPart));

    $pad0 = $nPart * $lenPart - $len;

    $fullstr = $id;
    for ($i = 0; $i < $pad0; $i++) {
        $fullstr = "0$fullstr";
    }

    $ret = '';
    for ($i = 0; $i < $nPart - 1; $i++) {
        $ret .= substr($fullstr, $i * $lenPart, $lenPart).'/';
    }

    return trim($ret, '/');
}

function ByteSize($bytes, $afterPoint = 2)
{
    $size = $bytes / 1024;
    if ($size < 1024) {
        $size = number_format($size, $afterPoint);
        $size .= ' KB';
    } else {
        if ($size / 1024 < 1024) {
            $size = number_format($size / 1024, $afterPoint);
            $size .= ' MB';
        } elseif ($size / 1024 / 1024 < 1024) {
            $size = number_format($size / 1024 / 1024, $afterPoint);
            $size .= ' GB';
        } elseif ($size / 1024 / 1024 / 1024 < 1024) {
            $size = number_format($size / 1024 / 1024 / 1024, $afterPoint);
            $size .= ' TB';
        }
    }

    return $size;
}

class Cartesian
{
    public static function build($set)
    {
        if (! $set) {
            return [[]];
        }

        $subset = array_shift($set);
        $cartesianSubset = self::build($set);

        $result = [];
        foreach ($subset as $value) {
            foreach ($cartesianSubset as $p) {
                array_unshift($p, $value);
                $result[] = $p;
            }
        }

        return $result;
    }
}

function isTestingDb(){
    $domain = UrlHelper1::getDomainHostName();
    if (isset($GLOBALS['mMapDomainDb']) && isset($GLOBALS['mMapDomainDb'][$domain])) {
        return $GLOBALS['mMapDomainDb'][$domain]['db_name'] == env('DB_DATABASE_GLX_TESTING');
    }
    return 0;
}

function getDbNameWithDomain()
{
    $domain = UrlHelper1::getDomainHostName();
    if (isset($GLOBALS['mMapDomainDb']) && isset($GLOBALS['mMapDomainDb'][$domain])) {
        return $GLOBALS['mMapDomainDb'][$domain]['db_name'];
    }

    return null;
}

function getSiteIDByDomain()
{
    $domain = UrlHelper1::getDomainHostName();
    if (isset($GLOBALS['mMapDomainDb']) && isset($GLOBALS['mMapDomainDb'][$domain])) {
        return $GLOBALS['mMapDomainDb'][$domain]['siteid'];
    }

    return null;
}

function getLayoutNameReturnDefaultIfNull($default = 'default')
{
    if ($lay = getLayoutName()) {
        return $lay;
    }

    return $default;
}

function getLayoutNameMultiReturnDefaultIfNull($default = 'default')
{
    $view = 'layouts_multi.'.$default;
    if ($lay = getLayoutName()) {
        $view0 = 'layouts_multi.'.$lay;
        if (view()->exists($view)) {
            $view = $view0;
        }
    }

    //    die("Vvvv = $view");
    return $view;
}

function getLayoutName()
{
    $domain = UrlHelper1::getDomainHostName();
    if (isset($GLOBALS['mMapDomainDb']) && isset($GLOBALS['mMapDomainDb'][$domain]) && $GLOBALS['mMapDomainDb'][$domain]['layout_name']) {
        return $GLOBALS['mMapDomainDb'][$domain]['layout_name'];
    }

    return null;
}

function getLogoDomain($returnDefault = '/images/logo/default_logo.png')
{
    $domain = UrlHelper1::getDomainHostName();
    if (isset($GLOBALS['mMapDomainDb']) && isset($GLOBALS['mMapDomainDb'][$domain]) && isset($GLOBALS['mMapDomainDb'][$domain]['logo'])) {
        return $GLOBALS['mMapDomainDb'][$domain]['logo'];
    }

    return $returnDefault;
}

function parse_size_($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function get_file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = parse_size_(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size_(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    if ($max_size < 0) {
        return 0;
    }

    return $max_size;
}

class clsConfigTimeFrame
{
    public static $class_data;

    public static $class_meta;

    public static $time_frame_type; //full/one

    public static $time_frame_range; //month/day

    //Trường phân loại bổ xung 1
    public static $cat1_field = 'cat1';

    public static $title;
}

class clsHtmlHelper
{
    /**
     * Hiển thị 1 bảng header html theo 1 cấu trúc của 1 mảng
     */
    public static function showTableHeaderHtmlWithArrayInputSample()
    {

        $mm = [
            'Thành x viên' => null,
            'Mã x số' => null,
            'Tháng' => null,
            'Lương cơ bản' => null,
            'Các khoản cộng' => [
                'Lương ca' => ['Đơn giá' => null, 'Số Ca' => null, 'Thành tiền' => null],
                'Tăng ca' => ['Đơn giá' => null, 'Số Ca1' => null, 'Thành tiền' => null],
                'Tăng ca lễ' => ['Đơn giá' => null, 'Số Ca2' => null, 'Thành tiền' => null],
                'Tổng cộng' => null,
            ],
            'Các khoản trừ' => [
                'Tiền cơm' => ['Đơn giá' => null, 'Số lượng' => null, 'Thành tiền' => null],
                'Tiền điện' => null,
                'Tiền nước' => null,
                'Tạm ứng' => null,
                'Chứng chỉ' => null,
                'Tổng trừ' => null,
            ],
            'Tổng hợp' => null,
        ];

        $totalCol = 0;
        echo "<table border='1' style='text-align: center'>";
        echo "\n<tr>";
        foreach ($mm as $name => $row0) {

            $span_row = $span_col = 0;
            if ($row0) {
                foreach ($row0 as $name1 => $row1) {
                    if ($row1) {
                        foreach ($row1 as $name2 => $row2) {
                            $span_col++;
                        }
                    } else {
                        $span_col++;
                    }
                }
            } else {
                $span_row = 3;
            }

            if (! $span_row) {
                $span_row = 1;
            }
            if (! $span_col) {
                $span_col = 1;
            }

            echo "\n<td colspan='$span_col' rowspan='$span_row'> $name </td> ";

            $totalCol += $span_col;

        }

        echo "\n</tr>";

        echo "\n\n<tr>";
        foreach ($mm as $name => $row0) {
            //    echo " $name | ";
            if ($row0) {

                foreach ($row0 as $name1 => $row1) {
                    $span_row = $span_col = 0;
                    if ($row1) {
                        foreach ($row1 as $name2 => $row2) {
                            $span_col++;
                        }
                    } else {
                        $span_row = 2;
                    }

                    if (! $span_row) {
                        $span_row = 1;
                    }
                    if (! $span_col) {
                        $span_col = 1;
                    }
                    echo "\n<td  colspan='$span_col' rowspan='$span_row' > $name1  </td> ";
                }

            }
        }
        echo "\n</tr>";

        echo "\n\n<tr>";
        foreach ($mm as $name => $row0) {
            //    echo " $name | ";
            if ($row0) {

                foreach ($row0 as $name1 => $row1) {
                    if ($row1) {
                        foreach ($row1 as $name2 => $row2) {

                            echo "\n<td colspan='1'> $name2 </td> ";
                        }
                    }

                }

            }
        }
        echo "\n</tr>";

        echo "\n<tr>\n";
        echo str_repeat('<td> 1... </td>', $totalCol);
        echo '</tr>';

        echo "\n<tr>\n";
        echo str_repeat('<td> 2... </td>', $totalCol);
        echo '</tr>';

        echo "\n</table>";

    }
}

function getDomainHostName()
{
    if (isset($_SERVER['HTTP_HOST'])) {
        return explode(':', $_SERVER['HTTP_HOST'])[0];
    }
    if (isset($_SERVER['SERVER_NAME'])) {
        return $_SERVER['SERVER_NAME'];
    }

    return null;
}

class clsPaginator
{
    public static function getPageRange($totalPage, $currentPage = 1, $range = 10)
    {

        if (! $totalPage) {
            return [0, 0];
        }

        if ($currentPage < floor($range / 2)) {
            $from = 1;
            $to = $totalPage < $range ? $totalPage : $range;

            return [$from, $to];
        }
        if ($currentPage > $totalPage - floor($range / 2)) {
            $from = $totalPage - $range;
            if ($from <= 0) {
                $from = 1;
            }
            $to = $totalPage;

            return [$from, $to];
        }

        $from = $currentPage - floor($range / 2);
        if ($from <= 0) {
            $from = 1;
        }
        $to = $currentPage + floor($range / 2);

        return [$from, $to];
    }

    public static function getNextPage($uriBase, $nPage, $cPage)
    {
        $padPage = '&page=';
        $padEnd = '&';
        if (strstr($uriBase, '/page/')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '/page/';
            $padEnd = '/';
        } elseif (strstr($uriBase, '?page=')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '?page=';
            $padEnd = '&';
        } else {
            if (strstr($uriBase, '?')) {
                return $uriBase.'&page=2';
            } else {
                return $uriBase.'?page=2';
            }
        }
        $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage + 1, $padPage, "$padEnd");

        return $uriOK;
    }

    public static function getPrevPage($uriBase, $nPage, $cPage)
    {
        $padPage = '&page=';
        $padEnd = '&';
        if (strstr($uriBase, '/page/')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '/page/';
            $padEnd = '/';
        } elseif (strstr($uriBase, '?page=')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '?page=';
            $padEnd = '&';
        }

        $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage - 1, $padPage = 0, "$padEnd");

        return $uriOK;
    }

    //2020
    public static function showPaginatorBasicStyle($uriBase = null, $totalItem = 0, $limitPerPage = 0, $currentPage = 0, $rangeShow = 5)
    {

        if ($totalItem <= 0) {
            return null;
        }

        $mm = clsPaginator::getArrayLinkPaginator($uriBase, $totalItem, $limitPerPage, $currentPage, $rangeShow);
        $ret = "<div class='paginator_glx'>";
        foreach ($mm as $k => $link) {

            $show = $k;
            if ($k == 'first') {
                $show = " <i class='fa fa-caret-left'></i> <i class='fa fa-caret-left'></i>";
            }
            if ($k == 'last') {
                $show = "<i class='fa fa-caret-right'></i><i class='fa fa-caret-right'></i>";
            }
            if ($k == 'prev') {
                $show = "<i class='fa fa-caret-left'> </i> ";
            }
            if ($k == 'next') {
                $show = " <i class='fa fa-caret-right'></i>";
            }
            $padClass = '';
            if ($k == 'current') {
                $padClass = 'pg_selecting';
                $show = " <b>$currentPage</b>";
            }

            if ($k == 'empty1' || $k == 'empty2') {
                $ret .= ' ... ';
            } else {
                $ret .= " <a class='link_pg $padClass' style='text-decoration: none' href='$link'>$show</a>";
            }
        }

        $fromItem = $limitPerPage * ($currentPage - 1) + 1;
        $toItem = $limitPerPage * ($currentPage);
        if ($toItem > $totalItem) {
            $toItem = $totalItem;
        }

        $ret .= " <span> Show <b>$fromItem - $toItem </b> of <b>$totalItem</b> </span>";

        $ret .= '</div>';

        return $ret;
    }

    /**
     * === Đưa ra một mảng limit, offset của 2 bảng, khi số trang được nhập vào
     * Mục đích: thông thường ta phân trang 1 bảng dễ dàng với limit, offset
     * Tuy nhiên, khi có 2 bảng ta cần nối nhau phân trang, bảng 1 rồi đến bảng 2
     * Vậy cần phân trang bảng 1 trước, hết trang có phần tử bảng 1 thì sẽ đến phần tử bảng 2
     * Như vậy tại mỗi trang, sẽ có 3 trường hợp tuần tự:
     * - chỉ có phần tử bảng 1 (là những trang đầu)
     * - hoặc có cả 2 phần tử thuộc bảng 1 và 2 (có 1 trang này , hoặc không có nếu số phần tử bảng 1 chia hết cho $limit)
     * - và cuối cùng là các trang thuộc bảng thứ 2
     * === Kết quả hàm này sẽ đưa ra offset, limit của 2 bảng để query db
     * Với đầu vào là $totalInTab1, $limit, và số $currentPage = trang hiện tại, chạy từ 1 đến N (N sẽ dừng khi hết bảng 2)
     *
     * @return array
     */
    public static function createArrayLimitOffset2TableToQueryPaginator($totalInTab1, $limit, $currentPage)
    {
        $limit1 = $limit2 = $offset1 = $offset2 = -1;
        //Tính Trang cuối có chứa phần tử của tbl1
        $lastPageHaveElmOfTbl1 = ceil($totalInTab1 / $limit);
        //Tính Trang đầu tiên xuất hiện phần tử của tbl2
        if ($totalInTab1 % $limit == 0) {
            $firstPageHaveElmOfTbl2 = $lastPageHaveElmOfTbl1 + 1;
            //Limit của trang đầu tiên có phần tử ở tbl2
            $firstLimitOfTbl2InFisrtHaveElm2 = $limit;
        } else {
            $firstPageHaveElmOfTbl2 = $lastPageHaveElmOfTbl1;
            //Limit tbl2 của trang đầu tiên xuất hiện phần tử của tbl2, là (Limit- số Dư của $totalInTab1 / $limit)
            $firstLimitOfTbl2InFisrtHaveElm2 = $limit - $totalInTab1 % $limit;
        }

        //Nếu trang hiện tại nhỏ hơn trang xuất hiện phần tử đầu tiên của tbl2, thì data sẽ ko có phần tử nào thuộc tbl2
        if ($currentPage < $firstPageHaveElmOfTbl2) {
            $limit1 = $limit;
            $offset1 = $limit1 * ($currentPage - 1);
        }
        //Nếu trang hiện tại = trang xuất hiện phần tử đầu tiên của tbl2
        //Thì có 2 trường hợp, có hoặc không còn phần tử tbl1
        elseif ($currentPage == $firstPageHaveElmOfTbl2) {
            $offset2 = 0;
            //Không còn phần tử của Tbl1
            if ($firstLimitOfTbl2InFisrtHaveElm2 == $limit) {
                //Khi này thì limit = gốc, và offset sẽ tính ra
                $limit2 = $limit;
            } //Còn phần tử của tbl1
            else {
                $limit1 = $totalInTab1 % $limit;
                $offset1 = $limit * ($currentPage - 1);
                $limit2 = $firstLimitOfTbl2InFisrtHaveElm2;
            }
        }
        //Nếu trang hiện tại lớn hơn trang đầu tiên có phần tử tbl2, thì đơn giản
        //chỉ cần tính bình thường
        elseif ($currentPage > $firstPageHaveElmOfTbl2) {
            $limit2 = $limit;
            $offset2 = $limit * ($currentPage - 1) - $totalInTab1;
        }

        return ['offset1' => $offset1, 'limit1' => $limit1, 'offset2' => $offset2, 'limit2' => $limit2];
    }

    public static function createArrayLimitOffset2TableToQueryPaginatorTester()
    {

        //Thay đổi số phần tử bảng 1 là đủ số liệu
        for ($totalInTable1 = 1; $totalInTable1 < 30; $totalInTable1++) {
            $limit = 10;
            $mRet = [];
            $totalElmIn2Table = 0;
            for ($i = 1; $i < 10; $i++) {
                $mRet[] = clsPaginator::createArrayLimitOffset2TableToQueryPaginator($totalInTable1, $limit, $i);
                $totalElmIn2Table += $limit;
            }
            //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //    print_r($mRet);
            //    echo "</pre>";
            $limit1 = $limit2 = 0;
            $foundElmInTable1 = $foundElmInTable2 = 0;
            for ($i = 0; $i < count($mRet); $i++) {
                $ret = $mRet[$i];
                //            echo "<br/>\n $i ---";
                //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //        print_r($ret);
                //        echo "</pre>";

                if ($i > 1) {
                    if ($ret['offset1'] > 0) {
                        //Kiểm tra xem offset mới có bằng offset cũ + limit không
                        if ($ret['offset1'] != $offset1 + $limit1) {
                            exit(" Có lỗi 1 - $i : $offset1 + $limit vs ".$ret['offset1']);

                            return 0;
                        }
                    }
                    if ($ret['offset2'] > 0) {
                        //Kiểm tra xem offset mới có bằng offset cũ + limit không
                        if ($ret['offset2'] != $offset2 + $limit2) {
                            exit(" Có lỗi 2 - $i : $offset2 + $limit vs ".$ret['offset2']);

                            return 0;
                        }
                    }
                }

                $limit1 = $ret['limit1'];
                $limit2 = $ret['limit2'];
                $offset1 = $ret['offset1'];
                $offset2 = $ret['offset2'];

                if ($limit2 >= 0 && $offset2 >= 0) {
                    $foundElmInTable2 += $limit2;
                    //                echo "<br/>\n --- $foundElmInTable2 ";
                }

                if ($limit1 >= 0 && $offset1 >= 0) {
                    $foundElmInTable1 += $limit1;
                    //                echo "<br/>\n --- $foundElmInTable1 ";
                }

            }

            if ($foundElmInTable1 != $totalInTable1) {
                exit(" Có lỗi 3 foundElmInTable1 != totalInTable1 : $foundElmInTable1 != $totalInTable1");

                return 0;
            }

            //        echo "<br/>\n TotalElm1 = $foundElmInTable1";
            //        echo "<br/>\n TotalElm2 = $foundElmInTable2";

            if ($foundElmInTable1 + $foundElmInTable2 == $totalElmIn2Table) {
                echo "<br/>\n OK số phần tử 2 table: $foundElmInTable1 + $foundElmInTable2 == $totalElmIn2Table";
            } else {
                exit("<br/>\n Có lỗi số phần tử 2 table cộng lại ko bằng tổng? ($foundElmInTable1 + $foundElmInTable2 == $totalElmIn2Table)");

                return 0;
            }
        }
    }

    /*
     * LAD 2020
     */
    public static function getArrayLinkPaginator($uriBase = null, $totalItem = 0, $limitPerPage = 0, &$currentPage = 0, $rangeShow = 5)
    {

        $nPage = ceil($totalItem / $limitPerPage);

        if ($currentPage > $nPage) {
            $currentPage = $nPage;
        }

        //echo "<br/>\n N PAg = $nPage, Cpage = $currentPage";

        $arrayWillShow = [];

        if ($rangeShow > $totalItem) {
            $rangeShow = $totalItem;
        }

        if ($currentPage < floor($rangeShow / 2)
            || $nPage <= $rangeShow
        ) {
            $page1 = 1;
        } else {
            $page1 = ceil($currentPage - $rangeShow / 2);
        }

        if ($nPage - $page1 < $rangeShow) {
            $page1 = $nPage - $rangeShow + 1;
        }

        if ($page1 < 1) {
            $page1 = 1;
        }

        $pageEnd = $page1 + $rangeShow - 1;

        if ($pageEnd > $nPage) {
            $pageEnd = $nPage;
        }

        $prevPage = $currentPage - 1;
        $nextPage = $currentPage + 1;
        if ($prevPage < 1) {
            $prevPage = 1;
        }

        if ($nextPage > $nPage) {
            $nextPage = $nPage;
        }

        if ($nextPage <= 0) {
            $nextPage = 1;
        }
        if ($pageEnd <= 0) {
            $pageEnd = $nPage;
        }

        //echo "<br/>\n Page1 = $page1 -> $pageEnd";

        //echo "<br/>\n Prev / Next  = $prevPage / $nextPage";

        //    $toMax = ($rangeShow + $currentPage) > $totalItem  ? $totalItem: ($rangeShow + $currentPage);
        //
        //    echo "<br/>\n $rangeShow + $currentPage / Max = $toMax";
        //
        //    for($i = $currentPage; $i<= $toMax; $i++){
        //        echo "<br/>\n rang: $i ";
        //    }

        $mm = [];

        $p = ctoolUrl::setUrlParam($uriBase, 'page', 1);
        $mm['first'] = $p;
        //
        $p = ctoolUrl::setUrlParam($uriBase, 'page', $prevPage);
        $mm['prev'] = $p;

        if ($page1 > 1) {
            $mm['empty1'] = '#';
        }

        //echo "<br/>\n Page1 = $page1 / $pageEnd / $currentPage";

        //for($i = 1; $i<=$nPage; $i++){
        for ($i = $page1; $i <= $pageEnd; $i++) {
            $p = ctoolUrl::setUrlParam($uriBase, 'page', $i);

            if ($currentPage == $i) {
                $mm['current'] = $p;
            } else {
                $mm[$i] = $p;
            }

        }

        if ($pageEnd < $nPage - 1) {
            $mm['empty2'] = '#';
        }

        if ($pageEnd < $nPage) {
            $p = ctoolUrl::setUrlParam($uriBase, 'page', $nPage);
            $mm[$nPage] = $p;
        }

        $p = ctoolUrl::setUrlParam($uriBase, 'page', $nextPage);
        $mm['next'] = $p;
        $p = ctoolUrl::setUrlParam($uriBase, 'page', $nPage);
        $mm['last'] = $p;

        //ctoolUrl::setUrlParam();
        return $mm;
    }

    public static function getPaginatorStringUlLi($uriBase, $nPage, $cPage, $limitPerPage, $totalItem, $range = 10)
    {

        if ($totalItem <= $limitPerPage) {
            return '';
        }
        echo "\n<ul class='pagination' data-code-pos='ppp17284349474731'>";

        $padPage = '&page=';
        $padEnd = '&';
        if (strstr($uriBase, '/page/')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '/page/';
            $padEnd = '/';
        } elseif (strstr($uriBase, '?page=')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '?page=';
            $padEnd = '&';
        }

        //Xoa het dau hieu page, để điền lại:
        //$uriBase = classStr::replaceStringBetween2String($uriBase,"/page/to/del","$padPage","&");
        //$uriBase = str_replace("/page/to/del", "", $uriBase);

        if (! strstr($uriBase, '?')) {
            $uriBase .= '?';
        }
        if (! strstr($uriBase, "$padPage")) {
            $uriBase .= "$padPage";
        }

        $strPaginator = '';

        $arrRange = clsPaginator::getPageRange($nPage, $cPage);

        $maxPage1 = $nPage < 3 ? $nPage : 3;
        //for ($i = $arrRange[0]; $i <= $arrRange[1]; $i++) {
        for ($i = 1; $i <= $maxPage1; $i++) {
            $uriOK = classStr::replaceStringBetween2String($uriBase, $i, $padPage, "$padEnd");
            if ($cPage == $i) {
                //$strPaginator .= " <b>[$i]</b> ";
                $strPaginator .= "<li class='page-item active'><a title='Go to $i' class='page-link'>$i</a></li>";
            } else {
                $strPaginator .= "<li class='page-item'><a title='Go to $i' class='page-link' href='$uriOK'>$i</a></li>";
                //$strPaginator .= "<a href='$uriOK'>" . $i . "</a>";
            }
        }

        //        if($nPage > 3)
        //            $strPaginator.= " ... ";

        if ($cPage > 3) {
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage, $padPage, "$padEnd");
            //$strPaginator .= "<a class=\"active\" href='$uriOK'>" . $cPage . "</a>";
            $strPaginator .= "<li class='page-item active'><a title='Go to $cPage' class='page-link' href='$uriOK'>$cPage</a></li>";
        }

        if ($cPage < $nPage) {
            $strPaginator .= ' ... ';
        }

        if ($nPage > $arrRange[1]) {
            //$strPaginator.= " ... <a href='$uriBase" . $nPage . "'>" . $nPage . "</a> ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $nPage, $padPage, "$padEnd");
            //$strPaginator.= "<a href='$uriOK'>" . $nPage . "</a>";
            $strPaginator .= "<li class='page-item'><a title='Go to $nPage'  class='page-link' href='$uriOK'>$nPage</a></li>";
        }

        if ($cPage >= $nPage) {
            $nextButton = "        <li class='page-item'>
            <a title='Next'  class='page-link' href='#' aria-label='Next'>
                <span  aria-hidden='true'>›</span>
            </a>
        </li>";
        } else {
            //$nextButton = " | <a href='$uriBase" . ($cPage + 1) . "'> Next</a> ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage + 1, $padPage, "$padEnd");
            $nextButton = "        <li class='page-item'>
            <a title='Next' class='page-link' href='$uriOK' aria-label='Next'>
                <span aria-hidden='true'>›</span>

            </a>
        </li>";
        }

        if ($cPage <= 1) {
            $preButton = "         <li class='page-item'>
            <a title='Previous' class='page-link' href='#' aria-label='Previous'>
                <span title='Previos' aria-hidden='true'>‹</span>
            </a>
        </li> ";
        } else {
            //$preButton = "<a href='$uriBase" . ($cPage - 1) . "'>Prev</a> | ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage - 1, $padPage, "$padEnd");
            $preButton = "        <li class='page-item'>
            <a title='Previous' class='page-link' href='$uriOK' aria-label='Previous'>
                <span title='Previos' aria-hidden='true'>‹</span>
            </a>
        </li>";
        }

        if ($arrRange[0] > 1) {
            $preButton .= '...';
        }

        $uriOK = classStr::replaceStringBetween2String($uriBase, 1, $padPage, "$padEnd");
        //$firstButton = "<a href='$uriOK'>&#171;</a>";
        $firstButton = "<li class='page-item'><a title='First' class='page-link' href='$uriOK'> « </a></li>";

        $uriOK = classStr::replaceStringBetween2String($uriBase, $nPage, $padPage, "$padEnd");
        //$firstButton = " | <a href='$uriOK" . 1 . "'> First</a> ";
        //$lastButton = "<a href='$uriBase" . $nPage . "'>Last</a> ";
        //$lastButton = "<a href='$uriOK'>&#187;</a>";
        $lastButton = "<li class='page-item'><a title='Last' class='page-link' href='$uriOK'> » </a></li>";

        $fromI = $limitPerPage * ($cPage - 1) + 1;
        $toI = $limitPerPage * $cPage;
        $toI = ($toI < $totalItem ? $toI : $totalItem);

        //        $firstButton = $lastButton = null;

        $strPaginator = "$firstButton $preButton $strPaginator $nextButton $lastButton";

        echo $strPaginator;

        echo "\n</ul>";
    }

    /*
     * Tạo str paginator
     */
    public static function getPaginatorString($uriBase, $nPage, $cPage, $limitPerPage, $totalItem, $range = 10)
    {

        if ($totalItem <= $limitPerPage) {
            return '';
        }

        $padPage = '&page=';
        $padEnd = '&';
        if (strstr($uriBase, '/page/')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '/page/';
            $padEnd = '/';
        } elseif (strstr($uriBase, '?page=')) {
            //classStr::replaceStringBetween2String("")
            $padPage = '?page=';
            $padEnd = '&';
        }

        //Xoa het dau hieu page, để điền lại:
        //$uriBase = classStr::replaceStringBetween2String($uriBase,"/page/to/del","$padPage","&");
        //$uriBase = str_replace("/page/to/del", "", $uriBase);

        if (! strstr($uriBase, '?')) {
            $uriBase .= '?';
        }
        if (! strstr($uriBase, "$padPage")) {
            $uriBase .= "$padPage";
        }

        $strPaginator = '';

        $arrRange = clsPaginator::getPageRange($nPage, $cPage);

        $maxPage1 = $nPage < 3 ? $nPage : 3;
        //for ($i = $arrRange[0]; $i <= $arrRange[1]; $i++) {
        for ($i = 1; $i <= $maxPage1; $i++) {

            $uriOK = UrlHelper1::setUrlParam($uriBase, 'page', $i);
            //            $uriOK = classStr::replaceStringBetween2String($uriBase, $i, $padPage, "$padEnd");
            if ($cPage == $i) {
                //$strPaginator .= " <b>[$i]</b> ";
                $strPaginator .= "<a class=\"active\">$i</a>";
            } else {
                $strPaginator .= "<a href='$uriOK'>".$i.'</a>';
            }
        }

        if ($nPage > 3) {
            $strPaginator .= ' ... ';
        }

        if ($cPage > 3) {
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage, $padPage, "$padEnd");
            $strPaginator .= "<a class=\"active\" href='$uriOK'>".$cPage.'</a>';
        }

        if ($cPage < $nPage) {
            $strPaginator .= ' ... ';
        }

        if ($nPage > $arrRange[1]) {
            //$strPaginator.= " ... <a href='$uriBase" . $nPage . "'>" . $nPage . "</a> ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $nPage, $padPage, "$padEnd");
            $strPaginator .= "<a href='$uriOK'>".$nPage.'</a>';
        }

        if ($cPage >= $nPage) {
            $nextButton = "<a href='#'>&#155;</a>";
        } else {
            //$nextButton = " | <a href='$uriBase" . ($cPage + 1) . "'> Next</a> ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage + 1, $padPage, "$padEnd");
            $uriOK = UrlHelper1::setUrlParam($uriBase, 'page', $cPage + 1);
            $nextButton = "<a href='$uriOK'>&#155;</i></a>";
        }

        if ($cPage <= 1) {
            $preButton = " <a href='#'>&#139;</a> ";
        } else {
            //$preButton = "<a href='$uriBase" . ($cPage - 1) . "'>Prev</a> | ";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $cPage - 1, $padPage, "$padEnd");
            $uriOK = UrlHelper1::setUrlParam($uriBase, 'page', $cPage - 1);
            $preButton = "<a href='$uriOK'>&#139;</a>";
        }

        if ($arrRange[0] > 1) {
            $preButton .= '...';
        }

        $uriOK = classStr::replaceStringBetween2String($uriBase, 1, $padPage, "$padEnd");
        $firstButton = "<a href='$uriOK'>&#171;</a>";
        //$firstButton = "<a href='$uriBase" . 1 . "'>First</a> ";

        $uriOK = classStr::replaceStringBetween2String($uriBase, $nPage, $padPage, "$padEnd");
        //$firstButton = " | <a href='$uriOK" . 1 . "'> First</a> ";
        //$lastButton = "<a href='$uriBase" . $nPage . "'>Last</a> ";

        $lastButton = " <a href='$uriOK'>&#187;</a>";

        $fromI = $limitPerPage * ($cPage - 1) + 1;
        $toI = $limitPerPage * $cPage;
        $toI = ($toI < $totalItem ? $toI : $totalItem);

        if ($cPage != $nPage) {
            $strPaginator = "$firstButton $preButton $strPaginator <a href='$uriOK'>$nPage</a>  $nextButton $lastButton";
        } else {
            $strPaginator = "$firstButton $preButton $strPaginator  $nextButton $lastButton";
        }

        $strSelect = "<select class='select_glx' onChange=\"window.location.href=this.value\">";

        for ($i = 1; $i <= $nPage; $i++) {
            //$link = "<a href='$uriBase?&page=$i'>$uriBase?&page=$i </a>";
            $uriOK = classStr::replaceStringBetween2String($uriBase, $i, $padPage, "$padEnd");
            //$uriOK = "<a href='$uriOK'> $uriOK </a> ";
            $pad = '';
            if ($cPage == $i) {
                $pad = ' selected ';
            }
            $strSelect .= "<option $pad value='$uriOK'>Page $i</option>";
        }

        $strSelect .= '</select>';
        $strPaginator .= $strSelect;
        //$strPaginator .= " <br/> [ $fromI - $toI of $totalItem ]";

        return ''.$strPaginator." <span class='pg_total'> $totalItem</span>";

    }
}

function getUUidGlx($n = 13)
{
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    return substr($uuid, 0, $n); // Lấy 18 ký tự đầu
}

function isUUidStr($id)
{
    if(!$id)
        return false;
    if(str_contains($id, '-') && strlen($id) > 10){
        return true;
    }
    return false;
}

class classStr
{

    public static function getRandStr($length)
    {
        $token = "";

        //$codeAlphabet = "ABCDEFGHJKMNPQRSTUVXYZ";
        $codeAlphabet = "";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        //$codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    public static function getRandNum($length)
    {
        $token = "";
        $codeAlphabet = "0123456789";
        //$codeAlphabet = "";
        //$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        //$codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    public static function toSlugPhp5($string, $getFromCache = 0)
    {

        if (! $string) {
            return null;
        }

        if ($getFromCache == 1) {
            $md5 = STH($string);
            $filesl = "/mnt/glx/cache/cache_slug/$md5";
            if (! file_exists(dirname($filesl))) {
                mkdir(dirname($filesl));
            }
            if (file_exists($filesl)) {
                return trim(file_get_contents($filesl));
            }
        }

        $string = trim(strtolower($string));
        $table = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'Đ' => 'd',
            'đ' => 'd', ' ' => '-',
        ];

        // -- Remove duplicated spaces
        //$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

        //$stripped = preg_replace('/[^a-z0-9-\/\s]/', '', $stripped);
        // -- Returns the slug
        $ret = strtr($string, $table);
        $ret = preg_replace('/[^a-z0-9-\/\s]/', '', $ret);
        $ret = str_replace('--', '-', $ret);
        $ret = str_replace('--', '-', $ret);
        $ret = str_replace('--', '-', $ret);

        if ($getFromCache == 1) {
            file_put_contents($filesl, $ret);
        }

        return $ret;
    }

    public static function toSlug($string, $getFromCache = 0)
    {

        if (! $string) {
            return null;
        }

        if ($getFromCache == 1) {
            $md5 = STH($string);
            $filesl = "/mnt/glx/cache/cache_slug/$md5";
            if (! file_exists(dirname($filesl))) {
                mkdir(dirname($filesl));
            }
            if (file_exists($filesl)) {
                return trim(file_get_contents($filesl));
            }
        }

        $string = trim(mb_strtolower($string));
        $table = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd', ' ' => '-',
        ];

        // -- Remove duplicated spaces
        //$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

        //$stripped = preg_replace('/[^a-z0-9-\/\s]/', '', $stripped);
        // -- Returns the slug
        $ret = strtr($string, $table);
        $ret = preg_replace('/[^a-z0-9-\/\s]/', '', $ret);
        $ret = str_replace('--', '-', $ret);
        $ret = str_replace('--', '-', $ret);
        $ret = str_replace('--', '-', $ret);

        $ret = trim($ret, '-');

        if ($getFromCache == 1) {
            file_put_contents($filesl, $ret);
        }

        return $ret;
    }

    //https://stackoverflow.com/questions/15737408/php-find-all-occurrences-of-a-substring-in-a-string
    //cstring::findAllStrInStr($txt, ["<pre>", '<pre ']);
    public static function findAllStrInStr($str, $needleOrArray)
    {

        $lastPos = 0;
        $positions = [];
        //        while (($lastPos = strpos($str, $needle, $lastPos))!== false) {

        $len = strlen($str);
        $cc = 0;
        while (1) {
            //đề phòng lỗi loop:
            if ($cc > $len) {
                break;
            }
            $cc++;

            if (is_array($needleOrArray)) {
                $pos = false;
                foreach ($needleOrArray as $needle0) {
                    if (! strlen($needle0)) {
                        continue;
                    }

                    $needle = $needle0;
                    $pos = strpos($str, $needle0, $lastPos);
                    if ($pos !== false) {
                        $lastPos = $pos;
                        break;
                    }
                }
                if ($pos === false) {
                    $lastPos = false;
                }

            } else {
                $needle = $needleOrArray;

                if (! strlen($needle)) {
                    return null;
                }

                $lastPos = strpos($str, $needle, $lastPos);
            }

            if ($lastPos === false) {
                break;
            }
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }

        return $positions;
    }

    public static function splitStringToTrunkNumberWord($str, $numberWordTrunk = 500, $includeDot = 0)
    {

        if ($includeDot) { //not fit with domain name...
            $str = str_replace('.<', '. <', $str);
            $str = str_replace(".\n", ". \n", $str);
            $str = str_replace(".\r", ". \r", $str);
        }

        $mm = explode(' ', $str);
        $m1 = [];
        $len = count($mm);

        //echo "<br/>\n TT = $len";

        $start = 0;
        $cc = 0;
        while (1) {
            $cc++;
            //echo "<br/>\n $cc ....";
            $str = '';
            for ($i = $start; $i < $start + $numberWordTrunk && $i < $len; $i++) {
                $str .= $mm[$i].' ';
            }

            //
            if ($includeDot) {
                for ($j = $i; $j < $len; $j++) {

                    //if(strstr($mm[$j], '.') !== false){
                    if (substr($mm[$j], -1) == '.') {
                        $start++;
                        $str .= $mm[$j].' ';
                        break;
                    }
                    $start++;
                    $str .= $mm[$j].' ';
                }
            }

            $start += $numberWordTrunk;
            //outputNotEndLine("/share/2.txt", $str);

            //$str = str_replace("\n ", "\n", $str);

            $m1[] = $str;
            if ($start > $len) {
                break;
            }

        }

        return $m1;
    }

    public static function substr_ucwords($str)
    {
        return ucwords(mb_strtolower($str));
    }

    /**
     * Chức năng: cắt đủ đến character cuối cùng
     *
     * @param  int  $from  : vị trí character
     * @param  $n:  số character
     * @param  int  $with3Dot  : thêm 3 chấm khi cần
     * @return false|string
     */
    public static function substr_fit_char_unicode($str, $from = 0, $n = 0, $with3Dot = 0)
    {
        $ret = mb_substr($str, 0, $n);
        if ($with3Dot) {
            if (strlen($ret) < strlen($str)) {
                return $ret.'...';
            }
        }

        return $ret;
    }

    /**
     * Chức năng: cắt đủ đến word cuối cùng, với unicode ok
     *
     * @param  int  $from  : vị trí character, không phải số word
     * @param  $n  : chú ý , n ở đây là số character, không phải số word
     * @param  int  $with3Dot:  thêm 3 chấm khi cần
     * @return false|mixed|string
     */
    public static function substr_fit_word_unicode($str, $from = 0, $n = 0, $with3Dot = 0)
    {

        if ($n > mb_strlen($str)) {
            $n = mb_strlen($str);
        }

        $pos = mb_strpos($str, ' ', $n);
        if ($pos !== false) {
            if ($pos > $n + 10) {
                $ret = mb_substr($str, 0, $n);
            }
            $ret = mb_substr($str, 0, $pos);
        } else {
            if (mb_strlen($str) > $n + 10) {
                $ret = mb_substr($str, 0, $n);
            }
            $ret = $str;
        }

        if ($with3Dot) {
            if (strlen($ret) < strlen($str)) {
                return $ret.'...';
            }
        }

        return $ret;
    }

    /**
     * Chức năng: cắt đủ đến word cuối cùng
     *
     * @param  int  $from  : vị trí character, không phải số word
     * @param  $n  : chú ý , n ở đây là số character, không phải số word
     * @param  int  $with3Dot:  thêm 3 chấm khi cần
     * @return false|mixed|string
     */
    public static function substr_fit_word($str, $from = 0, $n = 0)
    {
        if (! $str) {
            return null;
        }
        $pos = @strpos($str, ' ', $n);
        if ($pos !== false) {
            if ($pos > $n + 10) {
                return substr($str, 0, $n);
            }

            return substr($str, 0, $pos);
        } else {
            if (strlen($str) > $n + 10) {
                return substr($str, 0, $n);
            }

            return $str.'...';
        }
    }

    public static function getCurrentcyFormat($number)
    {
        return number_format($number, 0, ',', '.');
    }

    public static function convert_codau_khong_dau($str)
    {

        return \ClassUtilGlx::convert_codau_khong_dau($str);

    }

    /*
     * Tìm Chuỗi End xuất hiện Đầu tiên
abc12551155pbcq6655q55 =>
Array
(
    [0] => bc1255
    [1] => 12
)
     */
    public static function getStringBetween2String($str, $start, $end, $all = 0)
    {
        $str = str_replace("\n", '', $str);
        $str = str_replace("\r", '', $str);

        $regex = "/$start(.*?)$end/";
        if ($all) {
            $ret = preg_match_all($regex, $str, $matches);
        } else {
            $ret = preg_match($regex, $str, $matches);
        }
        if ($ret) {
            return $matches[1];
        }

        return null;
    }

    /*
     * Tìm chuỗi End xuất hiện cuối cùng (nếu có nhiều chuỗi End)
abc12551155pbcq6655q55
Array
(
    [0] => bc12551155pbcq6655q55
    [1] => 12551155pbcq6655q
)
     */
    public static function getStringBetween2StringType2($str, $start, $end, $all = 0)
    {
        $str = str_replace("\n", '', $str);
        $str = str_replace("\r", '', $str);

        $regex = "/$start(.*)$end/";
        if ($all) {
            $ret = preg_match_all($regex, $str, $matches);
        } else {
            $ret = preg_match($regex, $str, $matches);
        }
        if ($ret) {
            return $matches[1];
        }

        return null;
    }

    public static function getStringBetween2StringType3($str, $start, $end, $all = 0, $limit = 1000)
    {
        //        echo "<br/>\n $str, $start, $end,";

        $mret = [];
        $cc = 0;
        while (1) {
            $cc++;
            if ($cc > $limit) {
                break;
            }

            if (! $start) {
                $p1 = 0;
            } else {
                $p1 = strpos($str, $start);
            }

            if (! $end) {
                $p2 = strlen($str);
            } else {
                if ($x = strpos(substr($str, $p1), $end)) {
                    $p2 = $p1 + $x;
                } else {

                    if ($all) {
                        return $mret;
                    }

                    return null;
                }
            }

            $ret = substr($str, $p1 + strlen($start), $p2 - ($p1 + strlen($start)));

            $mret[] = $ret;

            if (! $all) {
                break;
            }

            $str = substr($str, $p2);

            usleep(1);
        }

        if ($all) {
            return $mret;
        }

        return $ret;
    }

    public static function addOrReplaceParamUrlV2NotFriendLy($url, $param, $val = null)
    {

        if (! strstr($url, '?')) {
            return $url."?$param=$val";
        }
        $query = explode('?', $url)[1];
        $url0 = explode('?', $url)[0];

        if (! strstr($query, '&')) {
            if (! strstr($query, '=')) {
                return $url;
            }

            [$p1, $v1] = explode('=', $query);
            if ($p1 == $param) {
                return $url0."?$param=$val";
            } else {
                return $url0."?$p1=$v1&$param=$val";
            }
        }

        $ar = explode('&', $query);
        $first = '?';
        $foundParamInOlrUrl = 0;
        foreach ($ar as $one) {
            [$p1, $v1] = explode('=', $one);
            if ($p1 == $param) {
                $url0 .= "$first$param=$val";
                $foundParamInOlrUrl = 1;
            } else {
                $url0 .= "$first$p1=$v1";
            }
            if ($first == '?') {
                $first = '&';
            }
        }

        if (! $foundParamInOlrUrl) {
            $url0 .= "$first$param=$val";
        }

        return $url0;
    }

    public static function addOrReplaceParamUrl($url, $param, $val = null, $friendLy = 0)
    {
        if (! strstr($url, "/$param/")
            && ! strstr($url, "?$param=")
            && ! strstr($url, "&$param=")
        ) {
            if ($val === null) {
                return $url;
            }

            if (strstr($url, '?') === false) {
                if ($friendLy) {
                    return $ret = str_replace('//', '/', $url."/$param/$val");
                }

                return $ret = $url."?$param=$val";
            } else {
                if ($friendLy) {
                    return $ret = str_replace('//', '/', $url."/$param/$val");
                }

                return $ret = $url."&$param=$val";
            }
        } else {
            if ($val === null) {
                $url = preg_replace("/\/$param\/(\w+)/", '/', $url);
                $url = preg_replace("/\?$param\=(\w+)/", '?', $url);
                $url = preg_replace("/\&$param\=(\w+)/", '&', $url);
            } else {
                $url = preg_replace("/\/$param\/(\w+)/", "/$param/$val", $url);
                $url = preg_replace("/\?$param\=(\w+)/", "?$param=$val", $url);
                $url = preg_replace("/\&$param\=(\w+)/", "&$param=$val", $url);
            }
        }
        $url = str_replace('&&', '&', $url);
        if ($url[strlen($url) - 1] == '&') {
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public static function replaceStringSample()
    {

        $str = 'qqq/abc/ppp/lllsdf/qqq/abc/ppp/lllsdf/';

        echo "<br/>$str";
        echo "<br/> Thay the 'abc' bang 'abc/12345'";

        echo '<br/>';
        echo preg_replace("/\/abc\/(\w+)/", '/abc/12345', $str);

        echo '<hr>';
        $string = 'April 15, 2003';
        echo "<br/>$string";
        echo '<br/>';
        $pattern = '/(\w+) (\d+), (\d+)/i';
        $replacement = '${1} 1,$3';
        echo preg_replace($pattern, $replacement, $string);
        echo '<hr>';

    }

    /*
        $linkOrg = "/abc/&sfilter=123&";
        $linkOrg = "/abc/&sfilter=123";
    */
    public static function replaceStringBetween2String($inputString, $replaceBy, $strStart, $strEnd)
    {

        if (! $inputString) {
            return $inputString;
        }

        if (! strstr($inputString, $strStart)) {
            return $inputString;
        }

        $len = strlen($inputString);
        $start = strpos($inputString, $strStart) + strlen($strStart);
        if (! $strEnd) {
            $end = strlen($inputString);
        } else {
            $end = strpos($inputString, $strEnd, $start);
            if (! $end) {
                $end = $len;
            }
        }

        return substr($inputString, 0, $start).$replaceBy.substr($inputString, $end, $len - $end);
    }

    /** 10.3.2020
     *  replaceStringBetween2StringV2($str, $start, $end, "$start-ABC123-$end",3);
     *  Vi du thay the hang loat IP
    user_pref("network.proxy.backup.ftp", "1111:19c0:0:fffe:17a:1:1:2");
    user_pref("network.proxy.backup.ssl", "1111:19c0:0:fffe:17a:1:1:2");
    user_pref("network.proxy.backup.ssl_port", 28888);
    user_pref("network.proxy.ftp", "1111:19c0:0:fffe:17a:785:371:436");
     */
    public static function replaceStringBetween2StringV2($stringIn, $start, $end, $by, $limit = 0)
    {
        $mm = explode($start, $stringIn);
        $cc = -1;
        $mmRet = [];
        foreach ($mm as $line) {

            $cc++;
            //ignore first
            if ($cc == 0) {
                $mmRet[] = $line;

                continue;
            }

            if ($limit && $cc > $limit) {
                $mmRet[] = $start.$line;

                continue;
            }

            //if($limit && $cc > $limit)
            //break;

            //echo "<br/>\n --- String = $str";
            $pos = null;
            if ($end) {
                $pos = strpos($line, $end);
            }
            //echo "<br/>\n POS = $pos";
            $sub = substr($line, 0, $pos + strlen($end));

            //echo "<br/>\n SUB = $sub";

            $strReplace = $sub;

            //echo "<br/>\n LINE0 = $line";
            $line = str_replace($strReplace, $by, $line);

            $mmRet[] = $line;
            // echo "<br/>\nFULL = $strReplace";

            //echo "<br/>\n LINE1 = $line";
        }

        //echo "<br/>\n\n --- \n\n";
        return implode('', $mmRet);
    }

    public static function replaceStringBetween2StringDel($inputString, $replaceBy, $strStart, $strEnd)
    {

        if (! $inputString) {
            return $inputString;
        }

        $str = preg_replace("/$strStart.*$strEnd/", $strStart.$replaceBy.$strEnd, $inputString);
        if ($str == $inputString) {
            $str = preg_replace("/$strStart.*/", $strStart.$replaceBy, $inputString);
        }

        return $str;

    }

    public static function convertToUpperCaseAfterDash($str)
    {
        return implode('', array_map('ucfirst', explode('-', $str)));
    }

    public static function convertToDashFromUpperCase($name)
    {
        return strtolower(preg_replace(
            '/(?<=[a-z])([A-Z]+)/',
            '-$1',
            $name
        ));
    }

    public static function removeKeepNFirstElement($str = null, $n = 0, $seperator = ',')
    {
        if (! $str) {
            return null;
        }
        $mm = explode($seperator, $str);
        $mm = array_filter($mm);
        if ($n >= count($mm)) {
            return $str;
        }
        $m1 = [];
        for ($i = 0; $i <= $n; $i++) {
            $m1[] = $mm[$i];
        }

        return $seperator.implode($seperator, $m1).$seperator;
    }

    public static function addElementToStringToBegin($str = null, $elm = '', $seperator = ',')
    {
        if (! $str) {
            $str = "$seperator$elm$seperator";
        } else {
            $str = "$seperator$elm$seperator".$str;
        }
        $str = str_replace("$seperator$seperator", $seperator, $str);

        return $str;
    }

    public static function addElementToString($str = null, $elm = '', $seperator = ',')
    {
        if (! $str) {
            $str = "$seperator$elm$seperator";
        } else {
            $str .= "$seperator$elm$seperator";
        }
        $str = str_replace("$seperator$seperator", $seperator, $str);

        return $str;
    }

    public static function checkElementInString($str, $elm, $seperator = ',')
    {
        if (strstr($str, "$seperator$elm$seperator") !== false) {
            return true;
        }

        return false;
    }

    public static function deleteElementInString($str, $elm, $seperator = ',')
    {
        $str = str_replace("$seperator$elm$seperator", ',', $str);

        return $str;
    }

    public static function removeElementInString($str, $elm, $seperator = ',')
    {
        return $str = cstring::deleteElementInString($str, $elm, $seperator);
    }

    public static function toTienVietNamString($number)
    {

        $hyphen = ' ';
        $conjunction = '  ';
        $separator = ' ';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $dictionary = [
            0 => 'Không',
            1 => 'Một',
            2 => 'Hai',
            3 => 'Ba',
            4 => 'Bốn',
            5 => 'Năm',
            6 => 'Sáu',
            7 => 'Bảy',
            8 => 'Tám',
            9 => 'Chín',
            10 => 'Mười',
            11 => 'Mười một',
            12 => 'Mười hai',
            13 => 'Mười ba',
            14 => 'Mười bốn',
            15 => 'Mười năm',
            16 => 'Mười sáu',
            17 => 'Mười bảy',
            18 => 'Mười tám',
            19 => 'Mười chín',
            20 => 'Hai mươi',
            30 => 'Ba mươi',
            40 => 'Bốn mươi',
            50 => 'Năm mươi',
            60 => 'Sáu mươi',
            70 => 'Bảy mươi',
            80 => 'Tám mươi',
            90 => 'Chín mươi',
            100 => 'trăm',
            1000 => 'ngàn',
            1000000 => 'triệu',
            1000000000 => 'tỷ',
            1000000000000 => 'nghìn tỷ',
            1000000000000000 => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'toTienVietNamString only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.ClassString::toTienVietNamString(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.ClassString::toTienVietNamString($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = ClassString::toTienVietNamString($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= ClassString::toTienVietNamString($remainder);
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string.'';
    }

    public static function toTienVietNamString1($num)
    {

        $str = number_format($num);
        $arr = explode(',', $str);
        $arr = array_reverse($arr);
        $ret = '';

        if (isset($arr[0])) {
            $val = intval($arr[0]);
            if ($val > 0) {
                $ret = $val.' đồng ';
            } else {
                $ret = ' đồng ';
            }
        }
        if (isset($arr[1])) {
            $val = intval($arr[1]);
            if ($val > 0) {
                $ret = $val.' nghìn '.$ret;
            }
            //else
            //$ret =  " . $ret;
        }
        if (isset($arr[2])) {
            $val = intval($arr[2]);
            if ($val > 0) {
                $ret = $val.' triệu '.$ret;
            }
        }
        if (isset($arr[3])) {
            $val = intval($arr[3]);
            if ($val > 0) {
                $ret = $val.' tỷ '.$ret;
            }
        }
        for ($i = 4; $i < count($arr); $i++) {
            $ret = $arr[$i].$ret;
        }

        return $ret;
    }

    /*
     * Đưa vào Chuỗi mẫu vào trước và sau 1 Tag HTML, nếu trước và sau chưa có chuỗi mẫu đó:
     * Ex: $ret = insertBeforeAndAfterTagHtml($str, "img", '<div style="text-align: center;">', "</div>");
     */
    public static function insertBeforeAndAfterTagHtml($str, $tag, $insertBefore, $insertAfter)
    {

        $len = strlen($str);

        //<p style="text-align: center;">
        //$insertBefore = '<div style="text-align: center;">';
        $lenCenter = strlen($insertBefore);

        $ret = '';
        $needInsert = 0;

        for ($i = 0; $i < $len; $i++) {

            if (substr($str, $i, strlen($tag) + 1) == '<'.$tag ||
                substr($str, $i, strlen($tag) + 2) == '<'.$tag.'>'
            ) {
                if ($tmp = $i - $lenCenter > 0) {
                    if (strstr(substr($str, $tmp, $len), $insertBefore) === false) {
                        $ret .= "$insertBefore";
                        $needInsert = 1;
                    }
                } else {
                    $ret .= "$insertBefore";
                    $needInsert = 1;
                }
            }

            $ret .= $str[$i];

            //  echo "<br/>\n $i: ".$str[$i];
            if ($needInsert) {
                //echo "<br/>\n OK?";
                if ($str[$i] == '>') {

                    $ret .= $insertAfter;
                    $needInsert = 0;
                }
            }
        }

        return $ret;
    }

    public static function trimRemoveFromEnd($haystack, $needle)
    {
        $haystack = trim($haystack);
        $length = strlen($needle);

        if (substr($haystack, -$length) === $needle) {
            $haystack = substr($haystack, 0, -$length);
        }

        return $haystack;
    }

    public static function trimRemoveFromBegin($haystack, $needle)
    {
        $haystack = trim($haystack);
        $length = strlen($needle);
        if (substr($haystack, 0, $length) === $needle) {
            $haystack = substr($haystack, $length);
        }

        return $haystack;
    }

    /*
     * $word1 = ["<pre>", '<pre '];
    $word2 = "</pre>";
    $mm = splitStringByBlockWidthSignature($txt, $word1, $word2);

    return array of strings, and signature info
     */
    public static function splitStringByBlockWidthSignature($txt, $signBlockStart, $signBlockEnd)
    {

        $ret0 = cstring::findAllStrInStr($txt, $signBlockStart);
        $ret1 = cstring::findAllStrInStr($txt, $signBlockEnd);

        $mm = [];
        $tt = count($ret0);
        $len2 = strlen($signBlockEnd);
        //$mm[] = substr($txt, 0, $ret0[0]);

        $j = 0;
        if ($ret0[0] > 0) {
            $mm[$j] = [];
            $mm[$j]['in_block_sign'] = 0;
            $mm[$j]['str'] = substr($txt, 0, $ret0[0]);
        }
        for ($i = 0; $i < $tt; $i++) {
            $p1 = $ret0[$i];
            $p2 = $ret1[$i];
            //echo "<br/>\n $p1 - $p2";
            //$mm[] = substr($txt, $p1, $p2 + $len2 - $p1);
            $j++;
            $mm[$j] = [];
            $mm[$j]['in_block_sign'] = 1;
            $mm[$j]['str'] = substr($txt, $p1, $p2 + $len2 - $p1);

            if (isset($ret0[$i + 1])) {
                //$mm[] = substr($txt, $p2 + $len2, $ret0[$i+1] - $p2 - $len2);
                $j++;
                $mm[$j] = [];
                $mm[$j]['in_block_sign'] = 0;
                $mm[$j]['str'] = substr($txt, $p2 + $len2, $ret0[$i + 1] - $p2 - $len2);

            }
        }

        //Phần tử cuối
        //$mm[] = substr($txt, end($ret1) + $len2);
        $j++;
        $mm[$j] = [];
        $mm[$j]['in_block_sign'] = 0;
        $mm[$j]['str'] = substr($txt, end($ret1) + $len2);

        return $mm;
    }

    public function _LAST()
    {
    }
}

class cstring extends classStr
{
}

function fixPhoneNumber($phone)
{
    //xoá tất cả ký tự không phải số (không phải từ 0-9)
//    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (substr($phone, 0, 3) == '084') {
        return '0'.substr($phone, 3);
    }
    if (substr($phone, 0, 3) == '+84') {
        return '0'.substr($phone, 3);
    }
    if (substr($phone, 0, 2) == '84') {
        return '0'.substr($phone, 2);
    }

    return $phone;
}


function file_get_content_cache($url, $foldCache = null, $timeout = 0, $overWrite = 0)
{
    if (! $foldCache) {
        $foldCache = sys_get_temp_dir().'/cache_php/crawl';
    }
    if (! file_exists($foldCache)) {
        mkdir($foldCache, 0755, 1);
    }

    $md5 = md5($url);
    $file = $foldCache.'/'.$md5;

    if(!$overWrite)
    if (file_exists($file)) {
        return file_get_contents($file);
    }

    $ctx = null;
    if($timeout){
        $ctx = stream_context_create(['http'=>
            [
                'timeout' => $timeout,  //1200 Seconds is 20 Minutes
            ]
        ]);
    }
    $cont = file_get_contents($url, null, $ctx);
    file_put_contents($file, $cont);

    return $cont;
}
function file_get_contents_timeout($url, $timeout = 0)
{

    if($timeout){
        $ctx = stream_context_create(['http'=>
            [
                'timeout' => $timeout,  //1200 Seconds is 20 Minutes
            ]
        ]);
        return file_get_contents($url, false, $ctx);
    }
    else
        return file_get_contents($url);
}

/**
 * @param $mm = [ [id1=> [quantity1, allow1, used1]], ....]
 * $mm = [
 * ['id' => 1, 'allow' => 10, 'quantity' => 1, 'used' => 0],
 * ['id' => 2, 'allow' => 50, 'quantity' => 1, 'used' => 0],
 * ['id' => 3, 'allow' => 10, 'quantity' => 2, 'used' => 0],
 * ['id' => 4, 'allow' => 10, 'quantity' => 1, 'used' => 0],
 * ];
 * @return void
 */
function updateArrayBillAndFillUsedNumber($count0, $mm)
{
//        $mm = [
//            ['id' => 1, 'allow' => 10, 'quantity' => 1, 'used' => 0],
//            ['id' => 2, 'allow' => 50, 'quantity' => 1, 'used' => 0],
//            ['id' => 3, 'allow' => 10, 'quantity' => 2, 'used' => 0],
//            ['id' => 4, 'allow' => 10, 'quantity' => 1, 'used' => 0],
//        ];

    foreach ($mm AS &$one0){
        $one = (object) $one0;
//            echo "<br/>\n $cc ";
        $quantity = $one->quantity;
        if(!$quantity)
            continue;
//            if($count0 <=0)
//                break;
        if($one->allow * $quantity < $count0){
            if($one->used != $one->allow * $quantity){
                $one->used = $one->allow  * $quantity;

            }
            $count0 -= $one->allow  * $quantity;
        }
        else{
            if($one->used != $count0){
                $one->used = $count0;

            }
//                echo "<br/>\n CC = 0";
            $count0 = 0;
        }
//            echo "<br/>\n Used: $one->used";
//            $one->save();

        $one0['used'] = $one->used;
    }

    $mm[] = ['id'=>0, 'free' => $count0];

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mm);
//        echo "</pre>";
    return $mm;

}

function buildMenu($menuItems, $parentId, $lang = '') {
    $html = '';
    $lang0 = $lang;
    $items = array_filter($menuItems, function($item) use ($parentId) {
        return $item->parent_id === $parentId;
    });

    if (count($items) > 0) {
        $html .= '<ul class="dropdown-menu" data-code-pos="ppp17598474971201">';
        foreach ($items as $item) {
            $children = buildMenu($menuItems, $item->id);
            $hasChildren = !empty($children);

            $linkLang = $lang . $item->link;
            if(str_starts_with($item->link, '/admin') || str_starts_with($item->link, '/member'))
                $linkLang = $item->link;

            $name = $item->name ?? '';
            if($item->translations ?? '')
                if(is_array($item->translations)) {
                    $name = $item->translations[$lang0] ?? $item->name;
                }

            $html .= '<li class="nav-item ' . ($hasChildren ? 'dropdown' : '') . '">';
            $html .= '<a class="dropdown-item ' . ($hasChildren ? 'dropdown-toggle' : '') . '" href="' . $linkLang . '">' . $name . '</a>';
            $html .= $children;
            $html .= '</li>';
        }
        $html .= '</ul>';
    }

    return $html;
}

// Render menu
function renderMenu($menuItems, $lang = '') {
    if(!$menuItems)
        return;
    $rootItems = array_filter($menuItems, function($item) {
        return $item->parent_id === 3;
    });

    $lang0 = $lang;

    foreach ($rootItems as $item) {
        $linkLang = $lang . $item->link;
        if(str_starts_with($item->link, '/admin') || str_starts_with($item->link, '/member'))
            $linkLang = $item->link;

        $name = $item->name ?? '';
        if($item->translations ?? '')
            if(is_array($item->translations)) {
                $name = $item->translations[$lang0] ?? $item->name;
            }

        $children = buildMenu($menuItems, $item->id);
        $hasChildren = !empty($children);
        echo '<li class="nav-item ' . ($hasChildren ? 'dropdown' : '') . '">';
        echo '<a class="nav-link ' . ($hasChildren ? 'dropdown-toggle' : '') . '" href="' .$linkLang . '">' . $name . '</a>';
        echo $children;
        echo '</li>';
    }
}

//2021:
//ID: admin@glx
//Password: galaxy@1
//Merchan ID : 35589
//Api Key: lIiC3Onw35hxPjqHcQ444V9ScT4eumGe
//Secret Key: DIO2o8EBGzol1IuJs4xyTvoKxJjbTq3W
//https://payment-docs.baokim.vn/docs/
//https://payment-docs.baokim.vn/docs/#send-order
class BaoKimAPI2021
{

    /* Bao Kim API key */
    const API_KEY = "lIiC3Onw35hxPjqHcQ444V9ScT4eumGe";
    const API_SECRET = "DIO2o8EBGzol1IuJs4xyTvoKxJjbTq3W";
    const TOKEN_EXPIRE = 86400; //token expire time in seconds
    const ENCODE_ALG = 'HS256';

    private static $_jwt = null;

    /**
     * Refresh JWT
     */
    public static function refreshToken()
    {

        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + self::TOKEN_EXPIRE;

        /*
        * Payload data of the token
        */
        $data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss' => self::API_KEY,     // Issuer
            'nbf' => $notBefore,        // Not before
            'exp' => $expire,           // Expire
            'form_params' => [
            ]
        ];

        /*
        * Encode the array to a JWT string.
        * Second parameter is the key to encode the token.
        *
        * The output string can be validated at http://jwt.io/
        */
        self::$_jwt = \JWT::encode(
            $data,      //Data to be encoded in the JWT
            self::API_SECRET, // The signing key
            'HS256'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return self::$_jwt;
    }

    /**
     * Get JWT
     */
    public static function getToken()
    {
        if (!self::$_jwt)
            self::refreshToken();

        try {
            \JWT::decode(self::$_jwt, self::API_SECRET, array('HS256'));
        } catch (Exception $e) {
            self::refreshToken();
        }

        return self::$_jwt;
    }
}

class JWT
{
    public $userid;
    public $uidRand;
    public $username;
    public $email;
    public $gidUsingApi;
    public $useridOther;
    public $expire_time;
    public $refresh_token;

    public function getFromObj($obj){

        foreach (get_class_vars(get_class($this)) as $k => $v) {
            $this->$k = null;
            if (isset($obj->$k)) {
                $this->$k = $obj->$k;
            }
        }
        //return $this;
    }

    public static function decode($jwt, $key = null, $verify = true)
    {
        try{

            $tks = explode('.', $jwt);
            if (count($tks) != 3) {
                throw new Exception('JWT: Wrong number of segments');
            }
            list($headb64, $bodyb64, $cryptob64) = $tks;
            if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
                throw new Exception('JWT: Invalid segment encoding');
            }
            if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
                throw new Exception('JWT: Invalid segment encoding');
            }
            $sig = JWT::urlsafeB64Decode($cryptob64);
            if ($verify) {
                if (empty($header->alg)) {
                    throw new Exception('JWT: Empty algorithm');
                }
                if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
                    throw new Exception('JWT: Signature verification failed');
                }
            }

            return $payload;
        }
            //LAD bổ xung exception, để sẽ trả về Number Lỗi chỉ định cho client biết token cần xóa, login lại:
        catch (Exception $exception){
            ClassException::setLastError($exception->getMessage());
            return null;
        }
    }

    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = array('typ' => 'JWT', 'alg' => $algo);
        $segments = array();
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = JWT::sign($signing_input, $key, $algo);
        $segments[] = JWT::urlsafeB64Encode($signature);
        return implode('.', $segments);
    }

    public static function sign($msg, $key, $method = 'HS256')
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('JWT: Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    public static function jsonDecode($input)
    {
        $obj = json_decode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::_handleJsonError($errno);
        } else if ($obj === null && $input !== 'null') {
            throw new DomainException('JWT: Null result with non-null input');
        }
        return $obj;
    }

    public static function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::_handleJsonError($errno);
        } else if ($json === 'null' && $input !== null) {
            throw new DomainException('JWT: Null result with non-null input');
        }
        return $json;
    }

    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function _handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException(
            isset($messages[$errno])
                ? $messages[$errno]
                : 'JWT: Unknown JSON error: ' . $errno
        );
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false)
    {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }
}

//Check unique script
function check_unique_script($checkFullAgv = 1, $withUserLogin = '')
{

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        return 0;

    if (!isCli())
        return;

    $pid = getmypid();

    exec('ps ax', $prolist);
    $sname = basename($_SERVER['SCRIPT_NAME']);

    echo("\nCurrent PID1 = $pid / " . $_SERVER['SCRIPT_NAME']);

    if ($checkFullAgv) {
        $sname = "" . trim(implode(" ", $_SERVER['argv']));
        //die("\n SNAME = $sname");
        //echo "\n SNAME = $sname";
    }

    $sname = str_replace('  ', ' ', $sname);
    $sname = str_replace('  ', ' ', $sname);
    $sname = trim($sname);

    for ($i = 0; $i < count($prolist); $i++) {
        $pI = $prolist[$i];
        $pI = str_replace('  ', ' ', $pI);
        $pI = str_replace('  ', ' ', $pI);
        //if (strstr($prolist[$i], $sname) != FALSE &&
        $pI = trim($pI);
        //if ($pI == $sname &&
        if (substr($pI, -1 * strlen($sname)) == $sname
            && strstr($pI, "$pid") == FALSE) {
            echo ("\nProcess da ton tai (1): " . $pI . "\n");
            exit(0);
            return;
        }
    }
}

function dumperrorglobal($strlog) {
    $remote_ip = getenv('REMOTE_ADDR');
    $File = $_SERVER["PHP_SELF"];
    $datetime = date('Y-m-d H:i:s');
    output(GLOBAL_ERROR_FILE, "$datetime#$File#$remote_ip#$strlog");
}


class clsValidate {


    //This array store errors string of each isValidate function
    //Mảng này chứa mọi lỗi được bắt tại phiên hiện tại
    static public $arrLastError = [];
    //Biến này cho biết có lỗi hay không để show lỗi ra
    static public $isFoundError = 0;
    /**
     * @var clsValidate[]
     */
    static public $arrObjectValidate = array();

    var $fieldName; //Tên trường valide
    var $isRequireNotEmpty; //Nếu có isset thì bắt buộc phải khác rỗng, khi insert, update
    var $isNumber; //Bắt buộc là số
    var $isUniqueInDb; //Trường là duy nhất trong DB, như email, username
    var $minLen;    //Length tối thiểu
    var $maxLen;    //Length tối đa
    var $inArray; // Giá trị bắt buộc trong mảng
    var $validFunctionCallBack; //Hàm callback check valid
    var $explainText;

    //Biến này tạo ra để fit với Autotester, sẽ lấy chuỗi báo lỗi kết quả như nhau, từ các hàm ttt....
    var $errorText;

    static function resetEmptyErrorArray(){
        clsValidate::$arrLastError = [];
    }
    static function getBlackWordList0(){
        $arrBlackWord = array("sex", "red sea", 'abominable',
            'crack', "pennis", "pussy", "cunt",
            "xxx", "porn",'pthc',"suck",'pornhub','amateurs','xvideo','cum', "fuck", 'teen', 'cave', 'jav', '9yo','8yo','7yo','5yo','4yo', 'uncensored','dâm', 'thủ dâm', 'hiếp', 'địt', 'đít');
        $arrBlackWord = array_map('strtolower', $arrBlackWord);
        return $arrBlackWord;
    }

    static function getBlackWordList(){
        $arrBlackWord = self::getBlackWordList0();
        return $arrBlackWord;
    }

    //Hàm này tạo ra để fit với Autotester, sẽ lấy chuỗi báo lỗi kết quả như nhau, từ các hàm ttt....
    function getErrorString($padMore = null){

        if($this->errorText)
            if($padMore)
                $padMore = " : " . lcfirst($padMore);

        if($this->errorText || $padMore)
            return $this->errorText . $padMore;

        return "Please check valid '$this->fieldName'";
    }

    static function addValidateObject($obj){
        clsValidate::$arrObjectValidate[] = $obj;
    }

    function addToValidateArray(){
        clsValidate::$arrObjectValidate[] = $this;
    }

    static function getValidArray($objCheck){

        //Bỏ reset ở đây, coder phải tự đặt, để có thể cho thêm chuỗi báo lỗi vào
        //clsValidate::$arrLastError = [];

        foreach (clsValidate::$arrObjectValidate AS $ovl){

            //Cho trường hợp nhiều field cùng 1 kiểu validate
            $mm = [$ovl->fieldName];
            //Nếu nhiều trường cùng 1 rule
            if(strstr($ovl->fieldName, ',')){
                $mm = explode(",", $ovl->fieldName);
            }

            //foreach ($mm AS $field)
            $field = $ovl->fieldName;
            {
                $sname = $field;

                //Nếu ko isset thì bỏ qua ko check, ví dụ trường hợp login chỉ với username, hoặc email
                //thì sẽ unset 1 trong 2 cái để ko check
                if(!isset($objCheck->$field))
                    continue;

                $val = $objCheck->$field;

                if($objCheck instanceof \Base\ModelNewsFile);

                if(method_exists($objCheck, 'getNameDescFromField')){
                    $sname = $objCheck->getNameDescFromField($field);
                }

                if($ovl->isRequireNotEmpty){
                    if(!$val)
                        clsValidate::addErrorStr($ovl->getErrorString(tttNeedInputValue() . " of '$sname' "));
                }

                if(\ClassNetwork::isIpLocalNetwork()){
                    //echo "<br/>\n xxxxx :$field / $val / require = $ovl->isRequireNotEmpty";
                }
                if(!$val)
                    continue;

                $ovl->validFunctionCallBack = str_replace("()", '', $ovl->validFunctionCallBack);
                //Nếu có function callback:
                if($ovl->validFunctionCallBack){
                    if(!is_callable($ovl->validFunctionCallBack)){
                        loi("Not is_callable function: $ovl->validFunctionCallBack of '$sname'");
                    }
                    if(!call_user_func($ovl->validFunctionCallBack, $val)){
                        clsValidate::addErrorStr($ovl->getErrorString());
                    }
                }
                if($ovl->isNumber && !is_numeric($val) ){
                    clsValidate::addErrorStr($ovl->getErrorString(tttValueMustNumber() . " of '$sname' : '$val' "));
                }

                if($ovl->isUniqueInDb){
                    $cls = get_class($objCheck);
                    $obj = new $cls;
                    if($objCheck instanceof \Base\ModelBase);
                    if($obj instanceof \Base\ModelBase)
                    {
                        $padSiteId = null;
                        if(ClassSetting::$siteId > 0)
                            $padSiteId = " AND siteid = ".ClassSetting::$siteId;
                        $padUid = null;
                        //Nếu có ID, thì là save, không có là insert thì mới check trùng
                        if($objCheck->getId())
                            $padUid = " AND id <>  ".$objCheck->getId();
                        if(ModelUserCms::getOneWhereStatic(" $field = '$val' $padSiteId $padUid ")){
                            \clsValidate::$arrLastError[] = $ovl->getErrorString( tttValueIsDuplicate($val) ." of field '$sname'");
                        }
                    }
                }

                if($ovl->minLen && strlen($val) < $ovl->minLen){
                    \clsValidate::$arrLastError[] = $ovl->getErrorString(tttRequireMinLen($sname) . " : $ovl->minLen");
                }
                if($ovl->maxLen && strlen($val) > $ovl->maxLen){
                    \clsValidate::$arrLastError[] = $ovl->getErrorString(tttRequireMaxLen($sname) . " : $ovl->maxLen");
                }
                if($ovl->inArray && count($ovl->inArray)){
                    if(!in_array($val, $ovl->inArray))
                        \clsValidate::$arrLastError[] = $ovl->getErrorString(tttNeedInArray() . " of '$sname': ".implode(',',$ovl->inArray));
                }
            }
        }

        if(\ClassNetwork::isIpLocalNetwork()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r(\clsValidate::$arrLastError);
//            echo "</pre>";
//            die();
        }
    }

    /**
     *
     * LAD: not valid with BR...
     * Will count number of <[a-z]> tag and </[a-z]> tag (will also validate the order)
     *
     * @source https://stackoverflow.com/a/5723868/1732359
     * @param  string $html
     * @param  boolean $checkOrder
     * @return boolean
     */
    static public function isValidateHtmlTag($html, $checkOrder = true) {

        // Add solidus characters to void elements, to prevent them from being matched in the regex that captures start tags.
        $html = preg_replace('#<((?!\/)(?:area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)(.*=".*")[^\/]*)>#i', '<${1} />', $html);

        preg_match_all('#<([a-z]+)(?: [a-z]+=".+")?>#i', $html, $start, PREG_OFFSET_CAPTURE);
        preg_match_all('#<\/([a-z]+)>#i', $html, $end, PREG_OFFSET_CAPTURE);

        $start = $start[1];
        $end = $end[1];
        if (count($start) != count($end)) {
            throw new Exception('Check numbers of tags');
        }

        if (! $checkOrder) {
            return true;
        }

        $decrementor = count($start) - 1;
        foreach ($end as $v) {

            if ($v[0] != $start[$decrementor][0] || $v[1] < $start[$decrementor][1]) {
                throw new Exception('End tag [' . $v[0] . '] not opened');
            }

            $decrementor--;
        }

        return true;
    }

    static public function errorToString($glue = "\n- "){
        $str = "";
        foreach (\clsValidate::$arrLastError as $item) {
            $str .= " - $item\n";
        }
        return $str;
        //return implode($glue, \clsValidate::$arrLastError);
    }
    static public function errorToStringBr(){
        $str = "";
        foreach (\clsValidate::$arrLastError as $item) {
            $str .= " - $item<br/>";
        }
        return $str;
        //return implode("<br/>- ", \clsValidate::$arrLastError);
    }

    static public function addErrorStr($str){
        \clsValidate::$arrLastError[] = $str;
        clsValidate::$isFoundError = 1;
    }

    static function isUsername($input = null, $option = null) {

        if(strlen($input) < 4)
            return 0;

        $pattern = '/^[A-Za-z][A-Za-z0-9._\-]{4,160}$/';
        if ((defined('DEF_ALLOW_NUMBER_USERNAME') &&  DEF_ALLOW_NUMBER_USERNAME == 1) || (isset($option['allow_number']) && $option['allow_number'] == 1)) {
            $pattern = '/^[A-Za-z0-9._\-]{4,160}$/';
        }
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    static function isStringAbcAndNumber($input = null, $option = null) {
        $pattern = '/^[A-Za-z][A-Za-z0-9]$/';
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    static function isStringNumberAndDash($input = null, $option = null) {
        if (!preg_match('/^[0-9-\s]+$/D', $input))
            return 0;
        return 1;
    }

    static function isGroupname($input, $option = null) {
        $pattern = '/^[A-Za-z][A-Za-z0-9._\- ]{4,30}$/';
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    static function isPassword($input) {
        if(!$input)
            return 0;
        $pattern = '/^[0-9A-Za-z!@#$%\._\+-\?\$\(\)]{5,30}$/';
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    /**
     * @param $input
     * @return int
     */
    static function isEmail($input) {
        if (filter_var($input, FILTER_VALIDATE_EMAIL))
            return 1;
        return 0;
    }

    static function isPhone($value)
    {
        if(strlen($value) < 10 || strlen($value) >13)
            return 0;
        return preg_match('/^[0-9]+$/', $value);
    }

    static function isStringSimple($input, $lenMin = 0, $lenMax = 50) {
        //Xem them lai cac ky tu dac biet
        if (!preg_match('/^[#:_\-\;\:\'\.,\(\)%" ?$+A-Za-z0-9_-]{' . $lenMin . ',' . $lenMax . '}$/', $input))
            return 0;
        return 1;
    }

    static function isStringSimpleVN($input, $lenMin = 0, $lenMax = 50, $addMore = "") {
        //Xem them lai cac ky tu dac biet
        if (!preg_match('/^[#:_\-\;\:\'\.,\(\)%"' . $addMore . ' ?+A-Za-z0-9_-àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐD]{' . $lenMin . ',' . $lenMax . '}$/', $input))
            return 0;
        return 1;
    }

    /*  https://wiki.umbc.edu/pages/viewpage.action?pageId=1867962
     *  $arrNotAllow = array("\\","/",":",";","*","?","\"","<",">","|","%",",","#","$","!","+","{","}","&","[","]","•","'");
     */

    static function isFilename($input, $lenMin = 1, $lenMax = 255) {

        $arrNotAllow = array("\r", "\n","\\", "/", "*", '?', "\"", "<", ">", "|", "\"");

        if (strlen($input) > 255 || strlen($input) < 1)
            return 0;

        for ($i = 0; $i < $len = strlen($input); $i++) {
            if (in_array($input[$i], $arrNotAllow))
                return 0;
        }

        //if (!preg_match('/^[#:_\-\;\:\'" %.,=+A-Za-z0-9_-àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐD]{' . $lenMin . ',' . $lenMax . '}$/', $input))
        // if(!preg_match('/^[\w\'\".-]{'.$lenMin.','.$lenMax.'}$/i', $input))
        //return 0;
        return 1;
    }

//    if(preg_match('/^[\w\'.-]{2,20}$/i', trim($_POST['first_name']))) {
//        $fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
//    } else {
//        $errors[] = 'first name';
//    }

    static function isDateString($myDateString)
    {
        return (bool)strtotime($myDateString);
    }


    static function isDomain($input) {
        if(!strstr($input, "."))
            return 0;
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $input) //valid chars check
            && preg_match("/^.{1,253}$/", $input) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $input) );
    }

    static function isCharacterAndNumberOnly($input, $min = 0, $max = 100) {
        $pattern = '/^[A-Za-z0-9]{' . $min . ',' . $max . '}$/';
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    static function isFieldNameDb($input) {
        $pattern = '/^[A-Za-z0-9_]{1,100}$/';
        if (!preg_match($pattern, $input))
            return 0;
        return 1;
    }

    static function isIpAddress($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP))
            return 1;
        return 0;
    }

    static function isURL($url) {

        if(substr($url, 0, 7)!='http://' && substr($url, 0, 8)!='https://'
            && substr($url, 0, 6)!='ftp://' && substr($url, 0, 7)!='ftps://')
            return 0;
        //$url = str_replace(" ", "", $url);

        //Truong hop url co Unicode can xly:
        //VD https://abc.com/wp-content/uploads/2020/01/Hoả-Vân-Tà-Thần-Hua-Yun-Xie-Shen-2020-poster.jpg
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        if (filter_var($url, FILTER_VALIDATE_URL))
            return 1;
        return 0;
    }

}


// $errno = 0:error
function xmlreturn($str, $success = 1)
{
    echo "<return><string>$str</string><value>$success</value></return>";
    die();
}

function xmlreturn_error($str, $success = 0)
{
    echo "<return><string>$str</string><value>$success</value></return>";
    die();
}

function STH($string)
{
    $strHex="";
    $strlen = strlen($string);
    for($i=0; $i<$strlen; $i++)
    {
        if(ord($string[$i])<16)
        {
            $strHex.="0".dechex(ord($string[$i]));
        }
        else
        {
            $strHex.=dechex(ord($string[$i]));
        }
    }
    return $strHex;
}

function HTS($string)
{
    $str = '';
    for($i=0; $i<strlen($string); $i+=2){
        $str .= chr(hexdec(substr($string,$i,2)));
    }
    return $str;
}

function testDownloadSession4s()
{
    function downloadFileNotBuffer($url)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept-language: en\r\n"
            ]
        ]);

// Open the file using the HTTP headers set above
        $file = fopen($url, 'rb', false, $context);

// Create a temporary stream
        $temp = fopen('php://temp', 'r+b');

// Copy the file to the temporary stream
//        stream_copy_to_stream($file, $temp);

// Rewind the temporary stream
//        rewind($temp);

// Read the contents of the temporary stream
//        while (!feof($temp)) {
//            fread($temp, 1024);
//        }

// Close the streams
        fclose($file);
        fclose($temp);

    }
    function downloadPartFile($url, $from = 0, $to = 0)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($to)
            curl_setopt($ch, CURLOPT_RANGE, "$from-$to"); // download the first 500 bytes

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    function getToken4S($user, $pw)
    {
        $url = "https://4share.vn/api/login-api";
        $data = array(
            'email' => $user,
            'password' => $pw
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response)->payload;
    }

    $tk = getToken4S('zzzzz1', dfh1b('3908080808080808080808'));
    $url = 'https://4share.vn/api/download-file/getLinkDownload?ide=ms6b5f525d525c5958'; // replace with your URL
//$cookie = "_tglx863516839=$tk;_tglx__863516839=$tk"; // replace with your cookie

    function getLinkDownloadInfo($url, $tk)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $tk
        ));

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    $ok = 0;

/////////////////////////////////////////////////////////////////
//Tạo link , done byte  = 0
    $ret = json_decode(getLinkDownloadInfo($url, $tk));

    $linkDl = $ret->payload->dlink;
    $done_bytes = $ret->payload->done_bytes;
    $sid0 = $ret->payload->sid;

    echo "<br/>\n SID = $sid0 <br>  DoneByte = $done_bytes <br> DLink = $linkDl";

    if ($done_bytes == 0) {
        echo "<br>CheckOK1!";
        $ok++;
    }


/////////////////////////////////////////////////////////////////
//Tải 1 phần của link, rồi check lại DoneByte khác 0
    downloadPartFile($linkDl, 0, 10000000);

    sleep(2);
    $ret = json_decode(getLinkDownloadInfo($url, $tk));
    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    print_r($ret);
    echo "</pre>";

    $done_bytes = $ret->payload->done_bytes;

    echo "<br> DONE = $done_bytes";

    if ($done_bytes > 0 && $done_bytes > 1000000) {
        echo "<br> CheckOK2!";
        $ok++;
    }

/////////////////////////////////////////////////////////////////
//Tải hết link, check lại thì sid sẽ tăng lên, vì đã tạo SID mới:
//    exec("wget  -O /dev/null '$linkDl'");

    downloadPartFile($linkDl);

    $ret = json_decode(getLinkDownloadInfo($url, $tk));
    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    print_r($ret);
    echo "</pre>";
    $linkDl = $ret->payload->dlink;
    $done_bytes = $ret->payload->done_bytes;
    $sid1 = $ret->payload->sid;

    if ($sid1 > $sid0 && $done_bytes == 0) {
        echo "<br> CheckOK3!";
        $ok++;
    }
    return $ok;
}

function resetKeepFieldObj($obj, $fields)
{
    // Get the object variables
    $objectVars = get_object_vars($obj);

    // Loop through the object variables
    foreach ($objectVars as $name => $value) {
        // If the field is not in the fields to keep, unset it
        if (!in_array($name, $fields)) {
            unset($obj->$name);
        }
    }
}

/**
 * @param $username_or_email
 * @param $password
 * @return User|\Illuminate\Database\Eloquent\Model|object|null
 * @throws Exception
 */
function checkAuthTool($username_or_email, $password){

    if(!$username_or_email)
        loi("Please enter user/email");

    ////////////////////////
    //Auth and get userinfo
    if(!($oUserCms = \App\Models\User::where('email', $username_or_email)->orWhere('username', $username_or_email)->first())){
        loi("Not found user2 ($username_or_email)!");
    }
    if($oUserCms->password != sha1($password . $oUserCms->id)){
        loi("Not valid user/password!($username_or_email...)");
    }
    return $oUserCms;
}


function get_filesize_remote($file_url)
{
    if (!fopen($file_url, "r"))
        loi("ERROR get_byte_range_stream_url - can not open file URL ($file_url)");

    $header = get_headers($file_url);

    $len = 0;
    foreach ($header as $key => $value) {
        $value = strtolower($value);

        if (strstr($value, "content-length") != FALSE) {
            list($tmp, $len) = explode(":", $value);

            $len = trim($len);
            if ($len <= 0 || empty($len) || !ctype_digit($len))
                loi("ERROR CheckSumFileURL, LEN NOT VALIDE??? = '$len'");
            else
                break;
        }
    }

    return $len;
}

//Upload tool, & upload new tool:
function test4SUploadFtpApi($token)
{
    //TOdo:  LAD tam dung de test ok github
    return;

    $success = 0;
    $urlUpload = 'http://v2up.4share.vn/api/member-file/upload';
    $ftp_server = 'v2up.4share.vn';
//require_once "/var/www/html/public/index.php";
    $ftp_user_name = env("USER_4S_TEST");
    $ftp_user_pass = env('PW_4S_TEST');
    $source_file = 'e:/tool.iso';
    $source_file = 'e:/tip.zip';
    $bname = basename($source_file);
    $destination_file = basename($source_file) . microtime(1);


    function uploadFile1($ftp_server, $ftp_user_name, $ftp_user_pass, $source_file, $destination_file)
    {
        // set up basic connection
        $conn_id = ftp_connect($ftp_server, 36880);

// login with username and password
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// check connection
        if ((!$conn_id) || (!$login_result)) {
            echo "FTP connection has failed!";
            echo "Attempted to connect to $ftp_server for user $ftp_user_name";
            exit;
        } else {
            echo "Connected to $ftp_server, for user $ftp_user_name";
        }

// turn passive mode on
        ftp_pasv($conn_id, true);

// open the file to be uploaded
        $file = fopen($source_file, 'r');

// the size of each chunk (in bytes)
        $chunk_size = 1024 * 1024; // e.g. 1 MB

// the position in the file
        $position = 0;

        while (!feof($file)) {
            // move the pointer to the current position
            fseek($file, $position);

            // upload a chunk to the FTP server
            $result = ftp_nb_fput($conn_id, $destination_file, $file, FTP_BINARY, $position);

            // check upload status
            while ($result == FTP_MOREDATA) {
                // continue uploading
                $result = ftp_nb_continue($conn_id);
            }

            if ($result != FTP_FINISHED) {
                echo "Error uploading $source_file";
                exit;
            }

            // update the position in the file
            $position += $chunk_size;
        }

// close the file
        fclose($file);
// close the FTP stream
        ftp_close($conn_id);

    }

    $destination_file = basename($source_file) . microtime(1);
    uploadFile1($ftp_server, $ftp_user_name, $ftp_user_pass, $source_file, $destination_file);

// POST to 4share.vn
    $post = [
        'file_path_local_upload_' => $destination_file,
        'file_name' => $bname,
        'file_size' => filesize($source_file)
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $urlUpload);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ]);

    $result = curl_exec($ch);

    echo "<br/>\n RET = $result";

    $js = json_decode($result);

    if ($js->code == 1 && $js->payload?->name == $bname && $js->payload?->id
        && md5_file($source_file) == $js->payload->md5
        ) {
        echo "<br/>\n OK";
        $success++;
    }




    ////////////////////////////
    // Upload OldTool:
    uploadFile1($ftp_server, $ftp_user_name, $ftp_user_pass, $source_file, $destination_file);

    $folderID = 0;
    $filename_on_server = $destination_file;
    $filesize_org = filesize($source_file);
    $username_or_email = env("USER_4S_TEST");
    $pass = env('PW_4S_TEST');
    $basename = $bname;
    $csign = '12345678';

    $str = base64_encode("$csign/$username_or_email/$pass/$filename_on_server/$basename/$filesize_org/$folderID");

//    echo "\n xxx = $csign/$username_or_email/$pass/$filename_on_server/$basename/$filesize_org/$folderID";
//    return;

    $ret = file_get_contents("http://v2up.4share.vn/tool/?upload_done=$str");
    //<return><string>Upload done: <a href='https://4share.vn/f/330507020a020700'>https://4share.vn/f/330507020a020700</a></string><value>1</value></return>
    if(strstr($ret, '<value>1</value>')){
        $success++;
    }
//    echo "<br/>\n STR = ";
//    echo $ret;
    return $success;
}


//Get check sum simple of file:
//Các file vài byte chưa check kỹ
function CheckSumFile($file, $nChar = 5) {
    if (!file_exists($file))
        loi("ERROR CheckSumFile File Not found: $file");
    if (is_dir($file))
        loi("ERROR CheckSumFile IS DIR!");

    $filesize = filesize($file);

    if ($filesize == 0)
        return 0;

    $fp = fopen($file, 'r');
    if (!$fp)
        return 0;

    //echo "\nFile = $file";

    if ($filesize < $nChar * 2) {
        return STH(file_get_contents($file));
    }

    if ($nChar >= $filesize) {
        $data = fgets($fp, $filesize);
        return dechex(ord($data));
    }

    $ret1 = @fseek($fp, 0);
    if ($ret1 == -1)
        loi("ERROR CheckSumFile fseek $file, 0");

    $data = fgets($fp, 2);
    if (strlen($data) == 0)
        loi("ERROR CheckSumFile fgets $file");

    $firstChar = dechex(ord($data));

    $ret1 = @fseek($fp, $filesize - 1);
    if ($ret1 == -1)
        loi("ERROR CheckSumFile fseek $file");

    $data = fgets($fp, 2);
    if (strlen($data) == 0)
        loi("ERROR CheckSumFile fgets $file");


    $lastChar = dechex(ord($data));

//    echo "<br />FirstChar = $firstChar , lastChar = $lastChar";

    $nChunk = $nChar;
    $chunkSize = floor($filesize / $nChunk);

    $ret = $firstChar;

    for ($i = 1; $i < $nChunk; $i++) {
        $pos = floor($chunkSize * $i);
        $ret1 = @fseek($fp, $pos);
        if ($ret1 == -1)
            loi("ERROR CheckSumFile fseek $file");
        $data = fgets($fp, 2);
        if (strlen($data) == 0)
            loi("ERROR CheckSumFile fgets $file");

        $HexNum = dechex(ord($data));
        $ret.= $HexNum;
    }

    $ret.= $lastChar;

    return $ret;
}


class CDisk {

    var $mount_point;
    var $device;
    var $disk_total_space;
    var $disk_free_space;
    var $disk_used_space;
    var $util;  //xx %
    var $wait;  //xx milisec
    var $checktime = 0;
    static $mountUtilWaitArray;

    /* Local get
     * $getUtil = 0: get util, wait or not (getUtil=1 will take about +3 second than not getUtil = 0)
     * $getInCache 0/1 : get direct or in cache file
     * $timeCacheRange: if cache file older more 20s than current, so get direct and rewite cache
     */

    function getDiskInfoArray($getUtil = 0, $getInCache = 1, $timeCacheRange = 20) {

        $cret = new CReturnError();

        $file = "/mnt/glx/weblog/cache/server/localDiskInfo";
        if ($getInCache) {

            if (file_exists($file) && filesize($file) > 0) {

                $filetime = filemtime($file);
                if ($filetime > time() - $timeCacheRange) {
                    CDisk::$mountUtilWaitArray = $arrCDiskObj = unserialize(file_get_contents($file));
                    if (is_array($arrCDiskObj))
                        return $arrCDiskObj;
                }
            }
        }

        $ret = shell_exec('df -Hl'); //$ret = shell_exec(HTS("6466202d486c"));
        $arr = explode("\n", $ret);
        $arrayDiskAndMount = array();

        $count = 0;
        //Get all mount point and dev
        foreach ($arr as $key => $str) {
            $count++;

            //B�? dòng đầu tiên
            if ($count == 1)
                continue;

            $str1 = trim($str);
            //if(substr($str1,0,5)=="/dev/")
            //$str1 = substr($str1,5);
            $arrTMP = explode(" ", $str1);
            $disk = trim($arrTMP[0]);
            $mount = trim($arrTMP[count($arrTMP) - 1]);

            if (empty($disk) || empty($mount))
                continue;

            $cdisk = new CDisk();
            $cdisk->device = $disk;
            $cdisk->mount_point = $mount;
            $cdisk->disk_free_space = disk_free_space($mount);
            $cdisk->disk_total_space = disk_total_space($mount);
            $cdisk->disk_used_space = $cdisk->disk_total_space - $cdisk->disk_free_space;
            $cdisk->checktime = time();
            //  if(strstr($mount,"/media"))
            //    continue;
            //        $arrayDisk[] = $str1;
            $arrayDiskAndMount[] = $cdisk;
        }

        if ($getUtil) {
            $ret = shell_exec("iostat -x 1 3");

            $arr = explode("\n", $ret);

            $diskSummary = array();
            $countDisk = array();
            $diskWait = array();
            $count = 0;

            /*
              ....
              [6] => sda               0.14   333.49   42.96    5.53  5460.36  1356.07   281.17     0.30    6.22    0.19   53.07   5.15  24.99
              [7] => sdb               0.95   264.05  134.65    3.60 16927.63  1070.62   260.38     0.00    2.49    2.17   14.21   0.46   6.40
              [8] => sdc               1.44   253.62  147.03    3.36 18575.80  1027.92   260.70     0.18    3.47    1.70   80.79   1.01  15.25
              [9] => sdd               1.85   239.42  137.93    3.22 17505.03   970.55   261.79     0.31    2.18    1.65   24.73   0.82  11.56
              [10] => sde               0.76   249.77  123.30    3.54 15472.77  1013.25   259.94     0.14    3.76    1.74   74.06   0.41   5.24
              [11] => sdf               0.99   217.71  129.66    3.17 16316.71   883.53   258.99     0.24    1.80    1.60    9.94   0.43   5.65
              [12] => sdg               0.03    69.36    9.90   21.92   423.88   365.21    49.59     0.12    3.93    0.64    5.41   0.17   0.55
              [13] => sdh               0.38  3299.06   93.72   33.81 11440.87 13331.50   388.49     0.15    3.84    2.01    8.90   0.77   9.88
              ...
              [19] => sda               0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00    0.00    0.00   0.00   0.00
              [20] => sdb               0.00     0.00  182.00    0.00 22780.00     0.00   250.33     4.68   25.64   25.64    0.00   5.45  99.20
              [21] => sdc               0.00     0.00  141.00    0.00 18048.00     0.00   256.00     2.78   19.62   19.62    0.00   6.24  88.00
              [22] => sdd               0.00     0.00  206.00    0.00 24856.00     0.00   241.32    20.64   92.19   92.19    0.00   4.85 100.00
              [23] => sde               0.00     0.00   76.00    0.00  9728.00     0.00   256.00     1.74   22.20   22.20    0.00  11.45  87.00
              ...
             */

            //�?ếm N lần rồi lấy trung bình
            for ($i = 0; $i < count($arr); $i++) {
                $line = trim($arr[$i]);
                $line = preg_replace("'\s+'", ' ', $line);
                if (substr($line, 0, 2) != 'sd' && substr($line, 0, 2) != 'hd')
                    continue;

                $arr1 = explode(" ", $line);

                $disk = $arr1[0];
                $util = trim($arr1[count($arr1) - 1]);
                $wait = trim($arr1[count($arr1) - 4]);

                if (!isset($diskSummary[$disk])) {
                    $diskSummary[$disk] = 1;    //new thì đặt = 1 lần sau isset OK
                    $countDisk[$disk] = 0;      //B�? qua first found, vì số liệu ko đúng
                    $diskWait[$disk] = 0;       //B�? qua first found, vì số liệu ko đúng
                } else {
                    $diskSummary[$disk] += $util;
                    $countDisk[$disk]++;
                    $diskWait[$disk] += $wait;
                }
            }

            //Lấy trung bình:
            foreach ($diskSummary as $disk => $util) {
                //Vì ổ (dev) có thể được phân nhi�?u vùng, nên chỗ này là để tính performance chung :
                $percent = number_format($diskSummary[$disk] / $countDisk[$disk], 0);
                $wait = number_format($diskWait[$disk] / $countDisk[$disk], 0);
                $wait = str_replace(",", "", $wait);

                //echo "<br/> Disk -> util =  $disk => $util";

                foreach ($arrayDiskAndMount AS $cDisk) {
                    if ($cDisk->device == '/dev/' . $disk) {
                        $cDisk->util = $percent;
                        $cDisk->wait = $wait;
                        //break;
                    }
                }
            }
        }


        //Write to cache:
        CDisk::$mountUtilWaitArray = $arrayDiskAndMount;

        $dir = dirname($file);
        if (!file_exists(($dir)))
            @mkdir($dir, 0777, 1);

        if (!file_exists($dir)) {
            return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": not found cache dir? $dir ", '###');
        }

        $serial = serialize($arrayDiskAndMount);
        outputW($file, $serial);
        $checkSerial = file_get_contents($file);
        if ($checkSerial <> $serial)
            return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": can not write serial cache?", '###');

        return $arrayDiskAndMount;
    }

    /*
     * $getUtil = 0: get util, wait or not (getUtil=1 will take about +3 second than not getUtil = 0)
     * $getInCache 0/1 : get direct or in cache file
     * $timeCacheRange: if cache file older more 20s than current, so get direct and rewite cache
     */

    function getDiskInfoArrayRemote($server = null, $getUtil = 0, $getInCache = 1, $timeCacheRange = 20) {

        $baseUrlRemoteServer = "";
        //if(defined('BASE_URL'))
        //  $baseUrl = "/".BASE_URL;

        if (!isset($server))
            loi("Error " . __FUNCTION__ . ": empty server?", '###');

        $link = "http://$server:" . SERVER_INFO_WEB_PORT . "$baseUrlRemoteServer/tool/sysinfo.php?getDiskArrInfo=1&getUtil=$getUtil&getInCache=$getInCache&dTimeCache=$timeCacheRange";
        $link = "https://$server/tool/sysinfo.php?getDiskArrInfo=1&getUtil=$getUtil&getInCache=$getInCache&dTimeCache=$timeCacheRange";

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $content = @file_get_contents($link, false, stream_context_create($arrContextOptions));

        if (isset($content) && !empty($content)) {

            $contentOK = trim($content);
//            echo "<br/>\n $contentOK ";
            $arrayDiskAndMount = unserialize(base64_decode($contentOK));

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($arrayDiskAndMount);
//            echo "</pre>";
//            echo "<br> $contentOK";
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($arrayDiskAndMount);
//            echo "</pre>";


            if (!is_array($arrayDiskAndMount)){

                loi( "Error " . __FUNCTION__ . ": not valid remote array disk info ($contentOK)?");
            }

            return $arrayDiskAndMount;
        }
        else
            loi("Error " . __FUNCTION__ . ": can not call remote html12: $server/tool/sysinfo ?");
    }

    function getDiskInfoFromArray($arrayDiskObj, $mount) {
        if (!is_array($arrayDiskObj))
            loi("Error " . __FUNCTION__ . ": not array disk obj?");
        $diskObj = new CDisk();
        foreach ($arrayDiskObj AS $diskObj) {

            if ($diskObj->mount_point == $mount)
                return $diskObj;
        }
        return null;
    }

    function getDiskObjArray($getUtil = 0, $getInCache = 1, $timeCacheRange = 20) {


        $file = "/mnt/glx/weblog/cache/server/localDiskInfo";
        if ($getInCache) {

            if (file_exists($file) && filesize($file) > 0) {

                $filetime = filectime($file);
                if ($filetime > time() - $timeCacheRange) {
                    CDisk::$mountUtilWaitArray = $arrCDiskObj = unserialize(file_get_contents($file));
                    if (is_array($arrCDiskObj))
                        return $arrCDiskObj;
                }
            }
        }

        $ret = shell_exec('df -Hl'); //$ret = shell_exec('df -Hl'); //$ret = shell_exec(HTS("6466202d486c"));


        $arr = explode("\n", $ret);
        $arrayDiskAndMount = array();

        $count = 0;
        //Get all mount point and dev
        foreach ($arr as $key => $str) {
            $count++;

            //B�? dòng đầu tiên
            if ($count == 1)
                continue;

            $str1 = trim($str);
            //if(substr($str1,0,5)=="/dev/")
            //$str1 = substr($str1,5);
            $arrTMP = explode(" ", $str1);
            $disk = trim($arrTMP[0]);
            $mount = trim($arrTMP[count($arrTMP) - 1]);

            if (empty($disk) || empty($mount))
                continue;

            $cdisk = new CDisk();
            $cdisk->device = $disk;
            $cdisk->mount_point = $mount;
            $cdisk->disk_free_space = disk_free_space($mount);
            $cdisk->disk_total_space = disk_total_space($mount);
            $cdisk->disk_used_space = $cdisk->disk_total_space - $cdisk->disk_free_space;
            $cdisk->checktime = time();
            //  if(strstr($mount,"/media"))
            //    continue;
            //        $arrayDisk[] = $str1;
            $arrayDiskAndMount[] = $cdisk;
        }

        if ($getUtil) {

            //$ret = shell_exec("iostat -x 1 3");
            $ret = shell_exec("iostat -x 1 3");

            $arr = explode("\n", $ret);

            $diskSummary = array();
            $countDisk = array();
            $diskWait = array();
            $count = 0;

            /*
              ....
              [6] => sda               0.14   333.49   42.96    5.53  5460.36  1356.07   281.17     0.30    6.22    0.19   53.07   5.15  24.99
              [7] => sdb               0.95   264.05  134.65    3.60 16927.63  1070.62   260.38     0.00    2.49    2.17   14.21   0.46   6.40
              [8] => sdc               1.44   253.62  147.03    3.36 18575.80  1027.92   260.70     0.18    3.47    1.70   80.79   1.01  15.25
              [9] => sdd               1.85   239.42  137.93    3.22 17505.03   970.55   261.79     0.31    2.18    1.65   24.73   0.82  11.56
              [10] => sde               0.76   249.77  123.30    3.54 15472.77  1013.25   259.94     0.14    3.76    1.74   74.06   0.41   5.24
              [11] => sdf               0.99   217.71  129.66    3.17 16316.71   883.53   258.99     0.24    1.80    1.60    9.94   0.43   5.65
              [12] => sdg               0.03    69.36    9.90   21.92   423.88   365.21    49.59     0.12    3.93    0.64    5.41   0.17   0.55
              [13] => sdh               0.38  3299.06   93.72   33.81 11440.87 13331.50   388.49     0.15    3.84    2.01    8.90   0.77   9.88
              ...
              [19] => sda               0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00    0.00    0.00   0.00   0.00
              [20] => sdb               0.00     0.00  182.00    0.00 22780.00     0.00   250.33     4.68   25.64   25.64    0.00   5.45  99.20
              [21] => sdc               0.00     0.00  141.00    0.00 18048.00     0.00   256.00     2.78   19.62   19.62    0.00   6.24  88.00
              [22] => sdd               0.00     0.00  206.00    0.00 24856.00     0.00   241.32    20.64   92.19   92.19    0.00   4.85 100.00
              [23] => sde               0.00     0.00   76.00    0.00  9728.00     0.00   256.00     1.74   22.20   22.20    0.00  11.45  87.00
              ...
             */

            //�?ếm N lần rồi lấy trung bình
            for ($i = 0; $i < count($arr); $i++) {
                $line = trim($arr[$i]);
                $line = preg_replace("'\s+'", ' ', $line);
                if (substr($line, 0, 2) != 'sd' && substr($line, 0, 2) != 'hd')
                    continue;

                $arr1 = explode(" ", $line);

                $disk = $arr1[0];
                $util = trim($arr1[count($arr1) - 1]);
                $wait = trim($arr1[count($arr1) - 4]);

                if (!isset($diskSummary[$disk])) {
                    $diskSummary[$disk] = 1;    //new thì đặt = 1 lần sau isset OK
                    $countDisk[$disk] = 0;      //B�? qua first found, vì số liệu ko đúng
                    $diskWait[$disk] = 0;       //B�? qua first found, vì số liệu ko đúng
                } else {
                    @$diskSummary[$disk] += $util;
                    $countDisk[$disk]++;
                    @$diskWait[$disk] += $wait;
                }
            }

            //Lấy trung bình:
            foreach ($diskSummary as $disk => $util) {
                //Vì ổ (dev) có thể được phân nhi�?u vùng, nên chỗ này là để tính performance chung :
                $percent = number_format($diskSummary[$disk] / $countDisk[$disk], 0);
                $wait = number_format($diskWait[$disk] / $countDisk[$disk], 0);
                $wait = str_replace(",", "", $wait);

                //echo "<br/> Disk -> util =  $disk => $util";

                foreach ($arrayDiskAndMount AS $cDisk) {
                    if ($cDisk->device == '/dev/' . $disk) {
                        $cDisk->util = $percent;
                        $cDisk->wait = $wait;
                        //break;
                    }
                }
            }
        }


        //Write to cache:
        CDisk::$mountUtilWaitArray = $arrayDiskAndMount;

        $dir = dirname($file);
        if (!file_exists(($dir)))
            @mkdir($dir, 0777, 1);

        if (!file_exists($dir)) {
            echo "Error " . __FUNCTION__ . ": not found cache dir? $dir", '###';
            return null;
        }


        if($getInCache){
            $serial = serialize($arrayDiskAndMount);
            outputW($file, $serial);
            $checkSerial = file_get_contents($file);
            if ($checkSerial <> $serial) {
                loi("Error " . __FUNCTION__ . ": may be can not write serial cache? ($file)", '###');
                return null;
            }
        }

        return $arrayDiskAndMount;
    }

    function getDiskObjArrayRemote($server = null, $getUtil = 0, $getInCache = 1, $timeCacheRange = 20) {

        $baseUrl = '';
        //if(defined('BASE_URL'))
        //  $baseUrl = "/".BASE_URL;

        $cret = new CReturnError();
        if (!isset($server))
            return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": empty server?", '###');

        $link = "http://$server:" . SERVER_INFO_WEB_PORT . "$baseUrl/tool/sysinfo.php?getDiskArrInfo=1&getInCache=$getInCache&dTimeCache=$timeCacheRange";

        //echo "<br/> link = $link";

        $content = @file_get_contents($link);
        if (isset($content) && !empty($content)) {

            $contentOK = ($content);
            $arrayDiskAndMount = @unserialize($contentOK);

            if (!is_array($arrayDiskAndMount))
                return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": not valid remote array disk info?", __FILE__ . "(" . __LINE__ . ")  ");

            return $arrayDiskAndMount;
        }
        else
            return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": can not call remote html ?", __FILE__ . "(" . __LINE__ . ") $link");
    }

    function getDiskObjFromArray($arrayDiskObj, $mount) {

        $cret = new CReturnError();

        if (!is_array($arrayDiskObj)) {
            return $cret = CReturnError::returnErrorStatic($cret, "Error " . __FUNCTION__ . ": not array disk obj?", '###');
        }

        $diskObj = new CDisk();
        foreach ($arrayDiskObj AS $diskObj) {

            if ($diskObj->mount_point == $mount)
                return $diskObj;
        }
        return null;
    }

    static function CheckMountValid($folder) {

        $arrMountDisk = GetMountPoints();

        $found = 0;
        for ($i = 0; $i < count($arrMountDisk); $i++) {
            //echo "<br />  $dev => $mount";
            if ($arrMountDisk[$i]['mount'] == "$folder") {
                return 1;
                //echo "<br /> DEV = ".$arrMountDisk[$i]['disk'];
            }
        }
        return 0;
    }

    //Lấy free disk chứa file:
    static function getTotalDiskSizeInFilePath($filePath){

        $oDisk = new CDisk('/');
        $mm = $oDisk->getDiskObjArray(1, 1);
        $fileP1 = $filePath;
        $maxLenMount = 0;
        $lastSizeOK = 0;

        //Tìm moutpoint có path dài nhất nằm trong filepath:
        //FilePath max = 100 /
        if($mm)
            for($i = 0; $i< 100; $i++){
                foreach ($mm AS $oDisk){
                    if($oDisk->mount_point == substr($fileP1, 0, strlen($oDisk->mount_point))) {
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($oDisk);
//                echo "</pre>";
                        if(strlen($oDisk->mount_point) > $maxLenMount){
                            $maxLenMount = strlen($oDisk->mount_point);
                            $lastSizeOK = $oDisk->disk_total_space;
                        }
                    }
                }
                $fileP1 = dirname($filePath);
                if($fileP1 == '/' || !$fileP1)
                    break;
            }
        return $lastSizeOK;
    }

    /*
     * Window: getDiskVolumeName("d");
     */
    static function getDiskVolumeName($disk){
        // Try to grab the volume name
        if (preg_match('#Volume in drive [a-zA-Z]* is (.*)\n#i', shell_exec('dir '.$disk.':'), $m)) {
            $volname = ' ('.$m[1].')';
        } else {
            $volname = '';
        }
        return $volname;
    }

    static function getFreeDiskInFilePathV2($filePath){
        return disk_free_space($filePath);
    }

    //Lấy free disk chứa file:
    static function getFreeDiskInFilePath($filePath, $getUtil = 0, $inCache = 0){

        $oDisk = new CDisk('/');
        $mm = $oDisk->getDiskObjArray($getUtil, $inCache);
        $fileP1 = $filePath;
        $maxLenMount = 0;
        $lastSizeOK = 0;

        //Tìm moutpoint có path dài nhất nằm trong filepath:
        //FilePath max = 100 /
        if($mm)
            for($i = 0; $i< 100; $i++){
                foreach ($mm AS $oDisk){
                    if($oDisk->mount_point == substr($fileP1, 0, strlen($oDisk->mount_point))) {
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($oDisk);
//                echo "</pre>";
                        if(strlen($oDisk->mount_point) > $maxLenMount){
                            $maxLenMount = strlen($oDisk->mount_point);
                            $lastSizeOK = $oDisk->disk_free_space;
                        }
                    }
                }
                $fileP1 = dirname($filePath);
                if($fileP1 == '/' || !$fileP1)
                    break;
            }
        return $lastSizeOK;
    }

    function _ALAST(){


    }
}



function base64url_encode($plainText)
{
    $base64 = base64_encode($plainText);
    $base64 = trim($base64, "=");
    $base64url = strtr($base64, '+/', '-_');
    return ($base64url);
}



class retCurl
{
    public $httpCode;
    public $response;


    function __construct($httpCode, $response)
    {
        $this->httpCode = $httpCode;
        $this->response = $response;
    }

    function status()
    {
        return $this->httpCode;
    }
    function getContent()
    {
        return $this->response;
    }
}


function dumpdebug($strlog, $strFile = null) {
    $remote_ip = getenv('REMOTE_ADDR');
    $File = $_SERVER["PHP_SELF"];
    $datetime = date('Y-m-d H:i:s');
    if($strFile)
        output($strFile, "\n$datetime#$File#$remote_ip#$strlog");
    else
        output(GLOBAL_DEBUG_FILE, "\n$datetime#$File#$remote_ip#$strlog");
}



function sumaryDiskInfo($option = '') {

    $ignoreTotalInfo=0;
    $sql = "SELECT * FROM cloud_servers WHERE enable > 0 ORDER BY domain LIMIT 100";
//    $ret = mysql_query($sql);
//    if (!$ret)
//        die("Error DB query!");
//
    $db = MysqliDb::getInstance();
    $ret = $db->rawQuery($sql);

    $ret1 = "sumaryDiskInfo2<table border='1' cellpadding='1' cellspacing='1' style=\"font: 12px 'Courie new',helvetica,sans-serif; \">";
    //    echo "<table class='glx01_courie'>";
    $ret1 .= "<th style='background-color: lightgray;'>MountPoint</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Physical</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Total</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Used</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Free</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Util</th>";
    //echo "<th>%Performance</th>";
    $ret1 .=  "<th style='background-color: lightgray;'>Write?</th>";
    $ret1 .=  "<td style='background-color: lightgray;'>Temperature</td>";
    $ret1 .=  "<td style='background-color: lightgray;'>Serial</td>";
    $ret1 .=  "<td style='background-color: lightgray;'>Model</td>";

    $totalStore = 0;
    $totalFree = 0;
    $totalUsed = 0;
    $totalDisk = $count = 0;
    $mSizeDisk = [];
    //while ($row = mysql_fetch_array($ret)) {
    if($ret)
        foreach ($ret as $row) {

            $count++;
            //echo "<hr>";
            $id = $row['id'];
            $name = $row['name'];
            $domain = trim($row['domain']);
            $ip_internet = $row['ip_internet'];
            $ip_local = $row['ip_local'];
            $ram = $row['ram'];
            $mount_list = $row['mount_list'];

            $arrMount = explode(",", $mount_list);

            $countMount = count($arrMount);
            $totalDisk+=$countMount;

            $firstMount = $arrMount[0];
            $lastMount = $arrMount[count($arrMount) - 1];


            $comment = $row['comment'];


//        $domain = ClassUser4S::getProxyDownloadServer($domain);


            [$domainProxy, $portP ]= ClassUser4S::getProxyDownloadServer($domain);
//        echo "\n domainProxy = $domainProxy";
            $svGLX = new CServerGlx();
            try {
                $diskArrRemote = $svGLX->getRemoteDataDiskInfo($domainProxy, 0);
            } catch (Exception $e) {
                baoloi("ERROR Connect server2: $domainProxy <br> " . $e->getMessage());
                continue;
            }

            $cServer = new CServerGlx();
            $cServer->getRemoteServerInfoGlx($domainProxy);
            if ($cServer->cpuLoad1 > 100 && $cServer->cpuLoad2 > 100 && $cServer->cpuLoad3 > 100)
                $strCPULOAD = " ( <font color='red'> <strong>$cServer->cpuLoad1 / $cServer->cpuLoad2 / $cServer->cpuLoad3</strong></font> )";
            else
                $strCPULOAD = " ($cServer->cpuLoad1 / $cServer->cpuLoad2 / $cServer->cpuLoad3)";

            for ($i = 0; $i < count($arrMount); $i++) {


                $mount = "/mnt/" . $arrMount[$i];
                if (isset($diskArrRemote[$mount])) {
                    $info = $diskArrRemote[$mount];
                    $physical = $info["physical"];

                    $info["wait"] = str_replace(",", '.', $info["wait"]);
                    $info["util"] = str_replace(",", '.', $info["util"]);

                    $wait = $info["wait"];
                    $temp1 = $info["temperature"]  ?? '---';;
                    $model1 =  $info["model"] ?? '---';
                    $serial1 = $info["serial"]  ?? '---';;


                    $util = $info["util"] . " ($wait) ";

                    $disk_free = ($info["disk_free"]);
                    $disk_total = ($info["disk_total"]);

                    if(!isset($mSizeDisk[$disk_total])){
                        $mSizeDisk[$disk_total] = 1;
                    }
                    else
                        $mSizeDisk[$disk_total]++;

                    $writable = $info["writable"];
                    if ($writable == 0)
                        $writable = "<strong>$writable</strong>";

                    $totalStore += $disk_total;
                    $totalFree += $disk_free;
                    $totalUsed += $disk_total - $disk_free;

                    if ($wait > 90)
                        $util = "<strong>$util </strong>";
                    //echo "<br /> OK $mount";

                    $freePercent = number_format(100 * $disk_free / $disk_total, 2);
                    if ($freePercent < 10)
                        $freePercent = "<strong>$freePercent</strong>";

                    $disk_totalGB = number_format($disk_total / _GB);
                    $disk_freeGB = number_format($disk_free / _GB);
                    $disk_UsedGB = number_format(($disk_total - $disk_free) / _GB);

                    $domainSort = $domain ;//strtoupper(strstr($domain, '.', true));

                    if ($i == 0) {
                        $ret1 .=  "<tr><td colspan=7 style='background-color: lavender;'> <center> <strong>$domainSort </strong> $strCPULOAD </center></td></tr>";
                        $ret1 .=  "</tr>";
                        $ret1 .=  "<td style='background-color: white;'>MountPoint</td>";
                        $ret1 .=  "<td style='background-color: white;'>Physical</td>";
                        $ret1 .=  "<td style='background-color: white;'>Total</td>";
                        $ret1 .=  "<td style='background-color: white;'>Used</td>";
                        $ret1 .=  "<td style='background-color: white;'>Free</td>";
                        $ret1 .=  "<td style='background-color: white;'>Util</td>";
                        $ret1 .=  "<td style='background-color: white;'>Write?</td>";
                        $ret1 .=  "<td style='background-color: white;'>Temperature</td>";
                        $ret1 .=  "<td style='background-color: white;'>Serial</td>";
                        $ret1 .=  "<td style='background-color: white;'>Model</td>";
                        $ret1 .=  "</tr>";
                    }

                    $padTR = "";
                    $padTR = " style='background-color: snow;'";
                    if ($count % 2 == 0)
                        $padTR = " style='background-color: beige;'";
                    $ret1 .=  "<tr $padTR>";
                    $padTD = "";
                    //$padTD = "style='background-color: green;'";

                    $ret1 .=  "<td $padTD>[$domainSort] <strong>$mount</strong></td>";
                    $ret1 .=  "<td>$physical</td>";
                    $ret1 .=  "<td style = 'text-align: right;'>$disk_totalGB GB</td>";
                    $ret1 .=  "<td style = 'text-align: right;'>$disk_UsedGB GB</td>";
                    $ret1 .=  "<td style = 'text-align: right;'>$disk_freeGB GB ($freePercent %)</td>";
                    $ret1 .=  "<td style = 'text-align: right;'>$util % </td>";
                    $ret1 .=  "<td><center>$writable</center></td>";

                    $temp10 = trim(explode("(", $temp1)[0]);
                    if($temp10 > 60)
                        $temp1 = "<strong style='color: red'>$temp1</strong>";
                    $ret1 .=  "<td style = 'text-align: left;'>$temp1 </td>";
                    $ret1 .=  "<td style = 'text-align: left;'>$serial1 </td>";
                    $ret1 .=  "<td style = 'text-align: left;'>$model1 </td>";
                    //echo "<td>$util</td>";

                    $ret1 .=  "</tr>";
                }
                else {
                    $padTR = " style='background-color: snow;'";
                    if ($count % 2 == 0)
                        $padTR = " style='background-color: beige;'";
                    $ret1 .=  "<tr $padTR>";
                    $domainSort = strtoupper(strstr($domain, '.', true));
                    $ret1 .=  "<td colspan=7 ><font color='red'>[$domainSort] $mount : <strong>Missing this mount? Please check in server $domainSort!</strong></font></td>";
                    $ret1 .=  "</tr>";
                }
            }
        }

    $ret1 .=  "</table>";

    if ($ignoreTotalInfo == 1)
        return;
    $totalStore = ByteSize($totalStore);
    $totalUsed = ByteSize($totalUsed);
    $totalFree = ByteSize($totalFree);
    $ret1 .=  "<strong>Total = $totalStore , Used = $totalUsed, Free = $totalFree</strong>, Total Disk: $totalDisk";
    $ret1 .=  "<br/>\n";
    foreach ($mSizeDisk AS $size=>$num){
        $ret1 .=  "DSize: " . ByteSize($size) . "($num) <br>";
    }
    echo $ret1;

    return $ret1;
}

function fixFileNameToIndexElastic($name){
    if(!$name)
        return null;
    return strtolower(str_replace(['_', ',', '>', '?', '<', '.', '-', '+', '|', '~', '!', '@', '`', '&', '^', '$', '(', ')', '[', ']', '{', '}', '=' , ':', "*", "/", '\\'], ' ', $name));
}


class clsMomo {

    public static $name = 'momo';

    //DEV
    //$partnerCode = "MOMON2RL20190410"; //4s
    //$accessKey = "tUt3U1aCcLNGdNyN";
    //$serectkey = "6UPGqftR07H2A89I5cXkptg8l9t8AWGj";
    //$endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";

    //PROD
    static public $partnerCode = "MOMON2RL20190410"; //4s
    static public $accessKey = "tXHg8ImeMuWoYHyz";
    static public $serectkey = "eTrRL7rIG1iBFfko3XOireM9C9WukD3l";
    static public $endpoint = "https://payment.momo.vn/gw_payment/transactionProcessor";
    static public $returnUrl = "https://4share.vn/buy-vip/momoReturn";
    static public $notifyurl = "https://4share.vn/buy-vip/momoNotify";



    static function momoNotifyOrReturnWeb($params, $web = 0) {
        $siteId = \App\Models\SiteMng::getSiteId();
        $fileLog = "/var/glx/weblog/momo_$siteId.log";
        outputT($fileLog,"---MOMO momoNotifyOrReturnWeb : --Web = $web  ");
        $partnerCode = self::$partnerCode;
        $accessKey = self::$accessKey;
        $serectkey = self::$serectkey;
        outputT($fileLog, serialize($params));
        try {
            if (isset($params['orderId'])) {
                //Check Valid param:
                $ret = [];
                $ret['partnerCode'] = $partnerCode;
                $ret['accessKey'] = $accessKey;

                $orderType = $params['orderType'];
                $transId = $params['transId'];
                $localMessage = $params['localMessage'];
                $orderInfo = $params['orderInfo'];
                $orderId = $params['orderId'];
                if ($odx = \App\Models\OrderInfo::where('transaction_id_local', $orderId)->first()) {
                    if($web){
                        bl("Đơn hàng đã thành công: $odx->created_at | $orderId", " <a href='/member'> Tiếp tục </a>");
                        return;
                    }
                    else{
                        ob_clean();
                        die(json_encode(['errorCode' => 0, 'extraData' => "OrderID $orderID da thanh cong!"]));
                    }
                }

                $responseTime = $params['responseTime'];
                $errorCode = $params['errorCode'];
                $payType = $params['payType'];
                $requestId = $params['requestId'];
                $amount = $params['amount'];
                $extraData = $params['extraData'];
                $signature = $params['signature'];
                $message = $_REQUEST['message'];

                $str = "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&amount=$amount&orderId=$orderId&orderInfo=$orderInfo&orderType=$orderType&transId=$transId&message=$message&localMessage=$localMessage&responseTime=$responseTime&errorCode=$errorCode&payType=$payType&extraData=$extraData";
                $sig = hash_hmac('sha256', $str, $serectkey);

                outputT($fileLog,"Sign calculate:");
                if ($signature != $sig) {
                    outputT($fileLog,"STR = $str");
                    outputT($fileLog,"*** Error: Not valid SIG? $sig VS $signature ");
                    if($web){
                        bl("Có lỗi: chữ ký không hợp lệ!");
                    }
                    else
                    {
                        $ret['errorCode'] = 3;
                        $ret['extraData'] = "Giao dịch có lỗi!";
                        $ret['signature'] = hash_hmac('sha256', "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&orderId=$orderId&errorCode=$errorCode&message=$message&responseTime=$responseTime&extraData=$extraData", $serectkey);
                        ob_clean();
                        die(json_encode($ret));
                    }
                }

                $orderAllInfo =  unserialize(\Illuminate\Support\Facades\Cache::get('momo_request_id.'.$orderId));
//                $uid = $orderAllInfo['userid'];
                $prodId = $orderAllInfo['product_id'];
                $prod = \App\Models\Product::find($prodId);
                if(!$prod){
                    outputT($fileLog,"Error: Not found product id = $prodId");
                    if($web){
                        bl("Có lỗi: sản phẩm không đúng, hãy liên hệ Admin ($orderId)!");
                    }
                    else {
                        $ret['errorCode'] = 1;
                        $ret['extraData'] = "Loi: not found productid : $prodId";
                        $ret['signature'] = hash_hmac('sha256', "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&orderId=$orderId&errorCode=$errorCode&message=$message&responseTime=$responseTime&extraData=$extraData", $serectkey);
                        ob_clean();
                        die(json_encode($ret));
                    }
                }

                $uid = explode("-", $orderId)[1];

                outputT($fileLog,"Signature OK - notification!");

                $ret['requestId'] = $params['requestId'];
                $ret['orderId'] = $params['requestId'];
                $ret['responseTime'] = nowyh();
//                $ret['message'] = "$thongTinGiaoDich!";
                if ($errorCode != 0) {
                    outputT($fileLog," Giao dịch KHÔNG thành công!");
                    if($web){
                        bl("Có lỗi: Giao dich co loi: $errorCode!");
                    }
                    else {
                        $ret['errorCode'] = 1;
                        $ret['extraData'] = "Giao dịch KHÔNG thành công!";
                        $ret['signature'] = hash_hmac('sha256', "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&orderId=$orderId&errorCode=$errorCode&message=$message&responseTime=$responseTime&extraData=$extraData", $serectkey);
                    }
                } else {
                    if($web)
                        bl("Giao dịch thành công!");
                    outputT($fileLog," Giao dịch thành công!");
//                    require_once "momo_insert_gold.php";

                    $orderStd = new \stdClass();
//                    $orderStd->id = $orderId;
                    $orderStd->user_id = $uid;
                    $orderStd->transaction_id_local = $orderId;
                    $orderStd->money = intval($amount);
                    $orderStd->transaction_id_remote = $transId;

                    \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($orderStd, $prod, request()->getClientIp(),self::$name);

                    if(!$web){
                        $ret['errorCode'] = 0;
                        $ret['extraData'] = "Giao dịch thành công!";
                        $ret['signature'] = hash_hmac('sha256', "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&orderId=$orderId&errorCode=$errorCode&message=$message&responseTime=$responseTime&extraData=$extraData", $serectkey);
                    }
                }

                if(!$web){
                    ob_clean();
                    die(json_encode($ret));
                }
            }
        } catch (Exception $e) {
            if($web){
                bl(" Giao dịch có lỗi:" . $e->getMessage());
            }
            else {
                $ret['errorCode'] = 1;
                $ret['message'] = $e->getMessage();
                $ret['extraData'] = "Giao dịch có lỗi!";
                $ret['signature'] = hash_hmac('sha256', "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&orderId=$orderId&errorCode=$errorCode&message=$message&responseTime=$responseTime&extraData=$extraData", $serectkey);
                outputT($fileLog," Giao dịch có lỗi:" . $e->getMessage());
                ob_clean();
                die(json_encode($ret));
            }
        }
    }

    static function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    static function buyVip($userid, \App\Models\Product $product, $params) {

        $amount = $price = $product->price;
        $amount = "$amount";
//        die("PRICE = $amount");

        //$amount = "2000";
//        $orderInfo = eth1b($userid);
        $orderInfo = "Mua $product->name";
        //$xid = microtime(1);

        $orderid = time() . "-".$userid;
        $requestId = time() . "-".$userid;
        $requestType = "captureMoMoWallet";
        $extraData = "merchantName=;merchantId=";//pass empty value if your merchant does not have stores else merchantName=[storeName]; merchantId=[storeId] to identify a transaction map with a physical store

        if(isset($params['buy_user_coin'])){
            $extraData = "buy_user_coin";
        }

        //before sign HMAC SHA256 signature
        \Illuminate\Support\Facades\Cache::put('momo_request_id.'.$orderid, serialize(['orderId'=>$orderid , 'userid'=>$userid, 'product_id'=>$product->id]), 7200);

        $partnerCode = self::$partnerCode;
        $accessKey = self::$accessKey;
        $serectkey = self::$serectkey;
        $endpoint = self::$endpoint;
        $returnUrl = self::$returnUrl;
        $notifyurl = self::$notifyurl;

//        echo "<br/>";
        $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId .
            "&amount=" . $price . "&orderId=" . $orderid . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl .
            "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData;
        //echo "<br> Raw signature: " . $rawHash . "\n";
        $signature = hash_hmac("sha256", $rawHash, $serectkey);

        $data = array('partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderid,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);

        $result = self::execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json

//    echo "Result: \n";
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($jsonResult);
//    echo "</pre>";

        if(isset($jsonResult['payUrl'])) {
            $urlCall = $jsonResult['payUrl'];
            header("Location: $urlCall");
            die();
            return;
//        echo "<br/>";
//        echo "<br/>Call = <a target='_blank' href='$urlCall'>$urlCall</a>";
//            echo "<br/>";
//            echo "<br/>";
//            tb("<div style='text-align: center; padding: 20px'>Bạn sẽ nạp $amount VNĐ vào tài khoản: <br/> <a href='$urlCall'> <br><button class='btn btn-info'>Tiếp tục Thanh toán với MOMO</button> </a></div> <br/>");
        }else{
            bl("Có lỗi xảy ra? Hãy liên hệ Admin, cảm ơn bạn!");
        }
    }
}

class clsBaoKim {

    public static $name = 'baokim';
    static function buyVip($params){
        try {
            $uid = getCurrentUserId();
            $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

            if (!$mrc_order_id = ($params['mrc_order_id'] ?? '')) {

                bl("Not valid?");

                header("Location: /buy-vip");
                die();
            }

            $siteId = \App\Models\SiteMng::getSiteId();

            $keyCache = "buy_vip.$siteId.".$mrc_order_id;

            $siteId = \App\Models\SiteMng::getSiteId();
            setLogFile("/var/glx/weblog/baokim_$siteId.log");
            ol00("-------------- ");

//
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($params);
//    echo "</pre>";
//
//    die();

            ol00(" mrc_order_id = $mrc_order_id");
            $prodId = explode("-", $mrc_order_id)[1];


            if (!$prodId || !is_numeric($prodId))
                loi("Not valid product id?");
            if (!$prod = \App\Models\Product::find($prodId))
                loi("Not found product Id!");

            if($prod->status != 1)
                loi("Product not active: $prodId");

            //Nếu url forward từ bk về:
            //Thanh toán xong, trở lại url:
            if (isset($params['created_at'])) {
                //$urlSuccess = "https://".DOMAIN_MAIN."/?created_at=2021-09-01+10%3A22%3A27&id=330169&mrc_order_id=4sh.3.1630496449.8666&stat=c&total_amount=10000.00&txn_id=107492&updated_at=2021-09-01+10%3A23%3A37&checksum=b33a17d541b16ab118fc4e0fd100e8d9a9fcf98f36bc9ba775f004bf0f9db721";
                $urlSuccess = \LadLib\Common\UrlHelper1::getFullUrl();

                //1. load array các tham số trên url_success,
                // loại bỏ trường checksum cũng như các tham số của merchant (không do bảo kim truyền)
                $parts = parse_url($urlSuccess);
                parse_str($parts['query'], $query);
                $checksum = $query['checksum'];
                unset($query['checksum']);

                $orderID = $params['mrc_order_id'];
                //2. sort array các tham số theo key
                ksort($query);
                $total_amount = $params['total_amount'];
                if ($prod->price != intval($total_amount)) {
                    loi("Price not valid?");
                }

                $uid = explode(".", $mrc_order_id)[0];


                //4. Tạo và so sánh checksum
                $myChecksum = hash_hmac('sha256', http_build_query($query), \BaoKimAPI2021::API_SECRET);
                $transId = $params['id'];
                if ($checksum == $myChecksum) {

//            \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($uid, $orderID, $total_amount, $prod, $transId, request()->getClientIp());
                    $orderStd = new \stdClass();
                    $orderStd->id = $orderID;
                    $orderStd->user_id = $uid;
                    $orderStd->transaction_id_local = $mrc_order_id;
                    $orderStd->money = $total_amount;
                    $orderStd->transaction_id_remote = $transId;

//                \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($uid, $orderID, $total_amount, $prod, $transId, request()->getClientIp());
                    \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($orderStd, $prod, request()->getClientIp(), self::$name);

                    ol00("DONE BILL: ");
                    echo "<br/>\n";
                    tb("Hóa đơn đã toán thành công: $orderID , Số tiền : $total_amount", "<a href='/'> TRỞ LẠI </a> ");
                    //echo("<h2  style='text-align: center'></a></h2>");
                    echo "<br/>\n";
                } else {
                    bl("Error: Not valid checksum payment?");
                }
            } else {
                $client = new \GuzzleHttp\Client(['timeout' => 20.0]);
                $options['query']['jwt'] = \BaoKimAPI2021::getToken();
                $total_amount = $prod->price;
                $options['form_params'] = [
                    'mrc_order_id' => $params['mrc_order_id'],// . ".$prod",
                    'total_amount' => $total_amount,
                    'description' => $params['description'],
                    'url_success' => 'https://' . $domain . '/buy-vip',
                    //      'bpm_id' => '97',
                    'merchant_id' => '35589',
                    //            'accept_qrpay'=>1,
                    'customer_email' => $params['customer_email'],
                    'customer_phone' => $params['customer_phone'],
                    'webhooks' => "https://$domain/webhookBk"
                    //        'customer_name' => 'Nguyen Van A',
                    //        'customer_address' => '102, Thái Thịnh, phường Trung Liệt, quận Đống Đa.'
                ];

                //echo '<pre>'.print_r($options, true).'</pre>';die();
                //https://api.baokim.vn/payment/
                $response = $client->request("POST", "https://api.baokim.vn/payment/api/v5/order/send", $options);
                //$response = $client->request("POST", "https://dev-api.baokim.vn/payment/api/v5/order/send", $options);
                $dataResponse = json_decode($response->getBody()->getContents());
                if (!isset($dataResponse->data)) {
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($options);
//            echo "</pre>";
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($dataResponse);
//            echo "</pre>";

                    if($dataResponse->code ?? ''){
                        if($dataResponse->code == 7){
                            $link = \Illuminate\Support\Facades\Cache::get($keyCache);
                            header("Location: $link");
//                            bl(" <a href='$link' style='color: royalblue'> Tiếp tục Thanh toán: <b> $prod->name </b> </a> hoặc  <a  style='color: royalblue' href='/buy-vip'> Trở lại </a> ");
                            die();
                        }
                    }

                    bl(serialize($dataResponse->message) , " <a href='/buy-vip'> Trở lại </a>");
                }
                else
                    if (isset($dataResponse->data->order_id)) {
                        if (isset($dataResponse->data->payment_url)) {
                            $linkBK = $dataResponse->data->payment_url;
                            $total_amountV = number_formatvn0($total_amount);

                            echo "<h4 data-code-pos='9834758934785934' style='text-align: center'> Bạn đang chọn gói <b> $prod->name </b> <br> $total_amountV VND </h4>";

                            //Tạo 1 cache để lưu lại thông tin gói vip mà user đang chọn
                            \Illuminate\Support\Facades\Cache::put($keyCache, $linkBK, 60 * 20);

                            if (!auth()->id()) {
                                echo ("Bạn cần <a href='/login'> Đăng nhập </a> để mua gói VIP");
                            } else {
                                echo("<a href='$linkBK' class='btn btn-primary rounded-pill'> Tiếp tục </a>");
                            }
                        }
                    }

            }


        } catch (Throwable $e) { // For PHP 7
            bl("Có lỗi: " . $e->getMessage(), "<a href='/buy-vip'> Trở lại </a>");

            if(isDebugIp()){
                $strTrace = $e->getTraceAsString();
                $m1 = explode("\n", $strTrace);
                if(0){
                    echo "\n<div style='text-align: left; font-size: 60%'>";
                    foreach ($m1 AS $line){
                        if(str_contains($line, '/vendor/'))
                            continue;
                        echo "<br/>\n -- $line";
                        //        echo "<pre>";
                        //        print_r($e->getTraceAsString());
                        //        echo "</pre>";
                    }
                    echo "\n</div>";
                }
            }
        }

    }
}


class clsReplicateFile
{
    static $lastServerAndDiskRepDone = '';

    /**
     * Kiểm tra trong cloud file trùng md5 (cả local và remote) với file khác, nếu trùng thì gán lại idlink cho các file upload
     * @param $cfile
     * @param $md5
     * @return int
     */
    static function checkIfDuplicateMD5AndReAsign($cfile, $md5)
    {
        if (!$md5)
            return 0;

        $mSameMd5 = \App\Models\FileCloud::where("id", '!=', $cfile->id)->where("md5", $md5)->get();
        $coFileTrung = 0;
        if ($md5 && \App\Models\FileCloud::where("id", '!=', $cfile->id)->where("md5", $md5)->count() > 0)
            foreach ($mSameMd5 as $fileSameMd5) {

                if ($fileSameMd5 instanceof \App\Models\FileCloud) ;
                //Kiem tra file remote co ton tai khong?
                //Nếu file remote còn tồn tại, thì gán idlink cho Các upload file, và xóa FileCloud này
                try {
                    if ($fileSameMd5->checkRemoteFileExist() || (file_exists($fileSameMd5->file_path) && filesize($fileSameMd5->file_path))) {
                        //Tim cac file co idlink nay:
                        $mFLink = \App\Models\FileUpload::where("cloud_id", $cfile->id)->get();
                        if ($mFLink->count() > 0) {
                            foreach ($mFLink as $fileUp) {
                                $fileUp->addLog("Change CloudLink $fileUp->cloud_id -> $cfile->id");
                                $fileUp->cloud_id = $fileSameMd5->id;
                                $fileUp->save();
                            }
                        }
                        $cfile->addLog("Same md5 of id = $fileSameMd5->id");
                        $cfile->save();
                        $coFileTrung = 1;
                    }
                } catch (\Exception $exception) {
                    echo "<br/>\n Error2: " . $exception->getMessage();
                    // continue;
                }
            }

        return $coFileTrung;

    }

    static function getDiskListRemote($domain)
    {
        $info = file_get_contents("https://$domain/tool/sysinfo.php?getDiskArrInfo=1&getUtil=1&getInCache=1");
        $info = base64_decode($info);
        $mmDisk = unserialize($info);

        return $mmDisk;
    }

    static function getDiskAllServerReplicate()
    {
        $cServer = new \App\Models\CloudServer();
        $mmSv = $cServer::where("replicate_now", 1)->get();
        $mServerDiskFree = [];
        foreach ($mmSv as $svx) {
            $domain = $svx->domain;
            //Get free disk remote :

            if($svx->proxy_domain)
                $domain = $svx->proxy_domain;

            $mDisk = self::getDiskListRemote($domain);
            foreach ($mDisk as $oneDisk) {
                if (str_starts_with($oneDisk->mount_point, '/mnt/sd')) {
                    if ($oneDisk->util < 90) {
                        $mServerDiskFree[] = "$domain/" . basename($oneDisk->mount_point);
                    }
                }
            }
        }
        return $mServerDiskFree;
    }

    static function getRemoteDiskInfo($server, $quick = 0) {
        $link = ("https://$server" . "/sysinfo_glx.html?disklist_ex2=1");
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 5,  //1200 Seconds is 20 Minutes
            )
        ));
        $str = @file_get_contents($link, false,$ctx);
        if (!$str) {
            return null;
        }
        $mm = json_decode($str);
        return $mm;
    }

    static function getDiskFreeToReplicate($fileSizeNeedFree = 0 ,$revert = 0)
    {
        if(!$fileSizeNeedFree)
            $fileSizeNeedFree = 20 * _GB;

        $cServer = new \App\Models\CloudServer();
        $mmSv = $cServer::where("replicate_now", 1)->get();
        if($revert)
            $mmSv = $cServer::where("replicate_now", 1)->orderBy('id', 'desc')->get();

        $mServerDiskFree = [];
        foreach ($mmSv as $svx) {
            $domain = $svx->domain;
            //Get free disk remote :

            $domainProx = $domain;
            if($svx->proxy_domain)
                $domainProx = $svx->proxy_domain;

//            $mDisk = self::getDiskListRemote($domain);
            $mDisk = self::getRemoteDiskInfo($domainProx);

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mDisk);
//            echo "</pre>";

            foreach ($mDisk as $oneDisk) {
                if (str_starts_with($oneDisk->mount, '/mnt/s')) {
                    if ($oneDisk->free > $fileSizeNeedFree + 20 * _GB) {
                        $mServerDiskFree["$domain/" . basename($oneDisk->mount)] = $oneDisk->free;
//                        $mServerDiskFree[] = "$domain/" . basename($oneDisk->mount);
                    }
                }
            }
        }
        return $mServerDiskFree;
    }

    static function getNextServerDiskFree($fileSizeNeedFree = 0, $revert = 0) {
        $mServerDiskFreeWithFree = self::getDiskFreeToReplicate($fileSizeNeedFree , $revert);
        $mServerDiskFree = array_keys($mServerDiskFreeWithFree);
        if (!$mServerDiskFree)
            return null;
        if (!self::$lastServerAndDiskRepDone)
            return self::$lastServerAndDiskRepDone = $mServerDiskFree[0];

        $markFound = 0;
        foreach ($mServerDiskFree as $index => $svDisk) {
            //Neu tim thay lastServerAndDiskRepDone thi lay next
            if (self::$lastServerAndDiskRepDone == $svDisk && $index < count($mServerDiskFree) - 1) {
                $markFound = 1;
                continue;
            }
            //Neu da đánh dấu thấy rồi thì return;
            if ($markFound) {
//                echo "\n FoundMard $svDisk";
                self::$lastServerAndDiskRepDone = $svDisk;
                break;
            }
        }
        //Nếu hết vòng lặp mà không có mark fond thì lấy phần tử đầu tiên
        if (!$markFound) {
            self::$lastServerAndDiskRepDone = $mServerDiskFree[0];
//            echo "\n --- Not markfound $lastServerAndDiskRepDone ";
        }
        return self::$lastServerAndDiskRepDone;
    }
}

function searchElastic($prs, $dbName){

    $svEl = 'elasticSv';
    $client = \Elastic\Elasticsearch\ClientBuilder::create()
        ->setBasicAuthentication('elastic',env('ELASTIC_PASSWORD'))
        ->setHosts(["http://$svEl:9200"])
        ->setSSLVerification(false)->build();


//        return;
////Kiểm tra xem Index đã tồn tại không
    $indexExist = $client->indices()->exists(['index' => $dbName]);
    $client->indices()->exists(['index' => $dbName]);
    if (!$indexExist) {
        $strRet = ("Error: db not ready2?");
        ol3($strRet);
        rtErrorApi($strRet);
        die();
    }

    $response = $client->search($prs);

    return $response;
}


function getParamForElastic($searchString, $params, $dbName) {

    $foundMulti = 0;
    $arrBlackWord = \clsValidate::getBlackWordList();
    //Kieu search cua vietmediaf
    if(strstr(UrlHelper1::getFullUrl(), '&ext=ts,mkv,iso,mp4,m2ts,avi,wmv,flv,mpeg,asf,flv,mka,m4a,aac') !== false){
        $fromSize = 800;
    }
//        require 'vendor/elastic/vendor/autoload.php';
    if (isset($params['ext']) && $params['ext']) {
        $params['ext'] = strtolower($params['ext']);
        $params['ext'] = str_replace(" ", '', $params['ext']);
        //$params['ext'] = preg_replace("/[^0-9A-Za-z]/", "", $params['ext']);
        $params['ext'] = substr($params['ext'], 0, 30);

        $mSearchExt = ["match" => ['ext' => $params['ext']]];
        if (strstr($params['ext'], ',')) {

            $params['ext'] = trim($params['ext']);
            $mE = explode(",", $params['ext']);
            if (count($mE) > 1) {
                $m11 = [];
                foreach ($mE as $kE => $ve) {
                    $m11[] = ["match" => ['ext' => $ve]];
                }
                $mSearchExt = ['bool' => ['should' => $m11]];
            }
        }
    }

//        $SELF = "/$current_module/$current_controller/$current_action";
//        $clink = UrlHelper1::getUrlRequestUri();
//        $linkNotSearch = UrlHelper1::setUrlParam(null, 'search_string', null);

    $linkSizeAsc = UrlHelper1::setUrlParam(null, "sort_by", 'size_asc');
    $linkSizeDes = UrlHelper1::setUrlParam(null, "sort_by", 'size_desc');

    if (isset($params['limit']) && is_numeric($params['limit']))
        $limit = $params['limit'];
    else
        $limit = $params['limit'] = 20;

    $cPage = 1;
    if (isset($params['page']))
        $cPage = $params['page'];

    if(!$cPage || $cPage <= 0 || !is_numeric($cPage))
        $cPage = $params['page'] = 1;

    $offset = ($cPage - 1) * $limit;

    $sortIcon = "";
    $linkSet = $linkSizeAsc;
    if (!isset($params['sort_by'])) {

    } else {
        $sort_by = $params['sort_by'];
        if ($sort_by == 'size_asc') {
            $linkSet = $linkSizeDes;

        }
        if ($sort_by == 'size_desc') {
            $linkSet = $linkSizeAsc;

        }
    }
    $padExactlyStyle = '';
    if (isset($params['exactly']) && $params['exactly']) {
    }

    $fromSize = 0;
    if (isset($params['from_size']) && is_numeric($params['from_size']) && $params['from_size'] > 0) {
        $fromSize = $params['from_size'];
    }

    $toSize = 0;
    if (isset($params['to_size']) && is_numeric($params['to_size']) && $params['to_size'] > 0) {
        $toSize = $params['to_size'];
        if ($fromSize > $toSize) {
            $tmp = $fromSize;
            $fromSize = $toSize;
            $toSize = $fromSize;
        }
    }

    //if(!$fromSize)
    if (isset($_GET['format_as']) && ($_GET['format_as'] == 'vietmediaf1' || $_GET['format_as'] == 'vietmediaf2')){
//            die('xxxx');
        $fromSize = 800;
    }

    if (strstr($searchString, ' ')) {
        $mm1 = explode(" ", $searchString);
        $qr1 = [];
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mm1);
//            echo "</pre>";
        foreach ($mm1 as $oneW) {
            if (strlen($oneW) >= 3) {
                $foundMulti = 1;
            }
        }
        if ($foundMulti) {
            $qr1 = ["bool" =>
                [
                    "should" => [
                        ["match_phrase" => ['name' =>
                            ['query' => $searchString,
                                "boost" => 10 //Ưu tiên xuất hiện cả 2 từ
                            ]]],
                        ["fuzzy" => ['name' => $searchString]],
                        //["match" => ['name' => $searchString2 ]],
                    ],
                ]
            ];

            if (isset($params['exactly']) && $params['exactly']) {
                $qr1 = ["bool" =>
                    [
                    ]
                ];

            }
            foreach ($mm1 as $oneW) {
                if (strlen($oneW) >= 3) {
                    $foundMulti = 1;
                    if (isset($params['exactly']) && $params['exactly']) {
                        $qr1['bool']['must'][] = ['match' => ['name' => $oneW]];

                    } else
                        $qr1['bool']['should'][] = ['fuzzy' => ['name' => $oneW]];
                }
            }
            if (isset($params['ext']) && $params['ext']) {
                $qr1 = ["bool" =>
                    [
                        "must" => [
                            $mSearchExt,
                            ["bool" => $qr1['bool']]
                        ],
                    ]
                ];
            }

            if ($fromSize)
                $qr1["bool"]['filter']['range']['size']['gte'] = $fromSize * _MB;
            if ($toSize)
                $qr1["bool"]['filter']['range']['size']['lte'] = $toSize * _MB;

        }
    }
    if (!$foundMulti) {
        if (isset($params['exactly']) && $params['exactly']) {
            $qr1 = [
                'match' => [
                    'name' => $searchString
                ]
            ];
        } else
            $qr1 = [
                'fuzzy' => [
                    'name' => $searchString
                ]
            ];
        if (isset($params['ext']) && $params['ext']) {
            $qr1 = ["bool" =>
                [
                    "must" =>
                        [
                            $qr1,
                            $mSearchExt,
                        ],
                ]
            ];
        }

        if ($fromSize) {
            if (!isset($qr1['bool'])) {
                $tmp = $qr1;
                $qr1 = [];
                $qr1["bool"]['must'] = $tmp;
//                    $qr1['bool']['filter']['range'] = ['size' => ['gte' => $fromSize * _MB]];
            }
            $qr1['bool']['filter']['range']['size']['gte'] = $fromSize * _MB;
        }

        if ($toSize) {
            if (!isset($qr1['bool'])) {
                $tmp = $qr1;
                $qr1 = [];
                $qr1["bool"]['must'] = $tmp;
//                    $qr1['bool']['filter']['range'] = ['size' => ['gte' => $fromSize * _MB]];
            }
            $qr1['bool']['filter']['range']['size']['lte'] = $toSize * _MB;
        }
    }

    $prs = [
        'index' => $dbName,
        'type' => 'article_type',
        'from' => $offset,
        'size' => $limit,
        'body' => ['query' => $qr1,
//            'sort' =>['id'=>['order'=>'desc']]
        ],
    ];

    if (isset($sort_by) && $sort_by == 'size_asc') {
        $prs['body']['sort'] = ['size' => ['order' => 'asc']];
    }
    if (isset($sort_by) && $sort_by == 'size_desc') {
        $prs['body']['sort'] = ['size' => ['order' => 'desc']];
    }

    if (isset($params['sort_by'])) {
        $sort_by = $params['sort_by'];
        if ($sort_by == 'old') {
            $prs['body']['sort'] = ['id' => ['order' => 'asc']];
        }
        if ($sort_by == 'new') {
            $prs['body']['sort'] = ['id' => ['order' => 'desc']];
        }
        if ($sort_by == 'null') {
        }
    }

    if (isset($params['count_down'])) {
        if ($params['count_down'] == 'asc') {
            $prs['body']['sort']['count_down'] = ['order' => 'asc'];
        }
        if ($params['count_down'] == 'desc') {
            $prs['body']['sort']['count_down'] = ['order' => 'desc'];
        }
    }

    if(isDebugIp()){
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($prs);
//        echo "</pre>";
//        die();
    }

    return $prs;
}



class ctool
{

    public static function getProcessList()
    {
        $wmi = new Wmi ();
        //echo ( "Process list :\n" ) ;
        $process_list = $wmi->QueryInstances('Win32_Process');

        $arrProcess = [];
        foreach ($process_list as $process) {
            //echo("\tProcess : ({$process -> ProcessId}) {$process [ 'CommandLine' ]}\n");
            $arrProcess[$process->ProcessId] = $process ['CommandLine'];
        }

        return $arrProcess;
    }

    static public function getTimeValidateSSLCertificate($url, $port = 443)
    {

        $url = trim($url);
        $host = $url;
        if (substr($url, 0, 8) == 'https://' || substr($url, 0, 7) == 'http://')
            $host = parse_url($url, PHP_URL_HOST);

        $get = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                "capture_peer_cert" => TRUE
            ]
        ]);

        $read = stream_socket_client("ssl://" . $host . ":$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);

        if (!$read)
            return null;

        $cert = stream_context_get_params($read);
        if (!$cert)
            return null;

        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
        if (!$certinfo)
            return null;

        return $certinfo['validTo_time_t'];
    }

    static public function getCookie($name)
    {
        if (isset($_COOKIE[$name]))
            return $_COOKIE[$name];
        return null;
    }

    static public function getArgvParamCli($paramSearch = null)
    {
        global $argv;
        $cc = 0;
        $mm = [];

        if ($argv && count($argv))
            foreach ($argv as $pr) {
                $cc++;
                //bo qua filename
                if ($cc == 1)
                    continue;

                if (strstr($pr, "=")) {
                    $x1 = explode("=", $pr)[0];
                    $x2 = explode("=", $pr)[1];
                    if (!$paramSearch)
                        $mm[$x1] = $x2;
                    if ($paramSearch == $x1) {
                        return $x2;
                    }
                } else {
                    $mm[$pr] = 0;
                }
                if ($pr == $paramSearch)
                    return 1;
            }

        if (!isset($paramSearch))
            return $mm;
        return null;
    }

    static public function getFidFromStaticImageLink($link)
    {
        if (!strstr($link, "image_static/"))
            return null;
        $m1 = explode("image_static/", $link)[1];
        $m2 = explode("/", $m1)[2];
        return $m2;
    }

    static public function getObjFileFromStaticImageLink($link)
    {
        if (!strstr($link, "image_static/"))
            return null;
        $m1 = explode("image_static/", $link)[1];
        $m2 = explode("/", $m1)[2];
        $fid = d_f_h_1b($m2);

        if (!is_numeric($fid)) {
            return null;
        }

        $ofile = new \Base\ModelCloudFile();
        if ($ofile->getOneWhere_(" id = '$fid' AND siteid > 0 "))
            return $ofile;
        return null;

    }

    static function insert_js($path)
    {
        echo '<script src="' . $path . '"></script>';
    }

    static function insert_css($path)
    {
        echo '<link type="text/css" rel="stylesheet" href="' . $path . '" />';
    }

    /*
     *  Cach dung:
     *      $arr = array();
     *      $dir = "/tmp/";
     *      tool::dir_list_content_to_array($dir, $arr);
     *
     *      Kets qua: $arr:
     *
     */
    static function dir_list_content_to_array($dir, &$arrFull)
    {
        DirListFullToArray($dir, $arrFull);
    }

    //Tuong tu nhu ham tren!
    static function list_dir_content_to_array($dir, &$arrFull)
    {
        DirListFullToArray($dir, $arrFull);
    }

    static function showDownloadLink($filepath, $style = '', $time = null, $fileName = '')
    {
        $filepathEnc = eth1b($filepath);
        if (!$fileName)
            $fileName = basename($filepath);
        return "<a $style href='/tool/cloud/show_download_file.php?fpEnc=$filepathEnc&name=$fileName&time=" . $time . "'>Download</a>";
    }

    //
    static function showImageSrc($filepath, $style = '', $time = null)
    {
        if ($time)
            return "<img $style src='/tool/cloud/show_img_file.php?fp=$filepath&time=$time'/>";
        else
            return "<img $style src='/tool/cloud/show_img_file.php?fp=$filepath'/>";
    }

    static function showImageSrcEncFilePath($filepath, $style = '', $time = null)
    {
        $filepath1 = eth1b($filepath);
        if ($time)
            return "<img $style src='/tool/cloud/show_img_file.php?fpEnc=$filepath1&time=$time'/>";
        else
            return "<img $style src='/tool/cloud/show_img_file.php?fpEnc=$filepath1'/>";
    }

    static function showImageSrcHexFilePath($filepath, $style = '', $time = null)
    {
        $filepath1 = STH($filepath);
        if ($time)
            return "<img $style src='/tool/cloud/show_img_file.php?fpHex=$filepath1&time=$time'/>";
        else
            return "<img $style src='/tool/cloud/show_img_file.php?fpHex=$filepath1'/>";
    }

    //Dua 1 string vao 1 file, ghi tiep vao file, xuống dòng
    static function output($filename, $string)
    {
        output($filename, $string);
    }

    //Ghi 1 string vào file, nếu có file thì ghi đè hết nội dung
    static function getRemoteIP()
    {
        return @$_SERVER['REMOTE_ADDR'];
    }

    static function getUserAgent()
    {
        return @$_SERVER['HTTP_USER_AGENT'];
    }

    static function getReferHttp()
    {
        return @$_SERVER['HTTP_REFERER'];
    }


    static function getRemoteIPCloudFlare()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return @$_SERVER['REMOTE_ADDR'];
    }

    static function getCallingFileName()
    {
        $bt = debug_backtrace();
        return basename(end($bt)['file']);
    }

    //Ghi 1 string vào file, nếu có file thì ghi đè hết nội dung
    static function outputW($filename, $string)
    {
        outputW($filename, $string);
    }

    //Ghi 1 string vào file, nếu có file thì ghi đè hết nội dung
    static function outputT($filename, $string)
    {
        outputT($filename, $string);
    }
    ////////////////////////////////////
    // Nhiệm vụ ghi $content vào $fileLog
    // Với điều kiện:
    // $fileMarkPath = /mnt/glx/cache/$limitTime/$folderMark/$fileMarkName và
    // Nếu chưa có file Hoặc file này này có filemtime quá thời gian $limitTime thì mới ghi vào $fileLog, và ghi 1 dấu . vào $fileMarkPath này
    ////////////
    static function output_log_limit($folderMark, $fileMarkName, $limitTime = 3600, $fileLog = '', $content = '')
    {
        $folder = "/mnt/glx/cache/" . $limitTime . "/" . $folderMark . "/";
        $fileMarkPath = $folder . $fileMarkName;

        if (!file_exists($folder)) {
            mkdir($folder, 0755, 1);
            if (!file_exists($folder)) {
                die("Error output_log_limit: can not create mark folder!");
            }
        }

        $folderLog = dirname($fileLog);
        if (!file_exists($folderLog))
            mkdir($folderLog, 0755, 1);
        if (!file_exists($folderLog)) {
            die("Error output_log_limit: can not create log folder!");
        }

        if (!file_exists($fileMarkPath) || filemtime($fileMarkPath) < time() - $limitTime) {
            outputW($fileMarkPath, ".");
            output($fileLog, $content);
        }
    }

    public static function bot_detected()
    {

        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return true;
        }
    }

    public static function isBotDetect($agent1 = null)
    {

        if (!$agent1 && isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT']))
            $agent1 = $_SERVER['HTTP_USER_AGENT'];
        $ret = 0;
        if (strstr($agent1, "bot"))
            $ret = 1;
        if (strstr($agent1, "crawl"))
            $ret = 1;
        if (strstr($agent1, "baiduboxapp"))
            $ret = 1;
        if (strstr($agent1, "MQQBrowser"))
            $ret = 1;
        if (strstr($agent1, "MMWEBSDK"))
            $ret = 1;
        if (strstr($agent1, "MiuiBrowser"))
            $ret = 1;
        if (strstr($agent1, "MicroMessenger"))
            $ret = 1;
        if (strstr($agent1, "MMWEBSDK"))
            $ret = 1;
        if (strstr($agent1, "HuaweiBrowser"))
            $ret = 1;
        if (strstr($agent1, "HUAWEIDUK"))
            $ret = 1;


        return $ret;
    }

    static function sleepToTimeFull($to, $secondCheck = 60, $outputText = null)
    {
        if (!$secondCheck || $secondCheck < 1)
            $secondCheck = 1;

        while (1) {
            if (nowyh() > $to) {
                break;
            }
            sleep($secondCheck);
        }

    }

    static function isWindow()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 1;
        } else {
            return 0;
        }
    }

    static function isMobile()
    {

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            //$useragent = $_SERVER['HTTP_USER_AGENT'];
            //return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", @$_SERVER["HTTP_USER_AGENT"]);
        }
        return null;
    }


    /*
     * Zip file in a folder, or a single file
     * Delete org file if need
     */
    static function ZipLogFileTool($filePathGlob, $deleteOldFile = null)
    {
        //$a = glob("c:/Users/lad/.galaxy_drive_info/all.log.*");

        if (is_file($filePathGlob)) {
            $a = [$filePathGlob];

        } elseif (is_dir($filePathGlob)) {
            $a = ListDirWithPath($filePathGlob);
        } else {
            if (!file_exists(dirname($filePathGlob)))
                return false;
            $a = glob($filePathGlob);
            if (!$a || !is_array($a))
                return true;
        }

        foreach ($a as $file) {
            //Neu da la file zip thi ko zip nua
            if (substr($file, -4) == '.zip')
                continue;
            $zfile = $file . '.zip';
            //if(file_exists($zfile))
            //continue;

            $zip = new ZipArchive;
            $res = $zip->open($zfile, ZipArchive::OVERWRITE | ZipArchive::CREATE);
            if ($res === TRUE) {
                //echo 'ok1';
                $zip->addFile($file, basename($file));
                $zip->close();

                if ($deleteOldFile) {
                    unlink($file);
                }
            } else {
                //echo 'failed, code:' . $res;
                return false;
            }
        }
        return true;
    }

    /*
     DEV:

    - Curl đoạn này chạy, nhưng filegetcontent lại báo lỗi, trường hợp link v6, proxy cũng v6:
    $link = "http://[2403:6a40:0:121::188:1010]/tool/ip.htm";
    $proxy = '[2405:19c0:0:fffe:17f:1:1:3]:60008';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$link);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    echo $ret;
     */

    // Proxy IPv6: ctool::file_get_content_lad($link, 5, '[2405:19c0:0:fffe:17f:1:1:2]:60002' );
    static function file_get_content_lad($link, $timeout = null, $proxy = null)
    {
        $mm = [];
        if ($timeout)
            $mm['timeout'] = $timeout;
        if ($proxy)
            $mm['proxy'] = 'tcp://' . $proxy;

        $ctx = stream_context_create(array('http' =>
            $mm
        ));
        return file_get_contents($link, false, $ctx);
    }

    static function file_get_content_post($link, $post = [])
    {

        $postdata = http_build_query(
            $post
        );

        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents($link, false, $context);

        return $result;
    }

    static function curl_init($url, $returnTransfer = true)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
        return $ch;
    }

    static function curl_set_user_agent(&$ch, $agent = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36")
    {
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36"); //03.2020
    }

    static function curl_set_post_array(&$ch, $arr)
    {
        curl_setopt($ch, CURLOPT_POST, count($arr));
        if (count($arr))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    }

    static function curl_set_proxy(&$ch, $proxy)
    {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
    }

    static function curl_set_return_header(&$ch)
    {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }

    static function curl_exec(&$ch)
    {
        return $result = curl_exec($ch);
    }

    static function get_curl_proxy($url, $param = null, $proxy = null, $header = null)
    {
        return ctool::postget1curl($url, $param, $proxy, $header);
    }

    static function postget1curl($url, $param = null, $proxy = null, $header = null)
    {
//        $param = array(
//            'name' => 'abc',
//            'diachi' => '123'
//        );
        // URL có chứa hai thông tin name và diachi
        //$url = 'post.php';
        // Khởi tạo CURL
        $ch = curl_init($url);
        // Thiết lập có return

        if ($proxy)
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36"); //03.2020
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Thiết lập sử dụng POST
        if ($param) {
            curl_setopt($ch, CURLOPT_POST, count($param));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        if ($header)
            curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    public static function getRandStr($length)
    {
        $token = "";

        //$codeAlphabet = "ABCDEFGHJKMNPQRSTUVXYZ";
        $codeAlphabet = "";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        //$codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    public static function getRandNum($length)
    {
        $token = "";
        $codeAlphabet = "0123456789";
        //$codeAlphabet = "";
        //$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        //$codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    static function _last()
    {
    }

}

class FileHelperGlx {

    static function deleteEmptyFolderInFolder($path){


        /**
         * Function to list all directories in a given path
         * @param string $path Directory path to scan
         * @return array Array of directory paths
         */
        function listDirectories($path) {

            $directories = [];

            // Check if the path exists and is a directory
            if (!is_dir($path)) {
                throw new Exception("Path không tồn tại hoặc không phải là thư mục");
            }

            // Get all items in the directory
            $items = new DirectoryIterator($path);

            foreach ($items as $item) {
                // Skip . and .. directories
                if ($item->isDot()) {
                    continue;
                }

                // If it's a directory, add it to our array
                if ($item->isDir()) {
                    $dirPath = $item->getPathname();
                    $directories[] = $dirPath;

                    // Recursively get subdirectories
                    $subDirs = listDirectories($dirPath);
                    $directories = array_merge($directories, $subDirs);
                }
            }

            return $directories;
        }

        /**
         * Function to check if a directory is empty
         * @param string $path Directory path to check
         * @return bool True if directory is empty, false otherwise
         */
        function isDirectoryEmpty($path)
        {
            $handle = opendir($path);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    closedir($handle);
                    return false;
                }
            }
            closedir($handle);
            return true;
        }

        $removedDirs = [];
        try {
            // Get all directories
            $directories = listDirectories($path);

            // Sort directories by depth (deepest first)
            usort($directories, function ($a, $b) {
                return substr_count($b, DIRECTORY_SEPARATOR) - substr_count($a, DIRECTORY_SEPARATOR);
            });

            // Check each directory
            foreach ($directories as $dir) {
                if (isDirectoryEmpty($dir)) {
                    if (rmdir($dir))
                    {
                        $removedDirs[] = $dir;
                    }
                }
            }
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage() . "\n";
        }
        return $removedDirs;
    }

}


function removeHtmlCommentsDel($html) {
    // Trường hợp 1: Lọc bỏ các thẻ block (<p>, <div>, etc.) có nội dung bắt đầu bằng ###
    $pattern1 = '/<(p|div|h[1-6]|li|td|th|blockquote|pre|address)[^>]*>\s*###.*?<\/\1>/s';
    $html = preg_replace($pattern1, '', $html);

    // Trường hợp 2: Lọc bỏ nội dung sau <br> và bắt đầu bằng ###
    // Loại bỏ từ <br> đến thẻ block tiếp theo hoặc <br> tiếp theo nếu dòng bắt đầu bằng ###
    $pattern2 = '/<br\s*\/?>\s*###.*?(?=<br|\n|<\/?(?:p|div|h[1-6]|li|td|th|blockquote|pre|address))/s';
    $html = preg_replace($pattern2, '', $html);

    // Trường hợp 3: Lọc bỏ nội dung ### ở đầu thẻ nhưng giữ lại thẻ
    $pattern3 = '/(<(p|div|h[1-6]|li|td|th|blockquote|pre|address)[^>]*>)\s*###(.*?)(<\/\2>)/s';
    $html = preg_replace($pattern3, '$1$4', $html);

    // Trường hợp 4: Xử lý trường hợp ### ở giữa nội dung sau <br> đến hết dòng
    $pattern4 = '/(<br\s*\/?>)([^<]*)###.*?(?=<br|\n|<)/s';
    $html = preg_replace($pattern4, '$1$2', $html);

    // Thêm pattern này để xử lý ### trong thẻ con
//    $pattern_nested = '/<(p|div|h[1-6]|li|td|th|blockquote|pre|address)[^>]*>.*?<[^>]*>\s*###.*?<\/\1>/s';
//    $html = preg_replace($pattern_nested, '', $html);

    // Xóa bỏ các thẻ rỗng sau khi đã lọc comment
    $html = preg_replace('/<(p|div|h[1-6]|li|td|th|blockquote|pre|address)[^>]*>\s*<\/\1>/s', '', $html);

    return $html;
}


/**
 * Hàm lọc bỏ các dòng comment từ văn bản thuần (không phải HTML)
 * Dòng comment được định nghĩa là dòng bắt đầu bằng ###
 *
 * @param string $text Văn bản cần lọc
 * @return string Văn bản đã được lọc bỏ comment
 */
function removeSMSTextComments($text) {
    // Đảm bảo đầu vào là UTF-8
    $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));

    // Tách văn bản thành các dòng
    $lines = explode("\n", $text);
    $filteredLines = [];

    // Lọc bỏ các dòng bắt đầu bằng ###
    foreach ($lines as $line) {
        // Sử dụng mb_substr để hỗ trợ Unicode
        $trimmedLine = trim($line);
        if (mb_strpos($trimmedLine, '###', 0, 'UTF-8') !== 0) {
            $filteredLines[] = $line;
        }
    }

    // Ghép các dòng lại với nhau
    return implode("\n", $filteredLines);
}

function removeCommentsWithDOM0($html) {
    // Nếu nội dung trống, trả về luôn
    if (empty($html)) {
        return $html;
    }

    // Tạo một đối tượng DOMDocument
    $dom = new DOMDocument('1.0', 'UTF-8');

    // Tắt báo lỗi HTML không chuẩn
    libxml_use_internal_errors(true);

    // Thêm header charset UTF-8 và wrapper để đảm bảo Unicode được xử lý đúng
    $html = '<?xml encoding="UTF-8">' .
        '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
        '<body><div id="wrapper">' . $html . '</div></body></html>';

    // Load HTML với các tùy chọn
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Lấy các lỗi và xóa chúng
    libxml_clear_errors();

    // Danh sách các phần tử cần xóa
    $nodesToRemove = [];

    // Tìm tất cả các thẻ p
    $paragraphs = $dom->getElementsByTagName('p');

    // Xử lý DOM theo cách bảo tồn các nút
    $paragraphsToRemove = [];
    foreach ($paragraphs as $p) {
        $paragraphsToRemove[] = $p;
    }

    // Xử lý từng paragraph
    foreach ($paragraphsToRemove as $p) {
        $hasComment = false;

        // Kiểm tra text content trực tiếp của p
        $content = trim($p->textContent);
        if (strpos($content, '###') === 0) {
            $hasComment = true;
        } else {
            // Nếu không có ### ở đầu text content, kiểm tra các span bên trong
            $spans = $p->getElementsByTagName('span');
            $spansArray = [];
            foreach ($spans as $span) {
                $spansArray[] = $span;
            }

            // Kiểm tra từng span trong p
            foreach ($spansArray as $span) {
                $spanContent = trim($span->textContent);
                if (strpos($spanContent, '###') === 0) {
                    $hasComment = true;
                    break;
                }
            }
        }

        // Nếu có comment ###, đánh dấu để xóa p
        if ($hasComment && $p->parentNode) {
            $nodesToRemove[] = $p;
        }
    }

    // Xóa các node đã đánh dấu
    foreach ($nodesToRemove as $node) {
        if ($node->parentNode) {
            $node->parentNode->removeChild($node);
        }
    }

    // Lấy nội dung HTML đã được xử lý
    $wrapper = $dom->getElementById('wrapper');
    $processedHtml = '';

    if ($wrapper) {
        // Lưu nội dung HTML của các nút con
        foreach ($wrapper->childNodes as $child) {
            $processedHtml .= $dom->saveHTML($child);
        }
    }

    return $processedHtml;
}


function removeCommentsWithDOM($html) {
    // Nếu nội dung trống, trả về luôn
    if (empty($html)) {
        return $html;
    }

    // Trước khi xử lý DOM, loại bỏ các dòng ### bằng regex
    // Tìm và xóa các dòng bắt đầu bằng ### (có thể có khoảng trắng trước)
    $lines = explode('<br />', $html);
    $filteredLines = [];

    foreach ($lines as $line) {
        // Loại bỏ các thẻ HTML để kiểm tra nội dung text thuần
        $textOnly = strip_tags($line);
        $textOnly = trim($textOnly);

        // Nếu dòng không bắt đầu bằng ###, giữ lại
        if (strpos($textOnly, '###') !== 0) {
            $filteredLines[] = $line;
        }
    }

    // Ghép lại các dòng đã lọc
    $html = implode('<br />', $filteredLines);

    // Tạo một đối tượng DOMDocument
    $dom = new DOMDocument('1.0', 'UTF-8');

    // Tắt báo lỗi HTML không chuẩn
    libxml_use_internal_errors(true);

    // Thêm header charset UTF-8 và wrapper để đảm bảo Unicode được xử lý đúng
    $html = '<?xml encoding="UTF-8">' .
        '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
        '<body><div id="wrapper">' . $html . '</div></body></html>';

    // Load HTML với các tùy chọn
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Lấy các lỗi và xóa chúng
    libxml_clear_errors();

    // Danh sách các phần tử cần xóa
    $nodesToRemove = [];

    // Tìm tất cả các thẻ p
    $paragraphs = $dom->getElementsByTagName('p');

    // Xử lý DOM theo cách bảo tồn các nút
    $paragraphsToRemove = [];
    foreach ($paragraphs as $p) {
        $paragraphsToRemove[] = $p;
    }

    // Xử lý từng paragraph
    foreach ($paragraphsToRemove as $p) {
        $hasComment = false;

        // Kiểm tra text content trực tiếp của p
        $content = trim($p->textContent);
        if (strpos($content, '###') === 0) {
            $hasComment = true;
        } else {
            // Nếu không có ### ở đầu text content, kiểm tra các span bên trong
            $spans = $p->getElementsByTagName('span');
            $spansArray = [];
            foreach ($spans as $span) {
                $spansArray[] = $span;
            }

            // Kiểm tra từng span trong p
            foreach ($spansArray as $span) {
                $spanContent = trim($span->textContent);
                if (strpos($spanContent, '###') === 0) {
                    $hasComment = true;
                    break;
                }
            }
        }

        // Nếu có comment ###, đánh dấu để xóa p
        if ($hasComment && $p->parentNode) {
            $nodesToRemove[] = $p;
        }
    }

    // Xóa các node đã đánh dấu
    foreach ($nodesToRemove as $node) {
        if ($node->parentNode) {
            $node->parentNode->removeChild($node);
        }
    }

    // Lấy nội dung HTML đã được xử lý
    $wrapper = $dom->getElementById('wrapper');
    $processedHtml = '';

    if ($wrapper) {
        // Lưu nội dung HTML của các nút con
        foreach ($wrapper->childNodes as $child) {
            $processedHtml .= $dom->saveHTML($child);
        }
    }

    return $processedHtml;
}


/**
 * Removes HTML paragraphs containing comment markers (starting with ###)
 * Works with comments in spans or directly in paragraph text
 *
 * @param string $html The HTML content to process
 * @return string The processed HTML with comment paragraphs removed
 */
function removeCommentsWithDOM2($html) {
    // Return immediately if content is empty
    if (empty($html)) {
        return $html;
    }

    // Create a DOMDocument object
    $dom = new DOMDocument('1.0', 'UTF-8');

    // Suppress warnings for malformed HTML
    libxml_use_internal_errors(true);

    // Add UTF-8 wrapper to ensure proper Unicode handling
    $html = '<?xml encoding="UTF-8">' .
        '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
        '<body><div id="wrapper">' . $html . '</div></body></html>';

    // Load HTML with options
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Clear any errors
    libxml_clear_errors();

    // Collect all paragraphs first to avoid modification during iteration
    $paragraphs = [];
    foreach ($dom->getElementsByTagName('p') as $p) {
        $paragraphs[] = $p;
    }

    // Process each paragraph
    foreach ($paragraphs as $p) {
        $hasCommentMark = false;

        // Check if paragraph's text content directly starts with ###
        if (strpos(trim($p->textContent), '###') === 0) {
            $hasCommentMark = true;
        } else {
            // Check spans within the paragraph
            foreach ($p->getElementsByTagName('span') as $span) {
                $content = trim($span->textContent);
                if (strpos($content, '###') === 0) {
                    $hasCommentMark = true;
                    break;
                }
            }
        }

        // Remove the paragraph if it contains a comment marker
        if ($hasCommentMark && $p->parentNode) {
            $p->parentNode->removeChild($p);
        }
    }

    // Extract the processed HTML content from the wrapper
    $wrapper = $dom->getElementById('wrapper');
    $processedHtml = '';

    if ($wrapper) {
        foreach ($wrapper->childNodes as $child) {
            $processedHtml .= $dom->saveHTML($child);
        }
    }

    return $processedHtml;
}


/**
 * Sinh ra ID cho DB, để tránh bị scan auto, lộ thông tin hệ thng
 */
class IdGenDbGlx
{
    /**
     * Base58 character set: loại bỏ 0, O, I, l để tránh nhầm lẫn
     * (theo chuẩn Bitcoin Base58)
     */
    private const BASE58_CHARS = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Epoch (2024-01-01 UTC) để rút ngắn timestamp
     */
    private const EPOCH = 1704067200;

    /**
     * Configurable format
     */
    private const PREFIX = 'G';             // Tiền tố để tránh toàn số & đánh dấu version
    private const TIMESTAMP_LENGTH = 5;     // 5 ký tự Base58 ~ 20.8 năm
    private const RANDOM_LENGTH = 6;        // 5 ký tự random ~ 6.56e8 combinations/giây

    /**
     * Generate unique ID
     * Format: PREFIX + Base58(timestamp) + Base58(random)
     */
    public static function generate(): string
    {
        $nowSeconds = time() - self::EPOCH;
        $timestamp = self::toBase58($nowSeconds, self::TIMESTAMP_LENGTH);
        $random = self::generateRandom(self::RANDOM_LENGTH);

        return self::PREFIX . $timestamp . $random;
    }

    /**
     * Convert number to Base58 string with padding
     */
    private static function toBase58(int $num, int $length): string
    {
        $chars = self::BASE58_CHARS;

        if ($num === 0) {
            return str_repeat('1', $length); // '1' là ký tự đầu trong Base58
        }

        $result = '';
        while ($num > 0) {
            $result = $chars[$num % 58] . $result;
            $num = intdiv($num, 58);
        }

        return str_pad($result, $length, '1', STR_PAD_LEFT);
    }

    /**
     * Generate random Base58 string
     */
    private static function generateRandom(int $length): string
    {
        $chars = self::BASE58_CHARS;
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= $chars[random_int(0, 57)];
        }
        return $random;
    }

    /**
     * Validate ID format (Prefix + Base58 chars)
     */
    public static function isValid(string $id): bool
    {
        $expectedLength = strlen(self::PREFIX) + self::TIMESTAMP_LENGTH + self::RANDOM_LENGTH;
        $pattern = '/^' . preg_quote(self::PREFIX, '/') . '[' . preg_quote(self::BASE58_CHARS, '/') . ']{' . ($expectedLength - strlen(self::PREFIX)) . '}$/';
        return strlen($id) === $expectedLength && preg_match($pattern, $id);
    }

    /**
     * Generate batch IDs
     */
    public static function generateBatch(int $count): array
    {
        $ids = [];
        for ($i = 0; $i < $count; $i++) {
            $ids[] = self::generate();
        }
        return $ids;
    }
}



class GlxSnowflake
{
    private $machineId;
    private $sequence = 0;
    private $lastTimestamp = 0;

//    const EPOCH = 1577836800000; // 2020-01-01 00:00:00 UTC
    const EPOCH = 1712699600000; // 2025...
    const MACHINE_BITS = 6;
    const SEQUENCE_BITS = 12;
    const TIMESTAMP_BITS = 46;

    private $maxSequence;
    private $maxMachineId;
    private $maxTimestamp;

    // Static instance for quick generation
    private static $defaultInstance = null;

    public function __construct($machineId)
    {
        $this->maxMachineId = (1 << self::MACHINE_BITS) - 1;
        $this->maxSequence = (1 << self::SEQUENCE_BITS) - 1;
        $this->maxTimestamp = self::EPOCH + ((1 << self::TIMESTAMP_BITS) - 1);

        if ($machineId < 0 || $machineId > $this->maxMachineId) {
            throw new \InvalidArgumentException("Machine ID phải từ 0 đến {$this->maxMachineId}");
        }

        $this->machineId = $machineId;
    }

    /**
     * Generate một Snowflake ID mới
     *
     * @return string
     * @throws \RuntimeException
     */
    public function generate()
    {
        $timestamp = $this->getCurrentTimestamp();

        // Clock backwards protection
        if ($timestamp < $this->lastTimestamp) {
            throw new \RuntimeException("Clock moved backwards. Refusing to generate id");
        }

        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & $this->maxSequence;

            // Nếu sequence overflow, chờ millisecond tiếp theo
            if ($this->sequence === 0) {
                $timestamp = $this->waitNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        // Tạo ID: [46-bit timestamp][6-bit machine][12-bit sequence]
        return (string) (
            (($timestamp - self::EPOCH) << (self::MACHINE_BITS + self::SEQUENCE_BITS))
            | ($this->machineId << self::SEQUENCE_BITS)
            | $this->sequence
        );
    }

    /**
     * Static method để sinh ID nhanh với machine ID mặc định
     *
     * @param int|null $machineId Machine ID (mặc định: 0)
     * @return string
     */
    public static function id($machineId = null)
    {
        if ($machineId !== null) {
            // Tạo instance mới với machine ID cụ thể
            $generator = new self($machineId);
            return $generator->generate();
        }

        // Sử dụng instance mặc định
        if (self::$defaultInstance === null) {
            self::$defaultInstance = new self(0);
        }

        $ret = self::$defaultInstance->generate();

        if(strlen("$ret") <17)
            throw new \RuntimeException("Snowflake ID quá ngắn, <17: $ret");

        return $ret;
    }

    /**
     * Parse một Snowflake ID thành các components
     *
     * @param string $id
     * @return array
     */
    public static function parse($id)
    {
        $id = (int) $id;

        $sequence = $id & ((1 << self::SEQUENCE_BITS) - 1);
        $machineId = ($id >> self::SEQUENCE_BITS) & ((1 << self::MACHINE_BITS) - 1);
        $timestamp = ($id >> (self::MACHINE_BITS + self::SEQUENCE_BITS)) + self::EPOCH;

        return [
            'timestamp' => $timestamp,
            'datetime' => date('Y-m-d H:i:s.v', $timestamp / 1000),
            'machine_id' => $machineId,
            'sequence' => $sequence,
            'binary' => sprintf('%064b', $id)
        ];
    }

    /**
     * Lấy timestamp hiện tại tính bằng milliseconds
     *
     * @return int
     * @throws \RuntimeException
     */
    private function getCurrentTimestamp()
    {
        $timestamp = (int) floor(microtime(true) * 1000);

        // Check timestamp overflow (sau ~2233 năm từ epoch)
        if ($timestamp > $this->maxTimestamp) {
            throw new \RuntimeException("Timestamp overflow - system exceeded maximum supported time");
        }

        return $timestamp;
    }

    /**
     * Chờ đến millisecond tiếp theo
     *
     * @param int $lastTimestamp
     * @return int
     */
    private function waitNextMillis($lastTimestamp)
    {
        $timestamp = $this->getCurrentTimestamp();

        while ($timestamp <= $lastTimestamp) {
            usleep(100); // Sleep 0.1ms để tránh busy waiting
            $timestamp = $this->getCurrentTimestamp();
        }

        return $timestamp;
    }

    /**
     * Set default machine ID cho static method
     *
     * @param int $machineId
     */
    public static function setDefaultMachineId($machineId)
    {
        self::$defaultInstance = new self($machineId);
    }

    /**
     * Lấy thông tin về cấu trúc Snowflake
     *
     * @return array
     */
    public static function getInfo()
    {
        $maxMachines = (1 << self::MACHINE_BITS) - 1;
        $maxSequence = (1 << self::SEQUENCE_BITS) - 1;
        $maxYears = floor(((1 << self::TIMESTAMP_BITS) - 1) / (365.25 * 24 * 60 * 60 * 1000));

        return [
            'structure' => sprintf('%d-bit timestamp + %d-bit machine + %d-bit sequence',
                self::TIMESTAMP_BITS, self::MACHINE_BITS, self::SEQUENCE_BITS),
            'epoch' => date('Y-m-d H:i:s', self::EPOCH / 1000) . ' UTC',
            'max_machines' => $maxMachines + 1,
            'max_sequence_per_ms' => $maxSequence + 1,
            'max_ids_per_second_per_machine' => ($maxSequence + 1) * 1000,
            'supported_years' => $maxYears,
            'supported_until' => date('Y-m-d', (self::EPOCH / 1000) + ($maxYears * 365.25 * 24 * 60 * 60))
        ];
    }
}

function deleteFilesWithSubstring($folderPath, $substring) {
    if (!is_dir($folderPath)) {
        return false;
    }

    $files = new \DirectoryIterator($folderPath);

    foreach ($files as $fileinfo) {
        if ($fileinfo->isFile() && str_contains($fileinfo->getFilename(), $substring) !== false) {
            unlink($fileinfo->getRealPath());
        }
    }

    return true;
}

function reLearnFace($image_list)
{
    $domain = getDomainHostName();
    $url = "http://$domain:50000/get_face_vector";
    //Truyền filepath vào cho python api:
    $idList = explode(",", $image_list);
    $tmp = '';
    $mFileCloudInfo = [];
    foreach ($idList as $fid) {
        $fid = trim($fid);
        //            echo "\n $id , ";
        $tmp .= $fid . ',';
        if ($fid) {
            $fclObj = FileUpload::getCloudObj($fid);
            if ($fclObj) {
                $mFileCloudInfo[] = (object)['cloud_id'=>$fclObj->id, 'file_path' => $fclObj->file_path ];
            }

            //                $link = trim($link, '/');
            //                $linkImg = $domain ."/". $link;
        }
    }

    //            die($mFileCloudInfo);

    //
    //        die("FilePathList = " . $mFileCloudInfo);
    //
    //        $linkImgHex = STH($tmp);
    //        $linkImgHex = STH($image_list);
    ////
    ////        $link = 'https://'. getDomainHostName() .'/tool1/_site/event_mng/face_recornize_python.php?link_file=' . $linkImgHex;
    ///
    ///
    //Post CURL

    $postData = [
        //'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
        'image_list_info' => $mFileCloudInfo,
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($postData))
    ]);
    if (!$response = curl_exec($ch)) {
        throw new \Exception("Face server not work???");
    }

    curl_close($ch);

    $ret = json_decode($response);

    //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //            print_r($ret);
    //            echo "</pre>";
    //
    //            die();
    if (!$ret) {
        throw new \Exception("Face server not response???");
    }

    if ($ret->status != 'success') {
        throw new \Exception("Face server error: " . ($ret->message ?? "Unknown error"));
    }
    if (!$ret->data) {
        throw new \Exception("Face server error: No vector returned");
    }

    //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //            print_r($ret);
    //            echo "</pre>";
    //            die();

    $nFace = count( $ret->data);

    $mFace = $ret->data;
    foreach ($mFace as $fileAndFace){

        $filePath = $fileAndFace->file_path;
        $cloudId = $fileAndFace->cloud_id;
        $face_vector = $fileAndFace->face_vector;
        if(!file_exists($filePath)){
            continue;
        }

        $evFace = EventFaceInfo::where('file_cloud_id', $cloudId)->first();
        if (!$evFace) {
            $evFace = new EventFaceInfo();
            $evFace->file_cloud_id = $cloudId;
            $evFace->face_vector = json_encode($face_vector);
            $evFace->save();
        } else {
            $evFace->face_vector = json_encode($face_vector);
            $evFace->save();
        }

    }
    return $nFace;
}

function saveMessZlToDb($mess, $chanel)
{
    global $flog;

    $mm = json_decode($mess, true);
    echo "Get mess:<pre>";
    print_r($mm);
    echo "</pre>";

//    return;

    $message = new \App\Models\CrmMessage();


    ladDebug::addTime(__FILE__, __LINE__);

    //Kiểm tra nếu có channel_name và msg_id roi thi ko save nua:
    if(!str_contains($chanel , 'taxi'))
    if(\App\Models\CrmMessage::where('msg_id', $mm['data']['msgId'])
        ->where('channel_name', $chanel)->first()){
        echo "\n<br >Message exist, skip";
        return;
    }

    ladDebug::addTime(__FILE__, __LINE__);

    $message->log = $mess; // Store the entire raw JSON
    if($mm['msg_send'] ?? ''){
        $message->content = $mm['msg_send'];
        $message->thread_id = $mm['threadId'] ?? '';
        $message->d_name = $mm['dbName'] ?? '';
        $message->save();

        die("save OK!");
        return;
    }
    ladDebug::addTime(__FILE__, __LINE__);
    // Map JSON fields to model fields
    $message->content = $mm['data']['content'] ?? null;
    if(!$message->content){
        file_put_contents($flog, date("Y-m-d H:i:s") ." # --- not content ? ---\n\n", FILE_APPEND);
        return;
    }
    if(isset($message->content['thumb']) || is_array($message->content)){
        $message->content = json_encode($message->content);
    }
    ladDebug::addTime(__FILE__, __LINE__);
    //Cho app taxi:
    if(str_contains($chanel , 'anh_taxi'))
        $message->content = mb_strtolower($message->content);

    $message->type = $mm['type'] ?? null;
    $message->action_id = $mm['data']['actionId'] ?? null;
    $message->action_id = $mm['data']['actionId'] ?? null;
    $message->msg_id = $mm['data']['msgId'] ?? null;
    $message->cli_msg_id = $mm['data']['cliMsgId'] ?? null;
    $message->msg_type = $mm['data']['msgType'] ?? null;
    $message->uid_from = $mm['data']['uidFrom'] ?? null;
    $message->id_to = $mm['data']['idTo'] ?? null;
    $message->d_name = $mm['data']['dName'] ?? null;
    $message->ts = $mm['data']['ts'] ?? null;
    $message->status = $mm['data']['status'] ?? null;

    $message->notify = $mm['data']['notify'] ?? null;
    $message->ttl = $mm['data']['ttl'] ?? null;
    $message->user_id_ext = $mm['data']['userId'] ?? null;
    $message->uin = $mm['data']['uin'] ?? null;
    $message->cmd = $mm['data']['cmd'] ?? null;
    $message->st = $mm['data']['st'] ?? null;
    $message->at = $mm['data']['at'] ?? null;
    $message->real_msg_id = $mm['data']['realMsgId'] ?? null;
    $message->thread_id = $mm['threadId'] ?? null;
    $message->is_self = $mm['isSelf'] ?? false;

    // Handle JSON objects by converting them to JSON strings
    $message->property_ext = isset($mm['data']['propertyExt']) ? json_encode($mm['data']['propertyExt']) : null;
    $message->params_ext = isset($mm['data']['paramsExt']) ? json_encode($mm['data']['paramsExt']) : null;

    // Set any additional fields or defaults
    //    $message->name = 'Zalo Message';
    //    $message->user_id = 1; // Set appropriate user ID or default
    $message->log = $mess; // Store the entire raw JSON

    $message->channel_name = $chanel;

    // Save to database
    $message->save();
    ladDebug::addTime(__FILE__, __LINE__);
    //Bắn tín hiệu taxi
    if($chanel == 'anh_taxi')
    {

        sendAlertForUser($message->id);
        //        searchTaxiMessages(
        //            $mm['data']['vi_tri1'] ?? '',
        //            $mm['data']['vi_tri2'] ?? '',
        //            $mm['data']['phut'] ?? '')

    }
    ladDebug::addTime(__FILE__, __LINE__);

    echo "\n". ladDebug::dumpDebugTime();
    echo "Message saved successfully with ID: " . $message->id;
}

function isValidTelegramBotToken($token) {
    // Telegram bot token format: {bot_id}:{bot_secret}
    // bot_id: số nguyên
    // bot_secret: chuỗi 35 ký tự chữ và số
    $pattern = '/^[0-9]{8,10}:[a-zA-Z0-9_-]{35}$/';
//123456789:AAH-BCDEFGHIJKLMNOPQRSTUVWXYZ12345
//8040174107:AAE-XqU-XaV0Y7v30pjZgbfGzHq88LQx0HQ

    return preg_match($pattern, $token);
}

function showDbCurrentInfo()
{
    $default = \DB::getDefaultConnection();
    echo "<br/>\n";
    echo "- Default connection name: $default\n";

    echo "<br/>\n";
// 2. Get connection config
    echo "\n- Connection Configuration:\n";
    $config = config('database.connections.' . $default);
    echo " <br>  Driver: " . ($config['driver'] ?? 'not set') . "\n";
    echo " <br>  Host: " . ($config['host'] ?? 'not set') . "\n";
    echo " <br>  Port: " . ($config['port'] ?? 'not set') . "\n";
    echo "  <br> Database: " . ($config['database'] ?? 'not set') . "\n";
    echo " <br>  Username: " . ($config['username'] ?? 'not set') . "\n";
    echo " <br>  Charset: " . ($config['charset'] ?? 'not set') . "\n";
    echo " <br>  Collation: " . ($config['collation'] ?? 'not set') . "\n";

}


function exportFieldDescriptionToJson($mmAllMetaDb, $tableModelSelecting, $langs = ['en', 'jp'])
{

    $jsonFieldDesc = [];
    foreach ($mmAllMetaDb AS $fieldX => $info) {
        $jsonFieldDesc[$fieldX] = $info->name;
    }

    $folder = "/var/glx/weblog/db_language";
    if(!file_exists($folder)){
        mkdir($folder, 0777, true);
    }


    foreach ($langs AS $lang){
        $fileJS = "$folder/".DEF_FILE_JSON_LANG_PREFIX.".$tableModelSelecting.$lang.json";
        if(file_exists($fileJS)){
            $objJs = json_decode(file_get_contents($fileJS));
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($objJs);
//                            echo "</pre>";
            foreach ($objJs AS $field=>$desc){
                if(isset($jsonFieldDesc[$field])){
                    $jsonFieldDesc[$field] = $desc;
                }
            }
        }


        file_put_contents($fileJS, json_encode($jsonFieldDesc, JSON_PRETTY_PRINT));
    }

    echo "<pre>  >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    print_r($jsonFieldDesc);
    echo "</pre>";

}


class clang1 {

    static public $enableLanguage = [
        'en' => "English",
        'vi' => "Việt",
        'ja' => "日本語",   // Japanese
        'ko' => "한국어",   // Korean
        'fr' => "Français",
//        'de' => "Deutsch",
//        'es' => "Español",
//        'km' => "ភាសាខ្មែរ",   // Khmer
//        'ru' => "Русский",   // Russian
//        'zh' => "中文",   // Chinese

    ];

    // English names for each language (for API)
    static public $enableLanguageEnglish = [
        'en' => "English",
        'vi' => "Vietnamese",
        'ja' => "Japanese",
        'ko' => "Korean",
        'fr' => "French",
//        'de' => "German",
//        'es' => "Spanish",
//        'zh' => "Chinese",
//        'km' => "Cambodian",
//        'ru' => "Russian",
    ];

    // Comprehensive flag mapping for all major languages
    // Using flag-icons library: https://flagicons.lipis.dev/
    static public $flagMap = [
        // Current enabled languages
        'vi' => 'vn', // Vietnam
        'en' => 'us', // English (USA) - can use 'gb' for UK
        'ja' => 'jp', // Japanese (Japan)
        'ko' => 'kr', // Korean (South Korea)
        'fr' => 'fr', // French (France)
        'de' => 'de', // German (Germany)
        'es' => 'es', // Spanish (Spain)

        // European languages
        'it' => 'it', // Italian (Italy)
        'pt' => 'pt', // Portuguese (Portugal)
        'nl' => 'nl', // Dutch (Netherlands)
        'pl' => 'pl', // Polish (Poland)
        'ru' => 'ru', // Russian (Russia)
        'uk' => 'ua', // Ukrainian (Ukraine)
        'tr' => 'tr', // Turkish (Turkey)
        'el' => 'gr', // Greek (Greece)
        'sv' => 'se', // Swedish (Sweden)
        'no' => 'no', // Norwegian (Norway)
        'da' => 'dk', // Danish (Denmark)
        'fi' => 'fi', // Finnish (Finland)
        'cs' => 'cz', // Czech (Czech Republic)
        'ro' => 'ro', // Romanian (Romania)
        'hu' => 'hu', // Hungarian (Hungary)
        'bg' => 'bg', // Bulgarian (Bulgaria)
        'hr' => 'hr', // Croatian (Croatia)
        'sk' => 'sk', // Slovak (Slovakia)
        'sl' => 'si', // Slovenian (Slovenia)
        'lt' => 'lt', // Lithuanian (Lithuania)
        'lv' => 'lv', // Latvian (Latvia)
        'et' => 'ee', // Estonian (Estonia)

        // Asian languages
        'zh' => 'cn', // Chinese (China) - Simplified
        'zh-TW' => 'tw', // Chinese (Taiwan) - Traditional
        'th' => 'th', // Thai (Thailand)
        'id' => 'id', // Indonesian (Indonesia)
        'ms' => 'my', // Malay (Malaysia)
        'tl' => 'ph', // Tagalog (Philippines)
        'hi' => 'in', // Hindi (India)
        'bn' => 'bd', // Bengali (Bangladesh)
        'ur' => 'pk', // Urdu (Pakistan)
        'fa' => 'ir', // Persian (Iran)
        'he' => 'il', // Hebrew (Israel)
        'ar' => 'sa', // Arabic (Saudi Arabia)
        'my' => 'mm', // Burmese (Myanmar)
        'km' => 'kh', // Khmer (Cambodia)
        'lo' => 'la', // Lao (Laos)
        'mn' => 'mn', // Mongolian (Mongolia)

        // Americas
        'pt-BR' => 'br', // Portuguese (Brazil)
        'es-MX' => 'mx', // Spanish (Mexico)
        'es-AR' => 'ar', // Spanish (Argentina)

        // Oceania
        'en-AU' => 'au', // English (Australia)
        'en-NZ' => 'nz', // English (New Zealand)

        // Africa
        'sw' => 'ke', // Swahili (Kenya)
        'zu' => 'za', // Zulu (South Africa)
        'af' => 'za', // Afrikaans (South Africa)
        'am' => 'et', // Amharic (Ethiopia)
    ];

    static function getLanguageList(){
        return self::$enableLanguage;
    }
    static function getLanguageListKey(){
        return  array_keys(self::$enableLanguage);
    }

    /**
     * Get default language code
     * Syncs with config('app.locale')
     *
     * @return string Default language code (e.g., 'vi')
     */
    static function getDefaultLanguage(){
        return config('app.locale', 'vi');
    }

}


function dataURLtoFile($dataUrl, $filename)
{
    if (!$dataUrl)
        return;
    $ifp = fopen($filename, 'wb');
    $data = explode(',', $dataUrl);
    fwrite($ifp, base64_decode($data[1]));
    fclose($ifp);
    return new \Illuminate\Http\UploadedFile($filename, $filename);
}

/**
 * Helper: Lọc và trả về mảng các files/folders thực sự tồn tại
 * Hỗ trợ glob pattern như /bin/glx* để match nhiều files
 *
 * @param array $paths Mảng các paths hoặc glob patterns
 * @return array Mảng các paths tồn tại
 */
function filterExistingPaths(array $paths): array
{
    $result = [];

    foreach ($paths as $path) {
        // Nếu có ký tự wildcard, dùng glob
        if (strpos($path, '*') !== false || strpos($path, '?') !== false) {
            $matches = glob($path);
            if ($matches) {
                $result = array_merge($result, $matches);
            }
        } else {
            // Path thông thường, kiểm tra tồn tại
            if (file_exists($path)) {
                $result[] = $path;
            }
        }
    }

    return $result;
}


/**
 * PDF Digital Signature Extractor Class
 *
 * Simple class to extract digital signatures from PDF files
 * Hỗ trợ tiếng Việt - Vietnamese Unicode Support
 *
 * Usage:
 *   require_once 'PDFSignatureExtractor.php';
 *   $extractor = new PDFSignatureExtractor('file.pdf');
 *   $signatures = $extractor->extract();
 *   foreach ($signatures as $sig) {
 *       echo $sig['Name'] . ': ' . $sig['Reason'] . "\n";
 *   }
 */


/**
 * PDF Digital Signature Extractor Class
 *
 * Simple class to extract digital signatures from PDF files
 * Hỗ trợ tiếng Việt - Vietnamese Unicode Support
 *
 * Usage:
 *   require_once 'PDFSignatureExtractor.php';
 *   $extractor = new PDFSignatureExtractor('file.pdf');
 *   $signatures = $extractor->extract();
 *   foreach ($signatures as $sig) {
 *       echo $sig['Name'] . ': ' . $sig['Reason'] . "\n";
 *   }
 */
class PDFSignatureExtractor
{
    private $pdfPath;
    private $pdfContent;
    private $signatures = [];

    /**
     * Constructor
     *
     * @param string $pdfPath Path to PDF file
     * @throws Exception If file not found
     */
    public function __construct($pdfPath)
    {
        if (!file_exists($pdfPath)) {
            throw new Exception("PDF file not found: $pdfPath");
        }

        $this->pdfPath = $pdfPath;
        $this->pdfContent = file_get_contents($pdfPath);

        if ($this->pdfContent === false) {
            throw new Exception("Cannot read PDF file: $pdfPath");
        }
    }

    /**
     * Extract all digital signatures from PDF
     *
     * @return array Array of signatures, each containing:
     *               - Name: Người ký (signer name)
     *               - Date: Ngày ký (signature date - DD/MM/YYYY HH:MM:SS)
     *               - Reason: Lý do ký (reason for signature)
     *               - Location: Địa điểm (location)
     *               - ContactInfo: Thông tin liên hệ (contact information)
     *               - Email: Email của người ký
     *               - SignatureType: Loại chữ ký (signature type)
     */
    public function extract()
    {
        $this->signatures = [];
        $sigIndex = 0;

        // Method 1: Find signature annotations from AcroForm /Fields array
        $sigRefs = $this->findSignatureAnnotations();

        if (!empty($sigRefs)) {
            // Extract signatures by their object references
            foreach ($sigRefs as $objNum) {
                $sigObj = $this->findPDFObject($objNum);
                if ($sigObj) {
                    $sigIndex++;
                    $info = $this->parsePDFSignature($sigObj);
                    if (!empty($info['Date'])) { // Only include if has valid date
                        $this->signatures[$sigIndex] = $info;
                    }
                }
            }
        }

        // Method 2: Also find signature objects NOT in AcroForm (signature revisions)
        $additionalSigs = $this->findAllSignatureObjects();
        foreach ($additionalSigs as $sigObj) {
            // Check if we already added this (compare by date + reason)
            $info = $this->parsePDFSignature($sigObj);
            if (!empty($info['Date'])) {
                $isDuplicate = false;
                foreach ($this->signatures as $existing) {
                    if ($existing['Date'] === $info['Date'] && $existing['Reason'] === $info['Reason']) {
                        $isDuplicate = true;
                        break;
                    }
                }

                if (!$isDuplicate) {
                    $sigIndex++;
                    $this->signatures[$sigIndex] = $info;
                }
            }
        }

        return $this->signatures;
    }

    /**
     * Find signature annotation object references from AcroForm
     * @internal
     */
    private function findSignatureAnnotations()
    {
        $refs = [];

        // Look for /Fields array in AcroForm
        if (preg_match('/\/AcroForm\s+(\d+)\s+0\s+R/', $this->pdfContent, $m)) {
            $formObjNum = $m[1];
            $formObj = $this->findPDFObject($formObjNum);

            if ($formObj && preg_match('/\/Fields\s*\[([^\]]+)\]/', $formObj, $m)) {
                $fieldsList = $m[1];

                // Extract all object references
                if (preg_match_all('/(\d+)\s+0\s+R/', $fieldsList, $m)) {
                    foreach ($m[1] as $fieldObjNum) {
                        $fieldObj = $this->findPDFObject($fieldObjNum);

                        // Check if this field is a signature annotation
                        if ($fieldObj && strpos($fieldObj, '/Sig') !== false &&
                            preg_match('/\/V\s+(\d+)\s+0\s+R/', $fieldObj, $vm)) {
                            $refs[] = $vm[1];  // Get the signature value object reference
                        }
                    }
                }
            }
        }

        return $refs;
    }

    /**
     * Find all /Type/Sig objects in PDF (including signature revisions)
     * @internal
     */
    private function findAllSignatureObjects()
    {
        $sigs = [];
        $foundObjNums = [];

        // Method: Find all objects and check if they contain /Type/Sig
        // Use regex to find all "N 0 obj << ... endobj" patterns
        if (preg_match_all('/(\d+)\s+0\s+obj\s*<<(.+?)endobj/s', $this->pdfContent, $matches)) {
            foreach ($matches[0] as $idx => $fullObj) {
                $objNum = (int)$matches[1][$idx];
                $objContent = $matches[2][$idx];

                // Check if this object contains /Type/Sig
                if (strpos($objContent, '/Type/Sig') !== false) {
                    $foundObjNums[] = $objNum;
                }
            }
        }

        // Extract the actual signature objects by their object numbers
        foreach ($foundObjNums as $objNum) {
            $sigObj = $this->findPDFObject($objNum);
            if ($sigObj && strlen($sigObj) > 50) {  // Filter out tiny objects
                $sigs[] = $sigObj;
            }
        }

        return $sigs;
    }


    /**
     * Find a PDF object by its object number
     * @internal
     */
    private function findPDFObject($objNum)
    {
        $pattern = '/' . (int)$objNum . ' 0 obj\s*<<(.+?)endobj/s';
        if (preg_match($pattern, $this->pdfContent, $m)) {
            return '<<' . $m[1];
        }
        return null;
    }

    /**
     * Get extracted signatures
     *
     * @return array Signatures
     */
    public function getSignatures()
    {
        return $this->signatures;
    }

    /**
     * Get signature count
     *
     * @return int Number of signatures
     */
    public function count()
    {
        return count($this->signatures);
    }

    /**
     * Get signature by index
     *
     * @param int $index Signature index (1-based)
     * @return array|null Signature or null if not found
     */
    public function getSignature($index)
    {
        return $this->signatures[$index] ?? null;
    }

    /**
     * Get PDF file path
     *
     * @return string PDF file path
     */
    public function getPDFPath()
    {
        return $this->pdfPath;
    }

    /**
     * Get PDF file size in bytes
     *
     * @return int File size
     */
    public function getPDFSize()
    {
        return filesize($this->pdfPath);
    }

    /**
     * Export signatures to JSON string
     *
     * @param bool $pretty Pretty-print JSON
     * @return string JSON string
     */
    public function toJSON($pretty = true)
    {
        $options = $pretty ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 0;
        return json_encode([
            'file' => $this->pdfPath,
            'size' => $this->getPDFSize(),
            'signature_count' => count($this->signatures),
            'signatures' => $this->signatures
        ], $options);
    }

    /**
     * Export signatures to CSV string
     *
     * @return string CSV string
     */
    public function toCSV()
    {
        $csv = "Name,Date,Reason,Location,ContactInfo,Email,SignatureType\n";

        foreach ($this->signatures as $sig) {
            $csv .= '"' . addslashes($sig['Name'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['Date'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['Reason'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['Location'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['ContactInfo'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['Email'] ?? '') . '",';
            $csv .= '"' . addslashes($sig['SignatureType'] ?? '') . '"' . "\n";
        }

        return $csv;
    }

    /**
     * Export signatures to XML string
     *
     * @return string XML string
     */
    public function toXML()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<signatures>\n";
        $xml .= "  <metadata>\n";
        $xml .= "    <file>" . htmlspecialchars($this->pdfPath) . "</file>\n";
        $xml .= "    <size>" . $this->getPDFSize() . "</size>\n";
        $xml .= "    <extraction_date>" . date('Y-m-d H:i:s') . "</extraction_date>\n";
        $xml .= "  </metadata>\n";
        $xml .= "  <signature_list count=\"" . count($this->signatures) . "\">\n";

        foreach ($this->signatures as $idx => $sig) {
            $xml .= "    <signature number=\"$idx\">\n";
            $xml .= "      <name>" . htmlspecialchars($sig['Name'] ?? 'N/A') . "</name>\n";
            $xml .= "      <date>" . htmlspecialchars($sig['Date'] ?? '') . "</date>\n";
            $xml .= "      <reason>" . htmlspecialchars($sig['Reason'] ?? '') . "</reason>\n";
            $xml .= "      <location>" . htmlspecialchars($sig['Location'] ?? '') . "</location>\n";
            $xml .= "      <contact_info>" . htmlspecialchars($sig['ContactInfo'] ?? '') . "</contact_info>\n";
            $xml .= "      <email>" . htmlspecialchars($sig['Email'] ?? '') . "</email>\n";
            $xml .= "      <type>" . htmlspecialchars($sig['SignatureType'] ?? '') . "</type>\n";
            $xml .= "    </signature>\n";
        }

        $xml .= "  </signature_list>\n";
        $xml .= "</signatures>\n";

        return $xml;
    }

    /**
     * Parse a single signature object from PDF
     * @internal
     */
    private function parsePDFSignature($sigObj)
    {
        $info = [
            'Name' => null,
            'Date' => null,
            'Reason' => null,
            'Location' => null,
            'ContactInfo' => null,
            'Email' => null,
            'SignatureType' => null,
        ];

        // Extract Name - check raw content BEFORE decoding for tool metadata
        $rawName = null;
        if (preg_match('/\/Name\s*\(([^\)]+)\)/', $sigObj, $m)) {
            $rawName = $m[1];
            // Check if raw name is tool metadata BEFORE decoding
            if (!$this->isToolMetadata($rawName)) {
                // Only decode if it's not tool metadata
                $info['Name'] = $this->decodeUTF16BE($rawName);
            }
            // If it's tool metadata, leave Name as null for now - we'll extract from Reason later
        }

        // Extract M (date)
        if (preg_match('/\/M\s*\(([^\)]+)\)/', $sigObj, $m)) {
            $info['Date'] = $this->formatPDFDate($m[1]);
        }

        // Extract Reason
        if (preg_match('/\/Reason\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $reason = $m[1];
            $info['Reason'] = $this->decodeUTF16BE($reason);

            // Extract email from reason (format: <email@domain.com>)
            $decodedReason = $info['Reason'];
            if (preg_match('/<([^>]+@[^>]+)>/', $decodedReason, $em)) {
                $info['Email'] = $em[1];
            }

            // Extract signer name from Reason if Name is tool metadata
            // Pattern: "Name<email> ..." or "Name<email> message"
            if (($info['Name'] === null || $this->isToolMetadata($rawName)) && preg_match('/^([^<]+)</', $decodedReason, $nm)) {
                $signerName = trim($nm[1]);
                if (!empty($signerName)) {
                    $info['Name'] = $signerName;
                }
            }
        }

        // Extract Location
        if (preg_match('/\/Location\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $location = $m[1];
            $info['Location'] = $this->decodeUTF16BE($location);
        }

        // Extract SubFilter (signature type)
        if (preg_match('/\/SubFilter\s*\/(\w+)/', $sigObj, $m)) {
            $info['SignatureType'] = $m[1];
        }

        // Extract ContactInfo
        if (preg_match('/\/ContactInfo\s*\(([^\)]*?)\)/s', $sigObj, $m)) {
            $contact = $m[1];
            $info['ContactInfo'] = $this->decodeUTF16BE($contact);

            // Extract email from ContactInfo (format: Email: email@domain.com)
            if (!$info['Email']) {
                $decodedContact = $info['ContactInfo'];
                if (preg_match('/Email:\s*([^;|\n]+)/', $decodedContact, $em)) {
                    $email = trim($em[1]);
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $info['Email'] = $email;
                    }
                }
            }
        }

        return $info;
    }

    /**
     * Check if Name field contains tool metadata instead of actual signer name
     * Works with BOTH raw PDF content and decoded strings
     * @internal
     */
    private function isToolMetadata($nameContent)
    {
        if (empty($nameContent)) return false;

        // Known tool names to detect
        $toolPatterns = [
            'iTextSharp',
            'iText',
            'Adobe',
            'Microsoft',
            'LibreOffice',
            'OpenOffice',
            'mPDF',
            'TCPDF'
        ];

        foreach ($toolPatterns as $pattern) {
            if (stripos($nameContent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Decode UTF-16BE string from PDF
     * Handles:
     * - Plain ASCII text
     * - Raw UTF-16BE bytes (with BOM feff)
     * - Escape sequences (\nnn notation)
     * @internal
     */
    private function decodeUTF16BE($hexStr)
    {
        // Handle hex string format (<...>)
        if (strpos($hexStr, '<') === 0) {
            return $this->decodeUTF16BEBytes(substr($hexStr, 1, -1));
        }

        // Empty string
        if (empty($hexStr)) {
            return '';
        }

        // Check if it's plain ASCII (no high bytes > 0x7F except BOM)
        $hasHighBytes = false;
        for ($i = 0; $i < strlen($hexStr); $i++) {
            $byte = ord($hexStr[$i]);
            if ($byte > 0x7F && !($i < 2 && ($i === 0 && $byte === 0xfe || $i === 1 && $byte === 0xff))) {
                $hasHighBytes = true;
                break;
            }
        }

        // If no high bytes, it's plain ASCII
        if (!$hasHighBytes) {
            return $hexStr;
        }

        // Check for escape sequences (\nnn notation)
        if (strpos($hexStr, '\\') !== false && preg_match('/\\\\[0-7]{1,3}/', $hexStr)) {
            return $this->decodeUTF16BEFromEscapes($hexStr);
        }

        // Otherwise, treat as raw UTF-16BE bytes
        return mb_convert_encoding($hexStr, 'UTF-8', 'UTF-16BE');
    }

    /**
     * Decode UTF-16BE from escape sequences (\nnn)
     * @internal
     */
    private function decodeUTF16BEFromEscapes($str)
    {
        $bytes = '';
        $i = 0;
        while ($i < strlen($str)) {
            if ($str[$i] === '\\' && $i + 1 < strlen($str)) {
                if (is_numeric($str[$i + 1])) {
                    // Octal escape sequence
                    $octal = substr($str, $i + 1, 3);
                    $bytes .= chr(octdec($octal));
                    $i += 4;
                } else {
                    $bytes .= $str[$i + 1];
                    $i += 2;
                }
            } else {
                $bytes .= $str[$i];
                $i++;
            }
        }

        return mb_convert_encoding($bytes, 'UTF-8', 'UTF-16BE');
    }

    /**
     * Decode UTF-16BE from hex string
     * @internal
     */
    private function decodeUTF16BEBytes($hexStr)
    {
        $bytes = '';
        for ($i = 0; $i < strlen($hexStr); $i += 2) {
            $bytes .= chr(hexdec(substr($hexStr, $i, 2)));
        }
        return mb_convert_encoding($bytes, 'UTF-8', 'UTF-16BE');
    }

    /**
     * Format PDF date to human-readable format
     * PDF date format: D:YYYYMMDDHHmmSS±HH'mm
     * Output format: DD/MM/YYYY HH:MM:SS
     * @internal
     */
    private function formatPDFDate($pdfDate)
    {
        // Remove D: prefix
        $pdfDate = str_replace('D:', '', $pdfDate);

        // Parse date components
        if (preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})([\+\-]\d{2})\'?(\d{2})?/', $pdfDate, $m)) {
            $year = $m[1];
            $month = $m[2];
            $day = $m[3];
            $hour = $m[4];
            $minute = $m[5];
            $second = $m[6];

            $formatted = sprintf(
                '%s/%s/%s %s:%s:%s',
                $day, $month, $year,
                $hour, $minute, $second
            );

            return $formatted;
        }

        return $pdfDate;
    }
}

/**
 * Test helper: Get content from URL with auth headers
 * Usage: testFileGetContent($url, $user)
 */
function testFileGetContent($url, $user = null)
{
    if (!$user) {
        $user = \App\Models\User::where('email', 'member@abc.com')->first();
    }

    if (!$user) {
        throw new Exception('User not found');
    }

    $token = $user->getUserToken();

    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\nCookie: _tglx863516839=$token\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $content = @file_get_contents($url, false, $context);

    // Get HTTP response code
    $statusCode = 200;
    if (!empty($http_response_header)) {
        preg_match('/HTTP\/\d\.\d (\d+)/', $http_response_header[0], $matches);
        $statusCode = (int)($matches[1] ?? 200);
    }

    return new class($content, $statusCode) {
        public function __construct(public $content, public $statusCode) {}
        public function getContent() { return $this->content; }
        public function getStatusCode() { return $this->statusCode; }
    };
}

/**
 * Test helper: POST request with auth headers
 * Usage: testFilePostContent($url, $data, $user)
 */
function testFilePostContent($url, $data = [], $user = null)
{
    if (!$user) {
        $user = \App\Models\User::where('email', 'member@abc.com')->first();
    }

    if (!$user) {
        throw new Exception('User not found');
    }

    $token = $user->getUserToken();
    $postData = is_array($data) ? http_build_query($data) : $data;

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Authorization: Bearer $token\r\nCookie: _tglx863516839=$token\r\nContent-Type: application/x-www-form-urlencoded\r\n",
            'content' => $postData
        ]
    ];

    $context = stream_context_create($options);
    $content = @file_get_contents($url, false, $context);

    // Get HTTP response code
    $statusCode = 200;
    if (!empty($http_response_header)) {
        preg_match('/HTTP\/\d\.\d (\d+)/', $http_response_header[0], $matches);
        $statusCode = (int)($matches[1] ?? 200);
    }

    return new class($content, $statusCode) {
        public function __construct(public $content, public $statusCode) {}
        public function getContent() { return $this->content; }
        public function getStatusCode() { return $this->statusCode; }
    };
}
