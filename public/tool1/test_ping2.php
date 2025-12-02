<?php


function nowyh($time = '')
{
    if (empty($time)) {
        return $datetime = date('Y-m-d H:i:s');
    } else {
        return $datetime = date('Y-m-d H:i:s', $time);
    }
}

function output($filename, $string, $createFolder = 0)
{
    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }

    $file = @fopen($filename, 'a');
    if (! $file) {
        return;
    }
    @fwrite($file, $string."\r\n");
    @fclose($file);
}

function outputW($filename, $string, $createFolder = 0)
{
    if (! $filename) {
        return;
    }
    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }
    $file = fopen($filename, 'w');
    if (! $file) {
        return;
    }
    fwrite($file, $string);
    fclose($file);
}

function outputT($filename, $strlog, $createFolder = 0)
{

    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }

    $datetime = date('Y-m-d H:i:s');
    output($filename, $datetime.'#'.$strlog);
}


class ThongKe{
    public static $nDie = 0;
    public static $nOK = 0;
}

function ol1($file, $str){
    echo "\n $str";
    outputT($file, $str);
}

function pingAddress($ip)
{
    $pingresult = exec("/bin/ping -c 10 $ip", $outcome, $status);

    echo "<pre>";
    print_r($outcome);
    echo "</pre>";

    $r1 = implode("\n", $outcome);

    if (0 == $status) {
        $status = "alive";
        ThongKe::$nOK++;
    } else {
        ThongKe::$nDie++;
        $status = "dead";
    }
    echo "\n The IP address, $ip, is  " . $status;

    return $status ."\n - Ping Log: " . $r1;
}

$ip = trim($argv[1]);

$file = "/var/glx/weblog/test_ping_$ip";

while (1){
    echo "\n --- ping '$ip' ".nowyh();
    $ret = pingAddress($ip);
    ol1($file, " $ret , nOK = ".ThongKe::$nOK." / nDie = ".ThongKe::$nDie );
    sleep(10);
}
