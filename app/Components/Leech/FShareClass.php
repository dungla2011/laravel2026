<?php
namespace App\Components\Leech;

class FShareClass{

    public static $token;
    public static $sid;
    public static $tokenGetTime;
    public static $setProxy = null;//'[2407:2ec0:0:216:33:3b:616:526]:28888';
//    public static $setProxy = '[2407:2ec0:0:216:33:3b:616:638]:28888';
    public static $userAgent = 'test123-FR5QTU';

    var $id;
    var $linkcode;
    var $name;
    var $secure;
    var $public;
    var $shared;
    var $directlink;
    var $type;
    var $path;
    var $owner_id;
    var $pid;
    var $size;
    var $downloadcount;
    var $deleted;
    var $mimetype;
    var $created;
    var $modified2;
    var $modified;
    var $file_type;
    var $pwd;
    var $crc32;
    var $folder_path;
    var $storage_id;
    var $realname;
    var $lastdownload;
    var $full_link;
    var $log;
    var $log_download; //email và thời gian download

    //////////////////////////////////////////////
    //LAD add extra field
    var $parent_leech;
    var $leech_id;
    var $summary;
    var $parent_folder_id_fshare;
    var $parent_folder_name_fshare;
    var $parentLink; //Link web co file nay
    var $lastLog; //trang thai cuoi download neu co loi

    var $point_; //sort high to low = count download /nday
    var $note_; // = -2 : crc bị sai, bỏ qua khi tải lại
    var $link4s_; // link 4share
    var $id4S;
    var $lastUpdatePoint_; //

    var $linkpost_; // link post ở các forum...

    function isFile()
    {
        return $this->type;
    }

    //Khi tải về,  nếu thấy sai crc, thì sẽ update =-2 trong db
    function isWrongCrc(){
        if($this->note_ == -2)
            return 1;
        return 0;
    }

    function isValidate($option = null,$param = null){
        return 1;
    }

    function isValidLinkFSNotDelete()
    {
        if (isset($this->public) && $this->public > -1 && isset($this->linkcode) && $this->linkcode) {
            return 1;
        }
        return 0;
    }

    public static function checkFshareLink($link){
        return FShareClass::ifFshareLink($link);
    }

    public static function ifFshareLink($link)
    {
        if (strstr($link, "fshare.vn/f"))
            return 1;
        return 0;
    }

    static function downloadFile($urlfile, $saveTo, $fileSizeToShowProgress = 0){

        if(!isCli()){
            echo("\n downloadFile Error: not cli?");
            return 0;
        }

        $dirname = dirname($saveTo);
        if (!file_exists($dirname)) {
            exec("mkdir $dirname -p");
        }

        if (!file_exists($dirname)){
            echo("\nError: can not create dir: $dirname");
            return 0;
        }


        $proxy = FShareClass::$setProxy;
        exec("wget -e use_proxy=yes -e http_proxy=$proxy $urlfile -O $saveTo");

        return 1;
    }

    public static function getUserInfo($uname, $pw)
    {
        echo "<br/>\n ---- UINO: $uname ---- ";
        $url = "https://api.fshare.vn/api/user/login/";

        $ch = curl_init($url);

        //setup request to send json via POST
        $data = array(
            'user_email' => $uname,
            'password' => $pw,
            'app_key' => 'L2S7R6ZMagggC5wWkQhX2+aDi467PPuftWUMRFSn'
        );

        $payload = json_encode($data);
        //if(isset(FShareClass::))
//attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        if (isset(FShareClass::$setProxy) && FShareClass::$setProxy) {
            echo "\n Proxy: " . FShareClass::$setProxy;
            curl_setopt($ch, CURLOPT_PROXY, FShareClass::$setProxy);
        }
//return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $fileSid = "/var/glx/weblog/fshare_sid_$uname";
        if (file_exists($fileSid) && filemtime($fileSid) < time() - 2 * _NSECOND_DAY) {
            echo "<br/>\n Old file, unlink: $fileSid";
            unlink($fileSid);
        }

        if (file_exists($fileSid)) {
            $sid = file_get_contents($fileSid);
        } else {
            //execute the POST request
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);

            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($info);
            echo "</pre>";

            echo "<br/>\n USER INFO: = $result";
            $mm = json_decode($result);

            if(!$mm)
                die("Not found json?");

            $token = $mm->token;
            $sid = $mm->session_id;
            if ($sid) {
                outputW($fileSid, $sid);
            }
        }

        if ($sid) {
            $urlUinfo = 'https://api.fshare.vn/api/user/get';
            for ($i = 0; $i < 10; $i++) {
                curl_setopt($ch, CURLOPT_URL, $urlUinfo);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: session_id=$sid"));
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpcode == 503) {
                    sleep(1);
                    continue;
                } else {
                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    print_r($result);
                    echo "</pre>";
                    break;
                }
            }
        }

        echo "<br/>\n 111";
        if ($httpcode == 201) {
            echo "<br/>\n httpcode = $httpcode";
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($result);
            echo "</pre>";
            return;
        }
        echo "<br/>\n 1112";
        if ($result) {
            $uif = json_decode($result);
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($uif);
            echo "</pre>";
            if ($uif && isset($uif->expire_vip)) {
                echo "<br/>\n StartTime/EndVip: " . nowyh($uif->joindate) . " / " . nowyh("$uif->expire_vip");
            }
        }
        echo "<br/>\n 113";
        //close cURL resource
        curl_close($ch);
    }

    public static function getFixLinkFshareFormat($linkFS)
    {

        if (isAdmin_()) {
            // die("ABC = $linkFS ");
        }

        if (!strstr($linkFS, 'fshare.vn/f')) {
            return $linkFS;
        }
        $linkFS = trim(strip_tags(urldecode($linkFS)));
        $linkFS = strip_tags($linkFS);

        if (substr($linkFS, -3) == "/rn") {
            $linkFS = trim($linkFS, "/rn");
        }
        if (substr($linkFS, -2) == "/r") {
            $linkFS = trim($linkFS, "/r");
        }
        if (substr($linkFS, -2) == "/n") {
            $linkFS = trim($linkFS, "/n");
        }

        $linkFS = preg_replace('/[[:^print:]]/', '', $linkFS);

        $linkFS = trim($linkFS, '/');
        $linkFS = trim($linkFS, "/\\");

        $linkFS = str_replace("www.fshare.vn", "fshare.vn", $linkFS);

        $linkFS = str_replace("http://www.fshare.vn/", "https://fshare.vn/", $linkFS);
        $linkFS = str_replace("http://fshare.vn/", "https://fshare.vn/", $linkFS);
        $linkFS = str_replace("https://www.fshare.vn/", "https://fshare.vn/", $linkFS);

        if (substr($linkFS, 0, 11) == 'fshare.vn/f') {
            $linkFS = "https://" . $linkFS;
        }
        return $linkFS;
    }

    public static function checkIfDeleteMongo($linkFS)
    {
        if (!strstr($linkFS, 'fshare.vn/f'))
            return 0;
        $tv = new ThuVienAz2();
        if ($tv->getOneWhere(['link' => $linkFS])) {
            if (isset($tv->content) && $tv->content == '404')
                return 1;
            if ($tv->content && isset($tv->content->public) && $tv->content->public == -1) {
                return 1;
            }
            if ($tv->content && isset($tv->content->public) && $tv->content->public != -1) {
                return 0;
            }
        }
        $dlink = new \Base\ModelDataLink();
        if ($dlink->getOneWhere_(" refer_remote = '$linkFS'")) {
            if ($dlink->refer_info) {
                $fsInfo = json_decode($dlink->refer_info);
                if (isset($fsInfo->public) && $fsInfo->public == -1)
                    return 1;
                if (isset($fsInfo->public) && $fsInfo->public != -1)
                    return 0;
            }
        }
        loi("Not found link in mongo? DataLinkID = $dlink->id , LinkFS: $linkFS");
        return 1;
    }

    public static function checkIfDelete($jsonString, $dlink = null)
    {
//        if(!$jsonString)
//            return 0;
        $jsonD = json_decode($jsonString);
        if (!$jsonD) {
            $pad = '';
            if ($dlink)
                $pad = $dlink->id;
            loi("Not json? ID = '$dlink->id'");
        }
        if ($jsonD) {
            $ob = new FShareClass();
            $ob->loadFromObj($jsonD);
            if (isset($ob->public) && $ob->public == -1)
                return 1;
        }
        return 0;
    }

    public static function checkIfHaveFshareLinkInDataLink($linkFs, $linkId)
    {
        $obj = new \Base\ModelDataLink();
        if ($obj->getOneWhere_("id = $linkId AND refer_remote = '$linkFs'")) {
            return 1;
        }
        return 0;
    }

    public static function checkIf4SHaveFshareLink($link)
    {
        $file = new \Base\ModelCloudFile();
        $sql = " refer_url = '$link'";
        //echo "<br/>\n SQL = $sql ";
        if ($file->getOneWhere_($sql)) {
            return $file;
        } else
            return 0;
    }

    public static function login($uname = null, $pw = null, $timeout = 3600){
        FShareClass::getTokenFshare($uname, $pw, $timeout);
    }

    public static function getCrc32b($file){
        return CFile::getCrc32b($file);
    }

    public static function getTokenFshare($uname = null, $pw = null, $timeout = 3600)
    {

        if (!$uname || !$pw) {
            //FShareClass::$setProxy = "[2405:19c0:0:fffe:152:1:2:4]:28888";
            $uname = 'hanoi010203040506@gmail.com';
            $pw = 'hanoi010203040506';
            $uname = 'sonsonvn2021@gmail.com';
            $pw = 'Hanoi123$5"';
        }

        FShareClass::$tokenGetTime = time();

//        if (ctool::isWindow())
//            $fileTk = "E:/tokenFshare.$uname";
//        else

        $fileTk = "/var/glx/weblog/tokenFshare.$uname";

        if (file_exists($fileTk)) {
            if (!strstr(file_get_contents($fileTk), "|" . $uname)) {
                unlink($fileTk);
            }
        }

        echo "<br/>\nCheck Token File Cache: $fileTk ";
        clearstatcache(1);
        if (file_exists($fileTk) && filemtime($fileTk) > time() - $timeout) {
            echo "<br/>\n Found Token Cache: ";
            $str = file_get_contents($fileTk);
            $mInfo = explode("|", $str);
            $token = $mInfo[0];
            $sid = $mInfo[1];
            if (isset($mInfo[2]))
                $userAcc = $mInfo[2];
            //list($token, $sid) = explode("|", $str);
            FShareClass::$token = $token;
            FShareClass::$sid = $sid;
            //echo "<br/>\nTOKEN = $token / SID = $sid";
            //getch("...");
            return 0;
        }

        echo "<br/>\n Sleep 3 to login";
        sleep(3);

//        getch("...");

//create a new cURL resource
        $url = "https://api.fshare.vn/api/user/login/";

        $ch = curl_init($url);

        //setup request to send json via POST
        $data = array(
            'user_email' => $uname,
            'password' => $pw,
            'app_key' => 'L2S7R6ZMagggC5wWkQhX2+aDi467PPuftWUMRFSn'
        );

        $payload = json_encode($data);

        if (isset(FShareClass::$setProxy) && FShareClass::$setProxy) {
            echo "\n  GlxProxy: " . FShareClass::$setProxy;
            curl_setopt($ch, CURLOPT_PROXY, FShareClass::$setProxy);
        }

//attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //2021.05
        curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/3.6.0');

        curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_2_0);

        //curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        //29.08.2020: bo xung them:
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//execute the POST request
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);

//close cURL resource
        curl_close($ch);

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

        echo "<br/>\n LOGIN RET = $result";

        if(!$result)
            return null;

        $mm = json_decode($result);

        if(!$mm)
            return null;

        if($mm->code != 200){
            echo "<br/>\n Login error: $mm->msg";
            return null;
        }

        $token = $mm->token;
        $sid = $mm->session_id;

        if (!$token) {
            die("\n Can not login, Not found token?");
        }

        FShareClass::$token = $token;
        FShareClass::$sid = $sid;

        echo "<br/>\nTOKEN = $token / SID = $sid";
        //getch("...");
        outputW($fileTk, "$token|$sid|$uname");
        exec("chown apache:apache $fileTk");
    }

    public static function getDirectLink1($link1)
    {
        //   $token = 'a38b44ae1aa2e875dcb5765ea2f27e16bab8e1e0';
        //   $sid = '4ash7c5mqiot1gu0tfli82rj5g';

        $token = FShareClass::$token;
        $sid = FShareClass::$sid;

        $data = [
            'token' => $token,
            'url' => $link1];

        $data = [
            'token' => $token,
            'url' => $link1,
        ];

        $url = 'https://api.fshare.vn/api/session/download';
        $ch = curl_init($url);

        $payload = json_encode($data);

        if (isset(FShareClass::$setProxy) && FShareClass::$setProxy) {
            curl_setopt($ch, CURLOPT_PROXY, FShareClass::$setProxy);
        }

//attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', "Cookie: session_id=$sid"]);
//return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userAgent = FShareClass::$userAgent;
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: session_id=$sid"));

        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

        //execute the POST request
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


//execute the POST request
        $result = curl_exec($ch);

//close cURL resource
        curl_close($ch);

        //echo "\n DLINK RET = $result ";
        //echo "<br/>\n$result";
        if ($result && json_decode($result) && isset(json_decode($result)->location)) {
            $ret = json_decode($result)->location;
            if (strstr($ret, "ERROR12") !== false) {
                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                print_r($ret);
                echo "</pre>";
                return null;
            }
            return $ret;
        }

        echo "<br/>\n $token / $sid";
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($result);
        echo "</pre>";

        return null;

    }

    /*
     * @return FShareClass
     */
    public static function getFileInfo($link1, &$returnCode = null)
    {
//        $token = 'a38b44ae1aa2e875dcb5765ea2f27e16bab8e1e0';
//        $sid = '4ash7c5mqiot1gu0tfli82rj5g';
        if (!strstr($link1, 'fshare.vn/f')) return null;

        $token = FShareClass::$token;
        $sid = FShareClass::$sid;

        if (!$token || !$sid) {
            die("Not login yet???");
        }

        for ($i = 0; $i < 3; $i++) {

            if ($i > 0)
                sleep(2);
            echo " $i--x # ";
            $data = [
                'token' => $token,
                'url' => $link1];

            $url = 'https://api.fshare.vn/api/fileops/get';
            $ch = curl_init($url);

            $payload = json_encode($data);
            //$payload = $data;

            //attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            //set the content type to application/json
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            //return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/3.6.0');

            //curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_2_0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', "Cookie: session_id=$sid"]);

            $userAgent = FShareClass::$userAgent;
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: session_id=$sid"));
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

            if (isset(FShareClass::$setProxy) && FShareClass::$setProxy) {
                curl_setopt($ch, CURLOPT_PROXY, FShareClass::$setProxy);
            }

            //execute the POST request
            $result = curl_exec($ch);

            //echo "\n DLINK RET = $result ";

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($result);
//            echo "</pre>";

            echo "<br/>\n CODE CURL RET = $httpcode";
            $returnCode = $httpcode;
            if ($httpcode == 201) {

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($result);
//                echo "</pre>";

                getch(" Error 201 ...");
                return;

            }
            if ($httpcode == 404) {
                echo "<br/>\n $httpcode qua tai hoac file ko con? ";

                return 404;

                $obj = new FShareClass();
                $obj->public = -1;
                $obj->addLog("FS File khong ton tai:! $link1");
                $obj->linkcode = basename($link1);
                return $obj;
            }

            if ($httpcode == 503) {
                echo "<br/>\n $httpcode Overload, query lại?";
                curl_close($ch);
                //sleep(2);
                continue;
                //echo "<br/>\n 503 qua tai hoac file ko con? ";
                //return null;
            }

            //close cURL resource
            curl_close($ch);

            if ($result) {
                $ret = json_decode($result);
                if ($ret) {
                    $obj = new FShareClass();
                    $obj->loadFromObj($ret);
                    if (isset($obj->id))
                        return $obj;
                }
            } else {

            }

        }

        return null;
    }

    /*
     * Check 07.2021 OK
     */
    public static function getFolderList($fid, $index = 0, &$retHtmlCode, $totalLimit = 0)
    {

        $limit = 1000;

        $fold = $fid;
        if (!strstr($fid, "fshare.vn/folder/"))
            $fold = 'https://www.fshare.vn/folder/' . $fid;

        echo "<br/>\n FOLD = $fold";

        $token = FShareClass::$token;
        $sid = FShareClass::$sid;
        $userAgent = FShareClass::$userAgent;

        if (!$token || !$sid) {
            die("Not login yet???");
        }

        $mret = [];

        while (1) {

            $data = [
                'token' => $token,
                'url' => $fold,
                'dirOnly' => 0,
                'pageIndex' => $index,
                'limit' => $limit,
            ];

            $url = 'https://api.fshare.vn/api/fileops/getFolderList';
            $ch = curl_init($url);

            if (isset(FShareClass::$setProxy) && FShareClass::$setProxy) {
                curl_setopt($ch, CURLOPT_PROXY, FShareClass::$setProxy);
            }

            $payload = json_encode($data);

            //attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            //set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', "Cookie: session_id=$sid"]);

            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

            //execute the POST request
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            //return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLINFO_HEADER_OUT, true);

            //execute the POST request

            //$result = curl_exec($ch);
            for ($i1 = 1; $i1 < 10; $i1++) {
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $retHtmlCode = $httpcode;
                if ($httpcode != 503) {
                    break;
                }
                echo "\n Httpcode = $httpcode , Sleep 2 to loop";
                sleep(2);
            }

            //close cURL resource
            curl_close($ch);


            if ($httpcode != 200) {

                echo "<br/>\nRET = $result, CODE = $httpcode";

                return $httpcode;
            }


            if (!$result)
                break;

            $mm = json_decode($result);
            if (!$mm)
                break;

            $tts = $cc = 0;

            if ($mm && is_array($mm))
                $mret = array_merge($mret, $mm);

            $ttRet = count($mm);

            echo "<br/>\n  index: $index , $fid, ttret = $ttRet, $totalLimit";

            //getch("... index: $index , $fid, ttret = $ttRet, $totalLimit");

            if($totalLimit)
                if( $ttRet > $totalLimit){
                    break;
                }

            //Nếu trả lại nhỏ hơn limit
            if ($ttRet < $limit)
                break;
            $index++;

            sleep(2);

            if ($ttRet > 100000) {
                loi("FS GetFolder, Index = $index to high???");
            }
        }

        //echo "<br/>\n $cc / Size = ".ByteSize($tts);

        return $mret;
    }
}
