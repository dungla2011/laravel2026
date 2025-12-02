<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//$_SERVER['SERVER_NAME'] = '';
require_once '/var/www/html/app/common.php';

function GetPing($ip = null)
{

    $exec = exec('ping -c 1 -s 64 -t 64 '.$ip);
    $ret = explode('=', $exec);
    $array = explode('/', end($ret));

    return ceil($array[1]).'ms';

}

//exec("pkill -f checkPing.php");

$i = 0;
$file = '/var/glx/weblog/check_ping_overtime.log';
outputT($file, 'Start check ...');
while (1) {
    $i++;
    if ($i % 3600 * 2 == 0) {
        outputT($file, "... pinging ($i) ...");
    }
    $time = date('Y-m-d H:i:s');
    $ret = GetPing('8.8.8.8');
    $ret1 = str_replace('ms', '', $ret);
    if ($ret1 > 50) {
        outputT($file, ' time= '.$ret);
    }
    echo "\n $i. RET = $ret";
    sleep(1);
}
