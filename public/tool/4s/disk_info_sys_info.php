<?php
set_time_limit(600);
define("DEF_TOOL_CMS", 1);
$_SERVER['SERVER_NAME'] = '4share.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";;

if(!isSupperAdmin_() && !isDebugIp()){
   die("NOT ADMIN OR IP");
}

//$cDisk = new CDisk();
//$mountUtilWaitArray = $cret = $cDisk->getDiskInfoArrayRemote('4ss.myftp.org', $getUtil = 1, $getInCache = 0);
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mountUtilWaitArray);
//echo "</pre>";
//
//return;

sumaryDiskInfo();
