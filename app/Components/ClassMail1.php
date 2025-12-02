<?php

namespace App\Components;

use PHPMailer\PHPMailer\PHPMailer;

class ClassMail1
{
    //Filepath => FileName
    public static $attachedFile = [];

    public static $nAccForce = -1;

    public static $debug = 0;

    public static $mAcc = [
        ['dungbkhn02@gmail.com', '325954405853435b584b435c5b445e4245'],
        ['dungla2011@gmail.com', '33585b5145425a5049404b5956595a525b'],
        ['megamail.vn@gmail.com','35514058425b5147414151404345564f57'],
        ];

    public static function sendMailApi($url, $from, $fromName, $to, $subject, $content, $debug = 0, $cc1 = '', $cc2 = '', $cc3 = '', $cc4 = '', $cc5 = '')
    {

        if (! $url) {
            $url = 'https://4share.vn/tool/sendmail-api.php';
        }

        $post = ['from' => $from,
            'fromName' => $fromName,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'cc1' => $cc1,
            'cc2' => $cc2,
            'cc3' => $cc3,
            'cc4' => $cc4,
            'cc5' => $cc5,
            'debug' => $debug,
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        //        curl_setopt($ch, CURLOPT_POSTFIELDS,
        //            "postvar1=value1&postvar2=value2&postvar3=value3");

        // In real life you should use something like:
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($post));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }

    public static function sendMail($from, $fromName, $to, $subject, $content, $debug = 0, $cc1 = '', $cc2 = '', $cc3 = '', $cc4 = '', $cc5 = '')
    {
        $logFile = '/var/glx/weblog/mail/_sendMail.log';
        //require_once "/var/www/galaxycloud/application/library/phpmailer/class.phpmailer.php";
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = $debug; // debugging: 1 = errors and messages, 2 = messages only

        if (static::$debug) {
            $debug = $mail->SMTPDebug = 1;
        }

        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465; // or 587;
        //        $mail->Port = 587;
        //        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        if (ClassMail1::$nAccForce >= 0 && ClassMail1::$nAccForce < count(ClassMail1::$mAcc)) {
            $mail->Username = ClassMail1::$mAcc[ClassMail1::$nAccForce][0];
            $mail->Password = dfh1b(ClassMail1::$mAcc[ClassMail1::$nAccForce][1]);
        } else {
            $rand = rand(0, count(ClassMail1::$mAcc) - 1);
            $mail->Username = ClassMail1::$mAcc[$rand][0];
            $mail->Password = dfh1b(ClassMail1::$mAcc[$rand][1]);
        }

        //        if($debug){
        //            echo "<br/>\nMAIL_SENT = $mail->Username";
        //            $fromName .= " / $mail->Username / " . nowyh();
        //            $subject .= " / $mail->Username / " . nowyh();
        //        }

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

        //Nếu là dấu ,
        if (strstr($cc1, ',')) {
            $mMail = explode(',', $cc1);
            foreach ($mMail as $cc) {
                $cc = trim($cc);
                if (! empty($cc) && is_valid_email($cc)) {
                    $mail->AddCC("$cc");
                }
            }
        }
        // Đính kèm file vào email
        //$file_path = '/path/to/your/file.ext'; // Đường dẫn đến tệp cần đính kèm

        if (self::$attachedFile) {
            foreach (self::$attachedFile as $path => $name) {
                if (file_exists($path) && $name) {
                    $mail->AddAttachment($path, $name);
                }
            }

            //            print_r(self::$attachedFile);
            //            getch("...");
        }

        if ($mail->Send()) {
            //            outputT($logFile, " Sendmail OK: To $to, From $from, Acc $mail->Username");
            if ($debug) {
                echo "\n<br>Mailer OK";
            }

            return true;
        } else {
            if ($debug) {
                echo "\n<br>Mailer Error: ".$mail->ErrorInfo;
            }

            //            outputT($logFile, " *** Error sendmail: To $to, From $from, Acc $mail->Username  / Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function testSendMail()
    {

        $t1 = time();
        for ($i = 0; $i < count(ClassMail1::$mAcc); $i++) {
            $acc = ClassMail1::$mAcc[$i][0];
            outputT(LOG_FILE_DAILY_TEST, " Test maiml: $acc");
            echo " \n --- SendMail $acc";
            $ret = ClassMail1::sendMail('dungla2011@gmail.com', 'Test Mail Glx', 'dungla2011@gmail.com', "Test mail ok: $acc", 'DONE', 1);
            if ($ret) {
                echo "<br/>\n --- OK MAIL $acc";
                outputT(LOG_FILE_DAILY_TEST, " OK send mail with acc : $acc");
            } else {
                echo "<br/>\n *** Error MAIL $acc";
                outputT(LOG_FILE_DAILY_TEST, " *** Error send mail with acc : $acc");
            }
            sleep(1);
        }

        echo "<br/>\n DTIME = ".(time() - $t1);
    }
}
