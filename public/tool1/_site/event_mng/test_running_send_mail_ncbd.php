<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/public/index.php';


exec("ps  -ef |grep php", $outputs );

foreach ($outputs AS $output)
    if(strstr($output, "event_mng/send_mail")){
        die("RunningOK");
    }
