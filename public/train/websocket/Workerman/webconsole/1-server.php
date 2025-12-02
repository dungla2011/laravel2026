<?php
require_once '/var/www/html/public/index.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

$context = [
    'ssl' => [
        'local_cert'  => '/etc/letsencrypt/live/mytree.vn-0002/fullchain.pem', // Path to your SSL certificate
        'local_pk'    => '/etc/letsencrypt/live/mytree.vn-0002/privkey.pem',  // Path to your SSL key
        'verify_peer' => false,
    ]
];

// Create WebSocket server with SSL context
$ws_worker = new Worker("websocket://0.0.0.0:51115", $context);

//Cho phép ssl
$ws_worker->transport = 'ssl';

// Lưu trạng thái tiến trình
$ws_worker->processes = [];

// Xử lý khi client kết nối
$ws_worker->onConnect = function (TcpConnection $connection) {
    echo "New connection\n";
};

// Xử lý khi nhận được tin nhắn
$ws_worker->onMessage = function (TcpConnection $connection, $data) use ($ws_worker) {
    $message = json_decode($data, true);

    if ($message['action'] === 'execute') {
        $command = $message['command'];
        echo "Executing: $command\n";

        // Thực thi lệnh
        $descriptorspec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"]  // stderr
        ];
        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            $ws_worker->processes[$connection->id] = ['process' => $process, 'pipes' => $pipes];

            // Đọc kết quả
            while ($line = fgets($pipes[1])) {
                $connection->send($line);
            }
        }
    } elseif ($message['action'] === 'stop') {
        echo "\n Stop cms ...";
        if (isset($ws_worker->processes[$connection->id])) {
            $processInfo = $ws_worker->processes[$connection->id];
            proc_terminate($processInfo['process']); // Dừng tiến trình
            fclose($processInfo['pipes'][1]); // Đóng pipe stdout
            unset($ws_worker->processes[$connection->id]);
        }
        $connection->send("Process stopped.");
    }
};

// Xử lý khi client ngắt kết nối
$ws_worker->onClose = function (TcpConnection $connection) use ($ws_worker) {
    echo "Connection closed\n";
    // Dừng tiến trình nếu còn chạy
    if (isset($ws_worker->processes[$connection->id])) {
        $processInfo = $ws_worker->processes[$connection->id];
        proc_terminate($processInfo['process']);
        unset($ws_worker->processes[$connection->id]);
    }
};

// Chạy server
Worker::runAll();
