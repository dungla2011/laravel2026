<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DEF_TOOL_CMS", 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";

$ip = gethostbyname('4ss.myftp.org');
$ipFile = '/var/glx/weblog/myip-pc001-ok.txt';
$ct = file_get_contents($ipFile);
if(strstr($ct, $ip)!==false){
    echo "\n OKIP610 $ct";
}
else{
    echo "\n NotOKIP";
    file_put_contents($ipFile, $ip."#".nowyh());
}
