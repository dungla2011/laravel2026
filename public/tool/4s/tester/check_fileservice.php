<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);


$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";
check_unique_script();



$domain = "sv205.4share.vn";

function checkFileService($domain)
{
    $ret = '';
    if ($pf = @fsockopen($domain, 16868, $err, $err_string, 1)) {
        $ret .=  " OK $domain | ";
        fclose($pf);
    } else {
        $ret .= " NOTOK $domain | ";
    }
    return $ret;
}

$mm = [];

$svs = \App\Models\CloudServer::where('replicate_now',  1)->get();
foreach ($svs as $obj){
//    echo "<br/>\n obj->domain = $obj->domain";
    $mm[] = $obj->domain;
}

//$mm = ['sv99.4share.vn',
//    'sv96.4share.vn',
//    'sv205.4share.vn',
//    'sv209.4share.vn',
//    'sv101.4share.vn',
//    ];


$retA = '';
foreach ($mm AS $domain){
    $retA .= checkFileService($domain);
}

echo $retA;
