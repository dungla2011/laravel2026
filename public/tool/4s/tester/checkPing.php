<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";
check_unique_script();

function GetPing($ip=NULL) {

    $exec = exec("ping -c 1 -s 64 -t 64 ".$ip);
    $ret = explode("=", $exec );
    $array = explode("/", end($ret) );
    return ceil($array[1]) . 'ms';

}

//exec("pkill -f checkPing.php");

$i = 0;
$file = "/var/glx/weblog/check_ping_overtime.10.0.5.83.log";
outputT($file , "Start check ...");
while(1) {
    $i++;
    if($i % 3600 * 2 == 0)
        outputT($file , "... pinging ($i) ...");
    $time = date("Y-m-d H:i:s");

    $ret = GetPing('10.0.5.83');
    $ret1 = str_replace("ms", '', $ret);
    if($ret1 > 50){
        outputT($file, " time= ".$ret);
    }
    echo "\n $i. RET = $ret";
    sleep(1);
}


?>
