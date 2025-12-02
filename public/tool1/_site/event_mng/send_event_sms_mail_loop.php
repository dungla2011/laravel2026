<?php

use App\Models\EventSendAction;

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

if(str_contains(gethostname(), 'mytree'))
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';
//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

check_unique_script();

//Kiểm tra các event xem đã hoàn thành chưa, nếu chưa hoàn thành thì ra lệnh chạy độc lập - ngầm
//mục đích là để có nhiều process song song
//Tách ra sms và email để chạy song song

while (1) {
    sleep(1);
    //tìm EventSendAction với done = 0 hoặc done IS null
    $mm = EventSendAction::where('done', 0)->orWhereNull('done')->get();

    //Ngày tạo không quá 24h
//    $mm = $mm->filter(function ($item) {
//        return strtotime($item->created_at) > strtotime('-1 day');
//    });

    echo("\n--- Start sendAllMessageLoop1");
    if (!$mm) {
        echo("\n Not have event?");
        continue;
    }
    foreach ($mm as $eventSendAction) {
        //$eventSendAction->created_at   nếu quá 24h thì thôi ko gửi:
        if (strtotime($eventSendAction->created_at) < strtotime('-1 day')) {
            echo "\n Quá 24h không gửi nữa! $eventSendAction->id,  $eventSendAction->event_id $eventSendAction->type, $eventSendAction->created_at";
            usleep(100000);
            continue;
        }

        echo "\n $eventSendAction->id,  $eventSendAction->event_id $eventSendAction->type, $eventSendAction->created_at";

        sleep(1);
        //Tách ra sms và email để chạy song song
        $cmd0 = "php /var/www/html/public/tool1/_site/event_mng/send_one_event_sms_mail.php $eventSendAction->event_id $eventSendAction->type";
        $cmd = "$cmd0 > /dev/null 2>&1 &";

        //ps -ef xem có  lệnh này chưa, nếu có thì không chạy
//        $cmdCheck = "ps -ef | grep '$cmd0' ";
        $cmdCheck = "ps -ef | grep '$cmd0' | grep -v grep";
        $output = shell_exec($cmdCheck);

//        echo "\n  OP = $output";

        if ($output ) {
            echo "\n Lệnh đang chạy, không chạy tiếp!";
            continue;
        }

        echo "\n RUN now: cmd = $cmd";
        exec($cmd);
    }
    echo "\n END";
    sleep(10);
}
