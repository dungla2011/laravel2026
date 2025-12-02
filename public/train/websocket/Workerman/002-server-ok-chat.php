<?php

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
$ws_worker = new Worker("websocket://0.0.0.0:51112", $context);

// Enable SSL
$ws_worker->transport = 'ssl';

// Number of processes (worker threads)
$ws_worker->count = 4;

// Store connected clients
$clients = [];

$ws_worker->onWebSocketConnect = function($connection, $http_header) use (&$clients) {
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
    $clients[$connection->user_id] = $connection;
    echo "\n Found_USER = $user->id / $user->email".' - '. nowyh();

    //Gửi 1 message tới client:
    $connection->send("ws_logined:$user->email");

};

// Handle new connections
$ws_worker->onConnect = function ($connection) {
    echo "\nNew connection: " . $connection->getRemoteAddress() .'-'. nowyh();

};

// Handle incoming messages from clients
$ws_worker->onMessage = function ($connection, $data) use (&$clients) {

    if($data == 'ls'){
        echo "\n LIST ALL CLIENTS: \n";
        foreach ($clients as $key => $value) {
            echo "\n $key ";
        }
        return;
    }

    echo "\nMessage: $data \n";

    $messageData = json_decode($data, true);
    $recipientId = $messageData['recipient_id'] ?? null;
    $message = $messageData['message'] ?? '';

    if($message == 'ls'){
        echo "\n LIST ALL CLIENTS: \n";
        foreach ($clients as $key => $value) {
            echo "\n $key ";
        }
        return;
    }

    if ($recipientId && isset($clients[$recipientId])) {
        $clients[$recipientId]->send("Message from {$connection->user_id}: $message");
    } else {
        echo "\nRecipient not found or not connected\n";
    }
};

// Handle connection closure
$ws_worker->onClose = function ($connection) use (&$clients) {

    if(!$clients)
        return;
    echo "\nConnection closed (uid = $connection->user_id) ". nowyh();
    unset($clients[$connection->user_id]);
};

// Start the server
Worker::runAll();

?>
