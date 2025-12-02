<?php

//cdg
//php public/train/websocket/Workerman/001-server-ok1.php start

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

require_once '/var/www/html/public/index.php';


use Workerman\Worker;
use Workerman\WebServer;



function getUserFromHeader($headers)
{
    $tkStr = '_tglx863516839=';
    $tk = '';
    foreach ($headers as $header) {
        if (strpos($header, 'GET /') === 0) {
            $tk = explode('tkx=', $header)[1];
            $tk = explode(" ", $tk)[0];
            echo "\n FOUND TK from GET..." .' - '. nowyh();;
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
    $user = \App\Models\User::where("token_user", $tk)->first();
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

// Create WebSocket server with SSL context
$ws_worker = new Worker("websocket://0.0.0.0:51111", $context);

// Enable SSL
$ws_worker->transport = 'ssl';

// Number of processes (worker threads)
$ws_worker->count = 4;

$ws_worker->onWebSocketConnect = function($connection, $http_header) {
    echo "\n\n Headers: \n$http_header\n";
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
    echo "\n Found_USER = $user->id / $user->email".' - '. nowyh();

    //Gửi 1 message tới client:
    $connection->send("ws_logined:$user->email");

};

// Handle new connections
$ws_worker->onConnect = function ($connection) {
    echo "\nNew connection: " . $connection->getRemoteAddress() .'-'. nowyh();

};

// Handle incoming messages from clients
$ws_worker->onMessage = function ($connection, $data) {
    if($data == 'ping1')
        return;
    $time = nowyh();
    echo "\nReceived message ($connection->user_id / $connection->email): $data - $time \n";
    $connection->send("UID = $connection->user_id ($connection->email)  , Server: $data"); // Send back to client
};

// Handle connection closure
$ws_worker->onClose = function ($connection) {
    echo "\nConnection closed (uid = $connection->user_id) ". nowyh();
};

// Start the server
Worker::runAll();

?>
