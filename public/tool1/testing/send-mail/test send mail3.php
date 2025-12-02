<?php

use App\Components\ClassMailV2;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';
require_once "/var/www/html/public/index.php";;

sendMailNcbd("nguyenhungson2005@yahoo.com", "Mời họp lần 2" , " Kính mời ông bà đến dự buổi họp tổng kết dự án NCBD tại phòng họp tầng 7. Vui lòng trả lời mail này!");

return;

$obj = new ClassMailV2();

$mailAndPwRand = ClassMailV2::getRandGmailMail();
//$obj->Username = $mailAndPwRand[0];
$obj->Username = "mail9@glx.com.vn";
$obj->Password = dfh1b($mailAndPwRand[1]);

$obj->Username = "events@dav.edu.vn";
$obj->Password = "Vienbiendong@t7";

echo "<br/>\n ". eth1b($obj->Username);
echo "<br/>\n ". eth1b($obj->Password);

return;

$obj->Host = "smtp.office365.com";
$obj->Port = "587";
$obj->SMTPSecure = 'tls';

echo "<br/>\n $obj->Username ";

//$obj->From = "dungla2011@gmail.com";
$obj->From = $obj->Username;
$obj->FromName = 'Lê Dũng 2';
//$obj->attachFile = ['/var/glx/upload_file_glx/user_files/siteid_36/000/002/2899/2899' => 'f1.txt'];
$obj->toAddress = 'dungbkhn02@gmail.com';
$obj->toAddress = 'buitrongmanh@gmail.com';
$obj->toAddress = 'dungla2011@gmail.com';
$obj->toAddress = 'dungbkhn@yahoo.com';
$obj->toAddress = 'tranthithuyduong@dav.edu.vn';
$obj->toAddress = 'nguyenhungson2005@yahoo.com';

$obj->Body = 'Mời Ông Bà có mặt tại phòng họp NCBD Tầng 7. Vui lòng trả lời mail này!!';
$obj->Subject = 'Thư mời họp - Test send mail';
if (! $obj->sendMailGlx()) {
    echo $obj->ErrorInfo;
}
