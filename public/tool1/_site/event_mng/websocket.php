<?php


//cdg
//php public/train/websocket/Workerman/001-server-ok1.php start

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

//Đoạn code chạy độc lập không dùng index.php
//Mẫu Như sau thì ko bị báo lỗi header, nếu ko thì bị báo lỗi header sent, không rõ tại sao...
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


check_unique_script();

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

function ol1($str, $echo = 1)
{
    if($echo)
        echo "\n$str";
    $time = nowyh();
    file_put_contents('/var/glx/weblog/web_socket_event_app.txt', "$time#".$str."\n", FILE_APPEND);
}

function getUserFromHeader($headers)
{
    $tkStr = '_tglx863516839=';
    $tk = '';
    $isAppMobile = false;
    foreach ($headers as $header) {
        if (strpos($header, 'GET /') === 0) {
            $tk = explode('tkx=', $header)[1];
            $tk = explode(" ", $tk)[0];
            echo "\n FOUND TK from GET... ($header)" .' - '. nowyh();;
            if(str_contains($header, 'mobile=1'))
                $isAppMobile = true;
            break;
        }
        if (strpos($header, 'Cookie: ') === 0) {
            $cookie = trim(substr($header, 8));
            // Extract the specific cookie _tglx863516839
            $cookie = explode(';', $cookie);
            foreach ($cookie as $c) {
                $c = trim($c);  // Remove leading/trailing spaces
                if (str_starts_with($c, $tkStr)) {
                    $tk = str_replace($tkStr, '', $c);
                    echo "\n FOUND TK from Cookie..." .' - '. nowyh();;
                    break;
                }
            }
        }
        if ($tk)
            break;
    }
    echo "\n\n Get user from Token: \n";
//    $user = \App\Models\User::where("token_user", $tk)->first();
    $user = \App\Models\User::getUserByTokenAccess($tk);
    if(!$user)
        return null;
    $user->is_mobile = $isAppMobile;
    return $user;
}


// Path to SSL certificate and key files
$context = [
    'ssl' => [
        'local_cert'  => '/etc/letsencrypt/live/events.dav.edu.vn/fullchain.pem', // Path to your SSL certificate
        'local_pk'    => '/etc/letsencrypt/live/events.dav.edu.vn/privkey.pem',  // Path to your SSL key
        'verify_peer' => false,
    ]
];

$global_last_check_ping_to_disconnect = 0;
$global_uid = 0;

// Khi người dùng kết nối, gắn uid cho kết nối và thông báo cho tất cả người dùng khác
function handle_connection($connection)
{
    global $text_worker, $global_uid;
    // Gắn một uid cho kết nối này
    $connection->index_con = ++$global_uid;
}

function getAllUserInfo(){

    checkLastPingToDisconnect();

    global $text_worker;
    $str = "\n";
    foreach ($text_worker->connections as $conn) {
        //15 giay truoc, mobile nay co ping
        $str .= "#Con = ".$conn->index_con ;
        $lastPing = nowyh($conn->last_ping ?? '');

        if(!isset($conn->user_id)){
            continue;
        }

        $isMobile = $conn->is_mobile ? '(Mobile)' : '';

        $str .= " - " . ($conn->user_id ?? '') .' - '. ($conn->email ?? '') . " $isMobile, Lastping: $lastPing ; ";
        $str .= "\n";
    }
    return $str;
}

//Mot so connection, khi bi ngat mang, van luu trong list, qua 30 giay khong co ping, thi disconnect
function checkLastPingToDisconnect()
{
    global $global_last_check_ping_to_disconnect;

    if(time() - $global_last_check_ping_to_disconnect < 30)
        return;
    $global_last_check_ping_to_disconnect = time();

    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        if($conn->user_id ?? '')
        if($conn->last_ping  ?? '')
        {
            if($conn->last_ping < time() - 60){
                $conn->close();
            }
        }
    }
}


// Khi người dùng gửi tin nhắn, chuyển tiếp tin nhắn cho tất cả mọi người
function handle_message(TcpConnection $connection, $data)
{

    $ipAddress = $connection->getRemoteIp();

    global $text_worker;

    $connection->last_ping  = time();
    if($data == 'ping1') {

        if($connection->index_con ?? '')
            if($connection->is_mobile ?? '')
                if($connection->user_id ?? '')
                    if($connection->email ?? '')
        {
            $ipAddress = $connection->getRemoteIp();
            file_put_contents("/var/glx/weblog/monitor_mobile_android_online.log",
                nowyh() . "# Ping1: $connection->index_con , $connection->user_id - $connection->email, $ipAddress \n", FILE_APPEND);
        }

        return;
    }

    echo "\n Client SAID: $data |$ipAddress " . nowyh();
    echo "\nAllUser: " . getAllUserInfo();


    if(str_starts_with($data, 'ping_check_alive_mobile:')){


        //Duyệt tất cả các uid mobile xem 1 cái online là ok:
        $isAlive = false;
        $emailUser = '___empty___';
        $timeLastPing = '';
        foreach ($text_worker->connections as $conn) {
            //15 giay truoc, mobile nay co ping
//            if($conn->is_mobile && $conn->last_ping > time() - 15)
            if($conn->is_mobile)
                {
//                $uidMobileOnline = $conn->user_id;
                $timeLastPing = $conn->last_ping;
                $emailUser = getCurrentUserEmail($conn->user_id);
                if($emailUser == 'tranthithuyduong@dav.edu.vn' || $emailUser ==  "nguyenhungson2005@yahoo.com"){
                    $isAlive = true;
                    break;
                }
            }
        }
        //Gửi tin đến client cho biết mobile có hoạt động
        if($isAlive)
            $connection->send("ping_check_alive_mobile:ok_alive:$emailUser, " . nowh($timeLastPing));
        else
            $connection->send("ping_check_alive_mobile:not_alive:");

        return;

        /////////////////////////////////////////////////////////////
        //Duyệt theo UID:
        //Duyệt các user trong connections, xem có user nào có id = $userid không, và is_mobile=1

        $userid = str_replace('ping_check_alive_mobile:', '', $data);
        //Duyệt các user trong connections, xem có user nào có id = $userid không, và is_mobile=1
        $isAlive = false;
        foreach ($text_worker->connections as $conn) {
            if($conn->user_id == $userid && $conn->is_mobile && $conn->last_ping > time() - 6){
                $isAlive = true;
                break;
            }
        }

        //Gửi tin đến client cho biết mobile có hoạt động
        if($isAlive)
            $connection->send("ping_check_alive_mobile:ok_alive:$userid");
        else
            $connection->send("ping_check_alive_mobile:not_alive:$userid");


        return;
    }


    if($data == 'send_all_sms_events_in_back_ground') {
        //Gui toi tat cac cac client:
        foreach ($text_worker->connections as $conn) {
            $conn->send($data);
        }
    }


    if(strstr($data, 'address') && strstr($data, 'body')){
        if($dtx = (json_decode($data) ?? '')) {
            print_r($dtx);
            return;
        }
    }

    $recipient_id = '';
    $msg = $data;
    if($json = (json_decode($data) ?? null)) {
        $recipient_id = $json->recipient_id ?? '';
        $msg = $json->message ?? $data;
    }

    echo "\n Sender and Msg: $recipient_id / " . substr($msg, 0, 50).'...';


    $cc = 0;
    //Tai sao can cai nay???
    if(0)
    foreach ($text_worker->connections as $conn) {
        if($recipient_id)
            if($conn->email ?? ''){
                if($conn->email != $recipient_id){
                    echo "\n Ignore $conn->email because send to $recipient_id";
                    continue;
                }
            }

        $uifo = getUserInfo($conn);
        $uifoFrom = getUserInfo($connection);
        $cc++;
        echo "\n$cc. Send MS to $uifo: " . substr($msg, 0, 50).'...';

//        $conn->send("$time : $uifoFrom said: $msg");
        $conn->send("$msg");

    }
}

function getUserInfo($connection)
{
    $str = $connection->index_con ." - ".($connection->user_id ?? '') .' - '. ($connection->email ?? '') . " - " . ($connection->is_mobile ?? '') . ' Last Ping' . nowyh($connection->last_ping);

    return $str;
}

// Khi người dùng ngắt kết nối, phát sóng cho tất cả người dùng khác
function handle_close($connection)
{
    global $text_worker;
    $ipAddress = $connection->getRemoteIp();
    $email = $connection->email ?? '';
    echo "\n Close Connection, Logout user, UID = $connection->index_con,$ipAddress, " . ($email ?? '');
//    $time = nowyh();
//    $uifo = getUserInfo($connection);
//    $conn->send("$time : $uifo logout | $ipAddress");

    $mailList = "";
    foreach ($text_worker->connections as $conn) {
        $ipAddress = $conn->getRemoteIp();
        $mailList .= $conn->email ?? '';
        if($conn->is_mobile ?? '')
            $mailList .= "(mobile),";
        else
            $mailList .= ",";
    }

    echo "\n Total User: " . count($text_worker->connections);
    ol1(" $ipAddress Logout user, UID = $email, number Connection: " . count($text_worker->connections));
    ol1(" User List: " . getAllUserInfo());

}

// Tạo một Worker với giao thức văn bản lắng nghe cổng 2347
$text_worker = new Worker("websocket://0.0.0.0:51111", $context);
$text_worker->transport = 'ssl';
// Chỉ kích hoạt 1 tiến trình, điều này làm cho việc truyền dữ liệu giữa các đối tượng kết nối dễ dàng hơn
$text_worker->count = 1;

$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';


$text_worker->onWebSocketConnect = function($connection, $http_header) use (&$clients) {

    global $text_worker;
    $ipAddress = $connection->getRemoteIp();
//    echo "\n\n Headers: \n$http_header\n";
    // Extract the Cookie header
    $headers = explode("\r\n", $http_header);

    echo "\n\n Get user from Token: \n";

    if(!$user = getUserFromHeader($headers)){
        echo "\n Not valid token? " .' - '. nowyh(); ;
        $connection->close();
        usleep(1000);
        return;
    }

    print_r($user->toArray());

    $connection->user_id = $user->id;
    $connection->email = $user->email;
    $connection->is_mobile = $user->is_mobile;
    $connection->last_ping  = time();
    //$clients[$connection->user_id] = $connection;
    echo "\n Found_USER = $user->id / $user->email".' - '. nowyh();

    //Gửi 1 message tới client:
    $connection->send("ws_logined:Đã kết nối tới Status Server!");

    $mailList = "";
    foreach ($text_worker->connections as $conn) {
        $mailList .= $conn->email  ?? '';
        if($conn->is_mobile ?? '')
            $mailList .= "(mobile),";
        else
            $mailList .= ",";
    }

    ol1(" $ipAddress | Login OK UID = $connection->email, number Connection: " . count($text_worker->connections));
    ol1(" User List: " . getAllUserInfo());

};


// Kiểm tra các kết nối định kỳ
//Worker::addPeriodicTimer(15, function() use ($text_worker) {
//    $current_time = time();
//    foreach ($text_worker->connections as $connection) {
//        if ($current_time - $connection->last_ping > 60) { // 120 giây không nhận được ping
//            echo "\n". getUserInfo($connection);
//            $connection->close();
//        }
//    }
//});

Worker::runAll();
