<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";


$url = "https://api.4share.vn/api/v1/?cmd=search_file_name&exactly=1&search_string=mkv";

$cont = file_get_contents($url);

$ret = json_decode($cont );

if($ret)
    echo "\nOK_SEARCH";
else
    echo "\nERROR_SEARCH";
