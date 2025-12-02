<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";
check_unique_script();


$ret = shell_exec("ps -ef |grep rplc_multi");

if(!strstr($ret, 'rplc_multi_2024.lib')){
    die("Error: not_found_rplc_multi.lib script running 1");
}
else{
    echo "\n OK found rplc_multi.lib 1";
}
