<?php

use App\Components\ClassMailV2;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';
require_once "/var/www/html/public/index.php";;

sendMailNcbd("dungla2011@gmail.com", "Mời họp lần 2" ,
    " Kính mời ông bà đến dự buổi họp tổng kết dự án NCBD tại phòng họp tầng 3!", null, 1);
 
