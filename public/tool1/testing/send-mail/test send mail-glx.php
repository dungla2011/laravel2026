<?php

use App\Components\ClassMailV2;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';
require_once "/var/www/html/public/index.php";;


$obj = new ClassMailV2();

$mailAndPwRand = ClassMailV2::getRandGmailMail();
//$obj->Username = $mailAndPwRand[0];
$obj->Username = "mail9@glx.com.vn";
$obj->Password = dfh1b($mailAndPwRand[1]);

$obj->Username = "events@dav.edu.vn";
$obj->Password = "Vienbiendong@t7";

echo "<br/>\n ". eth1b($obj->Username);
echo "<br/>\n ". eth1b($obj->Password);
