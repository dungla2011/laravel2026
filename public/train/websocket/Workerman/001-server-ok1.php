<?php

//cdg
//php public/train/websocket/Workerman/001-server-ok1.php start

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require_once '/var/www/html/public/index.php';
require_once "lib.php";
use Workerman\Worker;
use Workerman\WebServer;

// Path to SSL certificate and key files
$context = [
    'ssl' => [
        'local_cert'  => '/etc/letsencrypt/live/mytree.vn-0002/fullchain.pem', // Path to your SSL certificate
        'local_pk'    => '/etc/letsencrypt/live/mytree.vn-0002/privkey.pem',  // Path to your SSL key
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
        echo "\n Not valid token?\n";
        $connection->close();
        return;
    }
    $connection->user_id = $user->id;
    $connection->email = $user->email;
    echo "\n Found_USER = $user->id / $user->email";


};

// Handle new connections
$ws_worker->onConnect = function ($connection) {


    echo "\nNew connection: " . $connection->getRemoteAddress();

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
    echo "\nConnection closed (uid = $connection->user_id)\n";
};

// Start the server
Worker::runAll();

?>
