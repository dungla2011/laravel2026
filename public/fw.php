<?php
$remoteIP = $_SERVER['REMOTE_ADDR'] ?? '';

$mmx = ['0.0.0.1'];

if($remoteIP)
if(
    in_array($remoteIP, $mmx) ||
//    str_starts_with($remoteIP, '47.79') ||
//    str_starts_with($remoteIP, '47.82') ||
//    str_starts_with($remoteIP, '104.28.') ||
    str_starts_with($remoteIP, '103.145.4.122')

) {
    die("Not allow access2: $remoteIP");
}
