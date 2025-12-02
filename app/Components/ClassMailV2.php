<?php

namespace App\Components;

use PHPMailer\PHPMailer\PHPMailer;

class ClassMailV2 extends PHPMailer
{
    public $From;
    public $FromName;
    public $toAddress;
    //    public $subject;
    //    public $content;
    public $debug;
    public $cc = [];
    public $Host = 'smtp.gmail.com';
    public $Port = 465;
    public $CharSet = 'UTF-8';
    public $SMTPAuth = true;
    public $SMTPDebug = 0;
    public $SMTPSecure = 'ssl';
    public $attachFile = [];

    //Lấy mail random từ env để gửi
    public static function getRandGmailMail()
    {
        $str = env('ACC_GMAIL');
        $m2 = explode(',', $str);
        $mm = [];
        for ($i = 0; $i < count($m2); $i += 2) {
            $mm[$i / 2] = [$m2[$i], $m2[$i + 1]];
        }
        $rand = rand(0, count($mm) - 1);

        return $mm[$rand];
    }

    public function sendMailGlx()
    {
        if (!$this->toAddress) {
            $this->ErrorInfo = 'not found to address!';
            return 0;
            loi('not found to address!');
        }

        if (!filter_var($this->toAddress, FILTER_VALIDATE_EMAIL)) {
            $this->ErrorInfo = 'not valid toArrdess ' . $this->toAddress;
            return 0;
        }

        if($this->debug){
            $this->SMTPDebug = 2;
        }


        $this->IsSMTP(); // enable SMTP
        $this->IsHTML(true);

        $this->AddReplyTo("$this->From", "$this->FromName");
        if (is_string($this->toAddress)) {
            $this->AddAddress($this->toAddress);
        }
        if (is_array($this->toAddress)) {
            foreach ($this->toAddress as $em) {
                if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
                    $this->AddAddress($em);
                }
            }
        }

        if ($this->attachFile && is_array($this->attachFile)) {
            foreach ($this->attachFile as $path => $name) {
                if (file_exists($path) && $name) {
                    $this->AddAttachment($path, $name);
                }
            }
        }

        return $this->send();
    }

    public static function testSendMail()
    {

        $obj = new ClassMailV2();
        $mailAndPwRand = ClassMailV2::getRandGmailMail();
        $obj->Username = $mailAndPwRand[0];
        $obj->Password = dfh1b($mailAndPwRand[1]);

        $obj->From = 'dungla2011@gmail.com';
        $obj->FromName = 'ABCTEST';
        $obj->attachFile = ['/var/glx/upload_file_glx/user_files/siteid_36/000/002/2899/2899' => 'f1.txt'];
        $obj->toAddress = '....@gmail.com';
        $obj->Body = 'Xin chào!';
        $obj->Subject = 'Hello test email attach!';
        if (! $obj->sendMailGlx()) {
            echo $obj->ErrorInfo;
        }
    }

    function testSendMailMicrosoft()
    {

        $obj = new ClassMailV2();

        $mailAndPwRand = ClassMailV2::getRandGmailMail();

        $obj->Username = "events@dav.edu.vn";
        $obj->Password = "....";

        $obj->Host = "smtp.office365.com";
        $obj->Port = "587";
        $obj->SMTPSecure = 'tls';

        $obj->From = $obj->Username;
        $obj->FromName = 'ABC Test';

        //$obj->attachFile = ['/var/glx/upload_file_glx/user_files/siteid_36/000/002/2899/2899' => 'f1.txt'];

        $obj->toAddress = '...@gmail.com';
        $obj->Body = 'Xin chào!';
        $obj->Subject = 'Hello test email attach!';
        if (! $obj->sendMailGlx()) {
            echo $obj->ErrorInfo;
        }


    }
}
