<?php
set_time_limit(600);
define("DEF_TOOL_CMS", 1);
$_SERVER['SERVER_NAME'] = '4share.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";;

if(!isCli())
    die("NOT CLI");


check_unique_script();

$from = "dungla2011@gmail.com";
$to = "dungla2011@gmail.com";


while (1){
    $content =  \App\Components\U4sHelper::getDiskInfoRemote();
    $content .= \App\Components\U4sHelper::getUploaderSizeBig();
    \App\Components\ClassMail1::sendMail($from, '4S ADM', $to, "[4S LOG] ", $content);

    sleep(3600);
}
