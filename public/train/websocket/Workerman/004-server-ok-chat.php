<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require_once '/var/www/html/public/index.php';
require_once "lib.php";
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Connection\TcpConnection;

// Path to SSL certificate and key files
$context = [
    'ssl' => [
        'local_cert'  => '/etc/letsencrypt/live/mytree.vn-0002/fullchain.pem', // Path to your SSL certificate
        'local_pk'    => '/etc/letsencrypt/live/mytree.vn-0002/privkey.pem',  // Path to your SSL key
        'verify_peer' => false,
    ]
];

// Create WebSocket server with SSL context
//$ws_worker = new Worker("websocket://0.0.0.0:51112", $context);




$global_uid = 0;

// Khi người dùng kết nối, gắn uid cho kết nối và thông báo cho tất cả người dùng khác
function handle_connection($connection)
{
    global $text_worker, $global_uid;
    // Gắn một uid cho kết nối này
    $connection->uid = ++$global_uid;
}

// Khi người dùng gửi tin nhắn, chuyển tiếp tin nhắn cho tất cả mọi người
function handle_message(TcpConnection $connection, $data)
{
    echo "\n Client SAID: $data ";
    global $text_worker;
    $time = nowyh();


    $recipient_id = '';
    $msg = $data;
    if($json = (json_decode($data) ?? null)) {
        $recipient_id = $json->recipient_id ?? '';
        $msg = $json->message ?? $data;
    }
    echo "\n Recever and Msg: $recipient_id / $msg ";

    $cc = 0;
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
        echo "\n$cc. Send MS to $uifo: $msg ";
        $conn->send("$time : $uifoFrom said: $msg");
    }
}

function getUserInfo($connection)
{
    return $connection->uid ." - ".($connection->user_id ?? '') .' - '. ($connection->email ?? '');
}

// Khi người dùng ngắt kết nối, phát sóng cho tất cả người dùng khác
function handle_close($connection)
{
    $time = nowyh();
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $uifo = getUserInfo($connection);
        $conn->send("$time : $uifo logout");
    }
}

// Tạo một Worker với giao thức văn bản lắng nghe cổng 2347
$text_worker = new Worker("websocket://0.0.0.0:51112", $context);
$text_worker->transport = 'ssl';
// Chỉ kích hoạt 1 tiến trình, điều này làm cho việc truyền dữ liệu giữa các đối tượng kết nối dễ dàng hơn
$text_worker->count = 1;

$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';


$text_worker->onWebSocketConnect = function($connection, $http_header) use (&$clients) {
//    echo "\n\n Headers: \n$http_header\n";
    // Extract the Cookie header
    $headers = explode("\r\n", $http_header);

    echo "\n\n Get user from Token: \n";
    if(!$user = getUserFromHeader($headers)){
        echo "\n Not valid token? " .' - '. nowyh(); ;
        $connection->close();
        return;
    }
    $connection->user_id = $user->id;
    $connection->email = $user->email;
    //$clients[$connection->user_id] = $connection;
    echo "\n Found_USER = $user->id / $user->email".' - '. nowyh();

    //Gửi 1 message tới client:
    $connection->send("ws_logined:$user->email");

};


Worker::runAll();
