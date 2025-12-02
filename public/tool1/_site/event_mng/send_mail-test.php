<?php

use App\Components\ClassMailV2;
use function App\Http\ControllerApi\ol1;


$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

if(str_contains(gethostname(), 'mytree'))
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/public/index.php';




//$fromMail = "events@dav.edu.vn";;
//$pw = 'Hvng2025@';

//$fromMail = "4share@thietkeweb.pro";
//$pw = "Nam2025@123";


echo "<br/>\n PW = ". substr($pw, 0, 3) . "******";
echo "<br/>\n";
//$enc = eth1b($pw);

//echo "<br/>\n ENC : $enc |";


$fromName = "DAV EDU VN TEST ";
$obj = new ClassMailV2();
$obj->Username = $fromMail;
//Chua co cho luu password
//$obj->Password = dfh1b(explode(',', env('NCBD_ACC'))[1]);
$obj->Password = $pw;

$obj->Host = "smtp.office365.com";
$obj->Port = "587";
$obj->SMTPSecure = 'tls';
$obj->From = $fromMail;
$obj->addReplyTo('events@dav.edu.vn', $fromName);
$obj->FromName = $fromName;
$obj->toAddress = 'dungbkhn02@gmail.com';
//$obj->toAddress = 'dungbkhn@yahoo.com';
$obj->Body = "test email content";
$obj->Subject = "test email title " . date('Y-m-d H:i:s');
$obj->debug = 1;

if (!$obj->sendMailGlx()) {
    echo "<br/>\n Error ? ";
    echo $obj->ErrorInfo;
} else {

    echo "<br/>\n Send OK?";
}
