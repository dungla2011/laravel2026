<?php

//require "/var/www/html/public/index.php";

function ol1($str)
{
    file_put_contents("/var/glx/weblog/face-ai-log.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);
}

//Dump ra tất ca nhưng gì nhận được, $_REQUEST, phpInput...
$input = file_get_contents("php://input");
ol1("Input len:" . strlen($input)) ;

ol1("REQUEST: " . serialize($_REQUEST));

//Còn gì ngoaài INPUT và REQUEST không
$input = file_get_contents("php://input");
ol1("Input len:" . strlen($input)) ;
ol1("REQUEST: " . serialize($_REQUEST));
ol1("POST: " . serialize($_POST));
ol1("GET: " . serialize($_GET));
ol1("SERVER: " . serialize($_SERVER));
ol1("FILES: " . serialize($_FILES));
ol1("PHP_INPUT: " . serialize($input));
//2025-04-25 12:05:56#FILES: a:1:{s:5:"image";a:6:{s:4:"name";s:9:"image.jpg";s:9:"full_path";s:9:"image.jpg";s:4:"type";s:24:"application/octet-stream";s:8:"tmp_name";s:14:"/tmp/php8ucpyv";s:5:"error";i:0;s:4:"size";i:39379;}}
//Save file to /share/img.time(0) . ".jpg"
$img = $_FILES['image']['tmp_name'];

move_uploaded_file($img, "/share/img." . date("Y-m-d H-i") . ".jpg" );

ol1(" ... ");
ob_clean();
die(json_encode(['status' => 'OK', 'msg' => 'OK123 ' . date("H:i:s"), 'data' => []]));
