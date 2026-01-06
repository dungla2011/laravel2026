<?php


class ClassVmwareHost extends \Base\ClassBaseGlx {
    var $host;
    var $name;
    var $connection_state;
    var $power_state;
}

class ClassVMwareTool extends \Base\ClassBaseGlx {

    public static $sid;
    public static $domain;

    public static $fileCookieMobLogin = '/var/glx/weblog/glx_vc_cookie_get_token_wmks.txt';

    public static $tmpListTempId = [];

    /**
     * @var ClassVMinfo[]
     */
    public static $tmpListVmInfoCache = null;

    public static function getDomainNamePortMapViewConsole($domainVM){
        if ($domainVM == '103.74.121.166' || $domainVM == '192.168.20.11' || $domainVM == '10.0.1.11')
            //$domainAndPort = "sv991.pm33.net:20001";
            return "sv992.galaxycloud.vn:20001";
        elseif ($domainVM == '103.74.121.196' || $domainVM == '192.168.20.12' || $domainVM == '10.0.1.12')
            return "sv992.galaxycloud.vn:20002";
        elseif ($domainVM == '103.74.121.39' || $domainVM == '192.168.20.13' || $domainVM == '10.0.1.13')
            return "sv992.galaxycloud.vn:20000";
        elseif ($domainVM == '10.0.1.99')
            return "sv992.galaxycloud.vn:20004";
        elseif ($domainVM == '10.0.1.14')
            return "sv992.galaxycloud.vn:20005";
        elseif ($domainVM == '10.0.1.15')
            return "sv992.galaxycloud.vn:20015";
        elseif ($domainVM == '10.0.1.16')
            return "sv992.galaxycloud.vn:20016";
        elseif ($domainVM == '10.0.1.17')
            return "sv992.galaxycloud.vn:20017";
        elseif ($domainVM == '10.0.1.18')
            return "sv992.galaxycloud.vn:20018";
        elseif ($domainVM == '10.0.1.19')
            return "sv992.galaxycloud.vn:20019";
        elseif ($domainVM == '10.0.1.20')
            return "sv992.galaxycloud.vn:20020";
        elseif ($domainVM == '10.0.1.21')
            return "sv992.galaxycloud.vn:20021";
        elseif ($domainVM == '10.0.1.31')
            return "sv992.galaxycloud.vn:20031";
        elseif ($domainVM == '10.0.1.22')
            return "sv992.galaxycloud.vn:20022";
        return null;
    }

    public static function getLoginAndSetCookieFileVCenter($domain = null, $uname = null , $pw = null, $exprireTime = 900){

        if(!$domain && !$uname && !$pw){
            $file = "/var/ufile_cms2017LAD/vsphere-automation-sdk-java/test1";
            $uname = getUserVcFromFile($file)[0];
            $pw = getUserVcFromFile($file)[1];
            $domain = DEF_VCENTER_IP;
        }

        $fileCC = ClassVMwareTool::$fileCookieMobLogin;

        if(file_exists($fileCC))
            if(filemtime($fileCC) < time() - $exprireTime)
                unlink($fileCC);

        if(file_exists($fileCC))
            return;

        $un1 = $uname;
        $acc = $pw;

        $ch = curl_init("https://$domain/mob/");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_USERPWD, $un1.':'.$acc);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $fileCC);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch,CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $ret = curl_exec($ch);

        $no = curl_errno($ch);
        if($no!=0){
            $info = curl_getinfo($ch);
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($info);
            echo "</pre>";
            loi("*** ERROR: $no");
        }

        curl_close($ch);
    }

    //Bản này dùng filegetcontent VC7 ok
    public static function getWebTokenMks2020($domainVc, $vmId){

        $file = "/var/ufile_cms2017LAD/vsphere-automation-sdk-java/test1";
        $un1 = getUserVcFromFile($file)[0];
        $acc = getUserVcFromFile($file)[1];

//echo "<br/>\n '$un1'/'$acc'";

        $domain = $domainVc;

        $vmx = $vmId;

        $auth = base64_encode("$un1:$acc");
        $context = stream_context_create([
            "http" => [
                "header" => "Authorization: Basic $auth"
            ],
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ]);

        $ret = file_get_contents("https://$domain/mob/?moid=$vmx&method=acquireTicket", false, $context );

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($http_response_header);
//        echo "</pre>";

        $cookies = array();
        $vmware_debug_session_cookie = "";
        foreach ($http_response_header as $hdr) {
            if (preg_match('/^set-cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $cookies += $tmp;
                if(isset($tmp['vmware_debug_session'])){
                    $vmware_debug_session_cookie = "vmware_debug_session=".str_replace('"','',$tmp['vmware_debug_session']);
                }
            }
        }

//        print_r($cookies);
//        echo "<br/>\n COOKIE OK: $vmware_debug_session_cookie";
//        echo "<pre> >>> RET: " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($ret);
//        echo "</pre>";

        if(!$ret){{
            loi(" *** NOT RET");
        }}

//$out = json_decode($ret);
//sleep(2);



        $xx = str_get_html($ret);

        $session_nonce = $xx->find("input[name=vmware-session-nonce]",0)->value;

        //echo "<br/>\n session_nonce = $session_nonce";


        $postdata = http_build_query(
            ["vmware-session-nonce"=>"$session_nonce",
                "ticketType"=>"webmks"]
        );

        //echo "<br/>\n postdata = $postdata";

        $auth = base64_encode("$un1:$acc");
        $context = stream_context_create([
            "http" => [
                "header" => "Authorization: Basic $auth"
            ],
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ]);

        $context = stream_context_create([
            "http" => [
                "header" => "Cookie: $vmware_debug_session_cookie\r\nContent-Type: application/x-www-form-urlencoded",
                'method'  => 'POST',
                'content' => $postdata
            ],
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ]);
        $ret = file_get_contents("https://$domain/mob/?moid=$vmx&method=acquireTicket", false, $context );

//echo "RET = <pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($ret);
//echo "</pre>";

        if(!$ret){{
            die("<br>NOT RET2");
        }}


        $xx = str_get_html($ret);

        $hostOK = " NOT FOUND HOST0 ($domainVc) ";

        foreach ($xx->find("td.c2") AS $td){
            if($td instanceof simple_html_dom_node)
                if($td->innertext == 'host'){
                    if($td->next_sibling()->innertext == "string"){
                        $hostOK = $td->next_sibling()->next_sibling()->innertext;
                        $hostOK = str_replace('&quot;', "", $hostOK);
                        //echo "<br/>\n HOST  = $hostOK ";
                        break;
                    }
                }
        }

        $tokenWebmks =  cstring::getStringBetween2StringType3($ret, "showHideSecretField(this, '&quot;" , "&quot;');");

        //echo "<br/>\n HOST  = $hostOK ";
        //echo "<br/>\n Token webmks = $tokenWebmks";

        return [$hostOK, $tokenWebmks];
    }

    //Bản này dùng curl chỉ làm viêc với Vc6.x
    public static function getWebTokenMks($domainVc, $vmId){

        $domain = $domainVc;

        $fileCC = ClassVMwareTool::$fileCookieMobLogin;

        ////////////////////////
        $ccCont = file_get_contents($fileCC);

        $ch = curl_init("https://$domain/mob/?moid=$vmId&method=acquireTicket");
        curl_setopt($ch,CURLOPT_COOKIEFILE,$fileCC);
        //curl_setopt($ch,CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch,CURLOPT_REFERER, "https://$domain/mob/?moid=$vmId&method=acquireTicket");

        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);

        $no = curl_errno($ch);
        if($no!=0){
            $bname = basename($fileCC);
            loi("Error access ($no), can not get ticket1? getWebTokenMks");
        }

        if(!$ret){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($ret);
            echo "</pre>";
            $bname = basename($fileCC);
            loi("Error access, can not get ticket2? getWebTokenMks $domain");
        }

        $xx = str_get_html($ret);

        $session_nonce = $xx->find("input[name=vmware-session-nonce]",0)->value;

        //echo "session_nonce = $session_nonce";

        curl_close($ch);


        $x = ["vmware-session-nonce"=>"$session_nonce",
            "ticketType"=>"webmks"];

//$x = ["ticketType"=>"webmks"];

        $ch = curl_init("https://$domain/mob/?moid=$vmId&method=acquireTicket");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($x));
        curl_setopt($ch,CURLOPT_COOKIEFILE,$fileCC);
        //curl_setopt($ch,CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $un1.':'.$acc);

        curl_setopt($ch,CURLOPT_REFERER, "https://$domain/mob/?moid=$vmId&method=acquireTicket");

        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);

        $no = curl_errno($ch);

        $xx = str_get_html($ret);

        $hostOK = " NOT FOUND HOST1 ";

        foreach ($xx->find("td.c2") AS $td){
            if($td instanceof simple_html_dom_node)
                if($td->innertext == 'host'){
                    if($td->next_sibling()->innertext == "string"){
                        $hostOK = $td->next_sibling()->next_sibling()->innertext;
                        $hostOK = str_replace('&quot;', "", $hostOK);
                        //echo "<br/>\n HOST  = $hostOK ";
                        break;
                    }
                }
        }

        $tokenWebmks =  cstring::getStringBetween2StringType3($ret, "showHideSecretField(this, '&quot;" , "&quot;');");
//    echo "<br/>\n HOST  = $hostOK ";
//    echo "<br/>\n Token webmks = $tokenWebmks";
        curl_close($ch);
        return [$hostOK, $tokenWebmks];

    }

    public static function getLibListInFolder_($folderId){
        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;
        $url = "https://$domain/rest/com/vmware/content/library/item/?library_id=$folderId";

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);
        $info = json_decode($output);
        if(isset($info) && isset($info->value))
            return $info->value;
        return null;
    }

    public static function getLibInfo_($id){
//        echo "<br/>\n ----- CALL-".__FUNCTION__;
        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;
        $url = "https://$domain/rest/com/vmware/content/library/item/id:$id";

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(isset($info) && isset($info->value))
            return $info->value;
        return null;
    }

    public static function loginVCCache($domain = null, $uid = null, $pw = null, $timeOut = 180){
        if(!$domain)
            $domain = DEF_VCENTER_IP;

        $fileC = "/var/glx/weblog/login_vc_cache_$domain";
        if(file_exists($fileC) && filectime($fileC) > time() -  $timeOut){
            $mm = unserialize(file_get_contents($fileC));
            ClassVMwareTool::$domain = $mm[0];
            ClassVMwareTool::$sid = $mm[1];
            return 1;
        }
        @unlink($fileC);
        if(!ClassVMwareTool::loginVC()){
            return null;
        }

        outputW($fileC, serialize([$domain, ClassVMwareTool::$sid]));

        return 1;
    }

    public static function loginVC($domain = null, $uid = null, $pw = null){

        if(!$domain && !$uid && !$pw){
            $file = "/var/ufile_cms2017LAD/vsphere-automation-sdk-java/test1";
            $uid = getUserVcFromFile($file)[0];
            $pw = getUserVcFromFile($file)[1];
            $domain = DEF_VCENTER_IP;
        }

        //CURL RUN OK
        //system("curl -kv -X POST -H 'Accept: application/json' --basic -u administrator@vsphere.local:xxxxxxx! https://photon-machine/rest/com/vmware/cis/session");
        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);

        //// Phải có option này ko thì sẽ lỗi:
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        //curl -kv -X POST -H 'Accept: application/json' --basic -u administrator@vsphere.local:xxxxx https://photon-machine/rest/com/vmware/cis/session
        //curl_setopt($ch, CURLOPT_USERPWD, 'administrator@vsphere.local:xxx');
        curl_setopt($ch, CURLOPT_USERPWD, $uid.':'.$pw);

        curl_setopt($ch,CURLOPT_USERAGENT, 'curl/7.29.0');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        //In ra , so sánh với curl cmd
        //echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //print_r($info['request_header']);
        //echo "</pre>";


        //curl_close($ch);

        $out = json_decode($ret);

        ////echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        ////print_r($out);
        ////echo "</pre>";

        if ($out === false) {
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($ret);
            echo "</pre>";
            loi("Curl Error: ' . curl_error($ch)");
        }


        if(!isset($out->value)){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($ret);
            echo "</pre>";
            echo ("\n Can not login VC $domain!");
            return null;
        }

        if(!is_string($out->value)){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($out);
            echo "</pre>";
            echo ("\n Can not login VC $domain!");
            return null;
        }


        $sid = $out->value;
        ClassVMwareTool::$domain = $domain;
        ClassVMwareTool::$sid = $sid;
        return 1;
    }

    public static function updateHostNameOnVpsDb()
    {
        if(!ClassVMwareTool::loginVCCache()){
            bl("Khong the dang nhap VC?");
            return;
        }

        $mhost = ClassVMwareTool::getHostList();

        if(!$mhost){
            echo ("Not mhost");
            return;
        }

        $vmList = [];
        $cc = 0;
        foreach ($mhost as $objh) {
            $cc++;
//        //$vmList1 = ClassVMwareTool::getVMListPowerOn('name', "?filter.hosts.1=$objh->host");
            $vmList1 = ClassVMwareTool::getVMList(null, 'name', "?filter.hosts.1=$objh->host");
            $vmList = array_merge($vmList, $vmList1);
        }

//    $vmList = ClassVMwareTool::getVMList(null, 'name');

        foreach ($vmList as $vm) {
            echo "<br/>\n $vm->vm | $vm->hostId";
            if ($vm instanceof ClassVMinfo) ;
            foreach ($mhost as $host) {
                if ($vm->hostId == $host->host) {
//            $vm->hostName = $host->name;
                    echo "<br/>\n $host->name";

                    $vps = new \Base\modelHostingVps();
                    if ($vps->getOneWhere(['vmId' => $vm->vm])) {
                        echo "<br/>\n Found VM: $vm->name";
                        echo "<br/>\n $vps->hostName !=  $host->name";
                        if ($vps->hostName != $host->name) {
                            $vps->addLog("update host id, hostname: $vps->hostName => $host->name ");
                            $vps->hostName = $host->name;
                            $vps->hostId = $host->host;
                            $vps->updateMe();
                        }
                    }
                }
            }
        }
    }

    public static function updateVMHardWareDb()
    {

        ClassVMwareTool::loginVC();
        $mm = ClassVMwareTool::getVMList();
        $cc = 0;
        foreach ($mm as $vm) {
            $cc++;

//    if(!isCli())
//    if($cc > 5)
//        break;

            if ($vm instanceof ClassVMinfo) ;
            $vmId = $vm->vm;

            if ($vm->vm !== 'vm-12038') {
                echo "<br/>\n continue...";
                //continue;
            }

            echo "<br/>\n -- $cc. $vm->vm , $vm->name";
//    return;

            $vin = ClassVMwareTool::getVMinfo($vm->vm);

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($vin);
//            echo "</pre>";

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($vin->value->disks);
//    echo "</pre>";

            $ts = 0;
            foreach ($vin->value->disks as $disk) {
                //echo "<br/>\n DUNG LUONG: $disk->value->capacity";
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($disk);
//        echo "</pre>";
                $size = $disk->value->capacity ?? 0;
                $ts += $size;
            }

            echo "<br/>\n TOTAL SIZE : $ts : " . ByteSize($ts);

            //getch("...");

            $obj = new \Base\modelHostingVps();

            if ($obj->getOneWhere(['vmId' => $vmId])) {

//                echo "<br/>\n FOUND  $vmId ";

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($obj);
//                echo "</pre>";

                $isUpdate = 0;
                if ($vm->cpu_count != $obj->nCore) {
                    $isUpdate++;
                    $obj->nCore = $vm->cpu_count;
                    echo "<br/>\n Update Ncore";
                    $obj->addLog("Update nCore: $vm->cpu_count, from $obj->nCore");
                }
                if ($vm->memory_size_MiB != $obj->memorySize) {
                    $isUpdate++;
                    $obj->memorySize = $vm->memory_size_MiB;
                    echo "<br/>\n Update Memory";
                    $obj->addLog("Update Memory: $vm->memory_size_MiB, from $obj->memorySize");
                }

                if ($obj->ssdSize != $ts) {
                    $isUpdate++;
                    $obj->ssdSize = $ts;
                    echo "<br/>\n ---> Need Update disk size!";
                    $obj->addLog("Update disk: $ts, from $obj->ssdSize");
                }
//        else
//            echo "<br/>\n -- The same size, Not Update!";

                if ($isUpdate) {
                    $obj->updateMe();
                }

            } else {
                echo "<br/>\n Error, not found vmid in DB: $vmId";
            }
        }
    }
    public static function getLocalLibraryList()
    {
        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/com/vmware/content/local-library");
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(isset($info) && isset($info->value))
            return $info->value;
        return null;
    }

    public static function getLocalLibraryInfo($id)
    {
        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/com/vmware/content/local-library");
        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest//com/vmware/content/local-library/id:$id");
        $output = curl_exec($ch);
        $info = json_decode($output);
        return $info;
    }

    public static function getTasks(){
        //echo "<br/>\n ----- CALL-".__FUNCTION__;

        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            loi("Not valid SID/Domain? ");
        }

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/deployment");
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$output || !$info){
            loi("Can not get Task info?");
        }

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

    }

    public static function getTicketWebMksConsole($vmId){

        $json = "{
    'spec': {
        'type': 'VMRC'
    }
}";

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;
        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));
        $url = "https://$domain/rest/vcenter/vm/$vmId/console/tickets";

        echo "<br/>\n URL = $url ";
        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);
        $info = json_decode($output);
        return $info;
    }

    /**
     * @return ClassVmwareHost[]
     */
    public static function getHostList(){

        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            loi("Not valid SID/Domain? ");
        }

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));


        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/host");
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$output || !$info){
            loi("Can not get VM info?");
        }

        $md = [];
        foreach ($info->value AS $m1){

            $obj = new ClassVmwareHost();
            $obj->loadFromObj($m1);
            $md[] = $obj;

        }

        return $md;

    }

    /**
     * @param null $orderBy
     * @param null $filterString
     * @return ClassVMinfo[]
     */
    public static function getVMListPowerOn($orderBy = null, $filterString = null){
        return self::getVMList('POWERED_ON', $orderBy, $filterString);
    }


    /**
     * @return ClassVMinfo[]
     */

    public static function getVMList3($power_state = null, $orderBy = null, $filterString = null){
        //echo "<br/>\n ----- CALL-".__FUNCTION__;
        $mmh = ClassVMwareTool::getHostList();
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mmh);
//        echo "</pre>";
        $mret = [];
        foreach ($mmh as $item) {
            if($item instanceof ClassVmwareHost);
            //echo "<br/>\n $item->host / $item->name";

            $m1 = ClassVMwareTool::getVMList(null, null, '?filter.hosts.1='.$item->host."&filter.power_states.1=POWERED_ON");
            //echo "<br/>\n tt vm = ".count($m1);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($m1);
//            echo "</pre>";
            $mret = array_merge($mret, $m1);
        }
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mret);
//        echo "</pre>";

        return $mret;
    }

    public static function updateNCoreCpu($vmId, $nCore){

        $json = "{
    \"spec\" : {
        \"hot_remove_enabled\" : true,
        \"count\" : $nCore,
        \"hot_add_enabled\" : true,
        \"cores_per_socket\" : 1
    }
}";

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vmId/hardware/cpu");

        $output = curl_exec($ch);
        $info = json_decode($output);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($output);
//        echo "</pre>";
//
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";


    }

    public static function updateMemory($vmId, $mB){

        $json = '{"spec":{"hot_add_enabled":false,"size_MiB":'.$mB.'}}';

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vmId/hardware/memory");

        $output = curl_exec($ch);
        $info = json_decode($output);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($output);
//        echo "</pre>";
//
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";


    }

    /**
     * @param int $nSeconds
     * @param null $power_state
     * @param null $orderBy
     * @param null $filterString
     * @return ClassVMinfo[]|mixed
     */
    public static function getVMListCache($nSeconds = 60, $power_state = null, $orderBy = null, $filterString = null){
        if($ret = ClassCacheFile::getCacheContent($nSeconds.'/vm_list_ok/', 'vmlist')){
            return json_decode($ret);
        }
        ClassVMwareTool::loginVC();
        $mm = ClassVMwareTool::getVMList($power_state, $orderBy, $filterString);
        ClassCacheFile::setCacheContent($nSeconds.'/vm_list_ok/', 'vmlist',json_encode($mm));
        return $mm;
    }

    /**
     * @return ClassVMinfo[]
     */
    public static function getVMList($power_state = null, $orderBy = null, $filterString = null){
        //echo "<br/>\n ----- CALL-".__FUNCTION__;

        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            return null;
//            loi("Not valid SID/Domain? ");
        }
        $md = [];
        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));


        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm".$filterString);
        $output = curl_exec($ch);
        $info = json_decode($output);


        if(isAdmin_()){
//            echo "<br/>\n FS = $filterString";
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($info);
//            echo "</pre>";
        }

        if(!$output || !$info){
            loi("Can not get VM info?");
        }



        if(isset($info->value) && is_array($info->value))
        foreach ($info->value AS $m1){


            $obj = new ClassVMinfo();
            $obj->loadFromObj($m1);
            $obj->macList = [];

            if($power_state && isset($obj->power_state))
            if($power_state != $obj->power_state){
                continue;
            }

            if($filterString){
                if($host1 = ctoolUrl::getParamInUrl( 'filter.hosts', $filterString,1)){
                    $obj->hostId = $host1;
                }
            }

            $idvm = str_replace("vm-",'', $obj->vm);

            if($orderBy == 'name')
                $md[$obj->name .'_' . $idvm] = $obj;
            else
                $md[$idvm] = $obj;
        }
        ksort($md);
        $md1 = [];
        foreach ($md AS $k => $o1){
            $md1[$k] = $o1;
        }

        return $md1;
    }


    public static function getDataStoreInfo($nameHaveString1 = '',$nameHaveString2 = ''){

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));


        $url = "https://$domain/rest/vcenter/datastore";
        //ol1("<br/>\n URL = $url");
        curl_setopt($ch, CURLOPT_URL, $url);

        $output = curl_exec($ch);
        $info = json_decode($output);

        $md = [];
        if($info && $info->value)
        foreach ($info->value AS $m1){
            $obj = new ClassVmDataStoreInfo();
            $obj->loadFromObj($m1);
//            if(classYtb::isRoot() || !isCli()) {
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($obj);
//                echo "</pre>";
//            }

            if($nameHaveString1)
                if(strstr($obj->name , $nameHaveString1) === false){
                    continue;
                }
            if($nameHaveString2)
                if(strstr($obj->name , $nameHaveString2) === false){
                    continue;
                }
            $md[] = $obj;
        }
        return $md;
    }


    public static function getResourcePool($host = ''){

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        $url = "https://$domain/rest/vcenter/resource-pool";
        if($host)
            $url .= "?filter.hosts.1=$host";

        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);
        $info = json_decode($output);

        return $info;
    }

    public static function getVMinfoObj($vid){
        $val = ClassVMwareTool::getVMinfo($vid);
        if($val && isset($val->value)){
            $obj = new ClassVMinfo();
            $obj->loadFromObj($val->value);
        }
    }

    public static function getArrayTemplateIdInLib($listId){
        echo "<br/>\n ----- CALL-".__FUNCTION__;

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;
        //global $domain,$ch;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        $url = "https://$domain/rest/com/vmware/content/library/item/?library_id=$listId";
            //ol1("<br/>\n URL = $url");
        curl_setopt($ch, CURLOPT_URL, $url);

        $output = curl_exec($ch);
        $info = json_decode($output);

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";

        foreach ($info->value AS $name){
            //$nameTemp = getTemplateInfo($name);
            //ClassVMwareTool::$tmpListTempId[$name] = $nameTemp;
        }

        return $info->value;

    }

    /* Đổi network của vm, network lấy từ list network của vcenter
    Trả lại http 200 là ok
     * https://vdc-repo.vmware.com/vmwb-repository/dcr-public/1cd28284-3b72-4885-9e31-d1c6d9e26686/71ef7304-a6c9-43b3-a3cd-868b2c236c81/doc/operations/com/vmware/vcenter/vm/hardware/ethernet.update-operation.html
     * $nic: key number of NIC
     * $type = STANDARD_PORTGROUP , DISTRIBUTED_PORTGROUP
     */
    public static function setChangeNetworkNics($vm, $nic, $newNet, $type = 'DISTRIBUTED_PORTGROUP'){

        //PATCH https://{server}/rest/vcenter/vm/{vm}/hardware/ethernet/{nic}

        $link = "/rest/vcenter/vm/$vm/hardware/ethernet/$nic";

        /*
         * {
    "spec": {
        "allow_guest_control": false,
        "backing": {
            "distributed_port": "",
            "network": "",
            "type": "STANDARD_PORTGROUP"
        },
        "mac_address": "",
        "mac_type": "MANUAL",
        "start_connected": false,
        "upt_compatibility_enabled": false,
        "wake_on_lan_enabled": false
    }
}
         */

        $json = '{
    "spec": {
        "backing": {
            "network": "'.$newNet.'",
            "type": "DISTRIBUTED_PORTGROUP"
        }
    }
}';

        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            loi("Not valid SID/Domain? ");
        }

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        //curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));


        curl_setopt($ch, CURLOPT_URL, "https://$domain$link");

        $output = curl_exec($ch);
        $info = json_decode($output);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($httpcode == 200)
            return true;

        return false;

    }



    public static function getNetworkListVcenter(){
        $link = "/rest/vcenter/network";
        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            loi("Not valid SID/Domain? ");
        }

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        curl_setopt($ch, CURLOPT_URL, "https://$domain$link");
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$output || !$info){
            loi("Can not get VM info?");
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";

        if(!isset($info->value)){
            return null;
        }

        return $info;

    }

    //$info return from getVMinfo
    public static function getMacAddress($info){
        $mm = [];
        if(isset($info->value) && isset($info->value->nics)){
            foreach ($info->value->nics AS $nic){
                if(isset($nic->value) && isset($nic->value->mac_address)){
//                    echo "\n MAC: " . $nic->value->mac_address;
                    $mm[] = strtolower($nic->value->mac_address);
                }
            }
        }
        return $mm;
    }

    public static function getVMinfo($vid){
        //echo "<br/>\n ----- CALL-".__FUNCTION__;

        if(!ClassVMwareTool::$sid || !ClassVMwareTool::$domain){
            loi("Not valid SID/Domain? ");
        }

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));


        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vid");
        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$output || !$info){
            loi("Can not get VM info?");
        }

        if(!isset($info->value) || !isset($info->value->memory)){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($info);
            echo "</pre>";
            return null;
        }

        return $info;

    }

    public static function deleteVm($vid){

        //echo "<br/>\n ----- CALL-".__FUNCTION__;

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("vmware-api-session-id:$sid"));

        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vid");
        $output = curl_exec($ch);
        $info = json_decode($output);

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";
    }
    public static function resetVm($vmId){

        echo "<br/>\n Call OfVm $vmId ";

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/power/reset";

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";
    }

    public static function vmListAllNic($vmId){

        $info = ClassVMwareTool::getVMinfo($vmId);

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info->value->nics);
        echo "</pre>";;



        return 1;
        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;
        $vmInfo = ClassVMwareTool::getVMinfo($vmId);


        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/ethernet";

//        curl_setopt($ch, CURLOPT_POST, 0);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$info)
            return 0;

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";
        $ret = [];
        if(isset($info->value) && is_array($info->value)){
            foreach ($info->value AS $nic){
                $ret[] = $nic->nic;
            }
        }

        return $ret;

//        return $info;
    }

    public static function vmSetEnableAllNic($vmId, $enable){

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;
        $vmInfo = ClassVMwareTool::getVMinfo($vmId);


        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/ethernet";

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);




        if(!$info)
            return 1;


    }

    public static function vmSetBootFromDisk($vmId){

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;
        $vmInfo = ClassVMwareTool::getVMinfo($vmId);

        $diskId = @$vmInfo->value->disks[0]->key;

        if(!$diskId){
            loi("Can not found DiskId");
        }

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $json = '{
    "devices": [
        {
            "disks": ['.$diskId.'],
            "type": "DISK"
        }
    ]
}';

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/boot/device";

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$info)
            return 1;

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

    }

    public static function vmSetBootFromCD($vmId){

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $json = '{
    "devices": [
        {
            "type": "CDROM"
        }
    ]
}';

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/boot/device";

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);

        if(!$info)
            return 1;

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

    }

    public static function powerOfVm($vmId){

        echo "<br/>\n Call OfVm $vmId";

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/power/stop";

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";
    }

    public static function getNicInfo($vmId, $nickId){
        echo "<br/>\n Call disconnectNic $vmId";

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/ethernet";

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
//        curl_setopt($ch, CURLOPT_GE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($output);
        echo "</pre>";
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

    }
    public static function listNic($vmId){

        echo "<br/>\n Call listNic $vmId";

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/ethernet";

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
//        curl_setopt($ch, CURLOPT_GE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);
;
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info->value);
//        echo "</pre>";

        $mId = [];
        foreach ($info->value as $item) {
            $mId [] = $item->nic;
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mId);
//        echo "</pre>";

        return $mId;
    }

    public static function connectNic($vmId, $nicId, $cmd = 'connect'){
        self::disconnectNic($vmId, $nicId, $cmd);
    }

    public static function disconnectNic($vmId, $nicId, $cmd = 'disconnect'){

        echo "<br/>\n Call disconnectNic $vmId";

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/hardware/ethernet/$nicId?action=$cmd";
        //https://{api_host}/api/vcenter/vm/{vm}/hardware/ethernet/{nic}?action=disconnect
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        $output = curl_exec($ch);
        $info = json_decode($output);

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($output);
        echo "</pre>";
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";

    }



    public static function renameVm($vmId, $newName){

        //https://{api_host}/api/vcenter/vm/{vm}/guest/customization

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $json = '{
    "name": "'.$newName.'",
    "spec": {}
}';

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);


        $url = "https://$domain/rest/vcenter/vm/$vmId/guest/customization";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        //curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "");

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");


        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
        $output = curl_exec($ch);
        $info = json_decode($output);

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($output);
        echo "</pre>";
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($info);
        echo "</pre>";
    }

    public static function powerOnVm($vmId){

        $domain = ClassVMwareTool::$domain;
        $sid = ClassVMwareTool::$sid;

        if(!$domain)
            loi("Not domain?");
        if(!$sid)
            loi("Not sid?");

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);

        $url = "https://$domain/rest/vcenter/vm/$vmId/power/start";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
        $output = curl_exec($ch);
        $info = json_decode($output);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($info);
//        echo "</pre>";
    }

    public static function getImgBinary($ipVc, $vmid, $user, $pw){
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode("$user:$pw"),
            ),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ));
        $url = "https://$ipVc/screen?id=$vmid";
        return file_get_contents($url, false, $context);
    }

    /* 27.08.2020
     * For VCenter v7, esxi 6.7 test ok
     */
    public static function vMotionVM($vid, $host, $ds){

        if(!isCli()){
            loi("vMotionVM: NOT CLI!");
        }

        $json = "{
        \"spec\" : {
            \"placement\" : {
                \"datastore\" : \"datastore-59\",
                \"host\" : \"host-58\"
            }
        }
    }";


        $json = "{
        \"spec\" : {
            \"placement\" : {
                \"datastore\" : \"$ds\",
                \"host\" : \"$host\"
            }
        }
    }";

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        if(!isCli())
            curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vid?action=relocate&vmw-task=true");
        else
            curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm/$vid?action=relocate");

        $output = curl_exec($ch);
        $info = json_decode($output);

        echo "\n<br> RET = ";
        $ret = print_r($info);

    }

    /* 02.11.2020
     * For VCenter v7, esxi 6.7 test ok
     *
     * Chưa test
     */
    public static function cloneVm($newName, $vid, $host, $ds){

        $json = "{
        \"spec\" : {
            \"placement\" : {
                \"datastore\" : \"datastore-59\",
                \"host\" : \"host-58\"
            }
        }
    }";

        $json = "{
        \"spec\" : {
            \"name\" : \"$newName\",
            \"source\" : \"$vid\",
            \"placement\" : {
                \"datastore\" : \"$ds\",
                \"host\" : \"$host\"
            }
        }
    }";

//        echo "<br/>\n $json";
//        $json = null;

        $sid = ClassVMwareTool::$sid;
        $domain = ClassVMwareTool::$domain;;

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

//        if(!isCli())
//            curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm?action=clone&vmw-task=true");
//        else

        curl_setopt($ch, CURLOPT_URL, "https://$domain/rest/vcenter/vm?action=clone");

        $output = curl_exec($ch);
        $info = json_decode($output);

        echo "\n<br> RET = ";
        $ret = print_r($info);

    }
}

class ClassVMinfo extends \Base\ClassBaseGlx {

    var $memory_size_MiB;
    var $vm;
    var $name;
    var $power_state;
    var $cpu_count;
    var $ip_v4 = [];
    var $macList = [];
    var $hostId = '';
    var $hostName;
    var $ip_in_db = 'xxxxxxx';
    var $lastScanVmAndMac;
    var $lastScanIpAndMac;

    function isOn(){
        if($this->power_state == 'POWERED_ON'){
            return 1;
        }
        //elseif($this->power_state == 'POWERED_OFF'){
            return 0;
       // }
      //  loi("Not valid power_state!");
    }

    function isOff(){
        if($this->power_state == 'POWERED_OFF'){
            return 1;
        }
        return 0;
    }

    function isSuspended(){
        if($this->power_state == 'SUSPENDED'){
            return 1;
        }
        return 0;
    }

//

}

class ClassVmDataStoreInfo extends \Base\ClassBaseGlx {
    public $datastore;
    public $name;
    public $type;
    public $free_space;
    public $capacity;
}

function startIfVmOf($vmId){
    global $domain,$ch, $sid;
    $url = "https://$domain/rest/vcenter/vm/$vmId/power/start";
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "vmware-api-session-id:$sid"));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    $output = curl_exec($ch);
    $info = json_decode($output);
    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    print_r($info);
    echo "</pre>";
}

class ClassVeeamBak {

    static $veemServerIP = '10.0.1.3';

    static function getBackupJobIdByName($name = 'BackupRunning'){
        $auth = base64_encode("pc1:qqqppp@123");
//$auth = base64_encode("DESKTOP-E079NG8\pc1:qqqppp@123");

        $headers = "Authorization: Basic $auth\r\nContent-Type: application/x-www-form-urlencoded\r\n" .
            "Accept: application/xml\r\nContent-Length: 0";

        $ipVeeam = ClassVeeamBak::$veemServerIP;

        $context = stream_context_create([
            "http" => [
                "header" => $headers,
                'method'  => 'POST',
            ]
        ]);

       $link = "http://$ipVeeam:9399/api/sessionMngr/?v=v1_4";

        $response = file_get_contents($link, false, $context);

// X-RestSvcSessionId:

        $cookies = array();
        $cookieStr = '';
        foreach ($http_response_header as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $cookies += $tmp;
                foreach ($tmp AS $n1=>$s1){
                    $cookieStr .= "$n1=$s1;";
                }
            }
        }
        $cookieStr .= " Path=/api;";
        $headers = "Cookie: $cookieStr";
        $context = stream_context_create([
            "http" => [
                "header" => $headers,
                'method'  => 'GET',
            ]
        ]);

        $link = "http://$ipVeeam:9399/api/backupServers";
        $link = "http://$ipVeeam:9399/api/catalog/vms";
        $link = "http://$ipVeeam:9399/api/jobs";


        $response = file_get_contents($link, false, $context);

    //    echo "<br/>\nRETURN: ";
        $xml = simplexml_load_string($response);
   //     $mVm = [];

        foreach ($xml AS $obj){

//            echo "<br/>\n -------------------";
            $att = $obj->attributes();

            $nameJob = $att->Name->__toString();


            if($nameJob == $name)
                return $jobId = basename($att->Href->__toString());
//
//
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($jobId);
//            echo "</pre>";

        }

        return null;

    }

    static function listBackupVm($idJob, $cache = 0){

        $cacheName = 'cache_veem_vm_list_and_time2';

        if($cache){
            $cont = ClassCacheFile::getCacheContent(3600, $cacheName);
            if($cont){
                //echo "<br/>\n CONT: $cont ";
                return unserialize($cont);
            }
        }
        else{
            //delete Cache
            ClassCacheFile::setCacheContent(3600, $cacheName, '');
        }

        $auth = base64_encode("pc1:qqqppp@123");
//$auth = base64_encode("DESKTOP-E079NG8\pc1:qqqppp@123");

        $headers = "Authorization: Basic $auth\r\nContent-Type: application/x-www-form-urlencoded\r\n" .
            "Accept: application/xml\r\nContent-Length: 0";

        $context = stream_context_create([
            "http" => [
                'timeout' => 5,
                "header" => $headers,
                'method'  => 'POST',
            ]
        ]);

        //echo "<br/>\n xxx";

        $ipVeeam = ClassVeeamBak::$veemServerIP;

        $link = "http://$ipVeeam:9399/api/sessionMngr/?v=v1_4";

        $response = @file_get_contents($link, false, $context);
        if($response){
            //return null;
        }


//        echo "<br/>\n RES11 = ";
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($response);
//        echo "</pre>";


//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r(var_dump($http_response_header));
//        echo "</pre>";

// X-RestSvcSessionId:

        $cookies = array();
        $cookieStr = '';
        foreach ($http_response_header as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $cookies += $tmp;
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($tmp);
//        echo "</pre>";
//
                foreach ($tmp AS $n1=>$s1){
                    $cookieStr .= "$n1=$s1;";
                }
            }
        }
        $cookieStr .= " Path=/api;";

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($cookies);
//        echo "</pre>";

        $headers = "Cookie: $cookieStr";
        //echo "<br/>\n Header: $headers";
        $context = stream_context_create([
            "http" => [
                'timeout' => 5,
                "header" => $headers,
                'method'  => 'GET',
            ]
        ]);

        $link = "http://$ipVeeam:9399/api/backupServers";
        $link = "http://$ipVeeam:9399/api/catalog/vms";
        $link = "http://$ipVeeam:9399/api/jobs/cfac2910-8a1b-4091-9976-12f3e5967174/includes";
        $link = "http://$ipVeeam:9399/api/jobs/178493cb-3022-45da-8b06-d940e521fb3e/includes";
        $link = "http://$ipVeeam:9399/api/jobs/$idJob/includes";

        //

        $response1 = @file_get_contents($link, false, $context);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($response1);
//        echo "</pre>";

        $xml = simplexml_load_string($response1);

        $mVm = [];

        if(isAdminEmail()){
//           echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//           print_r($xml);
//           echo "</pre>";
        }

        $mVpsInJobList = [];
        if($xml)
        foreach ($xml AS $obj){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($obj);
//            echo "</pre>";
//            echo "<br/>\n N = $obj->Name";
            $mVpsInJobList[] = $obj->Name;
        }


        $link = "http://$ipVeeam:9399/api/vmRestorePoints";

        $response = file_get_contents($link, false, $context);
//        echo "<br/>\nRETURN: ";


        $xml = simplexml_load_string($response);

        $mVm = [];

        if(isAdminEmail()){
//           echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//           print_r($xml);
//           echo "</pre>";
        }

        foreach ($xml AS $obj){

           // echo "<br/>\n -------------------";

            //if(isAdminEmail())
            {


//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($obj->Links->Link[2]->attributes('Name'));
//                echo "</pre>";
//               echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//               print_r($obj->Links->Link[2]);
//               echo "</pre>";


                $fileName = $obj->Links->Link[2]->attributes()['Name']->__toString();

                //echo "<br/>\n FILENAME =  '$fileName'";
            }

            $att = $obj->attributes();



            $nameAndTime = $att['Name'];

            list($name, $time) = explode("@", $nameAndTime);

            $haveVpsInList = 0;
            foreach ($mVpsInJobList AS $idV => $nameV){
                if($nameV == $name)
                {
                    $haveVpsInList = 1;
                    break;
                }
            }

            if(!$haveVpsInList)
                continue;
            //echo "<br/>\n $name /$time ";

            if(!isset($mVm[$name])){
                $mVm[$name] = [$time."#".$fileName];
            }
            else
                $mVm[$name][] = $time."#".$fileName;

        }

        foreach ($mVm AS &$obj){
            //$obj[0] = "---------";
            arsort($obj);
            $obj = array_values($obj);
        }



//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mVm);
//        echo "</pre>";


        ClassCacheFile::setCacheContent(3600, $cacheName, serialize($mVm));

        return $mVm;
    }

}

