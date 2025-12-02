<?php

use App\Components\ClassMail1;
use PHPMailer\PHPMailer\PHPMailer;

require_once '../../index.php';

$mm = 'dungbkhn02@gmail.com
admin@glx.com.vn
';

$mm = explode("\n", $mm);
//
//$cont = "Chào bạn!<br> Phần mềm GiaPha.Galaxycloud.vn đã chuyển sang https://mytree.vn với cập nhật phiên bản mới, cải tiến rất nhiều sự thuận tiện dành cho bạn!
//    <br>
//    Xin mời bạn truy cập sử dụng tại  <a href='https://mytree.vn'>https://mytree.vn </a>
//    <br>
//    <a href='https://mytree.vn'><img style='max-width: 600px' src='https://mytree.vn/images/mytree/guide1.png' alt=''></a>
//    <br>
//Trân trọng cảm ơn bạn!<br>
//https://mytree.vn<br>
//-------------------------------<br>
//Bạn nhận được Email này vì đã từng đăng ký và sử dụng phần mềm GiaPha.Galaxycloud.vn
//<br>
//Nếu bạn muốn hủy không nhận các email cập nhật từ chúng tôi, xin vui lòng bấm vào link dưới đây: <br> https://mytree.vn/unsubscribe_email?email=$email
//<br>
//Trân trọng cảm ơn bạn!<br>
//";
//
//$title = "MyTree - Phần mềm Gia phả cập nhật phiên bản mới - 03.2023";


echo \App\Components\ClassMail1::sendMail('admin@glx.com.vn', 'MyTree.vn', 'dungla2011@gmail.com', "ABC", "123", 1);

return;


$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only

$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465; // or 587;
//        $mail->Port = 587;
//        $mail->Port = 587;
$mail->CharSet = 'UTF-8';


    $mail->Username = 'dungbkhn02@gmail.com';
    $mail->Password = 'kfrjaqijyqnivlpw';

    $mail->Username = 'dungla2011@gmail.com';
    $mail->Password = 'khbvqiczsxjejiah';

    $mail->Username = 'megamail.vn@gmail.com';
    $mail->Password = 'dumwndrttduvpczb';



//        if($debug){
//            echo "<br/>\nMAIL_SENT = $mail->Username";
//            $fromName .= " / $mail->Username / " . nowyh();
//            $subject .= " / $mail->Username / " . nowyh();
//        }
$from = 'dungla2011@gmail.com';
$fromName = "DUNGLA";
$subject = " Thư mời họp - Test send mail";
$content = "ABC";
$to = "dungla2011@gmail.com";
$mail->From = "$from";
$mail->FromName = "$fromName";
$mail->AddReplyTo("$from", "$fromName");
$mail->IsHTML(true);
$mail->Subject = "$subject";
$mail->Body = "$content";
$mail->AddAddress($to);

if (! empty($cc1) && is_valid_email($cc1)) {
    $mail->AddCC("$cc1");
}
if (! empty($cc2) && is_valid_email($cc2)) {
    $mail->AddCC("$cc2");
}
if (! empty($cc3) && is_valid_email($cc3)) {
    $mail->AddCC("$cc3");
}
if (! empty($cc4) && is_valid_email($cc4)) {
    $mail->AddCC("$cc4");
}
if (! empty($cc5) && is_valid_email($cc5)) {
    $mail->AddCC("$cc5");
}

if ($mail->Send()) {
    //            outputT($logFile, " Sendmail OK: To $to, From $from, Acc $mail->Username");

        echo "\n<br>Mailer OK";


    return true;
} else {

        echo "\n<br>Mailer Error: ".$mail->ErrorInfo;

    //            outputT($logFile, " *** Error sendmail: To $to, From $from, Acc $mail->Username  / Error: " . $mail->ErrorInfo);
    return false;
}
