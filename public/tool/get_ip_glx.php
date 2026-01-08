<?php
$mMac = [
    'win2019'=>'00-50-56-A0-14-61zzz',

];

$mMacTemplate = array_values($mMac);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mMacTemplate);
//echo "</pre>";
//
//die();

$mac = $_REQUEST['mac'] ?? '';

if(in_array($mac, $mMacTemplate)){
    die(".....");
}
echo "103.163.216.131,255.255.254.0,103.163.216.1,8.8.8.8,8.8.4.4,vps-prod-10";

