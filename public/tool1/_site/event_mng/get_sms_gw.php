<?php
//Đoạn code chạy độc lập không dùng index.php
//Mẫu Như sau thì ko bị báo lỗi header, nếu ko thì bị báo lỗi header sent, không rõ tại sao...
use App\Models\EventSendInfoLog;
use Pusher\Pusher;

error_reporting(E_ALL);
ini_set('display_errors', 1);



require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Kết thúc đoạn Init Framework Laravel
////////////////////////////////////////////////////////////////////

//loi("Start");

function ol1($str)
{
    file_put_contents("/var/glx/weblog/ncbd_editor_call_back_docx.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);
}

$uid = getCurrentUserId();

if($sms_id_sent = request('sms_id_sent_before')){

    $sms_id_sent = intval($sms_id_sent);
    $sms_id_sent = $sms_id_sent ? $sms_id_sent : 0;
    //Là ID của EventSendInfoLog
    $evSendLog = \App\Models\EventSendInfoLog::find($sms_id_sent);
    if(!$evSendLog){
        echo "Error: Not found EventSendInfoLog with ID = $sms_id_sent";
        return;
    }
    if($evSendLog->done_at){
        if(!$evSendLog->status){
            $evSendLog->status = 1;
            $evSendLog->save();
        }
        echo "Log Sent Before ok on server";
        return;
    }

    $evSendLog->done_at = nowyh();
    $evSendLog->status = 1;
    $evSendLog->save();
    $evSendLog->addLog("Sms have in Inbox of Phone, it sent before so ignore!",1);

    die("Log Sent Before ok on server");

}


if($sms_id_sent = request('sms_id_sent')){

    $sms_id_sent = intval($sms_id_sent);
    $sms_id_sent = $sms_id_sent ? $sms_id_sent : 0;
    //Là ID của EventSendInfoLog
    $evSendLog = \App\Models\EventSendInfoLog::find($sms_id_sent);
    if(!$evSendLog){
        echo "Error: Not found EventSendInfoLog with ID = $sms_id_sent";
        return;
    }

    //Có thể check ở đây nếu cần chặt chẽ
//    if($evSendLog->user_id != $uid){
//        echo "Error: Not your event ? $evSendLog->user_id != $uid | id EventSendLog : $sms_id_sent";
//        return;
//    }



    $evSendLog->done_at = nowyh();
    $evSendLog->status = 1;
    $evSendLog->addLog(" Seem OK, found SMS in Inbox on PHONE");
    $evSendLog->save();

    //Đếm số EventSendInfoLog có session_id = $evSendLog->session_id
//    $countEventSendInfoLog = \App\Models\EventSendInfoLog::where('session_id', $evSendLog->session_id)->count();

    //session_id là id của eventSentAction
    if($eventSendAction = \App\Models\EventSendAction::find($evSendLog->session_id)){
        if(!$eventSendAction->done){
            $eventSendAction->done = 1;
//            $eventSendAction->status = 1;
            $eventSendAction->addLog("Make DONE by OneSMS sent");
            $eventSendAction->save();
        }
    }

//    if(!$eventSendAction->count_success)
//        $eventSendAction->count_success = 1;
//    else
//        $eventSendAction->count_success++;
//    $eventSendAction->save();

    $options = [
        'cluster' => 'ap1',
        'useTLS' => true,
    ];

    $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        $options
    );


    $chanelPusher = $eventSendAction->pusher_chanel;


    //Đếm tổng số đã hoàn thaành của $eventSendAction
//    $countDone = \App\Models\EventSendInfoLog::where('session_id', $evSendLog->session_id)->whereNotNull('done_at')->count();

    $countDone = EventSendInfoLog::where('session_id', $evSendLog->session_id)->where('status',1)->count();

    $totalNeedSend = \App\Models\EventSendInfoLog::where('session_id', $evSendLog->session_id)->count();

    $data['message'] = " Đã gửi smsid: $sms_id_sent, DONE: $countDone / $totalNeedSend";

//<a target='_blank' href='/admin/event-send-info-log?seby_s4=$eventId&seoby_s5=C&seoby_s15=C&seby_s15=$eventSendAction->id'> <u>Xem chi tiết Tại đây! </u> </a> ";
    $data['event_id'] = $eventId = $eventSendAction->event_id;
    $pusher->trigger($chanelPusher, "my-event-pusher-web-$eventId", $data);

    die(" DONE UPDATE STATUS SENT, id=$sms_id_sent!");
    return;
}

if(request('get_list_sms_to_send')) {

    $auto_send_sms = request('auto_send_sms');

    header('Content-Type: application/json');

    //Mảng EventID => số SMS cần gửi
    $arrayEventAndNumberSMS = [];
//    echo "\nxxx1 $uid";
    $mSMS = [];

    /////////////////////////////////////////////////
    //Tìm tất cả eventInfo có user_id này
    //$eventInfo = \App\Models\EventInfo::where('user_id', $uid)->get();

    /////////////////////////////////////////////////
    //$eventInfo = \App\Models\EventInfo::getEventIdListInDeparmentOfUser($uid, 1);

    /////////////////////////////////////////////////
    //Tat ca su kien cua moi user
//    $eventInfo = \App\Models\EventInfo::all();
    $eventInfo = \App\Models\EventInfo::where("id", '<>',6)->get();

    //For testing, nếu query lên là mail naày, thì chỉ gửi event của user nay
    if(getCurrentUserEmail() == 'megamail.vn@gmail.com'){
//        $eventInfo = \App\Models\EventInfo::getEventIdListInDeparmentOfUser(getUserIdByEmail_('megamail.vn@gmail.com'), 1);
        //Chỉ lấy Event 6
        $eventInfo = \App\Models\EventInfo::where("id",6)->get();
//        die("testing123");

    }

    ol00(" get_list_sms_to_send : Admin = " . getCurrentUserEmail() ." | " . $_SERVER['REMOTE_ADDR'] . " # " . request()->url());

    //In ra tất cả id của event
//    $strEventId = '';
//    foreach ($eventInfo AS $event){
//        $strEventId .= $event->id . ', ';
//    }
//    echo "\n- Tất cả Event: $strEventId";
//    die();

    $timeRequest = nowyh();

    $nSMSQuaGio = 0;
    $nEmptyContent = 0;
    $nChuaGui = 0;
    foreach ($eventInfo AS $event){
//        echo "\n xxx $event->id ";
        //Nếu event đã xong, thì ko gửi  nữa
        if(!$event->status){
            continue;
        }

        //Tìm tất cả các EventSendInfoLog của event này
//        $eventSendInfoLog = \App\Models\EventSendInfoLog::where('event_id', $event->id)->where('type', 'sms')->where('count_retry_send', '<', 3)->get();
        $eventSendInfoLog = \App\Models\EventSendInfoLog::where('event_id', $event->id)->where('status', 0)->where('type', 'sms')->get();
        $cc00 = 0;
        foreach ($eventSendInfoLog AS $evSendLog){
            //Da xong:
            if($evSendLog->status){
                continue;
            }
            $cc00++;

            $nChuaGui++;

            $evSendLog->last_app_sms_request_to_send = $timeRequest;

            if(!$evSendLog->count_retry_send)
                $evSendLog->count_retry_send = 1;
            else
                $evSendLog->count_retry_send++;
            //Không gửi nếu qua 3 lần

            //Nếu là tự động gửi, thì chỉ gửi 3 lần
            //Bằng tay thì thoải mái lần
            if($auto_send_sms)
            if($evSendLog->count_retry_send > 3){
                continue;
            }

            $evSendLog->save();

            if($evSendLog->done_at)
                continue;
            //Chỉ gửi các tin ra lệnh trong vòng 24h
            if($evSendLog->created_at < nowyh(time() - 86400)){
                $nSMSQuaGio++;
                continue;
            }

//            echo "\n xxx $evSendLog->id";

            $evUid = $evSendLog->event_user_id;
            //Lấy số Phone
            $eventUser = \App\Models\EventUserInfo::find($evUid);

            //Cập nhật phone number vào đây

            if(!$eventUser){
                $evSendLog->done_at = "Error: User not found";
                $evSendLog->addLog('Error: User not found');
                $evSendLog->save();
                continue;
            }


            $evSendLog->content_sms = trim($evSendLog->content_sms);
            if(!$evSendLog->content_sms){
                $nEmptyContent++;
                $evSendLog->done_at = "Error: Content SMS is empty";
                $evSendLog->addLog('Error: Content SMS is empty');
                $evSendLog->save();
                continue;
            }

            $phone = $eventUser->phone;
            //Nếu số phone hợp lệ:
            //$phone = str_replace([' ','.',',', '+', '(', ')'], '', $phone);
            //thay thế mọi ký tự không phải là số
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if(strlen($phone) < 9 || strlen($phone) > 11){
                $evSendLog->done_at = "error phone $phone";
                $evSendLog->addLog('Phone không hợp lệ');
                $evSendLog->save();
                continue;
            }

            //Cập nhật lại số phone ngay tại đây, để sau so sánh đươ
            $evSendLog->phone_send = $phone;
            $evSendLog->addLog("Send SMS content to Android App to send ($cc00)");

            if($evSendLog->last_app_sms_request_to_send && $evSendLog->last_app_sms_request_to_send > nowyh(time() - 3)){
                //Nếu đã gửi yêu cầu trong vòng 1 phút, thì không gửi nữa
//                $evSendLog->addLog('Error: Already sent request to app in last 3 seconds');
//                $evSendLog->save();
//                continue;
            }


            //Có lúc sẽ retry liên tục nếu Done_at không có update
            $evSendLog->last_app_sms_request_to_send = $timeRequest;
            $evSendLog->content_sms = $content_sms = removeSMSTextComments($evSendLog->content_sms);
            $evSendLog->save();




            $arrayEventAndNumberSMS[$event->id] = ($arrayEventAndNumberSMS[$event->id] ?? 0) + 1;

//            $evSendLog->done_at = $evSendLog->created_at;
//            $evSendLog->addLog("Done by LAD code");
//            $evSendLog->save();



            $mSMS[] =  ['id'=> $evSendLog->id  , 'phone_number' => $phone, 'event_id' => $evUid , 'message_content' => $content_sms];
        }
    }

    $strDescription = '';
    foreach ($arrayEventAndNumberSMS AS $k => $v){
        $strDescription .= "- Co $v SMS cua su kien so: $k\n";
    }

    echo json_encode(['info'=>"\n- Server ghi nhan can gui : \n$strDescription \n- App SMS bo qua cac tin da gui bang cach so sanh noi dung; Co $nSMSQuaGio qua han truoc 24h! $nEmptyContent Tin nhan Rong! Check All: $nChuaGui not success!" , 'sms_list' => $mSMS]);
    return;
}

if(request('check_token')){
    ob_clean();
//    die("seem_token_not_valid");
    if($uid){
        die("Đã đăng nhập: " .getCurrentUserEmail());
    }
    die("seem_token_not_valid");
}

/**
 * Server trả về danh sachs SMS cần sync lên, nếu Rỗng thì sẽ gửi hết
 */
if(request('get_list_phone_to_sync')){
    header('Content-Type: application/json');



//    $mm = [
//        'limit_sms' => 100,
//        'phone_numbers_end_with' => []
//    ];
//
//    echo json_encode($mm);
//    die();
////

    $mmPhone = [];
    //Tìm các số trong Event của user này
//    $eventInfo = \App\Models\EventInfo::where('user_id', $uid)->get();

    $eventInfo = \App\Models\EventInfo::getEventIdListInDeparmentOfUser($uid, 1);

    foreach ($eventInfo AS $event) {
//        echo "\n xxx $event->id ";
        //Nếu event đã xong, thì ko gửi  nữa
        if (!$event->status) {
            continue;
        }
        //Tìm tất cả các EventSendInfoLog của event này
        $eventSendInfoLog = \App\Models\EventSendInfoLog::where('event_id', $event->id)->where('type', 'sms')->get();
//        $eventSendInfoLog = \App\Models\EventSendInfoLog::where('event_id', $event->id)
//            ->where('type', 'sms')
//            ->where(function($query) {
//                $query->whereNull('done_at')
//                    ->orWhere('done_at', '');
//            })
//            ->get();

        foreach ($eventSendInfoLog as $evSendLog) {
            if ($evSendLog->done_at)
                continue;
            //Lấy số Phone
            $evUid = $evSendLog->event_user_id;
            $eventUser = \App\Models\EventUserInfo::find($evUid);
            $phone = $eventUser->phone;
            //Chỉ lây ra 9 so cuoi cua so dien thoai
            $mmPhone[] = substr($phone, -9);
        }
    }

    $mmPhone1 = [
//            '966616368',
//            '902066768',
//            '978777088',
        '3689'
    ];

    $mmPhone = array_values(array_unique($mmPhone));

    $limit  = 1000;
    if(!$mmPhone){
        $limit = 5;
    }

    $mm = [
        'limit_sms' => $limit,
        'phone_numbers_end_with' => $mmPhone
    ];

    echo json_encode($mm);
//
//    echo "send_sms_list:$uid";
    return;
}

if(request('sms_list_inbox')){
    $m1 = json_decode(request('sms_list_inbox'));
//    print_r($m1);
    $total = count($m1);
    echo "- Số sms gửi lên kiểm tra đồng bộ: $total";

    \App\Http\ControllerApi\EventInfoControllerApi::markJoinOrDenyEventSms($m1);


    $cc = $found = 0;
    $notHaveSMSId = $doneBefore = 0;

    foreach ($m1 AS $m){
        //Các tin SENT
        if($m->type != 2)
            continue;
        $cc++;
        $phone_number_on_mobile = $m->address;
        $message_content = $m->body;
        //Ở cuối tin nhắn có [SMS-<id>] là mã số EventSendInfoLog
        //lay ra id
        preg_match('/\[SMS-(\d+)\]$/', $message_content, $matches);
        $sms_id = $matches[1] ?? null;
        if(!$sms_id){
            $notHaveSMSId++;
//            echo "\n$cc . *** Error: Not found SMS ID in message content";
            continue;
        }
        $oneDone = 0;
        if($evSendLog =
            \App\Models\EventSendInfoLog::find($sms_id))
//                ->where("content_sms", $message_content)
//                ->where("phone_send", 'LIKE', "%$phone_number_on_mobile9")
//                ->whereNull('done_at')
//                ->orWhere('done_at', '')
//                ->orderBy('id', 'desc')
//                ->first())
        {
            if($evSendLog->done_at) {
                $oneDone = $evSendLog->session_id;
                $doneBefore++;
                continue;
            }
            $evSendLog->done_at = nowyh();
            $evSendLog->addLog("SMS found in Inbox on PHONE");
            $evSendLog->save();
            $oneDone = $evSendLog->session_id;
            $found++;


        }else{
            echo "\n$cc . *** Error: Not found SMS ID in DB: $sms_id";
        }

        if($oneDone)
        if($eventSendAction = \App\Models\EventSendAction::find($oneDone)){
            if(!$eventSendAction->done){
                $eventSendAction->done = 1;
                $eventSendAction->addLog("Make DONE by OneSMS sent");
                $eventSendAction->save();
            }
        }
    }

    echo "\n- Cần cập nhật Trạng thái 'Đã gửi': $found, \n- Đã xác nhận Đã gửi trước đây: $doneBefore\n".
    "- Không có SmsId nên không cập nhật trạng thái: $notHaveSMSId\n";

//    echo " Server get all: " . serialize(request('sms_list'));
    return;
}
