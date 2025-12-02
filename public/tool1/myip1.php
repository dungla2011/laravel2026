<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//$rip = $ip = $_SERVER['REMOTE_ADDR'];
//echo $ip;
//if($ip == '103.163.216.29')

$rip = $_REQUEST['set_ip_remote'];

echo "<br/>\n";
$file = '/var/glx/weblog/myip_ok.txt';
if (! file_exists($file) || time() - filemtime($file) > 300) {
    file_put_contents('/var/glx/weblog/myip_ok.txt', $rip.'#');
}
