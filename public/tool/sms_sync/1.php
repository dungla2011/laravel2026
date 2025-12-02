<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";



if($sms = request('sms_list'))
    outputW("/var/glx/weblog/x1.tgz", $sms);

echo "\n DONE!!!";
