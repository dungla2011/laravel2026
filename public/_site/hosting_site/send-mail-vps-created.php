<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Components\ClassMailV2;
use App\Components\Helper1;
use App\Helpers\BkavEHoaDonAPI;
use App\Models\MailLog;
use App\Models\VpsOsVersion;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$remoteIP = $_SERVER['REMOTE_ADDR'] ?? '';

if($remoteIP != '127.0.0.1')
{
//    die("Only allow local access!");
}

$iid = request('instance_id');
if(!$objI = \App\Models\VpsInstance::find($iid)){
    die("Not valid iid");
}

$userid = $objI->user_id;
if(!$user = \App\Models\User::find($userid)){
    die("Not valid uid");
}
$toEmail = $user->email;

$vpsOs = VpsOsVersion::find($objI->init_os ?? '');
$username = '';
if($vpsOs)
    if($vpsOs->username){
        $username = $vpsOs->username;
    }
$ipList = "IP Khởi tạo: $objI->init_ip";
$upw = "P".substr(md5($objI->bios_uuid), -8)."@12";

$mailAndPwRand = ClassMailV2::getRandGmailMail();

$obj = new ClassMailV2();
$obj->isHTML(false);
$obj->debug = 1;

$obj->Username = $mailAndPwRand[0];
$obj->Password = dfh1b($mailAndPwRand[1]);

//echo ("<br>$obj->Username <br>");

$obj->From = 'sale@glx.com.vn';
$obj->FromName = 'GLX.COM.VN';
//$obj->attachFile = ['/var/glx/upload_file_glx/user_files/siteid_36/000/002/2899/2899' => 'f1.txt'];
$obj->toAddress = $toEmail;
$obj->Subject = "GLX.COM.VN - VPS đã tạo thành công: $iid";
$obj->Body = "Chào bạn! <br>
<br>
GalaxyCloud xin thông báo VPS đã tạo thành công với thông tin:<br>
Mã số VPS: $iid <br>
Địa chỉ IP: $objI->init_ip <br>
Username: $username <br>
Password: $upw <br>
<br>
(Bạn vui lòng đổi mật khẩu VPS để bảo mật!)<br>
<br>
Quản trị VPS:  https://glx.com.vn/member/vps-instance/edit/$iid <br>

Xin cảm ơn bạn!<br>
----------------------<br>
https://glx.com.vn/member
";

$pram = 'mail_created_vps_'. $iid;
if(MailLog::where('to_email', $toEmail)->where('param', $pram)->first()){
    die("Ignore!");
}

if (!$obj->sendMailGlx()) {
    echo "Error send mail : " . $obj->ErrorInfo;
    \App\Models\MailLog::addMail($toEmail, $obj->Subject, $obj->Body, 'error_send_mail');
}else{
    $obj->Body = str_replace($upw, "..." , $obj->Body);
    \App\Models\MailLog::addMail($toEmail, $obj->Subject, $obj->Body, $pram);
}

